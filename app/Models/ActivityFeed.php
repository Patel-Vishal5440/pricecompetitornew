<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
class ActivityFeed extends Model
{
    protected $fillable = ['model_id', 'user_id', 'price_old', 'price_new', 'type', 'created_at'];

    protected $table = 'activity_feeds';

    public function product()
    {
        return $this->belongsTo(Product::class, 'model_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // public function moderator()
    // {
    //     return $this->belongsTo(Moderator::class);
    // }
    
}
