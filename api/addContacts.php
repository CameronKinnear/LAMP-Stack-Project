<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/db.php";

$jsonInput = file_get_contents('php://input');
$requestData = json_decode($jsonInput, true);

$userId    = intval($requestData['userId'] ?? 0);
$firstName = trim($requestData['firstName'] ?? '');
$lastName  = trim($requestData['lastName'] ?? '');
$phone     = trim($requestData['phone'] ?? '');
$email     = trim($requestData['email'] ?? '');

if ($userId <= 0) {
    echo json_encode([
        "id" => 0,
        "error" => "Valid userId is required"
    ]);
    exit();
}

if ($firstName === '' && $lastName === '') {
    echo json_encode([
        "id" => 0,
        "error" => "At least a first name or last name is required"
    ]);
    exit();
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "id" => 0,
        "error" => "Invalid email address"
    ]);
    exit();
}

$stmt = $conn->prepare(
    "INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param("issss", $userId, $firstName, $lastName, $phone, $email);

if ($stmt->execute()) {
    echo json_encode([
        "id" => intval($stmt->insert_id),
        "error" => ""
    ]);
} else {
    echo json_encode([
        "id" => 0,
        "error" => "Contact could not be added"
    ]);
}

$stmt->close();
$conn->close();
?>