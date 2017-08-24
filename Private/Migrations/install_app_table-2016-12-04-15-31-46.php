<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_app_table.
 */
class install_app_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('apps', function($table) {
            $table->increments('id');
            $table->enum('type', ['widget', 'app']);
            $table->string('sys_name');
            $table->text('name');
            $table->binary('configs')->nullable();
            $table->boolean('disabled')->default(false);
            $table->string('version', 32)->default('1.0.0'); // symantic version style
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
        $now = date('Y-m-d H:i:s', time());

        $configs = new stdClass();
        $names = new stdClass();

        $configs->user = serialize([
            'registrationType' => 1, // 0 = invites, 1 = validation via email, 2 = full open without validation
            'captchaOnLogin' => 0,
            'captchaOnRegister' => 1
        ]);

        $configs->profile = serialize([
            'guestView' => 1,
            'wallPostOnPage' => 5,
            'wallPostOnFeed' => 10,
            'delayBetweenPost' => 30,
            'rating' => 1,
            'ratingDelay' => 60 * 60 * 24,
            'usersOnPage' => 10
        ]);

        $configs->content = serialize([
            'itemPerCategory' => 10,
            'userAdd' => 0,
            'multiCategories' => 1,
            'rss' => 1,
            'gallerySize' => 500,
            'galleryResize' => 250
        ]);

        $configs->feedback = serialize([
            'useCaptcha' => 1,
            'guestAdd' => 1
        ]);

        $configs->comments = serialize([
            'perPage' => 10,
            'delay' => 60,
            'minLength' => 10,
            'maxLength' => 5000,
            'guestAdd' => 0,
            'guestModerate' => 1,
            'onlyLocale' => 0
        ]);

        $configs->newcontent = serialize([
            'categories' => ['2','3'],
            'count' => '5',
            'cache' => '60'
        ]);

        $configs->contenttag = serialize([
            'count' => 10,
            'cache' => 120
        ]);

        $configs->newcomment = serialize([
            'snippet' => 50,
            'count' => 5,
            'cache' => 60
        ]);

        $configs->search = serialize([
            'itemPerApp' => 10,
            'minLength' => 3
        ]);

        $names->user = serialize([
            'en' => 'User identity',
            'ru' => 'Идентификация пользователя'
        ]);

        $names->profile = serialize([
            'en' => 'User profiles',
            'ru' => 'Профили пользователей'
        ]);

        $names->content = serialize([
            'en' => 'Content',
            'ru' => 'Контент'
        ]);

        $names->feedback = serialize([
            'en' => 'Feedback',
            'ru' => 'Обратная связь'
        ]);

        $names->comments = serialize([
            'en' => 'Comments',
            'ru' => 'Комментарии'
        ]);

        $names->newcontent = serialize([
            'en' => 'New content',
            'ru' => 'Новый контент'
        ]);

        $names->contenttag = serialize([
            'en' => 'Content tags',
            'ru' => 'Метки контента'
        ]);

        $names->newcomment = serialize([
            'en' => 'New comments',
            'ru' => 'Новые комментарии'
        ]);

        $names->search = serialize([
            'en' => 'Search',
            'ru' => 'Поиск'
        ]);

        $names->sitemap = serialize([
            'en' => 'Sitemap',
            'ru' => 'Карта сайта'
        ]);

        $this->getConnection()->table('apps')->insert([
            ['type' => 'app', 'sys_name' => 'User', 'name' => $names->user, 'configs' => $configs->user, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'app', 'sys_name' => 'Profile', 'name' => $names->profile, 'configs' => $configs->profile, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'app', 'sys_name' => 'Content', 'name' => $names->content, 'configs' => $configs->content, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'app', 'sys_name' => 'Feedback', 'name' => $names->feedback, 'configs' => $configs->feedback, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'app', 'sys_name' => 'Search', 'name' => $names->search, 'configs' => $configs->search, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'app', 'sys_name' => 'Sitemap', 'name' => $names->sitemap, 'configs' => '', 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'widget', 'sys_name' => 'Comments', 'name' => $names->comments, 'configs' => $configs->comments, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'widget', 'sys_name' => 'Newcontent', 'name' => $names->newcontent, 'configs' => $configs->newcontent, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'widget', 'sys_name' => 'Contenttag', 'name' => $names->contenttag, 'configs' => $configs->contenttag, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'widget', 'sys_name' => 'Newcomment', 'name' => $names->newcomment, 'configs' => $configs->newcomment, 'version' => '1.0.0', 'created_at' => $now, 'updated_at' => $now]
        ]);

    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('apps');
        parent::down();
    }
}