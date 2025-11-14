<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the permissions for this role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }
        
        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions($permissions)
    {
        if (is_array($permissions)) {
            $this->permissions()->sync($permissions);
        } else {
            $this->permissions()->sync([$permissions]);
        }
    }
} 