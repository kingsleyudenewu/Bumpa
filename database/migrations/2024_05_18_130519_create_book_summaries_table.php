<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bs_book_id')->constrained('books','book_id');
            $table->decimal('bs_balance',40,6);
            $table->date('bs_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_summaries');
    }
};
