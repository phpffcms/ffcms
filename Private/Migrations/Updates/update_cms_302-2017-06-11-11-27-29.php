<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class update_cms_302.
 */
class update_cms_302 extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        if ($this->getSchema()->hasColumn('user_recoveries', 'password')) {
            $this->getSchema()->table('user_recoveries', function ($table) {
                $table->dropColumn('password');
            });
        }
        parent::up();
    }

    /**
     * Seed created table via up() method with some data
     * @return void
     */
    public function seed() {}

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        if (!$this->getSchema()->hasColumn('user_recoveries', 'password')) {
            $this->getSchema()->table('user_recoveries', function ($table) {
                $table->string('password', 512);
            });
        }
        parent::down();
    }
}