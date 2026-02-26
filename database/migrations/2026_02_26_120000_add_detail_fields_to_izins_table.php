<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            if (!Schema::hasColumn('izins', 'nama')) {
                $table->string('nama')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('izins', 'kelas')) {
                $table->string('kelas')->nullable()->after('nama');
            }

            if (!Schema::hasColumn('izins', 'waktu_izin')) {
                $table->dateTime('waktu_izin')->nullable()->after('kelas');
            }

            if (!Schema::hasColumn('izins', 'alasan_izin')) {
                $table->text('alasan_izin')->nullable()->after('jenis_izin');
            }

            if (!Schema::hasColumn('izins', 'bukti_foto')) {
                $table->string('bukti_foto')->nullable()->after('alasan_izin');
            }

            if (!Schema::hasColumn('izins', 'paraf_siswa')) {
                $table->boolean('paraf_siswa')->default(false)->after('bukti_foto');
            }

            if (!Schema::hasColumn('izins', 'paraf_guru')) {
                $table->boolean('paraf_guru')->default(false)->after('paraf_siswa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            foreach (['nama', 'kelas', 'waktu_izin', 'alasan_izin', 'bukti_foto', 'paraf_siswa', 'paraf_guru'] as $column) {
                if (Schema::hasColumn('izins', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

