
/**
 * Handles all information related to posts (include from my posts / all posts etc)
 */
module.service('PostService', function() {

  var self = this;

  var posts = {
    current : undefined,
    all : [],
    mine : []
  };

  /**
   * Creates a new post
   */
  self.create = function ($scope) {
    util.geolocation(function(pos) {
      ws.send('posts/create', [ pos.coords.latitude, pos.coords.longitude, $scope.form.content ], function(data) {
        dialog.create.post.hide();
        $scope.form.disabled = false;
        $scope.$apply();
      }, function() {
        $scope.form.disabled = false;
        $scope.$apply();
      });
    }, function(err) {
      $scope.form.disabled = false;
      $scope.$apply();
    });
  };

  /**
   * Returns a list of all of the current users posts
   */
  self.mine = function ($scope) {
    ws.send('posts/mine', [], function(data) {
      posts.mine = data.result;
      $scope.posts.mine = data.result;
      $scope.$apply();
    });
  };

  /**
   * Returns a list of all of the posts within a specified radius (default 25 miles)
   */
  self.all = function ($scope) {
    util.geolocation(function(pos) {
      ws.send('posts/all', [ pos.coords.latitude, pos.coords.longitude, storage.get('post_radius') ], function(data) {
        posts.all = data.result;
        $scope.posts.all = data.result;
        $scope.$apply();
      });
    });
  }

  /**
   * Set the current post to view and update the scope
   */
  self.setCurrent = function (index, property) {
    posts.current = posts[property][index];
  };

  /**
   * Return the current post to view
   */
  self.getCurrent = function () {
    return posts.current;
  };

});
