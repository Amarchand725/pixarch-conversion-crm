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
        Schema::create('export_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();           // This is the "Name" column in your CSV
            $table->string('phone', 50)->nullable()->index();     // Indexed for fast lookup
            $table->string('email')->nullable()->index();      // Indexed for fast lookup
            $table->timestamp('created')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->text('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_contacts');
    }
};
