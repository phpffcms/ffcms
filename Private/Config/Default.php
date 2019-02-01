<?php return [
	'baseProto' => 'http',
	'baseDomain' => 'ffcms.test',
	'basePath' => '/',
	'timezone' => 'Europe/Moscow',
	'adminEmail' => false,
	'debug' => [
		'all' => false,
		'cookie' => [
			'key' => 'fdebug_39a435',
			'value' => '6c6ceb041e1e890e0ce762c1861da2857443a908660d7b29174799624b5b1041fc3b5749250b7296ff306b422aabadb016'
		]
	],
	'userCron' => false,
	'testSuite' => false,
	'theme' => [
		'Front' => 'default',
		'Admin' => 'default'
	],
	'database' => [
		'driver' => 'mysql',
		'host' => 'localhost',
		'username' => 'mysql',
		'password' => 'mysql',
		'database' => 'ffcms',
		'prefix' => 'ffcms_',
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci'
	],
	'mail' => [
		'enable' => '0',
		'host' => '',
		'port' => '',
		'user' => '',
		'password' => '',
		'encrypt' => ''
	],
	'baseLanguage' => 'en',
	'multiLanguage' => true,
	'singleLanguage' => 'en',
	'languages' => [
		'en',
		'ru'
	],
	'languageDomainAlias' => false,
	'trustedProxy' => false
];