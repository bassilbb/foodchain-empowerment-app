<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // For audit trail of important actions
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable(); // admin, user, system
            $table->text('description');
            $table->nullableMorphs('subject'); // The model being acted upon
            $table->nullableMorphs('causer'); // Who performed the action
            $table->json('properties')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index('log_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
