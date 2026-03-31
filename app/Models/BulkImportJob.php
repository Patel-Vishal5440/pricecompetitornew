<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BulkImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'total_rows',
        'processed_rows',
        'success_count',
        'failed_count',
        'errors',
        'meta',
        'uploaded_file_path',
        'message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'meta' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
