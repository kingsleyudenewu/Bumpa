<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $primaryKey = 'book_id';

    protected $guarded = ['book_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookSummary(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookSummary::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountStatement(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tx::class, 'tx_book_id');
    }
}
