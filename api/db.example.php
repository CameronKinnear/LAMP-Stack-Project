<?php
$DB_HOST = "localhost";
$DB_USER = "your_database_user";
$DB_PASS = "your_database_password";
$DB_NAME = "your_database_name";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "id" => 0,
        "error" => "Database connection failed"
    ]);
    exit();
}
?>