<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function scopeAutoload($query)
    {
        return $query->where('autoload', 'yes');
    }

    public function scopeFindByName($query, string $string)
    {
        return $query->where('name', $string)->first();
    }
}
