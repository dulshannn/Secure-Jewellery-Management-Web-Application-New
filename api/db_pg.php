<?php
// Secure Database Connection for PostgreSQL
$host = "localhost";
$port = "5432";
$dbname = "jewellery_secure_db";
$user = "postgres"; // Change to your DB user
$password = "2002"; // Change to your DB password

$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";
$db = pg_connect($conn_string);

if (!$db) {
    die("Error: Could not connect to database.");
}
?>