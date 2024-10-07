<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        } else {
            // Check if email already exists in the database
            $checkEmailQuery = "SELECT * FROM subscribers WHERE email = '$email'";
            $result = $conn->query($checkEmailQuery);

            if ($result->num_rows > 0) {
                $response = [
                    'success' => false,
                    'message' => 'Email already exists'
                ];
            } else {
                // Insert form data into the database
                $sql = "INSERT INTO subscribers (name, email) VALUES ('$name', '$email')";

                if ($conn->query($sql) === TRUE) {
                    // SMTP settings for Mailtrap
                    $smtpHost = 'sandbox.smtp.mailtrap.io';
                    $smtpPort = 2525;
                    $smtpUsername = '30546dc7e71e17';
                    $smtpPassword = '8a47d0f4b066c5';

                    // Recipient email address
                    $to = $email;

                    // Sender email address
                    $from = 'blueroseghofficial@gmail.com';

                    // Email subject
                    $subject = 'Subscription Confirmation';

                    // Email message
                    $message = 'Thank you for subscribing to our updates.';

                    // Include the PHPMailer library files
                    require 'PHPMailer/src/Exception.php';
                    require 'PHPMailer/src/PHPMailer.php';
                    require 'PHPMailer/src/SMTP.php';

                    $mail = new PHPMailer;

                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->SMTPAuth = true;
                    $mail->Port = $smtpPort;
                    $mail->Username = $smtpUsername;
                    $mail->Password = $smtpPassword;
                    
                    $phpmailer = new PHPMailer();

                    $mail->setFrom($from, 'Inara Updates');
                    $mail->addAddress($to);

                    $mail->isHTML(true);

                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    if (!$mail->send()) {
                        // Log detailed error information
                        error_log('Mailer Error: ' . $mail->ErrorInfo);
                        $response = [
                            'success' => false,
                            'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
                        ];
                    } else {
                        $response = [
                            'success' => true,
                            'message' => 'Subscription successful and email sent'
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Database error: ' . $conn->error
                    ];
                }
            }
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid request method'
        ];
    }

    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
