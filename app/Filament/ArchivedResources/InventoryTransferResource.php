<?php

namespace App\Filament\ArchivedResources;

use App\Filament\Resources\InventoryTransferResource\Pages;
use App\Filament\Resources\InventoryTransferResource\RelationManagers;
use App\Models\InventoryTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InventoryTransferResource extends Resource
{
    protected static ?string $model = InventoryTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transfer_number')
                    ->default('TRF-' . date('Ymd') . '-' . rand(1000, 9999))
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('requester_id')
                    ->relationship('requester', 'name')
                    ->default(Auth::id())
                    ->required(),
                Forms\Components\Select::make('source_type')
                    ->options([
                        'central' => 'Central Warehouse',
                        'doctor' => 'Doctor Inventory',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('source_id')
                    ->label('Source User')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->visible(fn (Forms\Get $get) => $get('source_type') === 'doctor')
                    ->required(fn (Forms\Get $get) => $get('source_type') === 'doctor'),
                Forms\Components\Select::make('target_type')
                    ->options([
                        'central' => 'Central Warehouse',
                        'doctor' => 'Doctor Inventory',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('target_id')
                    ->label('Target User')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->visible(fn (Forms\Get $get) => $get('target_type') === 'doctor')
                    ->required(fn (Forms\Get $get) => $get('target_type') === 'doctor'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id_virtual')
                            ->label('Select Product')
                            ->options(\App\Models\Product::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('item_sku', $product->sku);
                                    $set('item_name', $product->name);
                                    $set('unit', 'unit'); 
                                }
                            }),
                        Forms\Components\TextInput::make('item_sku')->required(),
                        Forms\Components\TextInput::make('item_name')->required(),
                        Forms\Components\TextInput::make('quantity_requested')->numeric()->required(),
                        Forms\Components\TextInput::make('unit')->required(),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transfer_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListInventoryTransfers::route('/'),
            'create' => Pages\CreateInventoryTransfer::route('/create'),
            'edit' => Pages\EditInventoryTransfer::route('/{record}/edit'),
        ];
    }
}
