<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->text('description')->nullable();
            $table->json('additional_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at'], 'user_logs_user_id_created_at_index');
            $table->index(['model_type', 'model_id'], 'user_logs_model_type_model_id_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_logs');
    }
};