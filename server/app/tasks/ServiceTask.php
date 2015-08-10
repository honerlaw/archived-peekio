<?php

use \Phalcon\CLI\Task;

use \Ratchet\Server\IoServer;
use \Ratchet\Http\HttpServer;
use \Ratchet\WebSocket\WsServer;

class ServiceTask extends Task
{

  public function listenAction()
  {
    $server = IoServer::factory(new HttpServer(
      new WsServer(
        new WSServiceComponent()
      )
    ), 8080);
    $server->run();
  }

}
