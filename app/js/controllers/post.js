
/**
 * Used to handle all stuff that deals with posts
 */
module.controller('PostController', function($scope, PostService) {

  /**
   * Get all posts
   */
  PostService.mine($scope);
  PostService.all($scope);

  /**
   * Store post information that is used for the view
   */
  $scope.posts = {
    current: PostService.getCurrent(),
    mine : [],
    all : []
  };

  /**
   * Store information that is used in the create post form
   */
  $scope.form = {
    disabled : false,
    content : ''
  };

  /**
   * Used for the dialog that can create a new post (basically a pop up form)
   */
  $scope.dialog = undefined;

  /**
   * Opens the create post form
   */
  $scope.openCreate = function () {
    if(!$scope.dialog) {
      ons.createDialog('create-post-dialog.html').then(function(dialog) {
        $scope.dialog = dialog;
        $scope.dialog.show();
      });
    } else {
      $scope.dialog.show();
    }
  };

  /**
   * Sends request to create a new post
   */
  $scope.create = function() {
    $scope.form.disabled = true;
    PostService.create($scope);
  };

  /**
   * View a specific post and its related comments
   */
  $scope.viewPost = function (index, type) {
    PostService.setCurrent(index, type);
    app.navigation.pushPage('view-post.html');
  };

});
