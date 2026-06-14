<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            ['key' => 'site_name',     'value' => 'VMS — Visitor Management System', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'primary_color', 'value' => 'blue',                             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_path',     'value' => null,                               'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
