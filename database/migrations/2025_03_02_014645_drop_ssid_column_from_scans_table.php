<?php

use App\Models\Scan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete scans with an SSID (i.e. probably detecting a WAP)
        Scan::whereNotNull('ssid')->delete();

        Schema::table('scans', function (Blueprint $table) {
            $table->dropColumn('ssid');
        });
    }

    public function down(): void
    {
        //
    }
};
