
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cab Booking with Location Tracking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
            margin: auto;
            max-width: 400px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cab Booking</h2>
        <form action="cab2.php" method="POST" id="bookingForm">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="pickup">Pickup Location:</label>
            <input type="text" id="pickup" name="pickup" required>
            
            <label for="drop">Drop Location:</label>
            <input type="text" id="drop" name="drop" required>

            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <button type="button" onclick="getLocation()">Track Location</button>
            <button type="submit">Book Now</button>
        </form>
        <p id="status"></p>
    </div>

    <script>
        function getLocation() {
            const status = document.getElementById('status');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        status.textContent = "Location tracked successfully!";
                    },
                    (error) => {
                        status.textContent = "Unable to retrieve location.";
                    }
                );
            } else {
                status.textContent = "Geolocation is not supported by this browser.";
            }
        }
    </script>
</body>
</html>
<?php
// Database connection details
$server = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$database = "workpauseDB"; // Replace with your database name

// Create a connection to the database
$conn = new mysqli($server, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $pickup = htmlspecialchars($_POST['pickup']);
    $drop = htmlspecialchars($_POST['drop']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);

    // SQL query to insert the booking details into the database
    $sql = "INSERT INTO cab_bookings (name, pickup_location, drop_location, latitude, longitude)
            VALUES ('$name', '$pickup', '$drop', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2>Booking Successful!</h2>";
        echo "<p>Your booking has been stored in our system.</p>";
        echo "<p>Tracked Location: Latitude $latitude, Longitude $longitude</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>