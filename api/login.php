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

$login = trim($requestData['login'] ?? '');
$password = $requestData['password'] ?? '';

if ($login === '' || $password === '') {
    echo json_encode([
        "id" => 0,
        "firstName" => "",
        "lastName" => "",
        "error" => "Login and password are required"
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Password FROM Users WHERE Login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row["Password"])) {
        echo json_encode([
            "id" => intval($row["ID"]),
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "error" => ""
        ]);
    } else {
        echo json_encode([
            "id" => 0,
            "firstName" => "",
            "lastName" => "",
            "error" => "Invalid username or password"
        ]);
    }
} else {
    echo json_encode([
        "id" => 0,
        "firstName" => "",
        "lastName" => "",
        "error" => "No Records Found"
    ]);
}

$stmt->close();
$conn->close();
?>