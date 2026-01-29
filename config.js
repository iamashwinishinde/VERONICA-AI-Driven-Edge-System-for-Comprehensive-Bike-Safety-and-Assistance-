// Google Maps API Configuration
const GOOGLE_MAPS_CONFIG = {
    apiKey: 'AIzaSyCCHQ1neszJqcpGyyffg8XiS3pGt84LGUw',
    libraries: ['places', 'geometry', 'drawing'],
    region: 'IN',
    language: 'en'
};

// Map Style Configuration
const MAP_STYLES = [
    {
        "featureType": "all",
        "elementType": "geometry",
        "stylers": [{"color": "#242f3e"}]
    },
    {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [{"color": "#38414e"}]
    },
    {
        "featureType": "road",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#212a37"}]
    },
    {
        "featureType": "road",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#9ca5b3"}]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [{"color": "#746855"}]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#1f2835"}]
    },
    {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [{"color": "#17263c"}]
    }
]; 