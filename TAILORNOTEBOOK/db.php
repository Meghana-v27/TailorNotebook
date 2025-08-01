<?php
$host = "localhost";        // Replace with your PostgreSQL host
$port = "5432";           // Default PostgreSQL port
$dbname = "tailors_notebook"; // Replace with your PostgreSQL database name
$user = "postgres";       // Replace with your PostgreSQL username (often 'postgres')
$password = "";           // Replace with your PostgreSQL password

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

$conn = pg_connect($connection_string);

if (!$conn) {
    die("Database connection failed in db.php: " . pg_last_error());
}
?>
