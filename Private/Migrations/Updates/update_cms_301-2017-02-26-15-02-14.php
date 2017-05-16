<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class update_cms_301.
 */
class update_cms_301 extends Migration implements MigrationInterface
{
    /**
     * Add database changes, provided in time between 3.0.0 and 3.0.1 release is done
     * @return void
     */
    public function up()
    {
        // use important column for content app
        if (!$this->getSchema()->hasColumn('contents', 'important')) {
            $this->getSchema()->table('contents', function ($table) {
                $table->boolean('important')->default(false);
            });
        }
        // add group display color features
        if (!$this->getSchema()->hasColumn('roles', 'color')) {
            $this->getSchema()->table('roles', function ($table) {
                $table->string('color')->default('#777777');
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
        if ($this->getSchema()->hasColumn('roles', 'color')) {
            $this->getSchema()->table('roles', function ($table) {
                $table->dropColumn('color');
            });
        }
        parent::down();
    }
}