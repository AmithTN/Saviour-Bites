<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Replace with your actual PhonePe credentials and secret key
$merchantId = "M22NGIIAX3JHB";
$secretKey = "3e5d7a36-2b53-4529-b34c-714e69a01d80";

// Create a debug log file
$debugLog = 'debug.log';

// Log raw input
$rawInput = file_get_contents('php://input');
file_put_contents($debugLog, "Raw Input:\n" . $rawInput . "\n\n", FILE_APPEND);

// Decode JSON input
$paymentData = json_decode($rawInput, true);
file_put_contents($debugLog, "Decoded Input:\n" . print_r($paymentData, true) . "\n\n", FILE_APPEND);

// Check if the necessary data is present
if (empty($paymentData['amount']) || empty($paymentData['merchantTransactionId'])) {
    $errorMessage = "Invalid request: Missing amount or merchantTransactionId";
    file_put_contents($debugLog, "Error: " . $errorMessage . "\n", FILE_APPEND);
    echo json_encode(["status" => "FAILED", "message" => $errorMessage]);
    exit;
}

// Prepare request data for PhonePe API
$paymentRequestData = [
    'merchantId' => $merchantId,
    'merchantTransactionId' => $paymentData['merchantTransactionId'], // Use transaction ID from frontend
    'merchantUserId' => $paymentData['merchantUserId'] ?? '', // Optional field
    'amount' => $paymentData['amount'], // Amount in paise
    'redirectUrl' => $paymentData['redirectUrl'] ?? '', // Fallback if missing
    'redirectMode' => $paymentData['redirectMode'] ?? 'POST', // Default to POST
    'callbackUrl' => $paymentData['callbackUrl'] ?? '', // Fallback if missing
    'mobileNumber' => $paymentData['mobileNumber'] ?? '', // Optional field
    'paymentInstrument' => $paymentData['paymentInstrument'] ?? '' // Optional field
];

// Log request data
file_put_contents($debugLog, "Request Data:\n" . print_r($paymentRequestData, true) . "\n\n", FILE_APPEND);

// Create the X-VERIFY header
$base64Payload = base64_encode(json_encode($paymentRequestData));
$keyIndex = '1'; // Replace with the actual key index
$xVerifyString = $base64Payload . "/pg/v1/pay" . $secretKey;
$xVerifyHex = strtoupper(hash('sha256', $xVerifyString));

// Add checksum to the request data
$checksumString = implode('', array_values($paymentRequestData)) . $secretKey;
$checksum = hash('sha256', $checksumString);
$paymentRequestData['checksum'] = $checksum;

// Log the checksum
file_put_contents($debugLog, "Checksum:\n" . $checksum . "\n\n", FILE_APPEND);

// Send payment request to PhonePe API
$apiUrl = 'https://api.phonepe.com/apis/hermes/pg/v1/pay';
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                    "X-VERIFY: " . $xVerifyHex . "###" . $keyIndex . "\r\n",
        'content' => json_encode($paymentRequestData),
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($apiUrl, false, $context);

// Log the API response or connection failure
if ($response === FALSE) {
    file_put_contents($debugLog, "Failed to connect to PhonePe API\n", FILE_APPEND);
    echo json_encode(["status" => "FAILED", "message" => "Failed to connect to PhonePe API"]);
    exit;
} else {
    file_put_contents($debugLog, "PhonePe Response:\n" . $response . "\n\n", FILE_APPEND);
}

// Parse the API response
$responseData = json_decode($response, true);

// Handle success or failure
if (isset($responseData['status']) && $responseData['status'] === 'SUCCESS') {
    echo json_encode(["status" => "SUCCESS", "paymentUrl" => $responseData['data']['instrumentResponse']['url'] ?? '']);
} else {
    $errorMessage = $responseData['message'] ?? 'Unknown error from PhonePe API';
    file_put_contents($debugLog, "API Error: " . $errorMessage . "\n\n", FILE_APPEND);
    echo json_encode(["status" => "FAILED", "message" => $errorMessage]);
}

// Log any PHP errors
if (!empty(error_get_last())) {
    file_put_contents($debugLog, "PHP Errors:\n" . print_r(error_get_last(), true) . "\n\n", FILE_APPEND);
}
?>
