<?php

use \Phalcon\Exception;
use \Phalcon\DI\FactoryDefault\CLI;
use \Phalcon\CLI\Console;

date_default_timezone_set('America/New_York');

//load composer dependencies
require __DIR__ . '/../../vendor/autoload.php';

//Using the CLI factory default services container
$di = new CLI();

//load configuration
$config = include __DIR__ . '/config.php';

//load classes
include __DIR__ . '/loader.php';

//load services class
include __DIR__ . '/services.php';

Services::load($di, $config);

//Create a console application
$console = new Console();
$console->setDI($di);

try
{
   $console->handle(array(
     'task' => 'Service',
     'action' => 'listen'
   ));
}
catch (\Phalcon\Exception $e)
{
 echo $e->getMessage();
 exit(255);
}

?>
