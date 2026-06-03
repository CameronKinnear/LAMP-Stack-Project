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

// extracts data requested by the client
$userId    = $requestData['userId'] ?? 0;
$firstName = $requestData['firstName'] ?? '';
$lastName  = $requestData['lastName'] ?? '';
$phone     = $requestData['phone'] ?? '';
$email     = $requestData['email'] ?? '';

// -- [DATABASE LOGIC WILL GO HERE] --

// logic for adding a contact
if ($userId > 0 && (!empty($firstName) || !empty($lastName))) {
    $response = array("error" => ""); // Success
} else {
    $response = array("error" => "Invalid user session or missing contact name.");
}

// sends the JSON response back to the client (to Postman)
echo json_encode($response);
?>