(function ($, Drupal) {


    Drupal.behaviors.builder_map = {
        attach: function (context, settings) {
            builder_map_init(context);
        }
    };


    function builder_map_init(context) {
        $('.builder-google-map', context).each(function () {
            var $map = $(this);
            var $this = $(this);
            var $map_settings = $.parseJSON($this.attr('data-settings'));
            var $addresses = $map_settings.address;

            $map.googleMap($addresses, {
                zoom: $map_settings.zoom,
                disableDefaultUI: true,
                scrollwheel: false,
                navigationControl: false,
                mapTypeControl: false,
                scaleControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }, $map_settings);


        });
    }

    $.fn.googleMap = function (addresses, options, $map_settings) {
        var defaults = {
            lat: 44.081996,
            long: -123.0286928,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [{
                "featureType": "water",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#d3d3d3"}]
            }, {
                "featureType": "transit",
                "stylers": [{"color": "#808080"}, {"visibility": "off"}]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{"visibility": "on"}, {"color": "#b3b3b3"}]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}]
            }, {
                "featureType": "road.local",
                "elementType": "geometry.fill",
                "stylers": [{"visibility": "on"}, {"color": "#ffffff"}, {"weight": 1.8}]
            }, {
                "featureType": "road.local",
                "elementType": "geometry.stroke",
                "stylers": [{"color": "#d7d7d7"}]
            }, {
                "featureType": "poi",
                "elementType": "geometry.fill",
                "stylers": [{"visibility": "on"}, {"color": "#ebebeb"}]
            }, {
                "featureType": "administrative",
                "elementType": "geometry",
                "stylers": [{"color": "#a7a7a7"}]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}]
            }, {
                "featureType": "landscape",
                "elementType": "geometry.fill",
                "stylers": [{"visibility": "on"}, {"color": "#efefef"}]
            }, {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{"color": "#696969"}]
            }, {
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [{"visibility": "on"}, {"color": "#737373"}]
            }, {
                "featureType": "poi",
                "elementType": "labels.icon",
                "stylers": [{"visibility": "off"}]
            }, {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [{"visibility": "off"}]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry.stroke",
                "stylers": [{"color": "#d6d6d6"}]
            }, {
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [{"visibility": "off"}]
            }, {}, {
                "featureType": "poi",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#dadada"}]
            }]
        };

        options = $.extend(defaults, options || {});

        var center = new google.maps.LatLng(options.lat, options.long);
        var map = new google.maps.Map(this.get(0), $.extend(options, {center: center}));

        var geocoder = new google.maps.Geocoder();
        var infowindow = new google.maps.InfoWindow();
        var marker;
        var $icon = $map_settings.icon;

        $.each(addresses, function ($k, address) {
            if (address !== '') {
                geocoder.geocode({address: address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK && results.length) {
                        if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                            map.setCenter(results[0].geometry.location);
                            marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map,
                                icon: $icon,
                            });


                            google.maps.event.addListener(marker, 'click', (function (marker, $k) {
                                return function () {
                                    infowindow.setContent(address);
                                    infowindow.open(map, marker);
                                }
                            })(marker, $k));

                        }
                    }
                });
            }
        })


    };


})(jQuery, Drupal);