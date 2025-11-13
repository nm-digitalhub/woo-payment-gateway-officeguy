<?php

namespace NmDigitalHub\SumitPayment\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use NmDigitalHub\SumitPayment\Models\PaymentToken;
use NmDigitalHub\SumitPayment\Filament\Resources\PaymentTokenResource\Pages;

class PaymentTokenResource extends Resource
{
    protected static ?string $model = PaymentToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Payment Tokens';

    protected static ?string $navigationGroup = 'SUMIT Payment';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('User ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('card_last4')
                    ->label('Card')
                    ->formatStateUsing(fn ($state) => '****' . $state),
                Tables\Columns\TextColumn::make('card_brand')
                    ->label('Brand')
                    ->sortable(),
                Tables\Columns\TextColumn::make('exp_month')
                    ->label('Expiration')
                    ->formatStateUsing(fn ($record) => 
                        str_pad($record->exp_month, 2, '0', STR_PAD_LEFT) . '/' . $record->exp_year
                    ),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Token'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Token Details')
                    ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_type')
                            ->label('Card Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_last4')
                            ->label('Last 4 Digits')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_brand')
                            ->label('Card Brand')
                            ->disabled(),
                        Forms\Components\TextInput::make('exp_month')
                            ->label('Expiration Month')
                            ->disabled(),
                        Forms\Components\TextInput::make('exp_year')
                            ->label('Expiration Year')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Token')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTokens::route('/'),
            'view' => Pages\ViewPaymentToken::route('/{record}'),
        ];
    }
}
