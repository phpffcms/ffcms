<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_commentanswer_table.
 */
class install_commentanswer_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('comment_answers', function($table) {
            $table->increments('id');
            $table->integer('comment_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('guest_name', 100);
            $table->text('message');
            $table->string('lang', 32)->default('en');
            $table->string('ip', 64)->default('127.0.0.1'); // ipv4 and ipv6 (32 chars + 7 digits is max)
            $table->boolean('moderate')->default(false);
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
        $this->getSchema()->dropIfExists('comment_answers');
        parent::down();
    }
}