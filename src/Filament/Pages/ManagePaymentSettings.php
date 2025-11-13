<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Filament\Pages;

use NmDigitalhub\WooPaymentGatewayAdmin\Settings\PaymentSettings;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;

class ManagePaymentSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Payment Settings';
    protected string $view = 'filament.pages.manage-payment-settings';
    
    public ?array $data = [];
    
    public function mount(PaymentSettings $settings): void
    {
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ])
            ->statePath('data');
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::pages/settings-page.form.actions.save.label'))
                ->submit('save'),
        ];
    }
    
    public function save(PaymentSettings $settings): void
    {
        $data = $this->form->getState();
        
        $settings->fill($data);
        $settings->save();
        
        \Filament\Notifications\Notification::make()
            ->success()
            ->title(__('filament-panels::pages/settings-page.notifications.saved.title'))
            ->send();
    }
}
