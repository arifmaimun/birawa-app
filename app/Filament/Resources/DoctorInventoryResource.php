<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorInventoryResource\Pages;
use App\Filament\Resources\DoctorInventoryResource\RelationManagers;
use App\Models\DoctorInventory;
use App\Filament\Exports\DoctorInventoryExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorInventoryResource extends Resource
{
    protected static ?string $model = DoctorInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Inventory';
    protected static ?string $pluralModelLabel = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->label('Owner (User)'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $product = \App\Models\Product::find($state);
                        if ($product) {
                            $set('item_name', $product->name);
                            $set('sku', $product->sku);
                            $set('unit', 'unit'); // Default or fetch from product if available
                            $set('selling_price', $product->price);
                        }
                    }),
                Forms\Components\Select::make('storage_location_id')
                    ->relationship('storageLocation', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU'),
                Forms\Components\TextInput::make('stock_qty')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('unit')
                    ->required(),
                Forms\Components\TextInput::make('alert_threshold')
                    ->numeric()
                    ->default(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('storageLocation.name')
                    ->label('Location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('Stock')
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('storageLocation')
                    ->relationship('storageLocation', 'name'),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'medicine' => 'Medicine',
                        'equipment' => 'Equipment',
                        'consumable' => 'Consumable',
                    ]),
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
            RelationManagers\BatchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorInventories::route('/'),
            'create' => Pages\CreateDoctorInventory::route('/create'),
            'edit' => Pages\EditDoctorInventory::route('/{record}/edit'),
        ];
    }
}
