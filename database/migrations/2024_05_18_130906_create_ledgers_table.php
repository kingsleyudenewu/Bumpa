<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('ledger_name');
            $table->string('ledger_status')->default('Enabled');
            $table->string('ledger_type')->default('LEDGER');
            $table->timestamps();
        });

        $firsttable = DB::table('ledgers')->insertGetId([
            'ledger_name' => 'AccountFunding'
        ]);

        $secondTable = DB::table('ledgers')->insertGetId([
            'ledger_name' => 'IncomeLedger'
        ]);

        DB::table('books')->insert([
            [
                'book_src_id' => $firsttable,
                'book_type' => 'LEDGER',
            ],
            [
                'book_src_id' => $secondTable,
                'book_type' => 'LEDGER',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
