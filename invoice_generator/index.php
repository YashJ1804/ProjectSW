<?php
require 'db_connect.php';
$edit_mode = false;
$invoice_data = [];

if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];

    // Fetch invoice data
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice_data = $result->fetch_assoc();
    $stmt->close();
}
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "invoice_generator");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submit handle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Static user_id (update later with login)
    $user_id = 1;

    // Get form data
    $business_name = $_POST["business_name"];
    $client_name = $_POST["client_name"];
    $client_email = $_POST["client_email"];
    $client_phone = $_POST["client_phone"];
    $client_address = $_POST["client_address"];
    $invoice_number = $_POST["invoice_number"];
    $invoice_date = $_POST["invoice_date"];
    $payment_term = $_POST["payment_term"];
    $due_date = $_POST["due_date"];
    $discount = $_POST["discount"];
    $shipping_amount = $_POST["shipping_amount"];
    $amount_paid = $_POST["amount_paid"];
    $total_amount = $_POST["total_amount"];
    $balance_due = $_POST["balance_due"];
    $terms = $_POST["terms"];

    // Insert invoice
    $stmt = $conn->prepare("INSERT INTO invoices (user_id, business_name, client_name, client_email, client_phone, client_address, invoice_number, invoice_date, payment_term, due_date, discount, shipping_amount, amount_paid, total_amount, balance_due, terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssssssdds", $user_id, $business_name, $client_name, $client_email, $client_phone, $client_address, $invoice_number, $invoice_date, $payment_term, $due_date, $discount, $shipping_amount, $amount_paid, $total_amount, $balance_due, $terms);

    if ($stmt->execute()) {
        $invoice_id = $stmt->insert_id;

        // Insert invoice items
        foreach ($_POST['item_name'] as $index => $item_name) {
            $qty = $_POST['quantity'][$index];
            $rate = $_POST['rate'][$index];

            $stmt_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, item_name, quantity, rate) VALUES (?, ?, ?, ?)");
            $stmt_item->bind_param("isid", $invoice_id, $item_name, $qty, $rate);
            $stmt_item->execute();
        }

        echo "<script>alert('Invoice Created Successfully!');</script>";
    } else {
        echo "<script>alert('Error creating invoice. Please check inputs or database.');</script>";
    }
}
?>

<!-- HTML Form below -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Invoice</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
        }
        .item-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .item-row input {
            flex: 1;
        }
        .btn {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn-danger {
            background: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Create Invoice</h2>
    <form method="post">
        <label>Business Name:</label>
        <input type="text" name="business_name" required>

        <label>Client Name:</label>
        <input type="text" name="client_name" required>

        <label>Client Email:</label>
        <input type="email" name="client_email">

        <label>Client Phone:</label>
        <input type="text" name="client_phone">

        <label>Client Address:</label>
        <textarea name="client_address"></textarea>

        <label>Invoice Number:</label>
        <input type="text" name="invoice_number" required>

        <label>Invoice Date:</label>
        <input type="date" name="invoice_date" required>

        <label>Payment Term:</label>
        <input type="text" name="payment_term">

        <label>Due Date:</label>
        <input type="date" name="due_date">

        <label>Discount:</label>
        <input type="number" step="0.01" name="discount" value="0">

        <label>Shipping Amount:</label>
        <input type="number" step="0.01" name="shipping_amount" value="0">

        <label>Amount Paid:</label>
        <input type="number" step="0.01" name="amount_paid" value="0">

        <label>Total Amount:</label>
        <input type="number" step="0.01" name="total_amount" required>

        <label>Balance Due:</label>
        <input type="number" step="0.01" name="balance_due" required>

        <label>Terms:</label>
        <textarea name="terms"></textarea>

        <h3>Invoice Items:</h3>
        <div id="item-container">
            <div class="item-row">
                <input type="text" name="item_name[]" placeholder="Item Name" required>
                <input type="number" name="quantity[]" placeholder="Quantity" required>
                <input type="number" step="0.01" name="rate[]" placeholder="Rate" required>
                <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
            </div>
        </div>
        <button type="button" class="btn" onclick="addItem()">Add Item</button>

        <br><br>
        <button type="submit" class="btn">Create Invoice</button>
    </form>
</div>

<script>
    function addItem() {
        const container = document.getElementById('item-container');
        const row = document.createElement('div');
        row.className = 'item-row';
        row.innerHTML = `
            <input type="text" name="item_name[]" placeholder="Item Name" required>
            <input type="number" name="quantity[]" placeholder="Quantity" required>
            <input type="number" step="0.01" name="rate[]" placeholder="Rate" required>
            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
        `;
        container.appendChild(row);
    }

    function removeItem(btn) {
        btn.parentElement.remove();
    }
</script>
</body>
</html>
