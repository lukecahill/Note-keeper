var mapLocation = {};

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

            var map = new google.maps.Map(document.getElementById('map_location'), {
                zoom: 4,
                center: mapLocation
            });

            var marker = new google.maps.Marker({
                position: mapLocation,
                map: map
            });
        });
    } else {
        console.warn('Geolocation is not available');
    }
}