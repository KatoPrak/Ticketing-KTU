<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'department_id',
        'role',
        'nik', 
        'location_id',
        'status',
        'phone',
        'last_login_at',
        'region_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
// app/Models/User.php
// App\Models\User.php
public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}

public function location()
{
    return $this->belongsTo(Location::class);
}

public function region()
{
    return $this->belongsTo(Region::class);
}

public function user() {
    return $this->belongsTo(User::class);
}

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the identifier that will be stored in authentication.
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Relations
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Role checking methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'tim it' || $this->role === 'it';
    }

    public function isITTeam()
    {
        return $this->role === 'tim it' || $this->role === 'it';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Remember token methods
     */
    public function isRememberTokenValid($token)
    {
        return $this->remember_token && hash_equals($this->remember_token, hash('sha256', $token));
    }

    public function generateRememberToken()
    {
        $token = \Illuminate\Support\Str::random(60);
        $this->remember_token = hash('sha256', $token);
        $this->save();
        
        return $token;
    }

    public function clearRememberToken()
    {
        $this->remember_token = null;
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeByNik($query, $nik)
    {
        return $query->where('nik', $nik);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeExceptAdmins($query)
    {
        return $query->where('role', '!=', 'admin');
    }

    /**
     * Check if user has specific attribute
     */
    public function hasAttribute($attribute)
    {
        return array_key_exists($attribute, $this->attributes) || $this->hasGetMutator($attribute);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Get formatted last login
     */
    public function getFormattedLastLoginAttribute()
    {
        return $this->last_login_at ? $this->last_login_at->format('d M Y H:i') : 'Never';
    }

    /**
     * Check if user is online (logged in within last 15 minutes)
     */
    public function isOnline()
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(15));
    }
}