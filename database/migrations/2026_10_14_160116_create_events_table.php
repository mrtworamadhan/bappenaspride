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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama acara (Contoh: Building Bappenas Pride)
            $table->string('slug')->unique(); // Untuk URL slug jika diperlukan
            $table->date('event_date')->nullable(); // Tanggal acara
            $table->string('location')->nullable(); // Lokasi acara
            $table->string('certificate_background')->nullable(); // Path file background e-sertifikat
            $table->boolean('is_active')->default(true); // Status apakah event ini yang sedang aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};