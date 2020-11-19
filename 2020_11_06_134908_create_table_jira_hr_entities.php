<?php

use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJiraHrEntities extends Migration
{
    private $api;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_hr_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('jira_entity_key', 11);
            $table->string('crowd_key', 45);
            $table->timestamps();
        });

        $hrEntities = new \App\Generators\HREntityGenerator();
        $hrEntities->fillTable();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jira_hr_entities');
    }
}
