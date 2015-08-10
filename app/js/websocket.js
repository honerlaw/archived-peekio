
/**
 * Handles all of the websocket related stuff
 * Creates a small simple API to be used for the rest of the app
 */

var ws = {
  socket: undefined,
  callbacks: {},
  connect: function() {
    ws.socket = new WebSocket("ws://peekio.derekhonerlaw.com:8080");
    ws.socket.onopen = ws.opened;
    ws.socket.onmessage = ws.received;
    ws.socket.onclose = ws.closed;
    ws.socket.onerror = ws.error;
  },
  send: function(event, params, success, error) {
    if(ws.socket !== undefined && ws.socket.readyState !== WebSocket.OPEN) {
      ws.connect();
    }
    var data = {
      event : event,
      params : params
    };
    if(storage.get('token') !== undefined && storage.get('token') !== 'undefined' && storage.get('token') !== null) {
      data.token = storage.get('token');
    }
    ws.socket.send(JSON.stringify(data));
    ws.callbacks[event] = function(data) {
      if(data.status === 'success') {
        if(success) success(data);
      } else if(data.status === 'error') {
        if(data.result) {
          var message = (data.result.constructor === Array ? data.result[0] : data.result);
          ons.notification.alert({ message: (message ? message : 'unknown error.') });
        }
        if(error) error(data);
      }
    };
  },
  opened: function() {
    storage.load();
    if(storage.get('token')) {
      app.navigation.resetToPage('posts.html');
    }
    console.log('opened');
  },
  received: function(event) {
    var data = JSON.parse(event.data);
    console.log(data);
    if(data.event === 'error_token') {
      app.navigation.resetToPage('splash.html');
      //basically passes undefined token which removes the token
      storage.setToken();
      app.menu.close();
    } else {
      if(ws.callbacks[data.event]) {
        ws.callbacks[data.event](data);
        ws.callbacks[data.event] = undefined;
      }
    }
  },
  closed: function() {
    ws.connect();
    console.log('close');
  },
  error: function() {
    ws.socket.close();
    console.log('error');
  }
};
