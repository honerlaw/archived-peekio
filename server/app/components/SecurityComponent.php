<?php

use \Phalcon\Mvc\User\Component;
use \Phalcon\Acl;
use \Phalcon\Acl\Role;
use \Phalcon\Acl\Resource;
use \Phalcon\Acl\Adapter\Memory;

class SecurityComponent extends Component
{

  /*
    Generates a new token given the user
    Also regenerates a token if there is an existing one for the user (basically
    invalidates the old token and creates a new one)

    return false on failure / return token value (string) on success
   */
  public function generate(&$user)
  {
    if(!is_object($user))
    {
      return false;
    }
    //delete all existing tokens
    $user->tokens->delete();

    $secret = openssl_random_pseudo_bytes(32);

    //generate new token
    $token = new Tokens();
    $token->users_id = $user->id;
    $token->secret_key = $this->crypt->encryptBase64($secret);
    $token->created = date('Y-m-d H:i:s');
    $token->hmac = hash_hmac('sha256', strtolower($user->username) . '|' . $token->created, $secret, false);

    //save token information
    if($token->save())
    {
      //return encrypted token
      return $this->crypt->encryptBase64($this->crypt->encrypt($user->username . '|' . $token->created . '|' . $token->hmac));
    }
    return false;
  }

  /*
    Validate a given token (check timestamps and validate integrity)

    return true on success / false on fail
   */
  public function validate($token)
  {
    if(!empty($token))
    {
      $decrypted = $this->crypt->decrypt($this->crypt->decryptBase64($token));
      $pieces = explode('|', $decrypted);
      if(count($pieces) === 3)
      {
        $user = Users::findFirstByUsername(strtolower($pieces[0]));
        if(is_object($user))
        {
          $stored_tokens = $user->getTokens(array('order' => 'created desc'));
          $stored_token = (count($stored_tokens) > 0 ? $stored_tokens[0] : null);
          if(!is_null($stored_token))
          {
            $vhash = hash_hmac('sha256', strtolower($user->username) . '|' . $stored_token->created, $this->crypt->decryptBase64($stored_token->secret_key), false);
            $received = trim($pieces[2]);
            if(strlen($vhash) === strlen($received))
            {
              $same = true;
              for($i = 0; $i < strlen($vhash); $i++)
              {
                if($vhash[$i] !== $received[$i])
                {
                  $same = false;
                  break;
                }
              }
              if($same)
              {
                return $user;
              }
            }
          }
        }
      }
    }
    return false;
  }

  public function access($role, $controller, $action)
  {
    return $this->acl()->isAllowed(strtolower($role), strtolower($controller), strtolower($action)) == Acl::ALLOW;
  }

  /**
   * Access Control List
   */
  public function acl()
  {
    $acl = new Memory();
    $acl->setDefaultAction(Acl::DENY);
    $roles = array('guests', 'users');
    $resources = array(
      'users' => array(
        'auth' => array('guests'),
        'create' => array('guests'),
        'logout' => array('users')
      ),
      'posts' => array(
        'create' => array('users'),
        'mine' => array('users'),
        'all' => array('users')
      )
    );
    foreach($roles as $role)
    {
      $acl->addRole(new Role($role));
    }
    foreach($resources as $resource => $actions)
    {
      $acl->addResource(new Resource($resource), array_keys($actions));
      foreach($actions as $action => $roles)
      {
        foreach($roles as $role)
        {
          $acl->allow($role, $resource, $action);
        }
      }
    }
    return $acl;
  }

}
