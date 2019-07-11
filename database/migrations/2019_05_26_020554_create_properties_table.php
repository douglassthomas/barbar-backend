<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string("name");
            $table->string("description");
            $table->string("banner_id")->nullable();
            $table->string("video_id")->nullable();
            $table->string("picture360_id")->nullable();
            $table->bigInteger("price")->nullable();
            $table->string("facilities")->nullable();
            $table->string("public_facilities")->nullable();
            $table->string("fee")->nullable();
            $table->string("information")->nullable();
            $table->string("address")->nullable();
            $table->uuid("city_id")->nullable();
            $table->double("area")->nullable();
            $table->integer("total_views")->nullable();
            $table->double("longitude")->nullable();
            $table->double("latitude")->nullable();
            $table->string("propertiable_id", 100)->nullable();
            $table->string("propertiable_type")->nullable();
//            $table->
//            $table->string("status");
            $table->string("owner_id", 100)->nullable();
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
        Schema::dropIfExists('properties');
    }
}
