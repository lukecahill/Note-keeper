var mapLocation = {};
var map;
var center;

function initMap() {
    if('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
			console.log(position.coords.latitude, position.coords.longitude);
			var longitude = position.coords.longitude;
			var latitude = position.coords.latitude;

            mapLocation = {
                lat: latitude,
                lng: longitude
            };

            map = new google.maps.Map(document.getElementById('map_location'), {
                zoom: 4,
                center: mapLocation,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            var marker = new google.maps.Marker({
                position: mapLocation,
                map: map
            });

            center = map.getCenter();
        });
    } else {
        console.warn('Geolocation is not available');
    }
}

(function() {
	$('#maps-header').on('click', function() {
        google.maps.event.trigger(map, "resize");
        map.setCenter(center);
	});
})();