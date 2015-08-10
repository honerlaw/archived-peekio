
/**
 * Static utility object used for various functions
 */
 

var util = {
  geolocation : function (success, error) {
    navigator.geolocation.getCurrentPosition(function(pos) {
      if(success) success(pos);
    }, function(err) {
      if(error) error(err);
      ons.notification.alert({ message: err.message });
    }, {
      enableHighAccuracy: true,
      timeout: 5000,
      maximumAge: 0
    });
  }
};
