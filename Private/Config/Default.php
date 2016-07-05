<?php return [
	'baseProto' => 'http',
	'baseDomain' => 'ffcms.app',
	'basePath' => '/',
	'passwordSalt' => '$2a$07$2NJgci5A7NI9m2J9Ii92a1I3DiH6$',
	'timezone' => 'Europe/Moscow',
	'debug' => [
		'all' => false,
		'cookie' => [
			'key' => 'fdebug_jepP2',
			'value' => 'pOH1Kj18fHN7mk9a81F32eJbl9d3AeDjLC1g7poPGE22Ii6pI68P5B6O5hKMofnLPB0lFdpo2a2o78NhedjGA'
		]
	],
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
	'adminEmail' => 'root@ffcms.app',
	'baseLanguage' => 'en',
	'multiLanguage' => false,
	'singleLanguage' => 'ru',
	'languages' => [
		'en',
		'ru'
	],
	'languageDomainAlias' => false,
	'gaClientId' => '570934480862-son8fr180fr6u6tlt9iqpjpoesngro0o.apps.googleusercontent.com',
	'gaTrackId' => false,
	'trustedProxy' => false
];