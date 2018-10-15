<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZarrinpalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zarrinpal_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('authority');
            $table->string('ref_id')->nullable();
            $table->integer('price')->nullable();
            $table->enum('status',['pending','successful','unsuccessful','canceled'])->default('pending');
            $table->string('status_code')->nullable();
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
        Schema::dropIfExists('zarrinpal_logs');
    }
}
