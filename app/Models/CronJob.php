<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CronJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'schedule_time',
        'schedule_times',
        'timezone',
        'command',
        'is_active',
        'last_run',
        'next_run',
        'created_by',
        'updated_by'
    ];

    /**
     * Boot the model.
     * Set default schedule times when creating a new cron job.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cronJob) {
            // Set default schedule times if not provided
            // Default: 12:00, 15:00, 18:00, 21:00 (UTC) - 4 times a day
            if (empty($cronJob->schedule_times)) {
                $cronJob->schedule_times = ['12:00', '15:00', '18:00', '21:00'];
            }
            
            // Set default timezone if not provided
            if (empty($cronJob->timezone)) {
                $cronJob->timezone = 'UTC';
            }
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
        'last_run' => 'datetime',
        'next_run' => 'datetime',
        'schedule_time' => 'datetime',
        'schedule_times' => 'array',
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

    public function getFormattedScheduleTimesAttribute()
    {
        if ($this->schedule_times && is_array($this->schedule_times) && count($this->schedule_times) > 0) {
            return implode(', ', $this->schedule_times);
        }
        return $this->formatted_schedule_time ?? 'N/A';
    }

    public function getScheduleTimesArrayAttribute()
    {
        if ($this->schedule_times && is_array($this->schedule_times)) {
            return $this->schedule_times;
        }
        // Fallback to single schedule_time if schedule_times is not set
        if ($this->schedule_time) {
            return [$this->schedule_time->format('H:i')];
        }
        return [];
    }

    /**
     * Calculate next run time for this cron job
     */
    public function calculateNextRun()
    {
        $scheduleTimes = $this->schedule_times_array;
        $timezone = $this->timezone ?? 'UTC';
        
        if (empty($scheduleTimes)) {
            return null;
        }
        
        $now = Carbon::now($timezone);
        $today = $now->copy()->startOfDay();
        $nextRun = null;
        
        // Find next scheduled time today
        foreach ($scheduleTimes as $time) {
            list($hour, $minute) = explode(':', $time);
            $scheduledTime = $today->copy()->setTime($hour, $minute);
            
            if ($scheduledTime->gt($now)) {
                if (!$nextRun || $scheduledTime->lt($nextRun)) {
                    $nextRun = $scheduledTime;
                }
            }
        }
        
        // If no time found today, use first time tomorrow
        if (!$nextRun && !empty($scheduleTimes)) {
            list($hour, $minute) = explode(':', $scheduleTimes[0]);
            $nextRun = $today->copy()->addDay()->setTime($hour, $minute);
        }
        
        return $nextRun ? $nextRun->setTimezone('UTC') : null;
    }
} 