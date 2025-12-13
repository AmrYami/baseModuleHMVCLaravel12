<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notify_id')->nullable();
            $table->enum('notify_type', ['comments', 'users'])->nullable();
            $table->enum('seen', ['0', '1'])->comment('0 == not seen | 1 == seen');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('related_type')->nullable();
            $table->string('related_id')->nullable();
            $table->string('body')->nullable();
            $table->string('title')->nullable();
            $table->string('route')->nullable();
            $table->string('read_at')->nullable();
            $table->string('icon')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
