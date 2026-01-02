<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\FileUpload::make('photo')
                    ->avatar()
                    ->imageEditor()
                    ->circleCropper(),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone')
                    ->tel(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
                Forms\Components\TextInput::make('first_name'),
                Forms\Components\TextInput::make('last_name'),
                Forms\Components\Toggle::make('is_business')
                    ->required(),
                Forms\Components\TextInput::make('business_name'),
                Forms\Components\TextInput::make('contact_person'),
                Forms\Components\TextInput::make('id_type'),
                Forms\Components\TextInput::make('id_number'),
                Forms\Components\TextInput::make('gender'),
                Forms\Components\TextInput::make('occupation'),
                Forms\Components\DatePicker::make('dob'),
                Forms\Components\TextInput::make('ethnicity'),
                Forms\Components\TextInput::make('religion'),
                Forms\Components\TextInput::make('marital_status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\View::make('filament.resources.clients.card'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('addPatient')
                    ->label('New Patient')
                    ->icon('heroicon-m-plus')
                    ->form([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('species')->required(),
                        Forms\Components\TextInput::make('breed'),
                        Forms\Components\Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female']),
                    ])
                    ->action(function (Client $record, array $data) {
                        $patient = \App\Models\Patient::create($data);
                        $record->patients()->attach($patient->id);
                    }),
                Tables\Actions\Action::make('linkPatient')
                    ->label('Link Patient')
                    ->icon('heroicon-m-link')
                    ->form([
                        Forms\Components\Select::make('patient_id')
                            ->label('Select Patient')
                            ->options(\App\Models\Patient::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Client $record, array $data) {
                        $record->patients()->syncWithoutDetaching([$data['patient_id']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Details')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Profile')
                            ->schema([
                                Infolists\Components\Split::make([
                                    Infolists\Components\ImageEntry::make('photo')
                                        ->circular()
                                        ->grow(false),
                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('name')->weight('bold')->size('lg'),
                                        Infolists\Components\TextEntry::make('phone'),
                                        Infolists\Components\TextEntry::make('address'),
                                    ]),
                                ])->from('md'),
                                Infolists\Components\Section::make('Personal Info')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('id_number')->label('ID'),
                                        Infolists\Components\TextEntry::make('gender'),
                                        Infolists\Components\TextEntry::make('occupation'),
                                    ])->columns(3),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Timeline')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('recent_visits')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('scheduled_at')
                                            ->label('Date')
                                            ->date(),
                                        Infolists\Components\TextEntry::make('patient.name')
                                            ->label('Patient'),
                                        Infolists\Components\TextEntry::make('visitStatus.name')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match (strtolower($state)) {
                                                'completed' => 'success',
                                                'scheduled' => 'warning',
                                                'cancelled' => 'danger',
                                                default => 'gray',
                                            }),
                                    ])
                                    ->columns(3),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Patients')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('patients')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')->weight('bold'),
                                        Infolists\Components\TextEntry::make('species'),
                                        Infolists\Components\TextEntry::make('breed'),
                                    ])->grid(3),
                            ]),
                    ])->columnSpanFull(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
