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

$firstName = trim($requestData['firstName'] ?? '');
$lastName  = trim($requestData['lastName'] ?? '');
$login     = trim($requestData['login'] ?? '');
$password  = $requestData['password'] ?? '';

if ($firstName === '' || $lastName === '' || $login === '' || $password === '') {
    echo json_encode([
        "id" => 0,
        "error" => "First name, last name, login, and password are required"
    ]);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode([
        "id" => 0,
        "error" => "Password must be at least 6 characters"
    ]);
    exit();
}

$checkStmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
$checkStmt->bind_param("s", $login);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        "id" => 0,
        "error" => "Username already exists"
    ]);
    $checkStmt->close();
    $conn->close();
    exit();
}

$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstName, $lastName, $login, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        "id" => intval($stmt->insert_id),
        "firstName" => $firstName,
        "lastName" => $lastName,
        "error" => ""
    ]);
} else {
    echo json_encode([
        "id" => 0,
        "error" => "Registration failed"
    ]);
}

$stmt->close();
$conn->close();
?>
