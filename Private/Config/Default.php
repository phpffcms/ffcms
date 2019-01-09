<?php return [
	'baseProto' => 'http',
	'baseDomain' => 'ffcms.test',
	'basePath' => '/',
	'timezone' => 'Europe/Moscow',
	'adminEmail' => false,
	'debug' => [
		'all' => false,
		'cookie' => [
			'key' => 'fdebug_51cc',
			'value' => '58928ebf3097fad30db28d34bfc52619efa0c5f191638563aca9f6dbcebec5b6936f89aeaea9ae7e0d6aa000455b114938b79c35d7'
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
		'host' => '127.0.0.1',
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