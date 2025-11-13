<?php

namespace App\Filament\Pages;

use App\Settings\PaymentSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagePaymentSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Payment Settings';
    protected static string $settings = PaymentSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Credentials')
                    ->schema([
                        Forms\Components\TextInput::make('company_id')
                            ->label('Company ID')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('api_key')
                            ->label('API Key')
                            ->required(),
                        Forms\Components\TextInput::make('secret_key')
                            ->label('Secret Key')
                            ->required()
                            ->password(),
                        Forms\Components\TextInput::make('private_key')
                            ->label('Private Key')
                            ->required()
                            ->password(),
                        Forms\Components\TextInput::make('public_key')
                            ->label('Public Key')
                            ->required(),
                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->nullable(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Environment Settings')
                    ->schema([
                        Forms\Components\Toggle::make('sandbox_mode')
                            ->label('Sandbox Mode')
                            ->helperText('Enable to use the test environment'),
                        Forms\Components\Select::make('environment')
                            ->label('Environment')
                            ->options([
                                'www' => 'Production',
                                'test' => 'Test',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('webhook_url')
                            ->label('Webhook URL')
                            ->url()
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Token Settings')
                    ->schema([
                        Forms\Components\Toggle::make('support_tokens')
                            ->label('Support Tokens')
                            ->helperText('Enable to support payment tokens for recurring payments'),
                        Forms\Components\Toggle::make('authorize_only')
                            ->label('Authorize Only')
                            ->helperText('Enable to authorize transactions without capturing'),
                        Forms\Components\Select::make('token_param')
                            ->label('Token Method')
                            ->options([
                                '2' => 'J2 - Standard Token',
                                '5' => 'J5 - Authorize Only Token',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
