<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailColumnsToBirthdayPushLogTable extends Migration
{
    public function up()
    {
        Schema::table('birthday_push_log', function (Blueprint $table) {
            $table->json('pushed_data')->nullable()->after('record_count');
            $table->text('guest_summary')->nullable()->after('pushed_data');
            $table->timestamp('sync_timestamp')->nullable()->after('guest_summary');
        });
    }

    public function down()
    {
        Schema::table('birthday_push_log', function (Blueprint $table) {
            $table->dropColumn(['pushed_data', 'guest_summary', 'sync_timestamp']);
        });
    }
}