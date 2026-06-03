<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/db.php";

$jsonInput = file_get_contents('php://input');
$requestData = json_decode($jsonInput, true);

$userId    = intval($requestData['userId'] ?? 0);
$contactId = intval($requestData['contactId'] ?? 0);

if ($userId <= 0 || $contactId <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Valid userId and contactId are required"
    ]);
    exit();
}

$stmt = $conn->prepare(
    "DELETE FROM Contacts
     WHERE ID = ? AND UserID = ?"
);

$stmt->bind_param("ii", $contactId, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        "success" => true,
        "error" => ""
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Contact not found or could not be deleted"
    ]);
}

$stmt->close();
$conn->close();
?>