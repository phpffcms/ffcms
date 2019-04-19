<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_spam_table.
 */
class install_spam_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('spams', function ($table){
            $table->increments('id');
            $table->string('ipv4', 36);
            $table->integer('user_id')->nullable();
            $table->integer('timestamp')->unsigned();
            $table->integer('counter')->default(0);

            $table->timestamps();

            $table->unique(['ipv4', 'user_id']);
        });
        parent::up();
    }

    /**
     * Seed created table via up() method with some data
     * @return void
     */
    public function seed() { }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('spams');
        parent::down();
    }
}