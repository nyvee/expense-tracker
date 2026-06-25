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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->dateTime('tanggal');
            $table->integer('nominal');
            $table->string('arus');
            $table->string('merchant');
            $table->string('sumber');
            $table->string('kategori')->default('Uncategorized');
            $table->string('status')->default('Needs Review');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
