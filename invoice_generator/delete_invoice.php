<?php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];

    // Delete from invoice_items first (foreign key relation)
    $stmt_items = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
    $stmt_items->bind_param("i", $invoice_id);
    $stmt_items->execute();
    $stmt_items->close();

    // Delete from invoices table
    $stmt_invoice = $conn->prepare("DELETE FROM invoices WHERE invoice_id = ?");
    $stmt_invoice->bind_param("i", $invoice_id);
    $stmt_invoice->execute();
    $stmt_invoice->close();
}

$conn->close();
header("Location: history.php");
exit();
