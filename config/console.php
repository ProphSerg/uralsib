<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
	'id' => 'UralsibPS',
	'name' => 'Сервисы Платежных систем',
	'language' => 'ru_RU',
	'sourceLanguage' => 'ru_RU',
	'timeZone' => 'Asia/Omsk',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'controllerNamespace' => 'app\commands',
	'aliases' => require(__DIR__ . '/aliases.php'),
	'components' => [
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
#        'db' => $db,
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
			#'db' => 'dbSys'
		],
	],
	'params' => $params,
	/*
	  'controllerMap' => [
	  'fixture' => [ // Fixture generation command line.
	  'class' => 'yii\faker\FixtureController',
	  ],
	  ],
	 */
];
foreach (require(__DIR__ . '/db.php') as $d => $c) {
#	$config['components'][] = $d;
	$config['components'][$d] = $c;
}

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
