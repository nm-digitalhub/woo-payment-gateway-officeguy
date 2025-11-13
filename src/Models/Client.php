<?php

namespace App\Models;

use App\Casts\BillingAddressCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Client Model - Business Layer Entity
 *
 * Represents the business/customer entity separate from system authentication.
 * This model handles all business logic, billing, orders, and customer relations.
 *
 * @property int                             $id
 * @property int                             $user_id                  Foreign key to users table
 * @property string                          $name                     Business/Client name
 * @property string                          $email                    Business email
 * @property string                          $description              Client description
 * @property bool                            $is_active                Client status
 * @property array                           $settings                 Client-specific settings (JSON)
 * @property int                             $account_manager_id       Assigned account manager
 * @property Carbon                          $created_at
 * @property Carbon                          $updated_at
 * @property Carbon                          $deleted_at
 * @property User                            $user                     Authentication entity
 * @property User                            $accountManager           Assigned manager
 * @property Order[]                         $orders                   Client orders
 * @property Invoice[]                       $invoices                 Client invoices
 * @property Payment[]                       $payments                 Client payments
 * @property Ticket[]                        $tickets                  Support tickets
 * @property string|null                     $client_name              Full name for CardCom transactions
 * @property string|null                     $client_email             Email for CardCom transactions
 * @property string|null                     $client_phone             Phone for CardCom transactions
 * @property string|null                     $mobile_phone
 * @property string|null                     $id_number
 * @property string|null                     $card_owner_id            Israeli ID for card owner verification
 * @property string|null                     $resellerclub_customer_id ResellerClub Customer ID - created via API only
 * @property string|null                     $maya_mobile_customer_id  Maya Mobile Customer ID - created via API only
 * @property string                          $resellerclub_sync_status
 * @property string                          $maya_mobile_sync_status
 * @property \Illuminate\Support\Carbon|null $resellerclub_last_sync
 * @property \Illuminate\Support\Carbon|null $maya_mobile_last_sync
 * @property string|null                     $first_name
 * @property string|null                     $last_name
 * @property string|null                     $phone
 * @property string                          $phone_country_code
 * @property                                 $billing_address
 * @property string|null                     $client_address           Street address for CardCom
 * @property string|null                     $client_address2
 * @property string|null                     $client_city              City for CardCom
 * @property string|null                     $client_state
 * @property string|null                     $client_country
 * @property string|null                     $client_postal_code       Postal code for CardCom
 * @property string|null                     $vat_number
 * @property string|null                     $lead_source
 * @property string|null                     $conversion_date
 * @property string|null                     $company
 * @property string                          $status
 * @property int                             $client_score
 * @property string                          $lifetime_value
 * @property string|null                     $last_interaction_at
 * @property-read Collection<int, Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read Collection<int, Activity> $crmActivities
 * @property-read int|null $crm_activities_count
 * @property-read Collection<int, Domain> $domains
 * @property-read int|null $domains_count
 * @property-read Collection<int, Order> $esimOrders
 * @property-read int|null $esim_orders_count
 * @property-read int $active_leads
 * @property-read int $active_services
 * @property-read string $card_com_email
 * @property-read string $card_com_name
 * @property-read string|null $card_com_phone
 * @property-read float $conversion_rate
 * @property-read int $converted_leads
 * @property-read int|null $customer_number
 * @property-read string $display_name
 * @property-read string $formatted_customer_id
 * @property-read int $open_opportunities
 * @property-read float $pipeline_value
 * @property-read string $primary_email
 * @property-read array $status_summary
 * @property-read int $total_leads
 * @property-read int $total_opportunities
 * @property-read int $total_orders
 * @property-read float $total_spent
 * @property-read float $total_won_value
 * @property-read float $weighted_pipeline_value
 * @property-read int $won_opportunities
 * @property-read Collection<int, HostingAccount> $hostingAccounts
 * @property-read int|null $hosting_accounts_count
 * @property-read int|null $invoices_count
 * @property-read Collection<int, Lead> $leads
 * @property-read int|null $leads_count
 * @property-read MayaMobileCustomer|null $mayaMobileCustomer
 * @property-read Collection<int, Opportunity> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read int|null $orders_count
 * @property-read int|null $payments_count
 * @property-read ResellerClubCustomer|null $resellerClubCustomer
 * @property-read int|null $tickets_count
 * @property-read Collection<int, VPS> $vps
 * @property-read int|null $vps_count
 *
 * @method static Builder<static>|Client active()
 * @method static Builder<static>|Client inactive()
 * @method static Builder<static>|Client newModelQuery()
 * @method static Builder<static>|Client newQuery()
 * @method static Builder<static>|Client onlyTrashed()
 * @method static Builder<static>|Client query()
 * @method static Builder<static>|Client search(string $search)
 * @method static Builder<static>|Client whereAccountManagerId($value)
 * @method static Builder<static>|Client whereBillingAddress($value)
 * @method static Builder<static>|Client whereCardOwnerId($value)
 * @method static Builder<static>|Client whereClientAddress($value)
 * @method static Builder<static>|Client whereClientAddress2($value)
 * @method static Builder<static>|Client whereClientCity($value)
 * @method static Builder<static>|Client whereClientCountry($value)
 * @method static Builder<static>|Client whereClientEmail($value)
 * @method static Builder<static>|Client whereClientName($value)
 * @method static Builder<static>|Client whereClientPhone($value)
 * @method static Builder<static>|Client whereClientPostalCode($value)
 * @method static Builder<static>|Client whereClientScore($value)
 * @method static Builder<static>|Client whereClientState($value)
 * @method static Builder<static>|Client whereCompany($value)
 * @method static Builder<static>|Client whereConversionDate($value)
 * @method static Builder<static>|Client whereCreatedAt($value)
 * @method static Builder<static>|Client whereDeletedAt($value)
 * @method static Builder<static>|Client whereDescription($value)
 * @method static Builder<static>|Client whereEmail($value)
 * @method static Builder<static>|Client whereFirstName($value)
 * @method static Builder<static>|Client whereId($value)
 * @method static Builder<static>|Client whereIdNumber($value)
 * @method static Builder<static>|Client whereIsActive($value)
 * @method static Builder<static>|Client whereLastInteractionAt($value)
 * @method static Builder<static>|Client whereLastName($value)
 * @method static Builder<static>|Client whereLeadSource($value)
 * @method static Builder<static>|Client whereLifetimeValue($value)
 * @method static Builder<static>|Client whereMayaMobileCustomerId($value)
 * @method static Builder<static>|Client whereMayaMobileLastSync($value)
 * @method static Builder<static>|Client whereMayaMobileSyncStatus($value)
 * @method static Builder<static>|Client whereMobilePhone($value)
 * @method static Builder<static>|Client whereName($value)
 * @method static Builder<static>|Client wherePhone($value)
 * @method static Builder<static>|Client wherePhoneCountryCode($value)
 * @method static Builder<static>|Client whereResellerclubCustomerId($value)
 * @method static Builder<static>|Client whereResellerclubLastSync($value)
 * @method static Builder<static>|Client whereResellerclubSyncStatus($value)
 * @method static Builder<static>|Client whereSettings($value)
 * @method static Builder<static>|Client whereStatus($value)
 * @method static Builder<static>|Client whereUpdatedAt($value)
 * @method static Builder<static>|Client whereUserId($value)
 * @method static Builder<static>|Client whereVatNumber($value)
 * @method static Builder<static>|Client withOrders()
 * @method static Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Client withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'description',
        'is_active',
        'settings',
        'account_manager_id',

        // CardCom required fields - using system field names
        'client_name',
        'client_email',
        'client_phone',
        'mobile_phone',
        'card_owner_id',
        'id_number',

        // Personal details
        'first_name',
        'last_name',
        'phone',
        'phone_country_code',

        // Address fields
        'billing_address',
        'client_address',
        'client_address2',
        'client_city',
        'client_state',
        'client_country',
        'client_postal_code',

        // Business fields
        'vat_number',
        'company',
        'status',

        // External service customer IDs (API only)
        'resellerclub_customer_id',
        'maya_mobile_customer_id',
        'sumit_customer_id',
        'resellerclub_sync_status',
        'maya_mobile_sync_status',
        'sumit_sync_status',
        'resellerclub_last_sync',
        'maya_mobile_last_sync',
        'sumit_last_sync',
    ];

    protected $attributes = [
        'is_active' => true,
        'settings' => '{}',
    ];

    // ====== RELATIONSHIPS ======

    /**
     * Get the user (authentication entity) that owns this client
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account manager assigned to this client
     */
    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    /**
     * Get all orders for this client
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all invoices for this client
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all payments made by this client
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all payment tokens for this client (via user relationship)
     */
    public function paymentTokens(): HasMany
    {
        return $this->hasMany(PaymentToken::class, 'user_id', 'user_id');
    }

    /**
     * Get active payment tokens for this client
     */
    public function activePaymentTokens(): HasMany
    {
        return $this->paymentTokens()->where('is_active', true);
    }

    /**
     * Get SUMIT payment tokens for this client
     */
    public function sumitPaymentTokens(): HasMany
    {
        return $this->paymentTokens()->where('gateway', 'sumit');
    }

    /**
     * Get CardCom payment tokens for this client
     */
    public function cardcomPaymentTokens(): HasMany
    {
        return $this->paymentTokens()->where('gateway', 'cardcom');
    }

    /**
     * Get SUMIT payment tokens directly for this client
     */
    public function sumitTokens(): HasMany
    {
        return $this->hasMany(SumitPaymentToken::class);
    }

    /**
     * Get active SUMIT payment tokens for this client
     */
    public function activeSumitTokens(): HasMany
    {
        return $this->sumitTokens()->active();
    }

    /**
     * Get all support tickets for this client
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get all domains owned by this client
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get all hosting accounts for this client
     */
    public function hostingAccounts(): HasMany
    {
        return $this->hasMany(HostingAccount::class);
    }

    /**
     * Get all VPS servers for this client
     */
    public function vps(): HasMany
    {
        return $this->hasMany(VPS::class);
    }

    /**
     * Get all eSIM orders for this client
     */
    public function esimOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('service_type', 'esim');
    }

    /**
     * Get all contacts for this client
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    // ====== BUSINESS LOGIC METHODS ======

    /**
     * Check if client is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate client
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate client
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get client display name
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->name ?: $this->user?->name ?: 'Client #'.$this->id;
        });
    }

    /**
     * Get client primary email
     */
    protected function primaryEmail(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->email ?: $this->user?->email ?: '';
        });
    }

    /**
     * Get client customer number from user
     */
    protected function customerNumber(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->user?->customer_number;
        });
    }

    /**
     * Get formatted customer ID
     */
    protected function formattedCustomerId(): Attribute
    {
        return Attribute::make(get: function (): string {
            $customerNumber = $this->customer_number;

            return $customerNumber ? '#'.str_pad($customerNumber, 4, '0', STR_PAD_LEFT) : 'לא הוקצה';
        });
    }

    /**
     * Get total orders count
     */
    protected function totalOrders(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->orders()->count();
        });
    }

    /**
     * Get total amount spent
     */
    protected function totalSpent(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->payments()->where('status', 'completed')->sum('amount');
        });
    }

    /**
     * Get active services count
     */
    protected function activeServices(): Attribute
    {
        return Attribute::make(get: function (): float|int|array {
            $domains = $this->domains()->where('status', 'active')->count();
            $hosting = $this->hostingAccounts()->where('status', 'active')->count();
            $vps = $this->vps()->where('status', 'active')->count();
            $esim = $this->esimOrders()->where('status', 'active')->count();

            return $domains + $hosting + $vps + $esim;
        });
    }

    /**
     * Get client status summary
     */
    protected function statusSummary(): Attribute
    {
        return Attribute::make(get: function (): array {
            return [
                'is_active' => $this->is_active,
                'total_orders' => $this->total_orders,
                'total_spent' => $this->total_spent,
                'active_services' => $this->active_services,
                'last_order' => $this->orders()->latest()->first()?->created_at,
                'last_payment' => $this->payments()->latest()->first()?->created_at,
            ];
        });
    }

    // ====== EXTERNAL INTEGRATIONS ======

    /**
     * Get Maya Mobile customer record
     */
    public function mayaMobileCustomer(): HasOne
    {
        return $this->hasOne(MayaMobileCustomer::class, 'user_id', 'user_id');
    }

    /**
     * Get ResellerClub customer record
     */
    public function resellerClubCustomer(): HasOne
    {
        return $this->hasOne(ResellerClubCustomer::class, 'user_id', 'user_id');
    }

    /**
     * Sync the linked User model with fields from Client.
     * Creates mapping for billing address and business fields.
     */
    public function syncUserFromClient(): User
    {
        $user = $this->user ?: User::findOrFail($this->user_id);

        // Prepare billing address array for user
        $billingAddress = is_array($user->billing_address) ? $user->billing_address : [];
        $billingAddress = array_filter(array_merge($billingAddress, [
            'street' => is_array($this->billing_address) ? ($this->billing_address['street'] ?? $this->client_address) : $this->client_address,
            'address2' => $this->client_address2 ?? ($billingAddress['address2'] ?? null),
            'city' => $this->client_city ?? ($billingAddress['city'] ?? null),
            'postal_code' => $this->client_postal_code ?? ($billingAddress['postal_code'] ?? null),
            'country' => $this->client_country ?? ($this->settings['country'] ?? ($billingAddress['country'] ?? null)),
            'state' => $this->client_state ?? ($this->settings['state'] ?? ($billingAddress['state'] ?? null)),
        ]));

        $user->fill([
            'name' => $this->name ?: $user->name,
            'email' => $this->email ?: $user->email,
            'phone' => $this->mobile_phone ?: $this->phone ?: $user->phone,
            'company' => $this->company ?: $user->company,
            'vat_number' => $this->vat_number ?: $user->vat_number,
            'billing_address' => $billingAddress,
            'address' => $this->client_address ?: $user->address,
            'address2' => $this->client_address2 ?? $user->address2,
            'id_number' => $this->id_number ?: $this->card_owner_id ?: $user->id_number,
            'status' => $this->status ?? $user->status,
            'is_active' => $this->is_active ?? $user->is_active,
            'preferred_language' => $this->settings['preferred_language'] ?? $user->preferred_language,
            'city' => $this->client_city ?? $user->city,
            'postal_code' => $this->client_postal_code ?? $user->postal_code,
            // Country/State prefer explicit client_* fields, fallback to settings
            'country' => $this->client_country ?? ($this->settings['country'] ?? $user->country),
            'state' => $this->client_state ?? ($this->settings['state'] ?? $user->state),

            // External service IDs back to User (where applicable)
            'resellerclub_customer_id' => $this->resellerclub_customer_id ?? $user->resellerclub_customer_id,
            'resellerclub_synced_at' => $this->resellerclub_last_sync ?? $user->resellerclub_synced_at,
        ]);

        $user->save();

        // ensure back-reference is current
        $this->setRelation('user', $user);

        return $user;
    }

    /**
     * Check if synced with Maya Mobile
     */
    public function isSyncedWithMayaMobile(): bool
    {
        return $this->maya_mobile_sync_status === 'synced' && ! empty($this->maya_mobile_customer_id);
    }

    /**
     * Check if synced with ResellerClub
     */
    public function isSyncedWithResellerClub(): bool
    {
        return $this->resellerclub_sync_status === 'synced' && ! empty($this->resellerclub_customer_id);
    }

    /**
     * Mark as synced with ResellerClub
     */
    public function markResellerClubSynced(string $customerId): void
    {
        $this->update([
            'resellerclub_customer_id' => $customerId,
            'resellerclub_sync_status' => 'synced',
            'resellerclub_last_sync' => now(),
        ]);
    }

    /**
     * Mark as synced with Maya Mobile
     */
    public function markMayaMobileSynced(string $customerId): void
    {
        $this->update([
            'maya_mobile_customer_id' => $customerId,
            'maya_mobile_sync_status' => 'synced',
            'maya_mobile_last_sync' => now(),
        ]);
    }

    /**
     * Mark as synced with SUMIT
     */
    public function markSumitSynced(int $customerId): void
    {
        $this->update([
            'sumit_customer_id' => $customerId,
            'sumit_sync_status' => 'synced',
            'sumit_last_sync' => now(),
        ]);
    }

    /**
     * Mark ResellerClub sync as failed
     */
    public function markResellerClubSyncFailed(): void
    {
        $this->update([
            'resellerclub_sync_status' => 'failed',
            'resellerclub_last_sync' => now(),
        ]);
    }

    /**
     * Mark Maya Mobile sync as failed
     */
    public function markMayaMobileSyncFailed(): void
    {
        $this->update([
            'maya_mobile_sync_status' => 'failed',
            'maya_mobile_last_sync' => now(),
        ]);
    }

    /**
     * Mark SUMIT sync as failed
     */
    public function markSumitSyncFailed(): void
    {
        $this->update([
            'sumit_sync_status' => 'failed',
            'sumit_last_sync' => now(),
        ]);
    }

    /**
     * Check if needs ResellerClub sync
     */
    public function needsResellerClubSync(): bool
    {
        return empty($this->resellerclub_customer_id) &&
               in_array($this->resellerclub_sync_status, ['not_required', 'failed', 'pending']);
    }

    /**
     * Check if needs Maya Mobile sync
     */
    public function needsMayaMobileSync(): bool
    {
        return empty($this->maya_mobile_customer_id) &&
               in_array($this->maya_mobile_sync_status, ['not_required', 'failed', 'pending']);
    }

    /**
     * Check if synced with SUMIT
     */
    public function isSyncedWithSumit(): bool
    {
        return $this->sumit_sync_status === 'synced' && ! empty($this->sumit_customer_id);
    }

    /**
     * Check if needs SUMIT sync
     */
    public function needsSumitSync(): bool
    {
        return empty($this->sumit_customer_id) &&
               in_array($this->sumit_sync_status, ['not_synced', 'failed', 'pending']);
    }

    // ====== SCOPES ======

    /**
     * Scope: Active clients only
     */
    protected function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Inactive clients
     */
    protected function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Clients with orders
     */
    protected function scopeWithOrders($query)
    {
        return $query->has('orders');
    }

    /**
     * Scope: Search by name or email
     */
    protected function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search): void {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search): void {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get CardCom-compatible client name
     */
    protected function cardComName(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->client_name ?: $this->name ?: $this->user?->name ?: 'Client #'.$this->id;
        });
    }

    /**
     * Get CardCom-compatible client email
     */
    protected function cardComEmail(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->client_email ?: $this->email ?: $this->user?->email ?: '';
        });
    }

    /**
     * Get CardCom-compatible client phone
     */
    protected function cardComPhone(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->client_phone ?: $this->phone ?: $this->user?->phone ?: '';
        });
    }

    /**
     * Get CardCom-compatible card owner ID
     */
    protected function cardOwnerId(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['card_owner_id'] ?: $this->user?->id_number;
        });
    }

    /**
     * Check if client has all required CardCom fields
     */
    public function hasCardComRequiredFields(): bool
    {
        return ! empty($this->cardcom_name) &&
               ! empty($this->cardcom_email) &&
               ! empty($this->cardcom_phone) &&
               ! empty($this->card_owner_id);
    }

    /**
     * Get data formatted for CardCom API
     */
    public function toCardComArray(): array
    {
        return [
            'CustomerName' => $this->cardcom_name,
            'CustomerEmail' => $this->cardcom_email,
            'CustomerPhone' => $this->cardcom_phone,
            'client_name' => $this->cardcom_name,
            'client_email' => $this->cardcom_email,
            'client_phone' => $this->cardcom_phone,
            'card_owner_id' => $this->card_owner_id,
        ];
    }

    // ====== STATIC METHODS ======

    /**
     * Create client from user with CardCom fields
     */
    public static function createFromUser(User $user): self
    {
        return self::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'description' => 'Client created from user registration',
            'is_active' => true,

            // CardCom fields
            'client_name' => $user->first_name && $user->last_name
                ? "{$user->first_name} {$user->last_name}"
                : $user->name,
            'client_email' => $user->email,
            'client_phone' => $user->phone,
            'card_owner_id' => $user->id_number,

            // Personal fields
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'phone_country_code' => $user->phone_country_code ?? '972',

            // Address and business fields
            'billing_address' => $user->billing_address,
            'vat_number' => $user->vat_number,
            'company' => $user->company,
        ]);
    }

    // ====== CRM RELATIONSHIPS ======

    /**
     * Leads created by this client
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Opportunities belonging to this client
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * Activities related to this client
     */
    public function crmActivities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    /**
     * Get recent CRM activities for this client
     */
    public function getRecentCrmActivities($limit = 10)
    {
        return $this->crmActivities()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get total leads count for this client
     */
    protected function totalLeads(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->leads()->count();
        });
    }

    /**
     * Get active leads count
     */
    protected function activeLeads(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->leads()
                ->whereIn('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation'])
                ->count();
        });
    }

    /**
     * Get converted leads count (won)
     */
    protected function convertedLeads(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->leads()->where('status', 'won')->count();
        });
    }

    /**
     * Get conversion rate
     */
    protected function conversionRate(): Attribute
    {
        return Attribute::make(get: function (): int|float {
            $totalLeads = $this->total_leads;
            $convertedLeads = $this->converted_leads;

            return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
        });
    }

    /**
     * Get total opportunities count
     */
    protected function totalOpportunities(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()->count();
        });
    }

    /**
     * Get open opportunities count
     */
    protected function openOpportunities(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()->where('status', 'open')->count();
        });
    }

    /**
     * Get won opportunities count
     */
    protected function wonOpportunities(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()->where('status', 'won')->count();
        });
    }

    /**
     * Get total pipeline value
     */
    protected function pipelineValue(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()
                ->where('status', 'open')
                ->sum('estimated_value');
        });
    }

    /**
     * Get weighted pipeline value (considering probability)
     */
    protected function weightedPipelineValue(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()
                ->where('status', 'open')
                ->get()
                ->sum(function ($opportunity): int|float {
                    return $opportunity->estimated_value * ($opportunity->probability / 100);
                });
        });
    }

    /**
     * Get total won value
     */
    protected function totalWonValue(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->opportunities()
                ->where('status', 'won')
                ->sum('actual_value');
        });
    }

    // ====== CRM HELPER METHODS ======

    /**
     * Create a new lead for this client
     */
    public function createLead(array $data): Lead
    {
        return $this->leads()->create(array_merge([
            'status' => 'new',
            'created_by' => auth()->id(),
        ], $data));
    }

    /**
     * Create a new opportunity for this client
     */
    public function createOpportunity(array $data): Opportunity
    {
        return $this->opportunities()->create(array_merge([
            'status' => 'open',
            'probability' => 10,
        ], $data));
    }

    /**
     * Add CRM activity for this client
     */
    public function addCrmActivity(string $type, string $subject, array $data = []): Activity
    {
        return $this->crmActivities()->create(array_merge([
            'type' => $type,
            'subject' => $subject,
            'user_id' => auth()->id(),
        ], $data));
    }

    /**
     * Get CRM performance summary for this client
     */
    public function getCrmPerformanceSummary(): array
    {
        return [
            'total_leads' => $this->total_leads,
            'active_leads' => $this->active_leads,
            'converted_leads' => $this->converted_leads,
            'conversion_rate' => $this->conversion_rate,
            'total_opportunities' => $this->total_opportunities,
            'open_opportunities' => $this->open_opportunities,
            'won_opportunities' => $this->won_opportunities,
            'pipeline_value' => $this->pipeline_value,
            'weighted_pipeline_value' => $this->weighted_pipeline_value,
            'total_won_value' => $this->total_won_value,
            'recent_activities' => $this->getRecentCrmActivities(5),
        ];
    }

    /**
     * Get leads that need follow-up
     */
    public function getLeadsNeedingFollowUp()
    {
        return $this->leads()
            ->where('next_follow_up_at', '<=', now())
            ->whereIn('status', ['new', 'contacted', 'qualified'])
            ->orderBy('next_follow_up_at');
    }

    /**
     * Get opportunities closing soon
     */
    public function getOpportunitiesClosingSoon($days = 7)
    {
        return $this->opportunities()
            ->where('status', 'open')
            ->where('expected_close_date', '<=', now()->addDays($days))
            ->orderBy('expected_close_date');
    }

    /**
     * Check if client has any CRM activity
     */
    public function hasCrmActivity(): bool
    {
        return $this->leads()->exists() ||
               $this->opportunities()->exists() ||
               $this->crmActivities()->exists();
    }

    /**
     * Get statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'active' => self::active()->count(),
            'inactive' => self::inactive()->count(),
            'with_orders' => self::withOrders()->count(),
            'maya_synced' => self::whereHas('mayaMobileCustomer', fn ($q) => $q->where('sync_status', 'synced'))->count(),
            'resellerclub_synced' => self::whereHas('resellerClubCustomer', fn ($q) => $q->where('sync_status', 'synced'))->count(),
        ];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
            'billing_address' => BillingAddressCast::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'resellerclub_last_sync' => 'datetime',
            'maya_mobile_last_sync' => 'datetime',
            'sumit_last_sync' => 'datetime',
        ];
    }
}
