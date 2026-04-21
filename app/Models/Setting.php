<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public $timestamps = false;

    public static function defaults(): array
    {
        return [
            'store_name' => 'Urbanist',
            'logo_path' => '',
            'contact_email' => 'hello@urbanist-store.com',
            'phone_number' => '+1 (212) 555-0148',
            'address' => '245 Mercer Street, SoHo, New York, NY 10012',
            'payment_gateway_keys' => 'Stripe live and Paystack production credentials are managed securely outside source control.',
            'shipping_settings' => 'Standard delivery in 3-5 business days, express delivery in 1-2 business days, and white-glove delivery for oversized furniture in select cities.',
        ];
    }

    public static function valueFor(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }
}
