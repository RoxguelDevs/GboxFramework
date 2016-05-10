<?php
// defined('GBOX_DEBUG') or define('GBOX_DEBUG', true);

require (__DIR__ . '/gbox/autoload.php');
$config = require (__DIR__ . '/gbox/config/config.php');

$autoload = new Gbox\Autoload;
$autoload->loadBase();
$autoload->requireAll();

(new Gbox($config))->run();