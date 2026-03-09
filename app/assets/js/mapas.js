
/* Mapa  Cajas de Compensación */

function initMap() {
    var map;
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        mapTypeId: 'roadmap',
		mapTypeControl: false,
		fullscreenControl: false,
		streetViewControl: false
    };
                    
    // Display a map on the web page
    map = new google.maps.Map(document.getElementById("mapCanvas"), mapOptions);
    map.setTilt(50);
        
    // Multiple markers location, latitude, and longitude
    var markers = [
        ['COMFACUNDI', 4.639987, -74.0647176],
        ['Indufamiliar', 4.62963, -74.066925],
        ['Caja de Compensación Familiar Aseguradores', 4.652199, -74.059127],
		['Caja de Compensación Familiar Asofamilias', 4.635536, -74.066121],
		['Comfenalco', 4.661344 , -74.058971],
		['Asocajas', 4.6776682 , -74.0464391],
		['Compensar', 4.6598114 , -74.0988296],
		['Colsubsidio', 4.6355217 , -74.1154541],
		['Comcaja', 4.6804159 , -74.0470292],
		['CAFAM', 4.661516 , -74.05598]
		
    ];
                        
    // Info window content
    var infoWindowContent = [
        ['<div class="info_content">' +
        '<h3>COMFACUNDI</h3>' +
        '<p>Cl. 53 #10-39, Bogotá<br>comfacundi.com.co</p>' + '</div>'],
		
        ['<div class="info_content">' +
        '<h3>Indufamiliar</h3>' +
        '<p>Cra. 13 #41-69, Bogotá</p>' +
        '</div>'],
		
        ['<div class="info_content">' +
        '<h3>Caja de Compensación Familiar Aseguradores</h3>' +
        '<p>Cl. 69 #25, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Caja de Compensación Familiar Asofamilias</h3>' +
        '<p>Cra. 13 #48-47, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Comfenalco</h3>' +
        '<p>Calle 75 #37, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Asocajas</h3>' +
        '<p>Cl. 94 #11-30, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Compensar</h3>' +
        '<p>Ak 68 # 49A-47, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Colsubsidio</h3>' +
        '<p>Dg. 86a #110-58, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>Comcaja</h3>' +
        '<p>Cra. 12 #96-23, Bogotá</p>' +
        '</div>'],
		
		['<div class="info_content">' +
        '<h3>CAFAM</h3>' +
        '<p>Cra. 11 ##76-53, Bogotá, Cundinamarca</p>' +
        '</div>']
    ];
        
    // Add multiple markers to map
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    
    // Place each marker on the map  
    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[i][0]
        });
        
        // Add info window to marker    
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i][0]);
                infoWindow.open(map, marker);
            }
        })(marker, i));

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);
    }

    // Set zoom level
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(13);
        google.maps.event.removeListener(boundsListener);
    });
    
}
// Load initialize function
google.maps.event.addDomListener(window, 'load', initMap);

/* Mapa Ubicación SuperSubsidio */

var map2;
  	 function mapa2() {
        map = new google.maps.Map(document.getElementById('mapCanvas2'), {
		  center: {lat: 4.6575505, lng: -74.1071942},
          zoom: 18,
		  mapTypeControl: false,
		  fullscreenControl: false,
		  streetViewControl: false
        });
		 
    var image = 'http://localhost/supersubsidio/assets/img/favicon.png';
  	var beachMarker = new google.maps.Marker({
    position: {lat: 4.6575505, lng: -74.1071942},
    map: map,
    icon: image
  });
      }
	
	google.maps.event.addDomListener(window, 'load', mapa2);
