<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

class Services
{

  public static function load(&$di, &$config)
  {

    /**
     * The URL component is used to generate all kind of urls in the application
     */
    $di->set('url', function () use ($config) {
      $url = new UrlResolver();
      $url->setBaseUri($config->application->baseUri);
      return $url;
    }, true);

    /**
     * Setting up the view component
     */
    $di->set('view', function () use ($config) {
      $view = new View();
      $view->setViewsDir($config->application->viewsDir);
      return $view;
    }, true);

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di->set('db', function () use ($config) {
      $db = new DbAdapter((array) $config->database);
      $db->timeout = $config->database->timeout;
      $db->start = time();
      $eventsManager = new \Phalcon\Events\Manager();
      $eventsManager->attach('db', function ($event, $db) {
        if ($event->getType() == 'beforeQuery') {
          $idle = time() - $db->start;
          if ($idle > $db->timeout) {
            $db->connect();
            $db->start = time();
          }
        }
        return true;
      });
      return $db;
    });

    /**
     * If the configuration specify the use of metadata adapter use it or use memory otherwise
     */
    $di->set('modelsMetadata', function () {
      return new MetaDataAdapter();
    });

    /**
     * Start the session the first time some component request the session service
     */
    $di->set('session', function () {
      $session = new SessionAdapter();
      $session->start();
      return $session;
    });

    /**
     * Set encryption
     */
    $di->set('crypt', function() {
      $crypt = new Phalcon\Crypt();
      $crypt->setKey('&fhm8.2$m62$/,1@');
      return $crypt;
    }, true);

    /**
     * Set security component to validate / generate tokens
     */
    $di->set('secure', function () {
      return new SecurityComponent();
    });

  }


}
