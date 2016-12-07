<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_contentcontenttag_table.
 */
class install_contentcontenttag_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        // @todo: develop migration up features
        $this->getSchema()->create('content_tags', function($table) {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->string('lang', 36)->default('en');
            $table->string('tag', 1024);
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
        $this->getSchema()->dropIfExists('content_tags');
        parent::down();
    }
}