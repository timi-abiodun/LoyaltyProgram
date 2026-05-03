<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
     /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'first_name', 'last_name',
        'username', 'email',
        'password',
    ];

    // prevent password leakage
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
            'password' => 'hashed'
        ];
    }

    // Add these to handle the UUID properly
    protected $keyType = 'string';
    public $incrementing = false;

    // Relationships

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                   ->withPivot('unlocked_at');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                   ->withPivot('unlocked_at');
    }

    public function cashbacks()
    {
        return $this->hasMany(Cashback::class);
    }
}
