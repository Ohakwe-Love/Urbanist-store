<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function wishlists()
    {
        // One user can have many wishlist entries
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        // Many-to-many relationship through wishlists table
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function isInWishlist($productId): bool
    {
        return $this->wishlistProducts()->where('product_id', $productId)->exists();
    }
}
