<?php
include 'includes/db.php'; // Assuming this now contains the PostgreSQL connection from the immersive
if (!$conn) {
    die("Database connection failed: " . pg_last_error());
}
$name = $_POST['name'];
$email = $_POST['email'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$stitch_date = $_POST['stitch_date'];
if (substr($phone, 0, 1) !== '+') {
    $phone = '+91' . $phone;
}
$sql = "INSERT INTO customers (cname, email, caddress, phone, stitch_date) VALUES ($1, $2, $3, $4, $5)"; // Use placeholders $1, $2,...
$result = pg_query_params($conn, $sql, array($name, $email, $address, $phone, $stitch_date)); // Pass parameters as an array

if ($result) {
    header("Location: gender.html?success=true");
    exit();
} else {
    echo "Error inserting customer data: " . pg_last_error($conn);
}
pg_free_result($result);
pg_close($conn);
?>
