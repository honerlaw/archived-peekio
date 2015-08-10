<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

try {

  require __DIR__ . '/../vendor/autoload.php';

  /**
   * Read the configuration
   */
  $config = include __DIR__ . "/../app/config/config.php";

  /**
   * Read auto-loader
   */
  include __DIR__ . "/../app/config/loader.php";

  /**
   * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
   */
  $di = new \Phalcon\DI\FactoryDefault();

  /**
   * Read services
   */
  include __DIR__ . "/../app/config/services.php";

  Services::load($di, $config);

  /**
   * Handle the request
   */
  $application = new \Phalcon\Mvc\Application($di, $config);

  echo $application->handle()->getContent();

}
catch (\Exception $e)
{
  echo $e->getMessage();
}
