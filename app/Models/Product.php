<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'odoo_id',
        'name',
        'default_code',
        'list_price',
        'extra_data',
        'barcode',
        'status',
        'category'
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function competitors()
    {
        return $this->hasMany(ProductCompetitorPrice::class);
    }

    public function competitorPrices()
    {
        return $this->hasMany(ProductCompetitorPrice::class);
    }

    public function activityFeed()
    {
        return $this->hasMany(ActivityFeed::class);
    }
}
