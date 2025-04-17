<?php
// delete_invoice.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';

if (isset($_GET['id'])) {
    $invoice_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Delete related invoice_items (if applicable)
    $stmt_items = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
    $stmt_items->bind_param("i", $invoice_id);
    $stmt_items->execute();
    $stmt_items->close();
    
    // Delete the invoice
    $stmt = $conn->prepare("DELETE FROM invoices WHERE invoice_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $invoice_id, $user_id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("Location: history.php");
exit();
?>
