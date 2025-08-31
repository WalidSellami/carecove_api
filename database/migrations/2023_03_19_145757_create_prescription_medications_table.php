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
        Schema::create('prescription_medications', function (Blueprint $table) {
            $table->id('prescription_medication_id');
            $table->string('dosage');
            $table->foreignId('prescription_id')->constrained('prescriptions', 'prescription_id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('medication_id')->constrained('medications', 'medication_id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_medications');
    }
};
