<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\BookEnum;
use App\Models\Book;
use App\Models\User;
use App\Service\LedgerService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(10)->create()
             ->each(fn($user) => Book::factory()->create([
                 'book_src_id' => $user->id,
                 'book_type' => BookEnum::CUSTOMER->value
             ])->each(fn($book) => resolve(LedgerService::class)->accountFundingLedger(
                     1000,
                     Str::uuid()->toString(),
                     $user->id,
                     BookEnum::CUSTOMER->value
             )));
    }
}
