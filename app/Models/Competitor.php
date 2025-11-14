<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model {
    use HasFactory;

    protected $table = 'competitors';
    
    protected $fillable = ['name', 'website', 'shortname', 'price_class_name', 'status'];

    public static function getCompetitorNames()
    {
        return self::orderBy('id')->get();
    }
}
