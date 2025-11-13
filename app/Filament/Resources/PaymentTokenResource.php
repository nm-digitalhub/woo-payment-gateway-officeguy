<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTokenResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentTokenResource extends Resource
{
    protected static $model = null; // Would be linked to a PaymentToken model
    protected static $navigationIcon = 'heroicon-o-key';
    protected static $navigationLabel = 'Payment Tokens';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->label('User ID')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('last_four')
                    ->label('Last 4 Digits')
                    ->maxLength(4),
                Forms\Components\Select::make('card_type')
                    ->label('Card Type')
                    ->options([
                        'visa' => 'Visa',
                        'mastercard' => 'Mastercard',
                        'amex' => 'American Express',
                        'discover' => 'Discover',
                    ]),
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date'),
                Forms\Components\Toggle::make('is_default')
                    ->label('Default Payment Method'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('User ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('card_type')
                    ->label('Card Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('last_four')
                    ->label('Last 4 Digits'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('card_type')
                    ->options([
                        'visa' => 'Visa',
                        'mastercard' => 'Mastercard',
                        'amex' => 'American Express',
                        'discover' => 'Discover',
                    ]),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Payment Method'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTokens::route('/'),
            'create' => Pages\CreatePaymentToken::route('/create'),
            'edit' => Pages\EditPaymentToken::route('/{record}/edit'),
        ];
    }
}
