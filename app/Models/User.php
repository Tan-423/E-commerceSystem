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
        'email',
        'password',
        'usertype',
        'locked',
        'failed_attempts',
        'last_failed_attempt',
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
            'password' => 'hashed',
            'locked' => 'boolean',
            'last_failed_attempt' => 'datetime',
        ];
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts()
    {
        $this->failed_attempts = $this->failed_attempts + 1;
        $this->last_failed_attempt = now();
        
        // Lock user if failed attempts reach 3
        if ($this->failed_attempts >= 3) {
            $this->locked = true;
        }
        
        $this->save();
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts()
    {
        $this->failed_attempts = 0;
        $this->last_failed_attempt = null;
        $this->save();
    }

    /**
     * Check if user should be auto-locked due to failed attempts
     */
    public function shouldAutoLock()
    {
        return $this->failed_attempts >= 3;
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


}
