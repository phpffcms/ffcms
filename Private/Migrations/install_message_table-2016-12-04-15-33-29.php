<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_message_table.
 */
class install_message_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('messages', function($table) {
            $table->bigIncrements('id');
            $table->integer('target_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->text('message');
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
        $this->getSchema()->dropIfExists('messages');
        parent::down();
    }
}