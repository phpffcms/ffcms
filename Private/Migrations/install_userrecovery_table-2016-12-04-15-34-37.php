<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_userrecovery_table.
 */
class install_userrecovery_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('user_recoveries', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('token', 128)->nullable();
            $table->boolean('archive')->default(false);
            $table->timestamps();
        });
        parent::up();
    }

    /**
     * Seed created table via up() method with some data
     * @return void
     */
    public function seed() {}

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('user_recoveries');
        parent::down();
    }
}