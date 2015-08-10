<?php

use \Phalcon\Mvc\Model;
use \Phalcon\Mvc\Model\Validator\Email;
use \Phalcon\Mvc\Model\Validator\Uniqueness;
use \Phalcon\Mvc\Model\Validator\StringLength;
use \Phalcon\Mvc\Model\Behavior\Timestampable;

class Users extends Model
{
  /*
    CREATE TABLE users (
      id INT NOT NULL AUTO_INCREMENT,
      username VARCHAR(255) NOT NULL,
      hash VARCHAR(255) NOT NULL,
      email TEXT,
      created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) Engine=InnoDB;
  */

  /**
   * @var integer
   */
  public $id;

  /**
   * @var varchar(255)
   */
  public $username;

  /**
   * @var varchar(255)
   */
  public $hash;

  /**
   * @var text
   */
  public $email;

  /**
   * @var timestamp
   */
  public $created;

  /**
   * Format username to use findFirstByUsername
   */
  public function beforeValidationOnCreate()
  {
    $this->username = strtolower($this->username);
    return $this->validation();
  }

  /**
   * Hash password after we verify the minimum length
   */
  public function afterValidationOnCreate()
  {
    $this->hash = $this->getDI()->getSecurity()->hash($this->hash);
  }

  /**
   * Define behaviors / relationships
   */
  public function initialize()
  {
    $this->addBehavior(new Timestampable(array(
      'beforeValidationOnCreate' => array(
        'field' => 'created',
        'format' => 'Y-m-d H:i:s'
      )
    )));
    $this->hasMany('id', 'Tokens', 'users_id', array(
      'alias' => 'tokens'
    ));
    $this->hasMany('id', 'Posts', 'users_id', array(
      'alias' => 'posts'
    ));
  }

  /**
   * Validate the model
   */
  public function validation()
  {
    $this->validate(new Email(array(
      'field' => 'email',
      'allowEmpty' => true,
      'message' => 'invalid email address.'
    )));
    $this->validate(new Uniqueness(array(
      'field' => 'username',
      'message' => 'username is already taken.'
    )));
    $this->validate(new StringLength(array(
      'field' => 'hash',
      'min' => '6',
      'messageMinimum' => 'password must be at least 6 characters long.'
    )));
    if ($this->validationHasFailed() == true)
    {
      return false;
    }
  }

  /**
   * Define model / database mapping
   */
  public function columnMap()
  {
    return array(
      'id' => 'id',
      'username' => 'username',
      'hash' => 'hash',
      'email' => 'email',
      'created' => 'created'
    );
  }

}
