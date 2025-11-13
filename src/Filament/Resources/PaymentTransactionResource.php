<?php

namespace NmDigitalHub\SumitPayment\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use NmDigitalHub\SumitPayment\Models\PaymentTransaction;
use NmDigitalHub\SumitPayment\Filament\Resources\PaymentTransactionResource\Pages;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $navigationGroup = 'SUMIT Payment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('auth_number')
                            ->label('Auth Number')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_last4')
                            ->label('Card Last 4')
                            ->disabled(),
                        Forms\Components\TextInput::make('document_id')
                            ->label('Document ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('customer_id')
                            ->label('Customer ID')
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Error Information')
                    ->schema([
                        Forms\Components\Textarea::make('error_message')
                            ->label('Error Message')
                            ->disabled()
                            ->rows(3),
                    ])
                    ->visible(fn ($record) => !empty($record->error_message)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('card_last4')
                    ->label('Card')
                    ->formatStateUsing(fn ($state) => $state ? '****' . $state : '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
        ];
    }
}
