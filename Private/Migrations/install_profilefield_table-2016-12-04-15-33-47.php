<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_profilefield_table.
 */
class install_profilefield_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('profile_fields', function($table) {
            $table->increments('id');
            $table->enum('type', ['text', 'link']);
            $table->text('name');
            $table->string('reg_exp', 512)->nullable();
            $table->tinyInteger('reg_cond')->default(0);
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
        $this->getSchema()->dropIfExists('profile_fields');
        parent::down();
    }
}