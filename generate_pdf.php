<?php
session_start();
ob_start(); // Prevent accidental output before PDF

require 'includes/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;

// ✅ Validate Invoice ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die("Invalid Invoice ID specified.");
}

$invoice_id = intval($_GET['id']);

// ✅ Validate User Authentication
if (!isset($_SESSION['user_id']) || !filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT)) {
    die("User not authenticated.");
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch invoice details
$stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_id = ? AND user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$invoice) {
    die("Invoice not found.");
}

// ✅ Fetch invoice items
$stmt_items = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$stmt_items->bind_param("i", $invoice_id);
$stmt_items->execute();
$invoice_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_items->close();

// ✅ Optional dummy product logic
if (!empty($invoice['include_extra'])) {
    $invoice_items[] = [
        'item_name' => 'Extra Product',
        'quantity' => 2,
        'rate' => 500
    ];
}

// ✅ Close DB
$conn->close();

// ✅ Escape HTML output
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ✅ Format currency values separately
$totalAmountFormatted = "₹" . number_format($invoice['total_amount'], 2);
$balanceDueFormatted = "₹" . number_format($invoice['balance_due'], 2);

// ✅ Build HTML
$items_rows = '';
foreach ($invoice_items as $item) {
    $total = $item['quantity'] * $item['rate'];
    $items_rows .= "<tr>
        <td>" . escape($item['item_name']) . "</td>
        <td>" . escape($item['quantity']) . "</td>
        <td>₹" . escape(number_format($item['rate'], 2)) . "</td>
        <td>₹" . escape(number_format($total, 2)) . "</td>
    </tr>";
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #{$invoice['invoice_number']}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      margin: 20px;
      font-size: 14px;
      color: #333;
    }
    .header {
      text-align: center;
      margin-bottom: 20px;
    }
    h1 {
      margin: 0;
      font-size: 28px;
    }
    h3 {
      margin: 5px 0;
      font-weight: normal;
    }
    .details, .items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .details th, .details td, .items th, .items td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: left;
    }
    .items th {
      background-color: #f5f5f5;
    }
    .total {
      text-align: right;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Invoice</h1>
    <h3>#{$invoice['invoice_number']}</h3>
  </div>

  <table class="details">
    <tr><th>Business Name</th><td>{$invoice['business_name']}</td></tr>
    <tr><th>Client Name</th><td>{$invoice['client_name']}</td></tr>
    <tr><th>Client Email</th><td>{$invoice['client_email']}</td></tr>
    <tr><th>Client Phone</th><td>{$invoice['client_phone']}</td></tr>
    <tr><th>Invoice Date</th><td>{$invoice['invoice_date']}</td></tr>
    <tr><th>Due Date</th><td>{$invoice['due_date']}</td></tr>
    <tr><th>Total Amount</th><td>{$totalAmountFormatted}</td></tr>
    <tr><th>Balance Due</th><td>{$balanceDueFormatted}</td></tr>
  </table>

  <h3>Invoice Items</h3>
  <table class="items">
    <thead>
      <tr>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      {$items_rows}
    </tbody>
  </table>
</body>
</html>
HTML;

// ✅ Generate PDF safely
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean(); // Clean output buffer before streaming
$dompdf->stream("Invoice_{$invoice['invoice_number']}.pdf", ["Attachment" => true]);

// Do NOT close PHP tag here to prevent output corruption
