<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vuestore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'name',
        'email',
        'phone',
        'country',
        'city',
        'company',
        'position',
        'join',
        'status',
    ];
    
    use HasFactory;
}
