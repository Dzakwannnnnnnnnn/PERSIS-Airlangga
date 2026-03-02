<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'card_uid')) {
                $table->string('card_uid', 100)->nullable()->unique()->after('kelas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'card_uid')) {
                $table->dropUnique('users_card_uid_unique');
                $table->dropColumn('card_uid');
            }
        });
    }
};
