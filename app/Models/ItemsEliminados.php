<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsEliminados extends Model
{
    use HasFactory;
    protected $table = 'items_eliminados';

    protected $fillable = [
        'reason_delete',
        'inventory_number',
        'description',
        'image',
        'brand',
        'model',
        'serie',
        'condition',
        'state',
        'user_id',
        'area_id',
    ];
}
