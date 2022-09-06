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
        Schema::create('whatups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()
                                       ->onDelete('cascade');
            $table->boolean("is_public")->default(1);
            $table->string("wp_title");
            $table->string("wp_cover");
            $table->string("wp_excerpt");
            $table->text("wp_body");
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
        Schema::dropIfExists('whatups');
    }
};
