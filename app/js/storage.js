
/**
 * Handles all storage (basically stuff to make localStorage easier to manage)
 */
var storage = {
  values : {
    token : undefined,
    post_radius : 25,
  },
  setToken: function(token) {
    storage.values.token = token;
    storage.save();
  },
  get: function(property) {
    var value = storage.values[property];
    if(value !== undefined && value !== 'undefined' && value !== null) {
      return value;
    }
    return false;
  },
  setPostRadius: function(radius) {
    storage.post_radius = radius;
    storage.save();
  },
  save: function() {
    for(var property in storage.values) {
      if(storage.values[property] === undefined) {
        localStorage.removeItem(property);
      } else {
        localStorage.setItem(property, storage.values[property]);
      }
    }
  },
  load: function() {
    for(var property in storage.values) {
      var t = localStorage.getItem(property);
      if(t !== undefined && t !== 'undefined' && t !== null) {
        storage.values[property] = t;
      }
    }
  }
};
