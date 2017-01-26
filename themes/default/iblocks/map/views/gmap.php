<div id="map" style="height:200px;"></div>
<script type="text/javascript">
    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.704937, lng: -74.019157},
            zoom: 15
        });
        marker = new google.maps.Marker({
            icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QUEzOEZBRjg2NjQ5MTFFNjhBOUFEMzM1RkUxNzVBMjAiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QUEzOEZBRjk2NjQ5MTFFNjhBOUFEMzM1RkUxNzVBMjAiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBQTM4RkFGNjY2NDkxMUU2OEE5QUQzMzVGRTE3NUEyMCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBQTM4RkFGNzY2NDkxMUU2OEE5QUQzMzVGRTE3NUEyMCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PhD0obgAAAI+SURBVHjarJVPiBJRHMffzM6fJYYCEcxKNqy0QxO0SFJHwRX02Oala4elCGILBEEWii4euoWIC10ivOx1lS5CUqfoWIQEirYQGquuCNYy9n0yLsPbpzuu/uAzDO/3e9/fe7957zcCmWxXQBjcAdfBBaCZvi7YA9/AZ/AB1IlNuw3emyJDm/wB2+DGNGEZvAD9GYRZ2uApEMaigkX8LXjAJN3Wdf1TJBIpB4PBlsvlOhwOh6TRaEjlcvl8sVhcq1arNxH3kJn3Bjwxk47sFbsah8ORzmQyzn6/r0JU4tFqtdRkMrkiy3KOs5vNsfgtcGB1apqWLpVKGkSOxNl6WhKp2WzWiSE2yW9wlcZuMY79RCLhxEQZKDxxJgmNUaLRKF1ok9F6JOKhsxNDodCB+WrYOHWjmHA4XOP4dJrgLzva6/XEo1MgCIeTlK2+Tqcjc0JG2htsiWKxmG6WSLJRIqnb7Sp+v3+dUyI6Ri6CKuPIpVKpFcMw1JMStNttNR6Pr3I+8hdwbhz7nHPMctjJ3UKhsNxsNo8lqVQqUj6f1wKBwDpHnBKzXjQVfDTbxDHz+XyGx+P5JYriHnYlDAaDS/V63V2r1YQJm3sNnvF6UGeONjFmF5yZVNaNOcW/AtdJ5zp9SvEf4Jqddr0E8jOK07awSmaws+aPxI44/W5r5BR2GfycJm7e5PtkDqMna39KksdkAXYPK/3HiiuK8pIs0Dat4l6vd4cs2txu9zsqjtbw3fzFLtzo7UwA5yyT/gswAFFzbzCLAwb+AAAAAElFTkSuQmCC',
            position: new google.maps.LatLng(40.704270866046265, -74.01835717553251),
            map: map,
            title: 'We here'
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaj4zCm8tA2qAeqPnQUoQ7c32j_T8GcF0&callback=initMap">
</script>
