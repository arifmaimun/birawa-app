<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\MessageTemplate;
use App\Filament\Exports\VisitExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;

use App\Forms\Components\LeafletMap;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Visit Information')
                    ->schema([
                        Forms\Components\Select::make('patient_id')
                            ->relationship('patient', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (!$state) return;
                                
                                $patient = Patient::with('client.addresses')->find($state);
                                if ($patient && $patient->client && $patient->client->addresses->isNotEmpty()) {
                                    $address = $patient->client->addresses->first();
                                    if ($address && $address->coordinates) {
                                        $parts = explode(',', $address->coordinates);
                                        if (count($parts) == 2) {
                                            $lat = trim($parts[0]);
                                            $lng = trim($parts[1]);
                                            $set('latitude', $lat);
                                            $set('longitude', $lng);
                                            // Update the map state specifically
                                            $set('location_map', "$lat,$lng");
                                            self::updateDistanceAndFee($get, $set);
                                        }
                                    }
                                }
                            }),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Veterinarian')
                            ->required()
                            ->default(fn () => Auth::id()),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->required(),
                        Forms\Components\Select::make('visit_status_id')
                            ->relationship('visitStatus', 'name')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Location & Routing')
                    ->schema([
                        LeafletMap::make('location_map')
                            ->label('Location (Drag marker to adjust)')
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $parts = explode(',', $state);
                                    if (count($parts) == 2) {
                                        $set('latitude', trim($parts[0]));
                                        $set('longitude', trim($parts[1]));
                                        self::updateDistanceAndFee($get, $set);
                                    }
                                }
                            }),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->numeric()
                                    ->readOnly(),
                                Forms\Components\TextInput::make('longitude')
                                    ->numeric()
                                    ->readOnly(),
                            ]),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('distance_km')
                                    ->label('Distance (km)')
                                    ->numeric()
                                    ->readOnly(),
                                Forms\Components\TextInput::make('estimated_travel_minutes')
                                    ->label('Est. Duration (min)')
                                    ->numeric()
                                    ->readOnly(),
                                Forms\Components\TextInput::make('transport_fee')
                                    ->label('Transport Fee')
                                    ->numeric()
                                    ->prefix('Rp'),
                            ]),
                    ]),

                Forms\Components\Section::make('Visit Details')
                    ->schema([
                        Forms\Components\Textarea::make('complaint')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('departure_time'),
                                Forms\Components\DateTimePicker::make('arrival_time'),
                                Forms\Components\TextInput::make('actual_travel_minutes')
                                    ->numeric(),
                            ]),
                    ])->collapsible(),
            ]);
    }

    protected static function updateDistanceAndFee(Get $get, Set $set)
    {
        $lat = $get('latitude');
        $lng = $get('longitude');
        $doctor = Auth::user();
        
        if ($lat && $lng && $doctor && $doctor->doctorProfile) {
            $docLat = $doctor->doctorProfile->latitude;
            $docLng = $doctor->doctorProfile->longitude;
            
            if ($docLat && $docLng) {
                // Use RoutingManager for calculation with fallback
                $routingManager = new \App\Services\RoutingManager(
                    new \App\Services\OsrmService(),
                    new \App\Services\HaversineService()
                );

                $routeData = $routingManager->getRoute($docLat, $docLng, $lat, $lng);
                
                $distance = $routeData['distance_km'];
                $duration = $routeData['duration_minutes'];
                
                $set('estimated_travel_minutes', $duration);
                $set('distance_km', round($distance, 2));
                
                // Show notification based on source
                if ($routeData['source'] !== 'osrm') {
                    \Filament\Notifications\Notification::make()
                        ->warning()
                        ->title('Routing Fallback Activated')
                        ->body('Using direct line distance (Haversine) because routing service is unavailable.')
                        ->send();
                } else {
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Route Calculated')
                        ->body("Distance: {$distance}km, Duration: {$duration} mins")
                        ->send();
                }
                
                // Fee Calculation
                $baseFee = $doctor->doctorProfile->base_transport_fee ?? 0;
                $ratePerKm = $doctor->doctorProfile->transport_fee_per_km ?? 0;
                $fee = $baseFee + ($distance * $ratePerKm);
                
                $set('transport_fee', round($fee, -2)); // Round to nearest 100
            }
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Schedule')
                    ->dateTime('D, d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Visit $record) => $record->patient->client->name ?? '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Doctor')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('visitStatus.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'info',
                        'In Progress' => 'warning',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distance')
                    ->suffix(' km')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_travel_minutes')
                    ->label('Est. Time')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport_fee')
                    ->label('Fee')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('visit_status_id')
                    ->relationship('visitStatus', 'name')
                    ->label('Status'),
                Tables\Filters\Filter::make('scheduled_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from'),
                        Forms\Components\DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('template_id')
                            ->label('Template Pesan')
                            ->options(MessageTemplate::all()->pluck('title', 'id'))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state, Visit $record) {
                                if (!$state) return;
                                $template = MessageTemplate::find($state);
                                if ($template) {
                                    $text = $template->content_pattern;
                                    $replacements = [
                                        '{nama_klien}' => $record->patient->client->name ?? 'Client',
                                        '{nama_pasien}' => $record->patient->name ?? 'Patient',
                                        '{jam_visit}' => $record->scheduled_at ? $record->scheduled_at->format('H:i') : '-',
                                    ];
                                    $text = str_replace(array_keys($replacements), array_values($replacements), $text);
                                    $set('message', $text);
                                }
                            }),
                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->rows(5)
                            ->required(),
                    ])
                    ->action(function (array $data, Visit $record) {
                        $phone = $record->patient->client->phone ?? '';
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        $text = urlencode($data['message']);
                        $url = "https://wa.me/{$phone}?text={$text}";
                        return redirect()->away($url);
                    }),
                Tables\Actions\Action::make('consultation')
                    ->label('Start Consultation')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary')
                    ->url(fn (Visit $record) => Pages\DoctorConsultation::getUrl(['record' => $record])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(VisitExporter::class),
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
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
            'consultation' => Pages\DoctorConsultation::route('/{record}/consultation'),
        ];
    }
}
