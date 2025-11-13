<?php

namespace NmDigitalHub\SumitPayment\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use NmDigitalHub\SumitPayment\Services\ApiService;

class SumitPaymentSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'SUMIT Settings';

    protected static ?string $navigationGroup = 'SUMIT Payment';

    protected static string $view = 'sumit-payment::filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'company_id' => config('sumit-payment.credentials.company_id'),
            'api_key' => config('sumit-payment.credentials.api_key'),
            'api_public_key' => config('sumit-payment.credentials.api_public_key'),
            'environment' => config('sumit-payment.environment'),
            'testing_mode' => config('sumit-payment.payment.testing_mode'),
            'merchant_number' => config('sumit-payment.payment.merchant_number'),
            'pci_mode' => config('sumit-payment.payment.pci_mode'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Credentials')
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

                Forms\Components\Section::make('Environment Settings')
                    ->schema([
                        Forms\Components\Select::make('environment')
                            ->label('Environment')
                            ->options([
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
