<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class update_profile_table.
 */
class update_profile_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        // @todo: develop migration up features
        $this->getSchema()->table('profiles', function ($table){
            $table->text('about')->nullable()->after('url');
        });
        parent::up();
    }

    public function seed(){}

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->table('profiles', function ($table){
            $table->dropColumn('about');
        });
        parent::down();
    }
}