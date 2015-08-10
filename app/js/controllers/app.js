
/**
 * Generic controller with control over the entire application
 * Only used for simple tasks (e.g. adding actions to the menu or other global actions)
 */
module.controller('AppController', function($scope, UserService) {

  /**
   * When everything is ready to go we attempt to connect to the server
   */
  ons.ready(function() {
    //loading etc occurs after we connect
    ws.connect();
  });

  /**
   * Logs a user out (used in the menu)
   */
  $scope.logout = function() {
    UserService.logout();
  };

  /**
   * Reset to the list with all pages
   */
  $scope.all = function () {
    if(app.navigation.getCurrentPage().page !== 'posts.html') {
      app.navigation.resetToPage('posts.html', { animation : 'slide' });
    }
    app.menu.close();
  };

  /**
   * Open list of my posts
   */
  $scope.mine = function () {
    if(app.navigation.getCurrentPage().page !== 'my-posts.html') {
      app.navigation.pushPage('my-posts.html', { animation : 'lift' });
    }
    app.menu.close();
  };

});
