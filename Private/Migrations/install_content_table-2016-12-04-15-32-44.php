<?php

use Ffcms\Core\Migrations\MigrationInterface;
use Ffcms\Core\Migrations\Migration;

/**
 * Class install_content_table.
 */
class install_content_table extends Migration implements MigrationInterface
{
    /**
     * Execute actions when migration is up
     * @return void
     */
    public function up()
    {
        $this->getSchema()->create('contents', function($table) {
            $table->increments('id');
            $table->text('title');
            $table->mediumText('text');
            $table->string('path');
            $table->integer('category_id');
            $table->integer('author_id');
            $table->string('poster', 255)->nullable();
            $table->boolean('display')->default(true);
            $table->text('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('views')->default(0);
            $table->integer('rating')->default(0);
            $table->string('source', 1024)->nullable();
            $table->string('comment_hash', 128)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        parent::up();
    }

    /**
     * Seed created table via up() method with some data
     * @return void
     */
    public function seed()
    {
        // create content items
        $content = new stdClass();
        $content->item1 = [
            'title' => serialize(['en' => 'FFCMS 3 - the content management system', 'ru' => 'FFCMS 3 - система управления содержимым сайта']),
            'text' => serialize([
                'en' => '<p><strong>FFCMS 3</strong> - the new version of ffcms content management system, based on MVC application structure. FFCMS writed on php language syntax and using mysql, pgsql, sqlite or other PDO-compatable as database storage.</p><p>FFCMS is fully free system, distributed "as is" under MIT license and third-party packages license like GNU GPL v2/v3, BSD and other free-to-use license.</p><div style="page-break-after: always"><span style="display: none;"> </span></div><p>In basic distribution FFCMS included all necessary applications and widgets for classic website. The management interface of website is developed based on principles of maximum user friendly for fast usage. Moreover, the functional features of system can be faster and dynamicly extended by <strong>applications</strong> and <strong>widgets</strong>.</p><p>The FFCMS system can be used in any kind of website regardless of the model of monetization. Using FFCMS you can get the source code of system and change it or redistribute as you wish.</p><p>Official websites: <a href="http://ffcms.org">ffcms.org</a>, <a href="http://ffcms.ru">ffcms.ru</a></p>',
                'ru' => '<p><strong>FFCMS 3</strong> - новая версия системы управления содержимым сайта FFCMS, основанная на принципах построения приложений MVC. Система FFCMS написана с использованием синтаксиса языка php и использующая в качестве хранилища баз данных mysql, pgsql, sqlite или иную базу данных, совместимую с PDO драйвером.</p><p>FFCMS абсолютно бесплатная система, распространяемая по принципу "как есть (as is)" под лицензией MIT и лицензиями GNU GPL v2/v3, BSD и другими в зависимости от прочих используемых пакетов в составе системы.</p><div style="page-break-after: always"><span style="display: none;"> </span></div><p>В базовой поставке система имеет весь необходимый набор приложений и виджетов для реализации классического веб-сайта. Интерфейс управления содержимым сайта реализован исходя из принципов максимальной простоты использования. Кроме того, функциональные возможности системы могут быть быстро и динамично расширены при помощи <strong>приложений</strong> и <strong>виджетов</strong>.</p><p>Система FFCMS может быть использована на любых сайтах в не зависимости от моделей монетизации. Система имеет полностью открытый исходный код, который может быть вами использован как угодно.</p><p>Официальные сайты проекта: <a href="http://ffcms.org">ffcms.org</a>, <a href="http://ffcms.ru">ffcms.ru</a></p>'
            ]),
            'path' => 'ffcms3-announce',
            'category_id' => 2,
            'author_id' => 1,
            'display' => 1,
            'comment_hash' => 'C89I9n3hhE4NAk0BoIG2eNDdhhc8CNigiL5GhG18Hjnlh672e77D7Laa8fG3cnl50imaC2OoKEACo6FD4nBpKnGDgE515lMeA8k',
            'created_at' => $this->now,
            'updated_at' => $this->now
        ];
        $content->item2 = [
            'title' => serialize(['en' => 'About', 'ru' => 'О сайте']),
            'text' => serialize([
                'en' => '<p>This page can be edited in administrative panel > App > Content > "About".</p>',
                'ru' => '<p>Данная страница может быть отредактирована в административной панели > приложения > контент -> "О сайте".</p>'
            ]),
            'path' => 'about-page',
            'category_id' => 3,
            'author_id' => 1,
            'display' => 1,
            'comment_hash' => 'b4kAhho4b4KPlagam3A4B0E12BHCe092KjdFGD349hKcH9f67pi8p',
            'created_at' => $this->now,
            'updated_at' => $this->now
        ];

        foreach ($content as $item) {
            $this->getConnection()->table('contents')->insert($item);
        }
    }

    /**
     * Execute actions when migration is down
     * @return void
     */
    public function down()
    {
        $this->getSchema()->dropIfExists('contents');
        parent::down();
    }
}