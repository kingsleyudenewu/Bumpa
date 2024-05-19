<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\BookEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be unique.
     *
     * @return array<string>
     */
    public function uniqueIds(): array
    {
        return ['code'];
    }

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if( Hash::needsRehash($value) ) {
            $value = Hash::make($value);
        }
        $this->attributes['password'] = $value;
    }

    /**
     * @return HasOne
     */
    public function books(string $type): HasOne
    {
        return $this->hasOne(Book::class,'book_src_id', 'id')
            ->where('book_type', $type)
            ->first();
    }

    /**
     * @param string $type
     * @return float|int
     */
    public function userBalance(string $type): float|int
    {
        $book =  $this->books($type);
        $book_id = $book->book_id;
        $totalBalance = BookSummary::where('bs_book_id', $book_id)->sum('bs_balance');
        $totalBalance = ($totalBalance != 0)? $totalBalance : 0;
        return -1*$totalBalance;
    }
}
