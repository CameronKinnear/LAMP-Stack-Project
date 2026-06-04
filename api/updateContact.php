<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "Summer#R007";
$dbname = "LampContactsManager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "id" => 0,
        "error" => "Database connection failed"
    ]);
    exit();
}
echo "Connected successfully";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/db.php";

$jsonInput = file_get_contents('php://input');
$requestData = json_decode($jsonInput, true);

$userId    = intval($requestData['userId'] ?? 0);
$contactId = intval($requestData['contactId'] ?? 0);
$firstName = trim($requestData['firstName'] ?? '');
$lastName  = trim($requestData['lastName'] ?? '');
$phone     = trim($requestData['phone'] ?? '');
$email     = trim($requestData['email'] ?? '');

if ($userId <= 0 || $contactId <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Valid userId and contactId are required"
    ]);
    exit();
}

if ($firstName === '' && $lastName === '') {
    echo json_encode([
        "success" => false,
        "error" => "At least a first name or last name is required"
    ]);
    exit();
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid email address"
    ]);
    exit();
}

$stmt = $conn->prepare(
    "UPDATE Contacts
     SET FirstName = ?, LastName = ?, Phone = ?, Email = ?
     WHERE ID = ? AND UserID = ?"
);

$stmt->bind_param("ssssii", $firstName, $lastName, $phone, $email, $contactId, $userId);
$stmt->execute();

if ($stmt->affected_rows >= 0) {
    echo json_encode([
        "success" => true,
        "error" => ""
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Contact could not be updated"
    ]);
}

$stmt->close();
$conn->close();
?>