<?php return [
	'basePath' => '/',
	'siteIndex' => 'Main::Index',
	'passwordSalt' => '$2a$10$1o81mUjA4NhQp8nSeaFmN8',
	'debug' => [
		'all' => false,
		'cookie' => [
			'key' => 'fdebug',
			'value' => 'jiSpq941Am'
		]
	],
	'theme' => [
		'Front' => 'default',
		'Admin' => 'default'
	],
	'database' => [
		'driver' => 'mysql',
		'host' => '127.0.0.1',
		'database' => 'ffcms',
		'username' => 'mysql',
		'password' => 'mysql',
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => 'ffcms_'
	],
	'adminEmail' => 'noreplay@ffcms.org',
	'baseLanguage' => 'en',
	'multiLanguage' => true,
	'singleLanguage' => 'en',
	'languages' => [
		'en',
		'ru'
	],
	'languageDomainAlias' => false
];