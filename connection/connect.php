<?php

// Load environment variables (if using vlucas/phpdotenv)

// Database connection details from environment 
error_reporting(0); 
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$ca_cert = getenv('DB_SSL_CERT'); // SSL cert stored as text

// Create a temporary file for the SSL certificate
$cert_file = tempnam(sys_get_temp_dir(), 'cert_');
file_put_contents($cert_file, $ca_cert);

// Create connection
$db = mysqli_init();

// Enable SSL using the temporary certificate file
mysqli_ssl_set($db, NULL, NULL, $cert_file, NULL, NULL);

// Establish connection
if (!mysqli_real_connect($db, $host, $username, $password, $dbname, $port)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Remove the temporary file after use
unlink($cert_file);

?>
