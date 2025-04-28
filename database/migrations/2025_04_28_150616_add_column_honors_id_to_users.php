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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('honors_id')->after('image')->constrained(
                table: 'honors',
                indexName: 'users_honors_id'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                // Hapus foreign key constraint dulu
                $table->dropForeign('users_honors_id');
                
                // Hapus kolom honors_id
                $table->dropColumn('honors_id');
            });
        });
    }
};
