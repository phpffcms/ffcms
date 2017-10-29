<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_contentcategory_table.
 */
class install_contentcategory_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('content_categories', function($table) {
            $table->increments('id');
            $table->string('path', 200)->unique();
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('configs')->nullable();
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
        // create categories
        $cat = new stdClass();
        $cat->General = [
            'title' => serialize([
                'ru' => 'Главная',
                'en' => 'General'
            ])
        ];

        $cat->News = [
            'title' => serialize([
                'ru' => 'Новости',
                'en' => 'News'
            ]),
            'configs' => serialize([
                'showDate' => '1',
                'showRating' => '1',
                'showCategory' => '1',
                'showAuthor' => '1',
                'showViews' => '1',
                'showComments' => '1',
                'showPoster' => '1',
                'showTags' => '1'
            ])
        ];

        $cat->Page = [
            'title' => serialize([
                'ru' => 'Страницы',
                'en' => 'Pages'
            ])
        ];

        $this->getConnection()->table('content_categories')->insert([
            ['id' => 1, 'path' => '', 'title' => $cat->General['title'], 'description' => '', 'configs' => '', 'created_at' => $this->now, 'updated_at' => $this->now],
            ['id' => 2, 'path' => 'news', 'title' => $cat->News['title'], 'description' => '', 'configs' => $cat->News['configs'], 'created_at' => $this->now, 'updated_at' => $this->now],
            ['id' => 3, 'path' => 'page', 'title' => $cat->Page['title'], 'description' => '', 'configs' => '', 'created_at' => $this->now, 'updated_at' => $this->now],
        ]);
    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('content_categories');
        parent::down();
    }
}