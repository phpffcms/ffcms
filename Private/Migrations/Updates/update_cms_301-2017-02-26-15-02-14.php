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
        if (!$this->getSchema()->hasColumn('contents', 'important')) {
            $this->getSchema()->table('contents', function ($table) {
                $table->boolean('important')->default(false);
            });
        }
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
        if ($this->getSchema()->hasColumn('contents', 'important')) {
            $this->getSchema()->table('contents', function ($table) {
                $table->dropColumn('important');
            });
        }
        parent::down();
    }
}