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
                columns: ['location_id', 'created_at'],
                name: 'scans_location_id_created_at_index',
                algorithm: 'btree'
            );
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropIndex('scans_location_id_created_at_index');
        });
    }
};
