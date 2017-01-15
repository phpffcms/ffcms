<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class update_cms_301.
 */
class update_cms_301 extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        // @todo: develop migration up features
        $this->getSchema()->create('update_test', function ($table){
            $table->increments('id');
            $table->string('column1', 1024)->default('none');
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
        // @todo: develop migration down features
        $this->getConnection()->table('update_test')->insert([
            ['column1' => 'Text data 1', 'created_at' => $this->now, 'updated_at' => $this->now],
            ['column1' => 'Text data 2', 'created_at' => $this->now, 'updated_at' => $this->now]
        ]);
    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('update_test');
        parent::down();
    }
}