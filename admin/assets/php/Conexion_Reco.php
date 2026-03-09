<?php 

/* conexion a base de datos */

$servername = "localhost";
$database = "electronika";
$username = "root";
$password = "";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
/*if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}else{

    echo ("Connected successfully");
}*/

/* Fin conexion a base de datos */

?>