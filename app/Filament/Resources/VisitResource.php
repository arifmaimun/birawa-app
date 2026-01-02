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

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if (!$state) return;
                        
                        $patient = Patient::with('client.addresses')->find($state);
                        if ($patient && $patient->client && $patient->client->addresses->isNotEmpty()) {
                            // Try to find an address with coordinates
                            $address = $patient->client->addresses->first();
                            if ($address && $address->coordinates) {
                                // Assume coordinates are "lat,lng"
                                $parts = explode(',', $address->coordinates);
                                if (count($parts) == 2) {
                                    $set('latitude', trim($parts[0]));
                                    $set('longitude', trim($parts[1]));
                                    self::updateDistanceAndFee($get, $set);
                                }
                            }
                        }
                    }),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(fn () => Auth::id()),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->required(),
                Forms\Components\Select::make('visit_status_id')
                    ->relationship('visitStatus', 'name'),
                Forms\Components\Textarea::make('complaint')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('transport_fee')
                    ->numeric(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateDistanceAndFee($get, $set)),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateDistanceAndFee($get, $set)),
                Forms\Components\TextInput::make('distance_km')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('departure_time'),
                Forms\Components\DateTimePicker::make('arrival_time'),
                Forms\Components\TextInput::make('estimated_travel_minutes')
                    ->numeric(),
                Forms\Components\TextInput::make('actual_travel_minutes')
                    ->numeric(),
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
                // Haversine Formula
                $earthRadius = 6371; // km
                $dLat = deg2rad($lat - $docLat);
                $dLon = deg2rad($lng - $docLng);
                
                $a = sin($dLat/2) * sin($dLat/2) +
                     cos(deg2rad($docLat)) * cos(deg2rad($lat)) * 
                     sin($dLon/2) * sin($dLon/2);
                     
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $distance = $earthRadius * $c;
                
                $set('distance_km', round($distance, 2));
                
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
                Tables\Columns\TextColumn::make('patient.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visitStatus.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transport_fee')
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance_km')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('departure_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_travel_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_travel_minutes')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
