-- MySQL dump 10.13  Distrib 5.5.48, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: ffcms
-- ------------------------------------------------------
-- Server version	5.5.48

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ffcms_apps`
--

DROP TABLE IF EXISTS `ffcms_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_apps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('widget','app') COLLATE utf8_unicode_ci NOT NULL,
  `sys_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `configs` blob,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `version` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1.0.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_apps`
--

LOCK TABLES `ffcms_apps` WRITE;
/*!40000 ALTER TABLE `ffcms_apps` DISABLE KEYS */;
INSERT INTO `ffcms_apps` VALUES (1,'app','User','a:2:{s:2:\"en\";s:13:\"User identity\";s:2:\"ru\";s:51:\"Идентификация пользователя\";}','a:3:{s:16:\"registrationType\";i:1;s:14:\"captchaOnLogin\";i:0;s:17:\"captchaOnRegister\";i:1;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(2,'app','Profile','a:2:{s:2:\"en\";s:13:\"User profiles\";s:2:\"ru\";s:41:\"Профили пользователей\";}','a:6:{s:9:\"guestView\";i:1;s:14:\"wallPostOnPage\";i:5;s:16:\"delayBetweenPost\";i:30;s:6:\"rating\";i:1;s:11:\"ratingDelay\";i:86400;s:11:\"usersOnPage\";i:10;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(3,'app','Content','a:2:{s:2:\"en\";s:7:\"Content\";s:2:\"ru\";s:14:\"Контент\";}','a:6:{s:15:\"itemPerCategory\";i:10;s:7:\"userAdd\";i:0;s:15:\"multiCategories\";i:1;s:3:\"rss\";i:1;s:11:\"gallerySize\";i:500;s:13:\"galleryResize\";i:250;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(4,'app','Feedback','a:2:{s:2:\"en\";s:8:\"Feedback\";s:2:\"ru\";s:27:\"Обратная связь\";}','a:2:{s:10:\"useCaptcha\";i:1;s:8:\"guestAdd\";i:1;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(5,'app','Search','a:2:{s:2:\"en\";s:6:\"Search\";s:2:\"ru\";s:10:\"Поиск\";}','a:2:{s:10:\"itemPerApp\";i:10;s:9:\"minLength\";i:3;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(6,'app','Sitemap','a:2:{s:2:\"en\";s:7:\"Sitemap\";s:2:\"ru\";s:21:\"Карта сайта\";}','',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(7,'widget','Comments','a:2:{s:2:\"en\";s:8:\"Comments\";s:2:\"ru\";s:22:\"Комментарии\";}','a:7:{s:7:\"perPage\";i:10;s:5:\"delay\";i:60;s:9:\"minLength\";i:10;s:9:\"maxLength\";i:5000;s:8:\"guestAdd\";i:0;s:13:\"guestModerate\";i:1;s:10:\"onlyLocale\";i:0;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(8,'widget','Newcontent','a:2:{s:2:\"en\";s:11:\"New content\";s:2:\"ru\";s:25:\"Новый контент\";}','a:3:{s:10:\"categories\";a:2:{i:0;s:1:\"2\";i:1;s:1:\"3\";}s:5:\"count\";s:1:\"5\";s:5:\"cache\";s:2:\"60\";}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(9,'widget','Contenttag','a:2:{s:2:\"en\";s:12:\"Content tags\";s:2:\"ru\";s:27:\"Метки контента\";}','a:2:{s:5:\"count\";i:10;s:5:\"cache\";i:120;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45'),(10,'widget','Newcomment','a:2:{s:2:\"en\";s:12:\"New comments\";s:2:\"ru\";s:33:\"Новые комментарии\";}','a:3:{s:7:\"snippet\";i:50;s:5:\"count\";i:5;s:5:\"cache\";i:60;}',0,'1.0.0','2016-12-07 07:29:45','2016-12-07 07:29:45');
/*!40000 ALTER TABLE `ffcms_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_blacklists`
--

DROP TABLE IF EXISTS `ffcms_blacklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_blacklists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `target_id` int(10) unsigned NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_blacklists`
--

LOCK TABLES `ffcms_blacklists` WRITE;
/*!40000 ALTER TABLE `ffcms_blacklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_blacklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_comment_answers`
--

DROP TABLE IF EXISTS `ffcms_comment_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_comment_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `guest_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_comment_answers`
--

LOCK TABLES `ffcms_comment_answers` WRITE;
/*!40000 ALTER TABLE `ffcms_comment_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_comment_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_comment_posts`
--

DROP TABLE IF EXISTS `ffcms_comment_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_comment_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pathway` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `guest_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_comment_posts`
--

LOCK TABLES `ffcms_comment_posts` WRITE;
/*!40000 ALTER TABLE `ffcms_comment_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_comment_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_content_categories`
--

DROP TABLE IF EXISTS `ffcms_content_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_content_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `configs` blob,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_categories_path_unique` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_content_categories`
--

LOCK TABLES `ffcms_content_categories` WRITE;
/*!40000 ALTER TABLE `ffcms_content_categories` DISABLE KEYS */;
INSERT INTO `ffcms_content_categories` VALUES (1,'','a:2:{s:2:\"ru\";s:14:\"Главная\";s:2:\"en\";s:7:\"General\";}','','','2016-12-07 07:29:45','2016-12-07 07:29:45'),(2,'news','a:2:{s:2:\"ru\";s:14:\"Новости\";s:2:\"en\";s:4:\"News\";}','','a:8:{s:8:\"showDate\";s:1:\"1\";s:10:\"showRating\";s:1:\"1\";s:12:\"showCategory\";s:1:\"1\";s:10:\"showAuthor\";s:1:\"1\";s:9:\"showViews\";s:1:\"1\";s:12:\"showComments\";s:1:\"1\";s:10:\"showPoster\";s:1:\"1\";s:8:\"showTags\";s:1:\"1\";}','2016-12-07 07:29:45','2016-12-07 07:29:45'),(3,'page','a:2:{s:2:\"ru\";s:16:\"Страницы\";s:2:\"en\";s:5:\"Pages\";}','','','2016-12-07 07:29:45','2016-12-07 07:29:45');
/*!40000 ALTER TABLE `ffcms_content_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_content_ratings`
--

DROP TABLE IF EXISTS `ffcms_content_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_content_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_content_ratings`
--

LOCK TABLES `ffcms_content_ratings` WRITE;
/*!40000 ALTER TABLE `ffcms_content_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_content_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_content_tags`
--

DROP TABLE IF EXISTS `ffcms_content_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_content_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  `lang` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `tag` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_content_tags`
--

LOCK TABLES `ffcms_content_tags` WRITE;
/*!40000 ALTER TABLE `ffcms_content_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_content_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_contents`
--

DROP TABLE IF EXISTS `ffcms_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `poster` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` text COLLATE utf8_unicode_ci,
  `meta_keywords` text COLLATE utf8_unicode_ci,
  `meta_description` text COLLATE utf8_unicode_ci,
  `views` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `source` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `comment_hash` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_contents`
--

LOCK TABLES `ffcms_contents` WRITE;
/*!40000 ALTER TABLE `ffcms_contents` DISABLE KEYS */;
INSERT INTO `ffcms_contents` VALUES (1,'a:2:{s:2:\"en\";s:39:\"FFCMS 3 - the content management system\";s:2:\"ru\";s:77:\"FFCMS 3 - система управления содержимым сайта\";}','a:2:{s:2:\"en\";s:1129:\"<p><strong>FFCMS 3</strong> - the new version of ffcms content management system, based on MVC application structure. FFCMS writed on php language syntax and using mysql, pgsql, sqlite or other PDO-compatable as database storage.</p><p>FFCMS is fully free system, distributed \"as is\" under MIT license and third-party packages license like GNU GPL v2/v3, BSD and other free-to-use license.</p><div style=\"page-break-after: always\"><span style=\"display: none;\"> </span></div><p>In basic distribution FFCMS included all necessary applications and widgets for classic website. The management interface of website is developed based on principles of maximum user friendly for fast usage. Moreover, the functional features of system can be faster and dynamicly extended by <strong>applications</strong> and <strong>widgets</strong>.</p><p>The FFCMS system can be used in any kind of website regardless of the model of monetization. Using FFCMS you can get the source code of system and change it or redistribute as you wish.</p><p>Official websites: <a href=\"http://ffcms.org\">ffcms.org</a>, <a href=\"http://ffcms.ru\">ffcms.ru</a></p>\";s:2:\"ru\";s:2137:\"<p><strong>FFCMS 3</strong> - новая версия системы управления содержимым сайта FFCMS, основанная на принципах построения приложений MVC. Система FFCMS написана с использованием синтаксиса языка php и использующая в качестве хранилища баз данных mysql, pgsql, sqlite или иную базу данных, совместимую с PDO драйвером.</p><p>FFCMS абсолютно бесплатная система, распространяемая по принципу \"как есть (as is)\" под лицензией MIT и лицензиями GNU GPL v2/v3, BSD и другими в зависимости от прочих используемых пакетов в составе системы.</p><div style=\"page-break-after: always\"><span style=\"display: none;\"> </span></div><p>В базовой поставке система имеет весь необходимый набор приложений и виджетов для реализации классического веб-сайта. Интерфейс управления содержимым сайта реализован исходя из принципов максимальной простоты использования. Кроме того, функциональные возможности системы могут быть быстро и динамично расширены при помощи <strong>приложений</strong> и <strong>виджетов</strong>.</p><p>Система FFCMS может быть использована на любых сайтах в не зависимости от моделей монетизации. Система имеет полностью открытый исходный код, который может быть вами использован как угодно.</p><p>Официальные сайты проекта: <a href=\"http://ffcms.org\">ffcms.org</a>, <a href=\"http://ffcms.ru\">ffcms.ru</a></p>\";}','ffcms3-announce',2,1,NULL,1,NULL,NULL,NULL,1,0,'','C89I9n3hhE4NAk0BoIG2eNDdhhc8CNigiL5GhG18Hjnlh672e77D7Laa8fG3cnl50imaC2OoKEACo6FD4nBpKnGDgE515lMeA8k','2016-12-07 07:29:45','2016-12-07 07:55:04',NULL),(2,'a:2:{s:2:\"en\";s:5:\"About\";s:2:\"ru\";s:13:\"О сайте\";}','a:2:{s:2:\"en\";s:81:\"<p>This page can be edited in administrative panel > App > Content > \"About\".</p>\";s:2:\"ru\";s:196:\"<p>Данная страница может быть отредактирована в административной панели > приложения > контент -> \"О сайте\".</p>\";}','about-page',3,1,NULL,1,NULL,NULL,NULL,0,0,'','b4kAhho4b4KPlagam3A4B0E12BHCe092KjdFGD349hKcH9f67pi8p','2016-12-07 07:29:45','2016-12-07 07:29:45',NULL);
/*!40000 ALTER TABLE `ffcms_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_feedback_answers`
--

DROP TABLE IF EXISTS `ffcms_feedback_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_feedback_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_feedback_answers`
--

LOCK TABLES `ffcms_feedback_answers` WRITE;
/*!40000 ALTER TABLE `ffcms_feedback_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_feedback_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_feedback_posts`
--

DROP TABLE IF EXISTS `ffcms_feedback_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_feedback_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `readed` tinyint(1) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_feedback_posts`
--

LOCK TABLES `ffcms_feedback_posts` WRITE;
/*!40000 ALTER TABLE `ffcms_feedback_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_feedback_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_invites`
--

DROP TABLE IF EXISTS `ffcms_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_invites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invites_token_unique` (`token`),
  UNIQUE KEY `invites_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_invites`
--

LOCK TABLES `ffcms_invites` WRITE;
/*!40000 ALTER TABLE `ffcms_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_invites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_messages`
--

DROP TABLE IF EXISTS `ffcms_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `readed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_messages`
--

LOCK TABLES `ffcms_messages` WRITE;
/*!40000 ALTER TABLE `ffcms_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_migrations`
--

DROP TABLE IF EXISTS `ffcms_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migrations_migration_unique` (`migration`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_migrations`
--

LOCK TABLES `ffcms_migrations` WRITE;
/*!40000 ALTER TABLE `ffcms_migrations` DISABLE KEYS */;
INSERT INTO `ffcms_migrations` VALUES (1,'install_app_table-2016-12-04-15-31-46','2016-12-07 07:29:45','2016-12-07 07:29:45'),(2,'install_blacklist_table-2016-12-04-15-32-21','2016-12-07 07:29:45','2016-12-07 07:29:45'),(3,'install_commentanswer_table-2016-12-04-15-32-30','2016-12-07 07:29:45','2016-12-07 07:29:45'),(4,'install_commentpost_table-2016-12-04-15-32-37','2016-12-07 07:29:45','2016-12-07 07:29:45'),(5,'install_contentcategory_table-2016-12-04-15-32-47','2016-12-07 07:29:45','2016-12-07 07:29:45'),(6,'install_contentcontenttag_table-2016-12-04-15-33-02','2016-12-07 07:29:45','2016-12-07 07:29:45'),(7,'install_contentrating_table-2016-12-04-15-32-55','2016-12-07 07:29:45','2016-12-07 07:29:45'),(8,'install_content_table-2016-12-04-15-32-44','2016-12-07 07:29:45','2016-12-07 07:29:45'),(9,'install_feedbackanswer_table-2016-12-04-15-33-12','2016-12-07 07:29:45','2016-12-07 07:29:45'),(10,'install_feedbackpost_table-2016-12-04-15-33-18','2016-12-07 07:29:45','2016-12-07 07:29:45'),(11,'install_invite_table-2016-12-04-15-33-22','2016-12-07 07:29:45','2016-12-07 07:29:45'),(12,'install_message_table-2016-12-04-15-33-29','2016-12-07 07:29:45','2016-12-07 07:29:45'),(13,'install_profilefield_table-2016-12-04-15-33-47','2016-12-07 07:29:45','2016-12-07 07:29:45'),(14,'install_profilerating_table-2016-12-04-15-33-52','2016-12-07 07:29:45','2016-12-07 07:29:45'),(15,'install_profile_table-2016-12-04-15-33-37','2016-12-07 07:29:45','2016-12-07 07:29:45'),(16,'install_role_table-2016-12-04-15-33-58','2016-12-07 07:29:46','2016-12-07 07:29:46'),(17,'install_session_table-2016-12-04-15-34-04','2016-12-07 07:29:46','2016-12-07 07:29:46'),(18,'install_system_table-2016-12-04-15-34-10','2016-12-07 07:29:46','2016-12-07 07:29:46'),(19,'install_userlog_table-2016-12-04-15-34-18','2016-12-07 07:29:46','2016-12-07 07:29:46'),(20,'install_usernotification_table-2016-12-04-15-34-25','2016-12-07 07:29:46','2016-12-07 07:29:46'),(21,'install_userprovider_table-2016-12-04-15-34-33','2016-12-07 07:29:46','2016-12-07 07:29:46'),(22,'install_userrecovery_table-2016-12-04-15-34-37','2016-12-07 07:29:46','2016-12-07 07:29:46'),(23,'install_user_table-2016-12-04-15-34-15','2016-12-07 07:29:46','2016-12-07 07:29:46'),(24,'install_wallanswer_table-2016-12-04-15-34-46','2016-12-07 07:29:46','2016-12-07 07:29:46'),(25,'install_wallpost_table-2016-12-04-15-34-50','2016-12-07 07:29:46','2016-12-07 07:29:46');
/*!40000 ALTER TABLE `ffcms_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_profile_fields`
--

DROP TABLE IF EXISTS `ffcms_profile_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_profile_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('text','link') COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `reg_exp` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reg_cond` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_profile_fields`
--

LOCK TABLES `ffcms_profile_fields` WRITE;
/*!40000 ALTER TABLE `ffcms_profile_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_profile_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_profile_ratings`
--

DROP TABLE IF EXISTS `ffcms_profile_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_profile_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `type` enum('+','-') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_profile_ratings`
--

LOCK TABLES `ffcms_profile_ratings` WRITE;
/*!40000 ALTER TABLE `ffcms_profile_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_profile_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_profiles`
--

DROP TABLE IF EXISTS `ffcms_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `nick` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hobby` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_data` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profiles_user_id_unique` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_profiles`
--

LOCK TABLES `ffcms_profiles` WRITE;
/*!40000 ALTER TABLE `ffcms_profiles` DISABLE KEYS */;
INSERT INTO `ffcms_profiles` VALUES (1,1,NULL,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2016-12-07 07:29:46','2016-12-07 07:29:46'),(7,7,NULL,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2016-12-08 06:55:48','2016-12-08 06:55:48'),(8,8,NULL,0,NULL,NULL,NULL,0,NULL,NULL,NULL,'2016-12-08 06:55:48','2016-12-08 06:55:48');
/*!40000 ALTER TABLE `ffcms_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_roles`
--

DROP TABLE IF EXISTS `ffcms_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_roles`
--

LOCK TABLES `ffcms_roles` WRITE;
/*!40000 ALTER TABLE `ffcms_roles` DISABLE KEYS */;
INSERT INTO `ffcms_roles` VALUES (1,'OnlyRead','','2016-12-07 07:29:45','2016-12-07 07:29:45'),(2,'User','global/write;global/file','2016-12-07 07:29:45','2016-12-07 07:29:45'),(3,'Moderator','global/write;global/modify;global/file','2016-12-07 07:29:45','2016-12-07 07:29:45'),(4,'Admin','global/all','2016-12-07 07:29:45','2016-12-07 07:29:45');
/*!40000 ALTER TABLE `ffcms_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_sessions`
--

DROP TABLE IF EXISTS `ffcms_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_sessions` (
  `sess_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sess_data` blob NOT NULL,
  `sess_lifetime` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `sess_time` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_sessions`
--

LOCK TABLES `ffcms_sessions` WRITE;
/*!40000 ALTER TABLE `ffcms_sessions` DISABLE KEYS */;
INSERT INTO `ffcms_sessions` VALUES ('00lq6etnkl12s3lph3tcvbun30','_sf2_attributes|a:1:{s:11:\"_csrf_token\";s:51:\"lHpjlpJN5C8GnkjB8AENoboab0likPoGm492nN9nDaI0Fekl7ll\";}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481124970;s:1:\"c\";i:1481124970;s:1:\"l\";s:5:\"86400\";}','86400','1481124971',NULL,NULL),('3thnqmtv1rv9iadrd3lr7gg9m4','_sf2_attributes|a:0:{}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481124971;s:1:\"c\";i:1481124971;s:1:\"l\";s:5:\"86400\";}','86400','1481124971',NULL,NULL),('86jtnod39flesvovtmkajfc762','_sf2_attributes|a:0:{}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481125788;s:1:\"c\";i:1481125788;s:1:\"l\";s:5:\"86400\";}','86400','1481125788',NULL,NULL),('hvcimis1unqr330qg95f4inca4','_sf2_attributes|a:1:{s:11:\"_csrf_token\";s:34:\"EL8glMPKCfo6CnE0HMi9gDnD8i6I0mi68H\";}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481125787;s:1:\"c\";i:1481125786;s:1:\"l\";s:5:\"86400\";}','86400','1481125787',NULL,NULL),('sispq52f0k5r7kd3o657ictnf0','_sf2_attributes|a:2:{s:11:\"_csrf_token\";s:34:\"FHle5ngLcBIAPhDoLNbg02k52BoL0DnEFP\";s:10:\"ff_user_id\";i:1;}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481127168;s:1:\"c\";i:1481095787;s:1:\"l\";s:5:\"86400\";}','86400','1481127168',NULL,NULL),('tdu91eonfgee1jplme8i62ccj1','_sf2_attributes|a:2:{s:11:\"_csrf_token\";s:49:\"30h8HmKiHKNeP2fe8BKhho500dc0OCik8PmJ79PEBhN48fMnJ\";s:7:\"captcha\";s:5:\"53nnd\";}_sf2_flashes|a:0:{}_sf2_meta|a:3:{s:1:\"u\";i:1481129614;s:1:\"c\";i:1481129553;s:1:\"l\";s:5:\"86400\";}','86400','1481129614',NULL,NULL);
/*!40000 ALTER TABLE `ffcms_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_systems`
--

DROP TABLE IF EXISTS `ffcms_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_systems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_systems`
--

LOCK TABLES `ffcms_systems` WRITE;
/*!40000 ALTER TABLE `ffcms_systems` DISABLE KEYS */;
INSERT INTO `ffcms_systems` VALUES (1,'version','3.0.0-RC','2016-12-07 07:29:46','2016-12-07 07:29:46');
/*!40000 ALTER TABLE `ffcms_systems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_user_logs`
--

DROP TABLE IF EXISTS `ffcms_user_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_user_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_user_logs`
--

LOCK TABLES `ffcms_user_logs` WRITE;
/*!40000 ALTER TABLE `ffcms_user_logs` DISABLE KEYS */;
INSERT INTO `ffcms_user_logs` VALUES (1,'1','AUTH','Успешная авторизация с адреса 127.0.0.1','2016-12-07 07:37:55','2016-12-07 07:37:55');
/*!40000 ALTER TABLE `ffcms_user_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_user_notifications`
--

DROP TABLE IF EXISTS `ffcms_user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_user_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `msg` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `vars` blob,
  `readed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_user_notifications`
--

LOCK TABLES `ffcms_user_notifications` WRITE;
/*!40000 ALTER TABLE `ffcms_user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_user_providers`
--

DROP TABLE IF EXISTS `ffcms_user_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_user_providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_user_providers`
--

LOCK TABLES `ffcms_user_providers` WRITE;
/*!40000 ALTER TABLE `ffcms_user_providers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_user_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_user_recoveries`
--

DROP TABLE IF EXISTS `ffcms_user_recoveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_user_recoveries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `password` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_user_recoveries`
--

LOCK TABLES `ffcms_user_recoveries` WRITE;
/*!40000 ALTER TABLE `ffcms_user_recoveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_user_recoveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_users`
--

DROP TABLE IF EXISTS `ffcms_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` tinyint(4) NOT NULL DEFAULT '2',
  `approve_token` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_users`
--

LOCK TABLES `ffcms_users` WRITE;
/*!40000 ALTER TABLE `ffcms_users` DISABLE KEYS */;
INSERT INTO `ffcms_users` VALUES (1,'zenn','zenn@ffcms.org','$2a$07$0ef8n5aI0ccmNOhKN7fhD.wJhmZLKYejGZVFlyohGrwvHf1ZcHnwi',4,'0','2016-12-07 07:29:46','2016-12-07 07:29:46'),(7,'test1','test1@gmail.com','$2a$07$0ef8n5aI0ccmNOhKN7fhD.pE2TAfunLPrvezw0aedJ8NZq8Azezeq',2,'0','2016-12-08 06:55:48','2016-12-08 06:55:48'),(8,'test2','test2@gmail.com','$2a$07$0ef8n5aI0ccmNOhKN7fhD.op.x4vGE7rsaJ7DE6hXSHwVYQOu4cXO',2,'0','2016-12-08 06:55:48','2016-12-08 06:55:48');
/*!40000 ALTER TABLE `ffcms_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_wall_answers`
--

DROP TABLE IF EXISTS `ffcms_wall_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_wall_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_wall_answers`
--

LOCK TABLES `ffcms_wall_answers` WRITE;
/*!40000 ALTER TABLE `ffcms_wall_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_wall_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ffcms_wall_posts`
--

DROP TABLE IF EXISTS `ffcms_wall_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ffcms_wall_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ffcms_wall_posts`
--

LOCK TABLES `ffcms_wall_posts` WRITE;
/*!40000 ALTER TABLE `ffcms_wall_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `ffcms_wall_posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-12-08  9:55:49
