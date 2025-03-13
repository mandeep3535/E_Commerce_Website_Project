<?php

$servername = "localhost"; 
$username = "root"; 
$password = "";  // Empty password since 'root' has no password
$database = "mv_data"; 
$port = 3307; // Specify the correct port



// Create connection

$conn = new mysqli($servername, $username, $password, $database,$port);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

} else {

}

?>
