
/**
 * Handles all information related to users
 */
module.service('UserService', function() {

  /**
   * Authenticates an existing user
   */
  this.authenticate = function (params, success, error) {
    ws.send('users/auth', params, success, error);
  };

  /**
   * Registers a new user
   */
  this.create = function (params, success, error) {
    ws.send('users/create', params, success, error);
  };

  /**
   * Destroy tokens server side and resets to splash page
   */
  this.logout = function() {
    ws.send('users/logout', [], function() {
      app.navigation.resetToPage('splash.html');
      storage.setToken();
      app.menu.close();
    });
  };

});
