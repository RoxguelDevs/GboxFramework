<?php
$params = require (__DIR__ . '/params.php');
$db = require (__DIR__ . '/db.php');
$mail = require (__DIR__ . '/mail.php');
$config = [
	'web' => rtrim('//' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/'),
	'path' => realpath(dirname(__DIR__) . '/..'),
	'timezone' => 'America/Argentina/Buenos_Aires',
	'cookieSalt' => 'GXskVmTTupFXdJoiVLEIKkERm6xUQUFx',
	'components' => [
        'user' => [
			'identityClass' => 'app\models\User',
		],
		'lang' => [
			'class' => 'app\components\ComponentLanguages',
			'params' => [
				'langs' => ['es', 'en'],
			],
		],
		'debug' => [
			'class' => 'app\components\ComponentDebug',
			'params' => [
				'path' => '@path/gbox/tmp/debug/',
			],
		],
		'sendmail' => [
			'class' => 'app\components\ComponentSendmail',
			'params' => [
				'config' => $mail,
			],
		],
		'errorHandler' => [
            'errorAction' => [
				403 => 'errors/403',
				404 => 'errors/404',
				500 => 'errors/500',
            ],
        ],
	],
	'shortUrl' => true,
	'params' => $params,
	'db' => $db,
	'modules' => [
	
	],
];

error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('GBOX_DEBUG') or define('GBOX_DEBUG', false);
if (GBOX_DEBUG)
{
    $config['modules']['debug'] = [
        'class' => 'app\debug\Module',
    ];
}
else
{
	error_reporting(0);
	ini_set('display_errors', '0');
}

return $config;