<?php
// Replace this with your actual secret key
$secretKey = "3e5d7a36-2b53-4529-b34c-714e69a01d80";

// Get the raw POST data from the request
$rawPayload = file_get_contents('php://input');
$decodedPayload = json_decode($rawPayload, true);

// Verify the payload
if (!$decodedPayload) {
    http_response_code(400); // Bad request
    echo json_encode(["message" => "Invalid payload"]);
    exit;
}

// Extract necessary fields
$merchantId = $decodedPayload['merchantId'] ?? null;
$transactionId = $decodedPayload['transactionId'] ?? null;
$status = $decodedPayload['status'] ?? null;
$amount = $decodedPayload['amount'] ?? null;
$checksum = $decodedPayload['checksum'] ?? null;

// Create the verification string
$verificationString = $rawPayload . $secretKey;

// Compute the checksum
$computedChecksum = hash('sha256', $verificationString);

// Compare the received checksum with the computed one
if ($computedChecksum !== $checksum) {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Checksum verification failed"]);
    exit;
}

// Handle payment success or failure
if ($status === 'SUCCESS') {
    // Save transaction details to a log file (temporary storage for testing)
    file_put_contents('transactions.log', $rawPayload . PHP_EOL, FILE_APPEND);

    // Respond with success
    http_response_code(200);
    echo json_encode(["message" => "Payment processed successfully"]);
} else {
    // Log failed transaction
    file_put_contents('transactions.log', $rawPayload . PHP_EOL, FILE_APPEND);

    // Respond with failure
    http_response_code(200); // Still respond with 200 to acknowledge the callback
    echo json_encode(["message" => "Payment failed or cancelled"]);
}
?>
