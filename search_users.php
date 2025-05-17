<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true || !isset($_SESSION["current_user"]) || !isset($_SESSION["current_user"]["is_admin"]) || $_SESSION["current_user"]["is_admin"] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if query parameter is provided
if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo json_encode(['error' => 'Search query is required']);
    exit;
}

$query = trim($_GET['query']);

// Prepare the search query with LIKE for multiple fields
$searchQuery = "SELECT userID, username, first_name, last_name, email, is_active, is_admin 
                FROM User 
                WHERE username LIKE ? 
                OR email LIKE ? 
                OR first_name LIKE ? 
                OR last_name LIKE ?
                ORDER BY username ASC
                LIMIT 50"; // Limit to prevent large result sets

$searchParam = "%" . $query . "%";

$stmt = $conn->prepare($searchQuery);
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);

$result = [];

if ($stmt->execute()) {
    $stmt->bind_result($userID, $username, $first_name, $last_name, $email, $is_active, $is_admin);
    
    $users = [];
    while ($stmt->fetch()) {
        $users[] = [
            'userID' => $userID,
            'username' => htmlspecialchars($username),
            'first_name' => htmlspecialchars($first_name),
            'last_name' => htmlspecialchars($last_name),
            'email' => htmlspecialchars($email),
            'is_active' => (bool)$is_active,
            'is_admin' => (bool)$is_admin
        ];
    }
    
    $result = ['users' => $users];
} else {
    $result = ['error' => 'Database error: ' . $conn->error];
}

$stmt->close();
$conn->close();

echo json_encode($result);
?>
