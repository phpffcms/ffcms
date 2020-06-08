<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class table_ban_install.
 */
class table_ban_install extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        // @todo: develop migration up features
        $this->getSchema()->create('ban', function ($table){
            $table->increments('id');
            $table->string('ipv4', 32)->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('ban_write')->default(true);
            $table->boolean('ban_read')->default(true);
            $table->timestamp('expired')->nullable();
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
        $this->getSchema()->dropIfExists('ban');
        parent::down();
    }
}