/*!
 * @package Store Locator.
 * @author: A.A.Treitjak
 * @copyright: 2012 - 2013 BelVG.com
 */

var BelVGStoreLocator = Class.create();

BelVGStoreLocator.prototype = {
    initialize:function (options) {
        this.map = 'map_canvas';
        this.directionsDisplay = null;
        this.directionsService = null;
        this.distanceMatrixService = null;
        this.directions = false;
        this.searchLocation = '';
        this.baseUrl = '';
        this.defaultLat = '';
        this.defaultLng = '';
        this.defaultMessage = '';
        this.showItemLoadPage = '';
        this.zoom = 4;
        this.zoom_marker = 15;
        this.size = 10;
        this.units = 'metric';
        this.markerClusterer = null;
        this.formkey = '';
        this.markers = [];
        this.bounds = null;
        this.currentLocation = '';
        this.infoWindow = '';
        this.infoWindowTemplate = '';
        this.init = false;

        var data = options.evalJSON();

        for(var key in data)
        {
            if (data.hasOwnProperty(key))
            {
                this[key] = data[key];
            }
        }
    },

    load:function () {

        if (this.init) {
            return true;
        }

        this.map = new google.maps.Map($(this.map), {
            center:new google.maps.LatLng(parseFloat(this.defaultLat, 10), parseFloat(this.defaultLng, 10)),
            zoom: parseInt(this.zoom, 10),
            mapTypeId:'roadmap',
            mapTypeControlOptions:{
                style:google.maps.MapTypeControlStyle.DROPDOWN_MENU
            }
        });

        if(this.units == 'metric') {
            this.units = google.maps.UnitSystem.METRIC;
        } else {
            this.units = google.maps.UnitSystem.IMPERIAL;
        }

        this.distanceMatrixService = new google.maps.DistanceMatrixService();

        if(this.directions) {
            this.directionsService = new google.maps.DirectionsService();
            this.directionsDisplay = new google.maps.DirectionsRenderer();
            this.directionsDisplay.setMap(this.map);
        }

        this.infoWindow = new google.maps.InfoWindow();
        this.bounds = new google.maps.LatLngBounds();

        if(this.searchLocation) {
            // Try HTML5 geolocation
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    var message = CurrentStoreLocator._base64decode(CurrentStoreLocator.defaultMessage);

                    var options = {
                        map: CurrentStoreLocator.map,
                        position: pos,
                        content: message
                    };

                    CurrentStoreLocator.map.setCenter(pos);

                    CurrentStoreLocator.updateLocation(CurrentStoreLocator.currentLocation);

                }, function() {
                    CurrentStoreLocator.handleNoGeolocation(true);
                });
            } else {
                // Browser doesn't support Geolocation
                CurrentStoreLocator.handleNoGeolocation(false);

                var latlng = new google.maps.LatLng(parseFloat(this.defaultLat, 10), parseFloat(this.defaultLng, 10));
                var message = this._base64decode(this.defaultMessage);

                var options = {
                    map: this.map,
                    position: latlng,
                    content: message
                };

                this.currentLocation = new google.maps.Marker({
                    position: latlng,
                    map: this.map,
                    title: message
                });

                this.map.setCenter(latlng);
            }
        }

        this.init = true;
    },

    reset:function (elem) {
        elem.getElements().each(function(item){
            item.value = '';
        });

        $('searchdistance').selectedIndex = 0;
    },

    handleNoGeolocation:function (errorFlag) {
        if (errorFlag) {
            var content = 'Error: The Geolocation service failed.';
        } else {
            var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        var latlng = new google.maps.LatLng(parseFloat(this.defaultLat, 10), parseFloat(this.defaultLng, 10));

        var options = {
            map: CurrentStoreLocator.map,
            position: latlng,
            content: content
        };

        CurrentStoreLocator.map.setCenter(options.position);
    },

    clearLocations:function () {
        this.infoWindow.close();

        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null);
        }

        this.markers.length = 0;
    },

    createMarker:function (data) {
        var location = data;
        var latlng = new google.maps.LatLng(parseFloat(location.lat, 10), parseFloat(location.lng, 10));

        var markerData = {};
        markerData.map = this.map;
        markerData.position = latlng;

        if(location.icon !== undefined) {
            markerData.icon = location.icon;
        }

        var marker = new google.maps.Marker(markerData);

        google.maps.event.addListener(marker, 'click', function () {
            CurrentStoreLocator.map.setZoom(CurrentStoreLocator.zoom_marker);
            CurrentStoreLocator.infoWindow.setContent(location.html);
            CurrentStoreLocator.infoWindow.open(this.map, marker);

        });

        this.markers.push(marker);
        this.bounds.extend(latlng);

        //this.map.setCenter(bounds.getCenter());
    },

    selectLocation: function(target) {
        var markerId = target.readAttribute('rel') - 1;
        var marker = this.markers[markerId];

        google.maps.event.trigger(marker, "click");

        this.map.setCenter(marker.getPosition());
    },

    updateLocation: function(marker) {
        new Ajax.Request( CurrentStoreLocator.baseUrl + 'updatelocation/', {
            method:'post',
            parameters: {
                'updateLocation[lat]': marker.getPosition().lat(),
                'updateLocation[lng]': marker.getPosition().lng(),
                'updateLocation[formkey]': CurrentStoreLocator.formkey
            }
        });
    },

    centerMap: function()
    {
        this.map.fitBounds(this.bounds);
    },

    initMarkerClusterer: function() {
        this.markerClusterer = new MarkerClusterer(this.map, this.markers, {
            maxZoom: CurrentStoreLocator.zoom,
            gridSize: CurrentStoreLocator.size
        });
    },

    getGoogleDistance: function(target) {
        var markerId = target.readAttribute('rel') - 1;
        var marker = this.markers[markerId];

        target.update('Calculate...');

        var origin = new google.maps.LatLng(parseFloat(CurrentStoreLocator.defaultLat, 10), parseFloat(CurrentStoreLocator.defaultLng, 10));
        var destination = marker.getPosition();

        CurrentStoreLocator.distanceMatrixService.getDistanceMatrix(
            {
                origins: [origin],
                destinations: [destination],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: CurrentStoreLocator.units,
                avoidHighways: false,
                avoidTolls: false
            }, function(response, status) {
                var results = response.rows[0].elements;

                for (var j = 0; j < results.length; j++) {
                    if (results[j].status == google.maps.DirectionsStatus.OK) {
                        var html = '<b>'+results[j].distance.text+'</b> '+results[j].duration.text;
                        target.replace(html);
                    } else {
                        target.replace('Not possible to calculate');
                    }
                }
            });
    },

    getGoogleDirection:function(target)
    {
        if(!this.directions) {
            return '';
        }

        var markerId = target.readAttribute('rel') - 1;
        var marker = this.markers[markerId];

        target.update('Calculate...');

        var start = new google.maps.LatLng(parseFloat(CurrentStoreLocator.defaultLat, 10), parseFloat(CurrentStoreLocator.defaultLng, 10));
        var end = marker.getPosition();
        var request = {
            origin:start,
            destination:end,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };

        CurrentStoreLocator.directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                CurrentStoreLocator.directionsDisplay.setDirections(response);
                target.update('Show Direction');
            } else {
                target.replace('Not possible to calculate');
            }
        });
    },

    _initLocation: function(location) {
        return location.evalJSON();
    },

    _base64decode: function(input) {
        var keyStr = "ABCDEFGHIJKLMNOP" +
            "QRSTUVWXYZabcdef" +
            "ghijklmnopqrstuv" +
            "wxyz0123456789+/" +
            "=";
        var output = "";
        var chr1, chr2, chr3 = "";
        var enc1, enc2, enc3, enc4 = "";
        var i = 0;

        // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
        var base64test = /[^A-Za-z0-9\+\/\=]/g;
        if (base64test.exec(input)) {
            alert("There were invalid base64 characters in the input text.\n" +
                "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
                "Expect errors in decoding.");
        }
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        do {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";

        } while (i < input.length);

        return unescape(output);
    }
};