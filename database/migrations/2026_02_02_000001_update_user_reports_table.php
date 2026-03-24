<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
            // Rename 'status' to 'admin_status' for clarity
            // Add 'report_category' column if missing
            if (!Schema::hasColumn('user_reports', 'admin_status')) {
                $table->renameColumn('status', 'admin_status');
            }
            
            if (!Schema::hasColumn('user_reports', 'report_category')) {
                $table->string('report_category')->nullable()->after('report_type');
                $table->index('report_category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
            if (Schema::hasColumn('user_reports', 'admin_status')) {
                $table->renameColumn('admin_status', 'status');
            }
            
            if (Schema::hasColumn('user_reports', 'report_category')) {
                $table->dropColumn('report_category');
                $table->dropIndex(['report_category']);
            }
        });
    }
};
