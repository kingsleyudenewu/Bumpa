<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bookSummary(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookSummary::class);
    }

    public function accountStatement(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tx::class, 'tx_book_id');
    }
}
