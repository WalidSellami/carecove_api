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
        Schema::table('pharmacists', function (Blueprint $table) {
            $table->foreignId('pharmacy_id')->constrained('pharmacies', 'pharmacy_id')->after('user_id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pharmacy_id');
        });
    }
};
