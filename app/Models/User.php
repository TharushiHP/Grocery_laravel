<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    /**
     * Create a new personal access token with advanced scopes.
     *
     * @param string $name
     * @param array $abilities
     * @param \DateTime|null $expiresAt
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'], \DateTime $expiresAt = null)
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
            'last_used_at' => now(),
            'device_id' => request()->header('X-Device-ID'),
            'device_name' => request()->header('X-Device-Name', 'Unknown Device'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }
    
    /**
     * Get active tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTokens()
    {
        return $this->tokens()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->orderBy('last_used_at', 'desc')
            ->get();
    }
    
    /**
     * Revoke all tokens for a specific device.
     *
     * @param string $deviceId
     * @return int
     */
    public function revokeTokensForDevice($deviceId)
    {
        return $this->tokens()
            ->where('device_id', $deviceId)
            ->delete();
    }
    
    /**
     * Check if user has permission for a specific ability.
     *
     * @param string $ability
     * @return bool
     */
    public function hasAbility($ability)
    {
        $token = $this->currentAccessToken();
        
        if (!$token) {
            return false;
        }
        
        return $token->can($ability);
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'phone_number',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if user is admin
     */
    /**
     * Check if the user has admin role.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
