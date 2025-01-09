<?php
// File: public_html/initiateTransaction.php

// Merchant details
$merchantId = "M22NGIIAX3JHB"; // Replace with your Merchant ID
$merchantSecret = "YOUR_MERCHANT_SECRET"; // Replace with your Merchant Secret
$baseUrl = "https://api.phonepe.com/apis/hermes"; // Production URL

// Get order details from front-end
$orderId = $_POST['orderId']; // Unique transaction ID
$amount = $_POST['amount']; // Amount in paisa (e.g., ₹1 = 100)
$callbackUrl = "https://saviourbites.com/public_html/callback.php"; // Your callback URL

// Prepare payload
$requestData = [
    "merchantId" => $merchantId,
    "transactionId" => $orderId,
    "amount" => $amount,
    "redirectUrl" => $callbackUrl,
    "paymentInstrument" => [
        "type" => "PAY_PAGE",
    ]
];

$requestBody = json_encode($requestData);

// Generate HMAC signature
$checksum = hash_hmac('sha256', $requestBody, $merchantSecret);

// Send API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/pg/v1/pay");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "X-VERIFY: $checksum#" . time(),
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// Return response to front-end
echo $response;
?>
