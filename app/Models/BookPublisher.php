<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookPublisher extends Model
{
    use HasFactory;
    protected $table = 'book_publishers';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'publisher_id');
    }
}
