<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            if (!Schema::hasColumn('izins', 'nama_guru_validator')) {
                $table->string('nama_guru_validator')->nullable()->after('paraf_guru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            if (Schema::hasColumn('izins', 'nama_guru_validator')) {
                $table->dropColumn('nama_guru_validator');
            }
        });
    }
};
