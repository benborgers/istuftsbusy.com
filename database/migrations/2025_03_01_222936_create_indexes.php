<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->index(
                columns: ['created_at', 'location_id'],
                name: 'scans_created_at_location_id_index',
                algorithm: 'btree'
            );
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropIndex('scans_created_at_location_id_index');
        });
    }
};
