<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBirthdayPushLogTable extends Migration
{
    public function up()
    {
        Schema::create('birthday_push_log', function (Blueprint $table) {
            $table->id();
            $table->integer('record_count')->default(0);
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->text('message')->nullable();
            $table->integer('attempts')->default(1);
            $table->string('webhook_url')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('birthday_push_log');
    }
}