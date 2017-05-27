<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_role_table.
 */
class install_role_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        // @todo: develop migration up features
        $this->getSchema()->create('roles', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->text('permissions');
            $table->string('color')->default('#777777');
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
        $this->getConnection()->table('roles')->insert([
            ['name' => 'OnlyRead', 'permissions' => '', 'color' =>  '#777777', 'created_at' => $this->now, 'updated_at' => $this->now],
            ['name' => 'User', 'permissions' => 'global/write;global/file', 'color' =>  '#777777', 'created_at' => $this->now, 'updated_at' => $this->now],
            ['name' => 'Moderator', 'permissions' => 'global/write;global/modify;global/file', 'color' => '#598de7', 'created_at' => $this->now, 'updated_at' => $this->now],
            ['name' => 'Admin', 'permissions' => 'global/all', 'color' => '#ff0000', 'created_at' => $this->now, 'updated_at' => $this->now]
        ]);
    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('roles');
        parent::down();
    }
}