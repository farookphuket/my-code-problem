<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()
                ->onDelete("cascade");
            $table->string('title');
            $table->text('body');
            $table->boolean('is_approved')->nullable()->default(1);
            $table->timestamps();
        });

        Schema::create('comment_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("comment_id")->constrained()
                ->onDelete("cascade");
            $table->foreignId("post_id")->constrained()
                ->onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('comment_post');
    }
};
