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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('id_type', ['ktp', 'sim', 'passport'])->nullable();
            $table->string('id_number', 50)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->text('purpose')->nullable();
            $table->string('vehicle_number', 20)->nullable();
            $table->string('badge_number', 20)->nullable();
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
