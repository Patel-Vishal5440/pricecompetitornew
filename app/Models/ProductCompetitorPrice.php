<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCompetitorPrice extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'competitor_id', 'competitor_url','price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }
}
