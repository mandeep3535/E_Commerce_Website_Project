<?php

$servername = "localhost"; 
$username = "root"; 
$password = "";  // Empty password since 'root' has no password
$database = "mv_data"; 
$port = 3307; // Specify the correct port

/* Varun's server */
/*
$servername = "localhost"; 
$username = "vgarg28"; 
$password = "vgarg28";  // Empty password since 'root' has no password
$database = "vgarg28"; 
$port = 3306; // Specify the correct port
*/

/* Mandeep's server */
/*
$servername = "localhost"; 
$username = "msingh78"; 
$password = "msingh78";  // Empty password since 'root' has no password
$database = "msingh78"; 
$port = 3306; // Specify the correct port
*/


// Create connection

$conn = new mysqli($servername, $username, $password, $database,$port);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

} else {

}

?>
