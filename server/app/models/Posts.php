<?php

use \Phalcon\Mvc\Model;
use \Phalcon\Mvc\Model\Validator\PresenceOf;
use \Phalcon\Mvc\Model\Validator\StringLength;

class Posts extends Model
{

  /*
    CREATE TABLE posts (
      id INT NOT NULL AUTO_INCREMENT,
      users_id INT NOT NULL,
      latitude FLOAT(10, 6) NOT NULL,
      longitude FLOAT(10, 6) NOT NULL,
      content TEXT NOT NULL,
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
   * @var float
   */
  public $latitude;

  /**
   * @var float
   */
  public $longitude;

  /**
   * @var content
   */
  public $content;

  /**
   * @var timestamp
   */
  public $created;

  public function beforeValidationOnCreate()
  {
    $this->created = date('Y-m-d H:i:s');
    return $this->validation();
  }

  /**
   * Set behaviors / relationships
   */
  public function initalize()
  {
    $this->belongsTo('users_id', 'Users', 'id', array(
      'alias' => 'user',
      'foreignKey' => array(
        'message' => 'invalid user.'
      )
    ));
  }

  /**
   * Validate the model
   */
  public function validation()
  {
    $this->validate(new PresenceOf(array(
      'field' => 'content',
      'message' => 'post content is requred.'
    )));
    $this->validate(new PresenceOf(array(
      'field' => 'latitude',
      'message' => 'latitude coordinate is requred.'
    )));
    $this->validate(new PresenceOf(array(
      'field' => 'longitude',
      'message' => 'longitude coordinate is requred.'
    )));
    $this->validate(new StringLength(array(
      'field' => 'content',
      'min' => 15,
      'max' => 250,
      'messageMinimum' => 'post content must be at least 15 characters.',
      'messageMaximum' => 'post content can not be more than 250 characters.'
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
      'users_id' => 'users_id',
      'latitude' => 'latitude',
      'longitude' => 'longitude',
      'content' => 'content',
      'created' => 'created'
    );
  }

}
