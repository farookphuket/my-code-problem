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
        Schema::create('reads', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('os');
            $table->string('browser');
            $table->string('device');
            $table->timestamps();
        });

        // read whatup count
        Schema::create('read_whatup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('read_id')->constrained()
                ->onDelete('cascade');
            $table->foreignId('whatup_id')->constrained()
                ->onDelete('cascade');
            $table->string('ip');
            $table->timestamps();
        });


        // read post count
        Schema::create('post_read', function (Blueprint $table) {
            $table->id();
            $table->foreignId('read_id')->constrained()
                ->onDelete('cascade');
            $table->foreignId('post_id')->constrained()
                ->onDelete('cascade');
            $table->string('ip');
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
        Schema::dropIfExists('reads');
        Schema::dropIfExists('read_wp');
        Schema::dropIfExists('post_read');
    }
};
