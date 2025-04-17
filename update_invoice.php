<?php
// update_invoice.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'includes/db.php';

$invoice_id = intval($_POST['invoice_id']);
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

// Update invoice details
$stmt = $conn->prepare("
    UPDATE invoices 
    SET business_name = ?, client_name = ?, client_email = ?, client_phone = ?, client_address = ?, 
        invoice_number = ?, invoice_date = ?, payment_term = ?, due_date = ?, 
        discount = ?, shipping_amount = ?, amount_paid = ?, total_amount = ?, balance_due = ?, terms = ? 
    WHERE invoice_id = ? AND user_id = ?
");
$stmt->bind_param("sssssssssssssdssi", 
    $business_name, $client_name, $client_email, $client_phone, $client_address, 
    $invoice_number, $invoice_date, $payment_term, $due_date, 
    $discount, $shipping_amount, $amount_paid, $total_amount, $balance_due, $terms, 
    $invoice_id, $user_id
);
$stmt->execute();
$stmt->close();

// Delete old invoice items
$conn->query("DELETE FROM invoice_items WHERE invoice_id = $invoice_id");

// Insert new invoice items
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

header("Location: history.php");
exit();
?>
