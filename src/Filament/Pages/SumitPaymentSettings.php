<?php

namespace NmDigitalHub\SumitPayment\Filament\Pages;

use Filament\Forms;
use Filament\Pages\SettingsPage;
use NmDigitalHub\SumitPayment\Settings\SumitPaymentSettings;

class ManageSumitPaymentSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'SUMIT Settings';

    protected static ?string $navigationGroup = 'SUMIT Payment';

    protected static ?string $title = 'SUMIT Payment Settings';

    protected static string $settings = SumitPaymentSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('Settings')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('API Configuration')
                        ->schema([
                            Forms\Components\Section::make('Credentials')
                                ->schema([
                                    Forms\Components\TextInput::make('company_id')
                                        ->label('Company ID')
                                        ->required(),
                                    Forms\Components\TextInput::make('api_key')
                                        ->label('API Private Key')
                                        ->password()
                                        ->required(),
                                    Forms\Components\TextInput::make('api_public_key')
                                        ->label('API Public Key')
                                        ->password()
                                        ->required(),
                                ])
                                ->columns(1),

                            Forms\Components\Section::make('API Settings')
                                ->schema([
                                    Forms\Components\TextInput::make('api_base_url')
                                        ->label('Production API URL')
                                        ->url()
                                        ->default('https://api.sumit.co.il'),
                                    Forms\Components\TextInput::make('api_dev_url')
                                        ->label('Development API URL')
                                        ->url()
                                        ->default('http://dev.api.sumit.co.il'),
                                    Forms\Components\TextInput::make('api_timeout')
                                        ->label('API Timeout (seconds)')
                                        ->numeric()
                                        ->default(180),
                                    Forms\Components\Toggle::make('api_ssl_verify')
                                        ->label('Verify SSL Certificates')
                                        ->default(true),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Payment Settings')
                        ->schema([
                            Forms\Components\Section::make('Environment')
                                ->schema([
                                    Forms\Components\Select::make('environment')
                                        ->label('Environment')
                                        ->options([
                                            'www' => 'Production',
                                            'dev' => 'Development',
                                        ])
                                        ->default('www')
                                        ->required(),
                                    Forms\Components\Toggle::make('testing_mode')
                                        ->label('Testing Mode')
                                        ->helperText('Enable for test transactions'),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Payment Configuration')
                                ->schema([
                                    Forms\Components\TextInput::make('merchant_number')
                                        ->label('Merchant Number'),
                                    Forms\Components\TextInput::make('subscription_merchant_number')
                                        ->label('Subscription Merchant Number'),
                                    Forms\Components\Select::make('pci_mode')
                                        ->label('PCI Mode')
                                        ->options([
                                            'yes' => 'Direct (PCI Compliant)',
                                            'no' => 'Tokenized',
                                            'redirect' => 'Redirect',
                                        ])
                                        ->default('redirect')
                                        ->required(),
                                    Forms\Components\Select::make('token_param')
                                        ->label('Token Parameter')
                                        ->options([
                                            'J2' => 'J2 - Standard',
                                            'J5' => 'J5 - Authorization Only',
                                        ])
                                        ->default('J2'),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Authorization Settings')
                                ->schema([
                                    Forms\Components\Toggle::make('authorize_only')
                                        ->label('Authorization Only Mode'),
                                    Forms\Components\TextInput::make('authorize_added_percent')
                                        ->label('Authorization Added Percent')
                                        ->numeric()
                                        ->default(0)
                                        ->suffix('%'),
                                    Forms\Components\TextInput::make('authorize_minimum_addition')
                                        ->label('Authorization Minimum Addition')
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->columns(3),

                            Forms\Components\Section::make('Document Settings')
                                ->schema([
                                    Forms\Components\Toggle::make('draft_document')
                                        ->label('Create Draft Documents')
                                        ->default(true),
                                    Forms\Components\Toggle::make('email_document')
                                        ->label('Email Documents to Customers')
                                        ->default(true),
                                    Forms\Components\Toggle::make('send_client_ip')
                                        ->label('Send Client IP Address')
                                        ->default(true),
                                ])
                                ->columns(3),
                        ]),

                    Forms\Components\Tabs\Tab::make('Documents & Language')
                        ->schema([
                            Forms\Components\Section::make('Document Language')
                                ->schema([
                                    Forms\Components\Toggle::make('document_auto_language')
                                        ->label('Auto-detect Language')
                                        ->default(true),
                                    Forms\Components\Select::make('document_default_language')
                                        ->label('Default Language')
                                        ->options([
                                            'he' => 'Hebrew',
                                            'en' => 'English',
                                        ])
                                        ->default('he'),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Installments')
                                ->schema([
                                    Forms\Components\TextInput::make('max_installments')
                                        ->label('Maximum Installments')
                                        ->numeric()
                                        ->default(12)
                                        ->required(),
                                ])
                                ->columns(1),
                        ]),

                    Forms\Components\Tabs\Tab::make('Features')
                        ->schema([
                            Forms\Components\Section::make('Stock Synchronization')
                                ->schema([
                                    Forms\Components\Toggle::make('stock_sync_enabled')
                                        ->label('Enable Stock Sync')
                                        ->default(false),
                                ])
                                ->columns(1),

                            Forms\Components\Section::make('Donations')
                                ->schema([
                                    Forms\Components\Toggle::make('donations_enabled')
                                        ->label('Enable Donations')
                                        ->default(false),
                                ])
                                ->columns(1),

                            Forms\Components\Section::make('Marketplace Integrations')
                                ->schema([
                                    Forms\Components\Toggle::make('marketplace_dokan_enabled')
                                        ->label('Enable Dokan Integration'),
                                    Forms\Components\Toggle::make('marketplace_wcfm_enabled')
                                        ->label('Enable WCFM Integration'),
                                    Forms\Components\Toggle::make('marketplace_wcvendors_enabled')
                                        ->label('Enable WC Vendors Integration'),
                                ])
                                ->columns(3),
                        ]),

                    Forms\Components\Tabs\Tab::make('Logging')
                        ->schema([
                            Forms\Components\Section::make('Logging Configuration')
                                ->schema([
                                    Forms\Components\Toggle::make('logging_enabled')
                                        ->label('Enable Logging')
                                        ->default(true),
                                    Forms\Components\Select::make('logging_level')
                                        ->label('Log Level')
                                        ->options([
                                            'debug' => 'Debug',
                                            'info' => 'Info',
                                            'warning' => 'Warning',
                                            'error' => 'Error',
                                        ])
                                        ->default('debug'),
                                ])
                                ->columns(2),
                        ]),
                ]),
        ];
    }
}

                                'www' => 'Production',
                                'dev' => 'Development',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('testing_mode')
                            ->label('Testing Mode')
                            ->helperText('Enable to run transactions in test mode'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Settings')
                    ->schema([
                        Forms\Components\TextInput::make('merchant_number')
                            ->label('Merchant Number'),
                        Forms\Components\Select::make('pci_mode')
                            ->label('PCI Mode')
                            ->options([
                                'yes' => 'Direct (PCI Compliant)',
                                'no' => 'Tokenized',
                                'redirect' => 'Redirect',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function testCredentials(): void
    {
        $apiService = app(ApiService::class);
        
        $error = $apiService->checkCredentials(
            $this->data['company_id'] ?? '',
            $this->data['api_key'] ?? ''
        );

        if ($error === null) {
            Notification::make()
                ->title('Credentials are valid')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Credentials validation failed')
                ->body($error)
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('testCredentials')
                ->label('Test Credentials')
                ->action('testCredentials'),
        ];
    }
}
