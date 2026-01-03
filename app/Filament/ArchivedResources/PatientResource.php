<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 1;

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
                Forms\Components\TextInput::make('species')
                    ->required(),
                Forms\Components\TextInput::make('breed'),
                Forms\Components\Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Forms\Components\DatePicker::make('dob')
                    ->label('Date of Birth'),
                Forms\Components\Toggle::make('is_sterile')
                    ->label('Sterile'),
                Forms\Components\Textarea::make('allergies')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('special_conditions')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('vaccination_history')
                    ->columnSpanFull(),
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
                Tables\Columns\Layout\View::make('filament.resources.patients.card'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('addOwner')
                    ->label('Add Owner')
                    ->icon('heroicon-m-user-plus')
                    ->form([
                        Forms\Components\Select::make('client_id')
                            ->label('Select Client')
                            ->options(\App\Models\Client::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Patient $record, array $data) {
                        $record->clients()->syncWithoutDetaching([$data['client_id']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\PatientExporter::class),
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
                                        Infolists\Components\TextEntry::make('name')
                                            ->weight('bold')
                                            ->size('lg'),
                                        Infolists\Components\TextEntry::make('species'),
                                        Infolists\Components\TextEntry::make('breed'),
                                    ]),
                                ])->from('md'),
                                Infolists\Components\Section::make('Info')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('gender'),
                                        Infolists\Components\TextEntry::make('dob')->date(),
                                        Infolists\Components\IconEntry::make('is_sterile')->boolean(),
                                    ])->columns(3),
                                Infolists\Components\Section::make('Medical Details')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('allergies')
                                            ->placeholder('No allergies recorded'),
                                        Infolists\Components\TextEntry::make('special_conditions')
                                            ->placeholder('No special conditions'),
                                        Infolists\Components\TextEntry::make('vaccination_history')
                                            ->placeholder('No vaccination history'),
                                    ])->columns(1),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Visits')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('visits')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('scheduled_at')
                                            ->label('Date')
                                            ->date(),
                                        Infolists\Components\TextEntry::make('visitStatus.name')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match (strtolower($state)) {
                                                'completed' => 'success',
                                                'scheduled' => 'warning',
                                                'cancelled' => 'danger',
                                                default => 'gray',
                                            }),
                                    ])->columns(2),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Medical Records')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('medicalRecords')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')->dateTime(),
                                        Infolists\Components\TextEntry::make('subjective')->label('Subjective'),
                                        Infolists\Components\TextEntry::make('objective')->label('Objective'),
                                        Infolists\Components\TextEntry::make('assessment')->label('Assessment'),
                                        Infolists\Components\TextEntry::make('plan_treatment')->label('Treatment Plan'),
                                    ])->columns(2),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Owners')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('clients')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name'),
                                        Infolists\Components\TextEntry::make('phone'),
                                        Infolists\Components\TextEntry::make('address'),
                                    ])->columns(3),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
