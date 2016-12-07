<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_feedbackanswer_table.
 */
class install_feedbackanswer_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('feedback_answers', function($table) {
            $table->increments('id');
            $table->integer('feedback_id')->unsigned();
            $table->string('name', 100);
            $table->string('email');
            $table->text('message');
            $table->boolean('is_admin')->default(false);
            $table->integer('user_id')->unsigned()->default(0);
            $table->string('ip', 64)->default('127.0.0.1'); // ipv6 & ipv4
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
        $this->getSchema()->dropIfExists('feedback_answers');
        parent::down();
    }
}