<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('escalated_to')->constrained('staff')->onDelete('cascade');
            $table->text('reason');
            $table->foreignId('escalated_by')->nullable()->constrained('admin_users')->onDelete('set null');
            $table->timestamp('escalated_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_escalations');
    }
};
