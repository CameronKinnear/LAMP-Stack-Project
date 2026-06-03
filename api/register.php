<?php
// allows any origin to access this API (prevents CORS policy errors)
header("Access-Control-Allow-Origin: *"); // although a temporary wildcard (*), this is subject to change with an actual domain
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// handles the browser's hidden OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // exit immediately with a 200 OK status so the browser proceeds to the real POST request
    http_response_code(200);
    exit();

// reads the raw incoming JSON data
$jsonInput = file_get_contents('php://input');
$requestData = json_decode($jsonInput, true);

// extracts registration data requested by the client
$firstName = $requestData['firstName'] ?? '';
$lastName  = $requestData['lastName'] ?? '';
$login     = $requestData['login'] ?? '';
$password  = $requestData['password'] ?? '';

// -- [DATABASE & SECURITY LOGIC WILL GO HERE] --

// registration check
if (!empty($login) && !empty($password)) {
    $response = array(
        "error" => "" // empty string means registration success
    );
} else {
    $response = array(
        "error" => "Missing required registration fields."
    );
}

// sends the JSON response back to the client (to Postman)
echo json_encode($response);
?>