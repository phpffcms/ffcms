<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_session_table.
 */
class install_session_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('sessions', function($table) {
            $table->string('sess_id', 128)->index()->primary()  ;
            $table->binary('sess_data');
            $table->string('sess_lifetime', 16); // shutout to laravel, if i make it like "integer" it automaticlly add "primary" key for it, hate this!!!
            $table->string('sess_time', 16);
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
        $this->getSchema()->dropIfExists('sessions');
        parent::down();
    }
}