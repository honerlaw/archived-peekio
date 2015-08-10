<?php

use \Phalcon\Mvc\Model;
use \Phalcon\Mvc\Model\Behavior\Timestampable;

class Tokens extends Model
{

  /*
    CREATE TABLE tokens (
      id INT NOT NULL AUTO_INCREMENT,
      users_id INT NOT NULL,
      secret_key TEXT NOT NULL,
      hmac TEXT NOT NULL,
      created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE
    ) Engine=InnoDB;
  */

  /**
   * @var integer
   */
  public $id;

  /**
   * @var integer
   */
  public $users_id;

  /**
   * @var text
   */
  public $secret_key;

  /**
   * @var text
   */
  public $hmac;

  /**
   * @var timestamp
   */
  public $created;

  /**
   * Set behaviors / relationships
   */
  public function initalize()
  {
    $this->addBehavior(new Timestampable(array(
      'beforeValidationOnCreate' => array(
        'field' => 'created',
        'format' => 'Y-m-d H:i:s'
      )
    )));
    $this->belongsTo('users_id', 'Users', 'id', array(
      'alias' => 'user',
      'foreignKey' => array(
        'message' => 'invalid user.'
      )
    ));
  }

  /**
   * Define model / database mapping
   */
  public function columnMap()
  {
    return array(
      'id' => 'id',
      'users_id' => 'users_id',
      'secret_key' => 'secret_key',
      'hmac' => 'hmac',
      'created' => 'created'
    );
  }

}
