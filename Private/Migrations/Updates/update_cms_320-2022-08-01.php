<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class update_cms_301.
 */
class update_cms_320 extends Migration implements MigrationInterface
{
    /**
     * Add database changes, provided in time between 3.1.0 and 3.1.0 release is done
     * @return void
     */
    public function up()
    {
        $this->getSchema()->table('contents', function(Blueprint $table){
            $table->fullText('text');
            $table->fullText('title');
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
        parent::down();
    }
}