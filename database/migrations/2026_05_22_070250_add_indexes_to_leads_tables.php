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
        // Leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->index('created_at', 'idx_leads_created_at');
        });

        // Entity Relationships table
        Schema::table('entity_relationships', function (Blueprint $table) {
            $table->index(
                ['user_id', 'model_type', 'model_id'],
                'idx_entity_relationships_user_model'
            );
        });

        // Status Logs table
        Schema::table('log_entity_statuses', function (Blueprint $table) {
            $table->index(
                ['model_id', 'status_id'],
                'idx_status_logs_model_status'
            );

            $table->index(
                'status_id',
                'idx_status_logs_status'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_created_at');
        });

        Schema::table('entity_relationships', function (Blueprint $table) {
            $table->dropIndex('idx_entity_relationships_user_model');
        });

        Schema::table('log_entity_statuses', function (Blueprint $table) {
            $table->dropIndex('idx_status_logs_model_status');
            $table->dropIndex('idx_status_logs_status');
        });
    }
};