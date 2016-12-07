<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_profilerating_table.
 */
class install_profilerating_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('profile_ratings', function($table) {
            $table->increments('id');
            $table->integer('target_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->enum('type', ['+', '-']);
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
        $this->getSchema()->dropIfExists('profile_ratings');
        parent::down();
    }
}