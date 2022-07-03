<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_profile_table.
 */
class install_profile_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('profiles', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->unique();
            $table->string('name')->nullable();
            $table->tinyInteger('sex')->default(0);
            $table->date('birthday')->nullable();
            $table->string('city')->nullable();
            $table->string('hobby')->nullable();
            $table->integer('rating')->default(0);
            $table->string('phone')->nullable();
            $table->string('url')->nullable();
            $table->text('custom_data')->nullable();
            $table->timestamps();
        });
        parent::up();
    }

    /**
     * Seed created table via up() method with some data
     * @return void
     */
    public function seed()
    {

    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('profiles');
        parent::down();
    }
}