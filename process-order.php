<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'shopping_db');

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method");
    }

    // Validate required fields
    $required_fields = ['name', 'email', 'contact', 'address'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Get and sanitize form data
    $orderNumber = 'ORD' . date('YmdHis');
    $name = $conn->real_escape_string($_POST['name']);
    $age = $conn->real_escape_string($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $occupation = $conn->real_escape_string($_POST['occupation'] ?? '');
    $officeAddress = $conn->real_escape_string($_POST['officeAddress'] ?? '');
    $orderItems = $conn->real_escape_string($_POST['orderItems']);
    $orderTotal = $conn->real_escape_string($_POST['orderTotal']);

    // Insert order into database
    $sql = "INSERT INTO orders (
        order_id, name, age, gender, dob, contact, email, 
        address, occupation, office_address, order_items, order_total, status
    ) VALUES (
        '$orderNumber', '$name', '$age', '$gender', '$dob', '$contact', 
        '$email', '$address', '$occupation', '$officeAddress', '$orderItems', 
        '$orderTotal', 'Pending'
    )";

    if (!$conn->query($sql)) {
        throw new Exception("Database error: " . $conn->error);
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'orderNumber' => $orderNumber,
        'message' => 'Order processed successfully'
    ]);

    // Close database connection
    $conn->close();

} catch (Exception $e) {
    // Send error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 