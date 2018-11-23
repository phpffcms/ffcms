<?php return [
	'baseProto' => 'http',
	'baseDomain' => 'ffcms3.test',
	'basePath' => '/',
	'passwordSalt' => '$2a$07$GEc5J9C48A0Aom5ph53Al13aa$',
	'timezone' => 'Europe/Moscow',
	'adminEmail' => false,
	'debug' => [
		'all' => false,
		'cookie' => [
			'key' => 'fdebug_ebcfb0',
			'value' => 'e377c1648b7f1f6364c081bd339a93c101881f77da9b815a4ed605822ed33dee9debee99611afe0001be2c8e7e07705833901954543'
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
		'host' => 'localhost',
		'port' => '23',
		'user' => 'root@localhost.ltd',
		'encrypt' => 'tls',
		'password' => '1234'
	],
	'baseLanguage' => 'en',
	'multiLanguage' => true,
	'singleLanguage' => 'en',
	'languages' => [
		'en',
		'ru'
	],
	'languageDomainAlias' => false,
	'gaClientId' => '570934480862-son8fr180fr6u6tlt9iqpjpoesngro0o.apps.googleusercontent.com',
	'gaTrackId' => false,
	'trustedProxy' => false
];