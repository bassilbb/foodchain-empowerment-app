<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer delivery address book.
 *
 * The marketplace previously captured a single flat address on `users`
 * (address/city/state) and snapshotted it onto each order. This adds a proper
 * multi-address book so a customer can save several delivery locations and pick
 * one at checkout.
 *
 * Deliberately separate from `users` so historical orders keep their own
 * snapshot columns (marketplace_orders.delivery_*) and are never rewritten by
 * later address edits.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Nigerian marketplace address shape.
            $table->string('label')->nullable();            // e.g. Home, Office
            $table->string('full_name');
            $table->string('phone', 30);
            $table->string('address');
            $table->string('state');
            $table->string('lga')->nullable();
            $table->string('city')->nullable();
            $table->string('landmark')->nullable();
            $table->text('delivery_instructions')->nullable();

            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
