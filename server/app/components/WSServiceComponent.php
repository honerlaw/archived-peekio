<?php

use \Phalcon\Mvc\User\Component;

use \Ratchet\MessageComponentInterface;
use \Ratchet\ConnectionInterface;

class WSServiceComponent extends Component implements MessageComponentInterface
{

  /**
   * Stores all of the active connections
   */
  private $clients;

  /**
   * Initialize the client storage object
   */
  public function __construct()
  {
    $this->clients = new \SplObjectStorage();
  }

  /**
   * Called when a websocket connection is opened
   */
  public function onOpen(ConnectionInterface $conn)
  {
    $this->clients->attach($conn);
  }

  /**
   * Called when a message is received from a websocket connection
   */
  public function onMessage(ConnectionInterface $conn, $msg)
  {
    try
    {
      $json = json_decode($msg);
      if(count(explode('/', $json->event)) === 2)
      {
        $controller = ucfirst(strtolower(explode('/', $json->event)[0]));
        $action = strtolower(explode('/', $json->event)[1]);
        if(isset($json->token))
        {
          $user = $this->secure->validate(isset($json->token) ? $json->token : '');
          if(is_object($user))
          {
            //valid token so we continue with the request
            $this->handle($json, $conn, 'users', $controller, $action, $user);
          }
          else
          {
            //invalid token so send error response
            $conn->send(json_encode(array(
              'event' => 'error_token',
              'status' => 'error',
              'result' => 'invalid token.'
            )));
          }
        }
        else
        {
          //no token passed so we just assume guest role
          $this->handle($json, $conn, 'guests', $controller, $action);
        }
      }
      else
      {
        $conn->send(json_encode(array(
          'event' => 'error',
          'status' => 'error',
          'result' => 'invalid request.'
        )));
      }
    }
    catch(Exception $e)
    {
      echo $e->getMessage();
    }
  }

  /**
   * Called when a connection is closed
   */
  public function onClose(ConnectionInterface $conn)
  {
    $this->clients->detach($conn);
  }

  /**
   * Called when an error is thrown
   */
  public function onError(ConnectionInterface $conn, \Exception $e)
  {
    echo $e->getMessage();
    $conn->close();
  }

  /**
   * Checks that the passed role has access to the requested controller / action
   * if it does then we simply handle the request
   */
  private function handle($json, &$conn, $role, $controller, $action, $user = null)
  {
    if($this->secure->access($role, $controller, $action))
    {
      $controllerClassName = $controller . 'Component';
      $controllerClass = new $controllerClassName();
      $params = (isset($json->params) ? ((array) $json->params) : null);
      if(!is_null($user))
      {
        if(!is_array($params))
        {
          $params = array($user);
        }
        else
        {
          $params[] = $user;
        }
      }
      $result = call_user_func_array(array($controllerClass, explode('/', $json->event)[1]), $params);
      if($result[0] === false)
      {
        $conn->send(json_encode(array(
          'event' => $json->event,
          'status' => 'error',
          'result' => (array_key_exists(1, $result) ? $result[1] : 'failed.')
        )));
      }
      else
      {
        $conn->send(json_encode(array(
          'event' => $json->event,
          'status' => 'success',
          'result' => (array_key_exists(1, $result) ? $result[1] : 'failed.')
        )));
      }
    }
    else
    {
      $conn->send(json_encode(array(
        'event' => $json->event,
        'status' => 'error',
        'result' => 'invalid request.'
      )));
    }
  }

}
