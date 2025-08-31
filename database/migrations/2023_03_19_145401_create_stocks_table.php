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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('stock_id');
            $table->integer('quantity');
            $table->date('date_of_manufacture');
            $table->date('date_of_expiration');
            $table->string('medication_image' , 300)->nullable();
            $table->foreignId('pharmacy_id')->constrained('pharmacies', 'pharmacy_id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('medication_id')->constrained('medications', 'medication_id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
