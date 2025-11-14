<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone_number',
        'country',
        'city',
        'company_name',
        'website',
        'bio',
        'skills',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'skills' => 'array',
    ];

    /**
     * Get the role that belongs to the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all permissions for the user through their role
     */
    public function permissions()
    {
        return $this->role ? $this->role->permissions : collect();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->permissions->contains('name', $permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (!$this->role) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if (!$this->role) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role && in_array(strtolower($this->role->name), ['admin', 'super admin']);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role && strtolower($this->role->name) === 'super admin';
    }

    /**
     * Check if user is moderator
     */
    public function isModerator()
    {
        return $this->role && $this->role->name === 'moderator';
    }
}
