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

$userId = intval($requestData['userId'] ?? 0);
$search = trim($requestData['search'] ?? '');

if ($userId <= 0) {
    echo json_encode([
        "results" => [],
        "error" => "Valid userId is required"
    ]);
    exit();
}

$likeSearch = "%" . $search . "%";

$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Phone, Email
     FROM Contacts
     WHERE UserID = ?
       AND (
            FirstName LIKE ?
         OR LastName LIKE ?
         OR Phone LIKE ?
         OR Email LIKE ?
       )
     ORDER BY LastName ASC, FirstName ASC"
);

$stmt->bind_param("issss", $userId, $likeSearch, $likeSearch, $likeSearch, $likeSearch);
$stmt->execute();

$result = $stmt->get_result();
$contacts = [];

while ($row = $result->fetch_assoc()) {
    $contacts[] = [
        "id" => intval($row["ID"]),
        "firstName" => $row["FirstName"],
        "lastName" => $row["LastName"],
        "phone" => $row["Phone"],
        "email" => $row["Email"]
    ];
}

echo json_encode([
    "results" => $contacts,
    "error" => ""
]);

$stmt->close();
$conn->close();
?>