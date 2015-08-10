<?php

return new \Phalcon\Config(array(
  'database' => array(
    'adapter'     => 'Mysql',
    'host'        => '0.0.0.0',
    'username'    => 'root',
    'password'    => 'peekio',
    'dbname'      => 'main',
    'charset'     => 'utf8',
    'timeout'     => 28800 //default value for wait_timeout
  ),
  'application' => array(
    'tasksDir'       => __DIR__ . '/../../app/tasks/',
    'componentsDir'  => __DIR__ . '/../../app/components/',
    'componentsModelsDir' => __DIR__ . '/../../app/components/models/',
    'modelsDir'      => __DIR__ . '/../../app/models/',
    'baseUri'        => '/',
  )
));
