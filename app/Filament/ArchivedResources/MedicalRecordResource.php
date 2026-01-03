<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Filament\Resources\MedicalRecordResource\RelationManagers;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use App\Models\DoctorInventory;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Grid;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Visit & Vitals')
                        ->schema([
                            Forms\Components\Section::make('Visit Details')
                                ->schema([
                                    Forms\Components\Select::make('visit_id')
                                        ->relationship('visit', 'id')
                                        ->required()
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('doctor_id')
                                        ->relationship('doctor', 'name')
                                        ->required()
                                        ->default(Auth::id())
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('patient_id')
                                        ->relationship('patient', 'name')
                                        ->required()
                                        ->searchable()
                                        ->preload(),
                                ])->columns(3),
                            
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

                            Forms\Components\Section::make('Subjective (Owner Complaint)')
                                ->schema([
                                    Forms\Components\Textarea::make('subjective')
                                        ->label('Complaints / History')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Examination')
                        ->schema([
                            Forms\Components\Section::make('Objective (Physical Exam)')
                                ->schema([
                                    Forms\Components\Textarea::make('objective')
                                        ->label('Findings')
                                        ->rows(5)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Assessment & Plan')
                        ->schema([
                            Forms\Components\Section::make('Assessment')
                                ->schema([
                                    Forms\Components\Textarea::make('assessment')
                                        ->label('Diagnosis / Assessment')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                            
                            Forms\Components\Section::make('Plan')
                                ->schema([
                                    Forms\Components\Textarea::make('plan_treatment')
                                        ->label('Treatment Plan')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('plan_recipe')
                                        ->label('Prescription')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Inventory Usage')
                                ->schema([
                                    Forms\Components\Repeater::make('usageLogs')
                                        ->relationship()
                                        ->schema([
                                            Forms\Components\Select::make('doctor_inventory_id')
                                                ->label('Item')
                                                ->options(function () {
                                                    return DoctorInventory::where('user_id', Auth::id())
                                                        ->where('stock_qty', '>', 0)
                                                        ->pluck('item_name', 'id');
                                                })
                                                ->searchable()
                                                ->required()
                                                ->reactive(),
                                            Forms\Components\TextInput::make('quantity_used')
                                                ->label('Qty')
                                                ->numeric()
                                                ->default(1)
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                    $weight = $get('../../vitalSign.weight');
                                                    $inventoryId = $get('doctor_inventory_id');
                                                    
                                                    if ($weight && $inventoryId && $state) {
                                                        $inventory = DoctorInventory::with('product')->find($inventoryId);
                                                        $product = $inventory->product;
                                                        
                                                        if ($product && ($product->min_dose_per_kg || $product->max_dose_per_kg)) {
                                                            $minDose = $product->min_dose_per_kg ? $product->min_dose_per_kg * $weight : 0;
                                                            $maxDose = $product->max_dose_per_kg ? $product->max_dose_per_kg * $weight : 999999;
                                                            
                                                            if ($state < $minDose) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title('Underdose Alert')
                                                                    ->body("Recommended Min: $minDose")
                                                                    ->send();
                                                            } elseif ($state > $maxDose) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title('Overdose Alert')
                                                                    ->body("Recommended Max: $maxDose")
                                                                    ->send();
                                                            }
                                                        }
                                                    }
                                                }),
                                        ])
                                        ->label('Items Used')
                                ]),
                                
                            Forms\Components\Toggle::make('is_locked')
                                ->required()
                                ->label('Lock Record'),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Current Visit Details')
                            ->schema([
                                TextEntry::make('visit.id')->label('Visit ID'),
                                TextEntry::make('created_at')->dateTime()->label('Date'),
                                TextEntry::make('doctor.name')->label('Doctor'),
                                TextEntry::make('patient.name')->label('Patient'),
                                TextEntry::make('vitalSign.weight')->label('Weight (kg)'),
                                TextEntry::make('vitalSign.temperature')->label('Temp (C)'),
                            ])->columnSpan(2),
                        
                        Section::make('Summary')
                            ->schema([
                                TextEntry::make('subjective')->label('Subjective'),
                                TextEntry::make('assessment')->label('Assessment'),
                            ])->columnSpan(1),
                    ]),

                Section::make('Patient History Timeline')
                    ->schema([
                        ViewEntry::make('history')
                            ->view('filament.infolists.medical-record-timeline')
                            ->viewData([
                                'records' => fn ($record) => MedicalRecord::where('patient_id', $record->patient_id)
                                    ->where('id', '!=', $record->id)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit.id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_locked')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'view' => Pages\ViewMedicalRecord::route('/{record}'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
