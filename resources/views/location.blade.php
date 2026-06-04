<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location App</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #1a73e8;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover { background: #1557b0; }
        button:disabled { background: #aaa; cursor: not-allowed; }

        .result-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: none;
        }

        .result-card h3 {
            color: #1a73e8;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .result-item {
            background: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .result-item .label {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }

        .result-item .value {
            font-size: 22px;
            font-weight: bold;
            color: #1a73e8;
        }

        #map {
            width: 100%;
            height: 450px;
            border-radius: 12px;
            display: none;
        }

        .error {
            background: #ffeaea;
            color: #d32f2f;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }

        .loading {
            text-align: center;
            color: #1a73e8;
            padding: 10px;
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📍 Location Distance Finder</h1>

    <div class="card">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <label>🟢 Pickup Location</label>
        <input type="text" id="pickup" placeholder="Enter pickup location...">

        <label>🔴 Drop Location</label>
        <input type="text" id="drop" placeholder="Enter drop location...">

        <button id="calculateBtn" onclick="calculateDistance()">
            Calculate Distance & Show Route
        </button>

        <div class="error" id="errorMsg"></div>
        <div class="loading" id="loading">⏳ Calculating...</div>
    </div>

    <!-- Results -->
    <div class="result-card" id="resultCard">
        <h3>📊 Trip Details</h3>
        <div class="result-grid">
            <div class="result-item">
                <div class="label">Total Distance</div>
                <div class="value" id="distanceVal">-</div>
            </div>
            <div class="result-item">
                <div class="label">Estimated Time</div>
                <div class="value" id="durationVal">-</div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="card" id="mapCard" style="display:none; padding: 15px;">
        <h3 style="color:#1a73e8; margin-bottom:15px;">🗺️ Route Map</h3>
        <div id="map"></div>
    </div>
</div>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&libraries=places&callback=initAutocomplete" async defer></script>

<script>
    var map, directionsService, directionsRenderer;

    function initAutocomplete() {
        // Pickup autocomplete
        var pickupInput = document.getElementById('pickup');
        var pickupAuto  = new google.maps.places.Autocomplete(pickupInput);

        // Drop autocomplete
        var dropInput = document.getElementById('drop');
        var dropAuto  = new google.maps.places.Autocomplete(dropInput);

        // Init map
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: { lat: 20.5937, lng: 78.9629 } // India center
        });

        directionsService  = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);
    }

    function calculateDistance() {
        var pickup = document.getElementById('pickup').value;
        var drop   = document.getElementById('drop').value;
        var btn    = document.getElementById('calculateBtn');
        var error  = document.getElementById('errorMsg');

        // Reset
        error.style.display  = 'none';
        error.innerText      = '';

        if (!pickup || !drop) {
            error.innerText      = '⚠️ Please enter both pickup and drop locations.';
            error.style.display  = 'block';
            return;
        }

        // Show loading
        btn.disabled = true;
        btn.innerText = 'Calculating...';
        document.getElementById('loading').style.display = 'block';

        // AJAX call to Laravel backend
        fetch('/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pickup: pickup, drop: drop })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerText = 'Calculate Distance & Show Route';
            document.getElementById('loading').style.display = 'none';

            if (data.success) {
                // Show results
                document.getElementById('distanceVal').innerText = data.distance;
                document.getElementById('durationVal').innerText = data.duration;
                document.getElementById('resultCard').style.display = 'block';

                // Show map
                document.getElementById('mapCard').style.display = 'block';
                document.getElementById('map').style.display      = 'block';

                // Draw route on map
                directionsService.route({
                    origin:      data.pickup,
                    destination: data.drop,
                    travelMode:  google.maps.TravelMode.DRIVING
                }, function(result, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    }
                });

            } else {
                error.innerText     = '❌ ' + data.message;
                error.style.display = 'block';
            }
        })
        .catch(err => {
            btn.disabled  = false;
            btn.innerText = 'Calculate Distance & Show Route';
            document.getElementById('loading').style.display = 'none';
            error.innerText     = '❌ Something went wrong. Please try again.';
            error.style.display = 'block';
        });
    }
</script>
</body>
</html>