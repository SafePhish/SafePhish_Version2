<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_list', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('unique_url_id',30);
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
        Schema::drop('mailing_list');
    }
}
