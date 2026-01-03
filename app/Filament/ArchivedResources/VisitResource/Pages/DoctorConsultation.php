<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Visit;
use App\Models\MedicalRecord;
use App\Models\DoctorInventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalUsageLog;
use App\Models\VitalSign;
use App\Models\Diagnosis;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action;

class DoctorConsultation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = VisitResource::class;

    protected static string $view = 'filament.resources.visit-resource.pages.doctor-consultation';

    public Visit $record;
    public ?array $data = [];

    public function mount(Visit $record): void
    {
        $this->record = $record;
        
        // Ensure only the assigned doctor can access
        if ($record->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this visit.');
        }

        $this->form->fill([
            'visit_id' => $record->id,
            'doctor_id' => Auth::id(),
            'patient_id' => $record->patient_id,
            'vitalSign' => [
                'weight' => null,
                'temperature' => null,
                'heart_rate' => null,
            ],
            'usageLogs' => [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Medical Record')
                            ->schema([
                                Forms\Components\Hidden::make('visit_id'),
                                Forms\Components\Hidden::make('doctor_id'),
                                Forms\Components\Hidden::make('patient_id'),

                                Forms\Components\Section::make('Vital Signs')
                                    ->schema([
                                        Forms\Components\TextInput::make('vitalSign.weight')
                                            ->label('Weight (kg)')
                                            ->numeric()
                                            ->live()
                                            ->required(),
                                        Forms\Components\TextInput::make('vitalSign.temperature')
                                            ->label('Temperature (C)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('vitalSign.heart_rate')
                                            ->label('Heart Rate (bpm)')
                                            ->numeric(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Diagnosis & Notes')
                                    ->schema([
                                        Forms\Components\Select::make('diagnoses')
                                            ->options(Diagnosis::pluck('name', 'id'))
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\Textarea::make('subjective')->label('Subjective (S)')->rows(3),
                                        Forms\Components\Textarea::make('objective')->label('Objective (O)')->rows(3),
                                        Forms\Components\Textarea::make('assessment')->label('Assessment (A)')->rows(3),
                                        Forms\Components\Textarea::make('plan_treatment')->label('Plan - Treatment (P)')->rows(3),
                                        Forms\Components\Textarea::make('plan_recipe')->label('Plan - Recipe')->rows(3),
                                    ])->columns(1),
                            ])->columnSpan(2),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Prescription & Services')
                            ->description('Items added here will sync to Invoice')
                            ->schema([
                                Forms\Components\Repeater::make('usageLogs')
                                    ->schema([
                                        Forms\Components\Select::make('doctor_inventory_id')
                                            ->label('Item/Service')
                                            ->options(function () {
                                                return DoctorInventory::where('user_id', Auth::id())
                                                    ->where('stock_qty', '>', 0)
                                                    ->get()
                                                    ->mapWithKeys(function ($item) {
                                                        return [$item->id => $item->item_name . ' (Stock: ' . $item->stock_qty . ')'];
                                                    });
                                            })
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state) {
                                                    $item = DoctorInventory::find($state);
                                                    if ($item) {
                                                        $set('unit_price', $item->selling_price);
                                                    }
                                                }
                                            }),
                                        Forms\Components\TextInput::make('quantity_used')
                                            ->label('Qty')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->reactive(),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Price')
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp'),
                                    ])
                                    ->columns(3)
                                    ->label('Items Used')
                                    ->live()
                            ]),
                            
                        Forms\Components\Section::make('Invoice Preview')
                            ->schema([
                                Forms\Components\Placeholder::make('total_estimate')
                                    ->label('Estimated Total')
                                    ->content(function (Get $get) {
                                        $items = $get('usageLogs');
                                        $total = 0;
                                        if (is_array($items)) {
                                            foreach ($items as $item) {
                                                $price = floatval($item['unit_price'] ?? 0);
                                                $qty = floatval($item['quantity_used'] ?? 0);
                                                $total += $price * $qty;
                                            }
                                        }
                                        // Add Transport Fee from Visit
                                        $transport = $this->record->transport_fee ?? 0;
                                        $total += $transport;
                                        
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    }),
                            ]),
                    ])->columnSpan(1),
            ])
            ->statePath('data')
            ->columns(3);
    }

    public function save()
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            // 1. Create Medical Record
            $record = MedicalRecord::create([
                'visit_id' => $this->record->id,
                'doctor_id' => Auth::id(),
                'patient_id' => $this->record->patient_id,
                'subjective' => $data['subjective'],
                'objective' => $data['objective'],
                'assessment' => $data['assessment'],
                'plan_treatment' => $data['plan_treatment'],
                'plan_recipe' => $data['plan_recipe'],
                'is_locked' => true,
            ]);

            // 2. Save Vital Signs
            if (!empty($data['vitalSign']['weight'])) {
                VitalSign::create([
                    'medical_record_id' => $record->id,
                    'weight' => $data['vitalSign']['weight'],
                    'temperature' => $data['vitalSign']['temperature'],
                    'heart_rate' => $data['vitalSign']['heart_rate'],
                ]);
            }

            // 3. Attach Diagnoses
            if (!empty($data['diagnoses'])) {
                $record->diagnoses()->sync($data['diagnoses']);
            }

            // 4. Create Usage Logs & Calculate Invoice Items
            $invoiceItems = [];
            foreach ($data['usageLogs'] as $log) {
                // Log Usage
                MedicalUsageLog::create([
                    'medical_record_id' => $record->id,
                    'doctor_inventory_id' => $log['doctor_inventory_id'],
                    'quantity_used' => $log['quantity_used'],
                ]);

                // Prepare Invoice Item
                $inventory = DoctorInventory::find($log['doctor_inventory_id']);
                $invoiceItems[] = [
                    'description' => $inventory->item_name,
                    'quantity' => $log['quantity_used'],
                    'unit_price' => $inventory->selling_price,
                    'unit_cost' => $inventory->average_cost_price,
                ];
                
                // Deduct Stock
                $inventory->decrement('stock_qty', $log['quantity_used']);
            }

            // 5. Create/Update Invoice
            $invoice = Invoice::firstOrCreate(
                ['visit_id' => $this->record->id],
                [
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
                    'payment_status' => 'unpaid',
                    'total_amount' => 0
                ]
            );

            // Add Transport Fee if new invoice
            if ($invoice->wasRecentlyCreated && $this->record->transport_fee > 0) {
                 InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Transport Fee (' . $this->record->distance_km . ' km)',
                    'quantity' => 1,
                    'unit_price' => $this->record->transport_fee,
                    'unit_cost' => 0,
                ]);
            }

            // Add Medical Items to Invoice
            foreach ($invoiceItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            // Recalculate Total
            $total = $invoice->invoiceItems()->sum(DB::raw('quantity * unit_price'));
            $invoice->update(['total_amount' => $total]);
            
            // Update Visit Status
            $completedStatus = \App\Models\VisitStatus::where('slug', 'completed')->first();
            if ($completedStatus) {
                $this->record->update(['visit_status_id' => $completedStatus->id]);
            }
        });

        Notification::make()
            ->title('Consultation Saved')
            ->success()
            ->send();

        return redirect()->to(VisitResource::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
