<?php

use \Phalcon\Mvc\User\Component;
use \Phalcon\Mvc\Model\Query;

class PostsComponent extends Component
{

  /**
   * Creates a new post
   */
  public function create($latitude, $longitude, $content, $user)
  {
    $post = new Posts();
    $post->users_id = $user->id;
    $post->latitude = $latitude;
    $post->longitude = $longitude;
    $post->content = $content;
    if($post->save())
    {
      return array(true, $post->toArray());
    }
    else
    {
      $errors = array();
      foreach($post->getMessages() as $message)
      {
        $errors[] = (string) $message;
      }
      return array(false, $errors);
    }
  }

  /**
   * Return a list of all the posts that the user has created
   */
  public function mine($user)
  {
    return array(true, $user->posts->toArray());
  }

  /**
   * Return a list of all posts within a given radius of the lat / long
   */
  public function all($latitude, $longitude, $radius, $user)
  {
    $query = new Query('select Posts.id, Posts.users_id, Posts.latitude, Posts.longitude, Posts.content, Posts.created, ( 3959 * acos( cos( radians(:lat:) ) * cos( radians( Posts.latitude ) ) * cos( radians(Posts.longitude) - radians(:lng:)) + sin(radians(:lat:)) * sin( radians(Posts.latitude)))) AS miles from Posts having miles <= :radius:', $this->getDI());
    $posts = $query->execute(array(
      'lat' => $latitude,
      'lng' => $longitude,
      'radius' => $radius
    ));
    return array(true, $posts->toArray());
  }

}
