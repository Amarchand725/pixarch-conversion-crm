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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->index();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID of the model');
            $table->string('model_type', 256)->nullable()->comment('Type of the model');
            $table->boolean('is_system')->default(0);
            $table->string('type', 256)->nullable()->comment('Type of the attachment');
            $table->string('title', 256)->nullable();
            $table->string('path', 256)->nullable();
            $table->string('cloud_path')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedTinyInteger('orientation')->nullable();
            $table->text('comment')->nullable();
            $table->string('md5')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
