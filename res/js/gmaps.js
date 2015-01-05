	

    function codeAddress(address) {
        
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                document.getElementById('latitude').value = results[0].geometry.location.lat();
                document.getElementById('longitude').value = results[0].geometry.location.lng();
            } else {
                alert('Ocorreu um erro em localizar o endereço: ' + status);
            }
        });
    }

