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
        Schema::create('book_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
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
