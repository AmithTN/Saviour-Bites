<?php
// File: public_html/callback.php

// Read PhonePe's response
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate HMAC signature
$merchantSecret = "YOUR_MERCHANT_SECRET"; // Replace with your Merchant Secret
$checksum = hash_hmac('sha256', $data['response'], $merchantSecret);

if ($checksum !== $_SERVER['HTTP_X_VERIFY']) {
    http_response_code(400); // Invalid request
    die("Invalid checksum");
}

// Check payment status
if ($data['success'] && $data['code'] == "PAYMENT_SUCCESS") {
    // Payment successful, save order details to database
    $orderId = $data['transactionId'];
    $amount = $data['amount'];

    // Save order details in your database here
    // Example:
    // $db->query("UPDATE orders SET status='completed' WHERE orderId='$orderId'");

    echo "Order placed successfully. Thank you!";
} else {
    // Payment failed
    echo "Payment failed. Please try again.";
}
?>
