<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'inara';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response = [
        'success' => false,
        'message' => 'Connection failed: ' . $conn->connect_error
    ];
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $service = $_POST['service'];
        $noowigs = $_POST['noofwigs'];
        $delivery = $_POST['delivery'];
        $message = $_POST['message'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        } else {
            // Check if email already exists in the database
            $checkEmailQuery = "SELECT * FROM orders WHERE email = '$email'";
            $result = $conn->query($checkEmailQuery);

            if ($result->num_rows > 0) {
                $response = [
                    'success' => false,
                    'message' => 'Email already exists'
                ];
            } 
            else {
                // Insert form data into the database
                $sql = "INSERT INTO orders (name, email, service, noofwigs, delivery, message) VALUES ('$name', '$email, $service, $noofwigs, $delivery, $message)";

                    }

?>                    