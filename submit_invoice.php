<?php
// submit_invoice.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'includes/db.php';

// Get POST data
$user_id = $_SESSION['user_id'];
$business_name = trim($_POST['business_name']);
$client_name = trim($_POST['client_name']);
$client_email = trim($_POST['client_email']);
$client_phone = trim($_POST['client_phone']);
$client_address = trim($_POST['client_address']);
$invoice_number = trim($_POST['invoice_number']);
$invoice_date = trim($_POST['invoice_date']);
$payment_term = trim($_POST['payment_term']);
$due_date = trim($_POST['due_date']);
$discount = floatval($_POST['discount']);
$shipping_amount = floatval($_POST['shipping_amount']);
$amount_paid = floatval($_POST['amount_paid']);
$total_amount = floatval($_POST['total_amount']);
$balance_due = floatval($_POST['balance_due']);
$terms = trim($_POST['terms']);

// Insert invoice into `invoices` table
$stmt = $conn->prepare("
    INSERT INTO invoices 
    (user_id, business_name, client_name, client_email, client_phone, client_address, invoice_number, invoice_date, payment_term, due_date, discount, shipping_amount, amount_paid, total_amount, balance_due, terms) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("issssssssssssdds", 
    $user_id, $business_name, $client_name, $client_email, $client_phone, $client_address, 
    $invoice_number, $invoice_date, $payment_term, $due_date, $discount, 
    $shipping_amount, $amount_paid, $total_amount, $balance_due, $terms
);
$stmt->execute();

// Get the invoice ID for inserting items
$invoice_id = $stmt->insert_id;
$stmt->close();

// Insert invoice items into `invoice_items` table
if (!empty($_POST['item_name']) && is_array($_POST['item_name'])) {
    $item_stmt = $conn->prepare("
        INSERT INTO invoice_items (invoice_id, item_name, quantity, rate)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_POST['item_name'] as $index => $item_name) {
        $quantity = intval($_POST['quantity'][$index]);
        $rate = floatval($_POST['rate'][$index]);

        $item_stmt->bind_param("isid", $invoice_id, $item_name, $quantity, $rate);
        $item_stmt->execute();
    }

    $item_stmt->close();
}

$conn->close();

// Redirect to history page after saving
header("Location: history.php");
exit();
?>
