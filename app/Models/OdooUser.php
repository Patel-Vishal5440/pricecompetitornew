<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooUser extends Model
{
    use HasFactory;
    protected $fillable = ['username', 'api_key', 'odoo_user_id'];
    protected $table = 'odoo_users';
}
