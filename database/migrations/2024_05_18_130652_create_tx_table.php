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
        Schema::create('tx', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tx_book_id')->constrained('books','book_id');
            $table->date('tx_date');
            $table->date('tx_value_date');
            $table->decimal('tx_amount',40,6);
            $table->string('tx_remarks');
            $table->string('tx_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tx');
    }
};
