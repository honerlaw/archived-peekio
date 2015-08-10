<?php

use \Phalcon\Mvc\User\Component;

class UsersComponent extends Component
{

  /*
    Authenticates an existing user
   */
  public function auth($username, $password)
  {
    $user = Users::findFirstByUsername(strtolower($username));
    if(is_object($user))
    {
      if($this->security->checkHash($password, $user->hash))
      {
        return array(true, $this->secure->generate($user));
      }
    }
    return array(false, 'invalid username or password.');
  }

  /*
    Create a new user
   */
  public function create($username, $password, $vpassword)
  {
    if(empty($password))
    {
      return array(false, 'password is required.');
    }
    if($password !== $vpassword)
    {
      return array(false, 'passwords do not match.');
    }
    $user = new Users();
    $user->username = $username;
    $user->hash = $password;
    if($user->save())
    {
      return array(true, $this->secure->generate($user));
    }
    else
    {
      $errors = array();
      foreach($user->getMessages() as $message)
      {
        $errors[] = (string) $message;
      }
      return array(false, $errors);
    }
  }

  /**
   * Destroys all valid tokens for the user
   */
  public function logout($user)
  {
    $user->tokens->delete();
    return array(true);
  }

}
