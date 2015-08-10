
/**
 * Used for anything that modifies the user in some way
 */
module.controller('UserController', function($scope, UserService) {

  /**
   * Holds all of the information for the forms
   */
  $scope.form = {
    login: {
      username : '',
      password : ''
    },
    register: {
      username : '',
      password : '',
      vpassword : ''
    }
  };

  /**
   * Logs a user in (authentication)
   */
  $scope.login = function () {
    UserService.authenticate([ $scope.form.login.username, $scope.form.login.password ], function(data) {
      storage.setToken(data.result);
      app.navigation.resetToPage('posts.html');
    });
  };

  /**
   * Registers a new user (creation)
   */
  $scope.register = function () {
    UserService.create([ $scope.form.register.username, $scope.form.register.password, $scope.form.register.vpassword ], function(data) {
      storage.setToken(data.result);
      app.navigation.resetToPage('posts.html');
    });
  };

});
