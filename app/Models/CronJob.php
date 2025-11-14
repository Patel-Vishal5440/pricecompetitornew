<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CronJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'schedule_time',
        'command',
        'is_active',
        'last_run',
        'next_run',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run' => 'datetime',
        'next_run' => 'datetime',
        'schedule_time' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_run',
        'next_run',
        'schedule_time'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    public function getFormattedScheduleTimeAttribute()
    {
        return $this->schedule_time ? $this->schedule_time->format('H:i') : 'N/A';
    }

    public function getFormattedLastRunAttribute()
    {
        return $this->last_run ? $this->last_run->format('Y-m-d H:i:s') : 'Never';
    }

    public function getFormattedNextRunAttribute()
    {
        return $this->next_run ? $this->next_run->format('Y-m-d H:i:s') : 'N/A';
    }
} 