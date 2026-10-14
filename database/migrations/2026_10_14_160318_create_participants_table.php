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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique(); // Untuk barcode / unique identifier
            $table->string('name');
            $table->string('phone'); // No HP / WhatsApp (format 62...)
            $table->string('nip')->nullable();
            $table->string('position')->nullable(); // Jabatan
            $table->string('work_unit')->nullable(); // Unit Kerja
            $table->text('signature')->nullable(); // Path file gambar TTD digital
            $table->enum('attendance_status', ['pending', 'present'])->default('pending'); // Status kehadiran
            $table->timestamp('attended_at')->nullable(); // Waktu scan hadir
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};