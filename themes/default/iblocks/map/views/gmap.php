<div id="map" style="height:200px;"></div>
<script type="text/javascript">
    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.704937, lng: -74.019157},
            zoom: 15
        });
        marker = new google.maps.Marker({
            icon: 'https://mapbuildr.com/assets/img/markers/default.png',
            position: new google.maps.LatLng(40.704270866046265, -74.01835717553251),
            map: map,
            title: 'We here'
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaj4zCm8tA2qAeqPnQUoQ7c32j_T8GcF0&callback=initMap">
</script>
