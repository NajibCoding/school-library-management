<?php

namespace App\Models;

use App\Models\BookAuthor;
use App\Models\BookPublisher;
use App\Http\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, HasStatus;
    protected $table = 'books';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function author()
    {
        return $this->belongsTo(BookAuthor::class, 'author_id');
    }

    public function publisher()
    {
        return $this->belongsTo(BookPublisher::class, 'publisher_id');
    }
}
