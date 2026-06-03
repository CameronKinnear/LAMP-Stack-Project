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
$contactId = $requestData['contactId'] ?? 0;

// --- [DATABASE LOGIC WILL GO HERE] ---

// logic for deleting contact data
if ($userId > 0 && $contactId > 0) {
    $response = array("error" => ""); // Success
} else {
    $response = array("error" => "Invalid delete request parameters.");
}

// sends the JSON response back to the client (to Postman)
echo json_encode($response);
?>