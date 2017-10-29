<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_usernotification_table.
 */
class install_usernotification_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('user_notifications', function($table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('msg', 2048);
            $table->string('uri', 2048);
            $table->text('vars')->nullable();
            $table->boolean('readed')->default(false);
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
        $this->getSchema()->dropIfExists('user_notifications');
        parent::down();
    }
}