<?php
include 'db.php'; // DB connection

// Collect form data
$name = $_POST['name']; // Customer's name
$chest = $_POST['chest']; // Chest measurement
$waist = $_POST['waist']; // Waist measurement
$hips = $_POST['hips']; // Hips measurement
$inseam = $_POST['inseam']; // Inseam measurement
$message = $_POST['message']; // Additional notes

// Step 1: Get phone number from customers table using the customer's name
$phone = '';
$stmt = $conn->prepare("SELECT phone FROM customers WHERE name = ?");
$stmt->bind_param("s", $name); // Bind the name parameter
$stmt->execute();
$stmt->bind_result($phone);
$stmt->fetch();
$stmt->close();

// Step 2: Check if phone number is found
if (empty($phone)) {
    echo "<div style='color: red; text-align:center;'>Phone number not found or is NULL for this customer!</div>";
    exit();
}

// Step 3: Insert measurement details into the measurements table
$sql = "INSERT INTO measurements (name, chest, waist, hips, inseam, message) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $chest, $waist, $hips, $inseam, $message);

if ($stmt->execute()) {
    // Step 4: Send SMS using Fast2SMS
    $fields = array(
        "sender_id" => "FSTSMS",
        "message" => "Thank you for your order! - Tailor's Notebook",
        "language" => "english",
        "route" => "p",
        "numbers" => $phone,
    );

    $curl = curl_init();

    // Start time measurement
    $start_time = microtime(true);

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => array(
            "authorization: FinHpT4UsMqRGWlZDQvO6z7ke1rPyBodaIKXwb0mf5jYCJS2ux9MmQ5lfDyUCHbz7hZBVpideRgot2AN",
            "accept: /",
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // End time measurement
    $end_time = microtime(true);

    // Calculate the time taken to send the message
    $time_taken = $end_time - $start_time;

    // Step 5: Output success message and time taken
    if ($response) {
        echo "<div style='color: lightgreen; font-weight: bold; text-align:center; margin-top:20px;'>Submitted successfully! SMS sent in " . round($time_taken, 2) . " seconds.</div>";
    } else {
        echo "<div style='color: red; text-align:center;'>SMS failed to send. Please try again later.</div>";
    }
} else {
    echo "<div style='color: red; text-align:center;'>Error: " . $stmt->error . "</div>";
}
?>