<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorProfileResource\Pages;
use App\Filament\Resources\DoctorProfileResource\RelationManagers;
use App\Models\DoctorProfile;
use App\Services\TimezoneService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorProfileResource extends Resource
{
    protected static ?string $model = DoctorProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('bank_account_details')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('bio')
                    ->columnSpanFull(),
                Forms\Components\Select::make('timezone')
                    ->label('Time Zone')
                    ->options(fn () => app(TimezoneService::class)->getTimezonesForSelect())
                    ->searchable()
                    ->required()
                    ->default(config('app.timezone')),
                Forms\Components\TextInput::make('specialty'),
                Forms\Components\TextInput::make('service_radius_km')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('base_transport_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('latitude')
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric(),
                Forms\Components\TextInput::make('transport_fee_per_km')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('emergency_contact_name'),
                Forms\Components\TextInput::make('emergency_contact_number'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Doctor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('timezone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('specialty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_radius_km')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_transport_fee')
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
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport_fee_per_km')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('emergency_contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emergency_contact_number')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListDoctorProfiles::route('/'),
            'create' => Pages\CreateDoctorProfile::route('/create'),
            'edit' => Pages\EditDoctorProfile::route('/{record}/edit'),
        ];
    }
}
