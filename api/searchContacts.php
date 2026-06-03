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
$userId = $requestData['userId'] ?? 0;
$search = $requestData['search'] ?? '';

// -- [DATABASE LOGIC WILL GO HERE] --

// (temporary code block) fake a matching search result array to test your front-end rendering later
if ($userId > 0) {
    $response = array(
        "results" => array(
            array(
                "id" => 101,
                "firstName" => "John",
                "lastName" => "Doe",
                "phone" => "407-313-0124",
                "email" => "johndoe@example.com"
            )
        ),
        "error" => ""
    );
} else {
    $response = array(
        "results" => array(),
        "error" => "Invalid user session."
    );
}
// sends the JSON response back to the client (to Postman)
echo json_encode($response);
?>