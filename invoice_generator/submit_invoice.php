<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $rate = $_POST['rate'];
    $amount = $quantity * $rate;

    $sql = "INSERT INTO invoices (from_name, to_name, item_description, quantity, rate, amount) VALUES ('$from', '$to', '$item', '$quantity', '$rate', '$amount')";

    if ($conn->query($sql) === TRUE) {
        echo "Invoice generated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>