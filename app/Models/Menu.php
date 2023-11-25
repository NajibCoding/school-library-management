<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
