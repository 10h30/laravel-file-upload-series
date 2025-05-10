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
        Schema::table('uploads', function (Blueprint $table) {
            // Check if the columns exist before trying to drop them (optional, but good practice)
            if (Schema::hasColumn('uploads', 'filename')) {
                $table->dropColumn('filename');
            }
            if (Schema::hasColumn('uploads', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            // Re-add the columns if rolling back.
            // Adjust the type if they were different (e.g., text)
            // Making them nullable as they might not have data if rolled back after new entries.
            $table->string('filename')->nullable();
            $table->string('thumbnail')->nullable();
        });
    }
};
