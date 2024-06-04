<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'items';

    protected $fillable = [
        'inventory_number',
        'description',
        'image',
        'brand',
        'model',
        'serie',
        'condition',
        'comments',
        'state',
        'user_id',
        'area_id',
    ];
}
