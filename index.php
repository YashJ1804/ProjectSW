<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';

$editMode = false;
$invoice = [];
$invoice_items = [];

if (isset($_GET['edit_id'])) {
    $editMode = true;
    $edit_id = intval($_GET['edit_id']);
    // Fetch invoice details
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $edit_id, $_SESSION['user_id']);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch invoice items for editing
    $stmt = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $invoice_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $editMode ? "Edit Invoice" : "Create Invoice"; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
  :root {
    --primary: #4a63d9;
    --accent: #f1f5ff;
    --danger: #dc3545;
    --text: #333;
    --input-border: #ccc;
    --btn-hover: #3a4fbd;
    --bg: #ffffff;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--accent);
    margin: 0;
    color: var(--text);
    animation: fadeIn 0.6s ease-in;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  main {
    padding: 30px 15px;
  }

  .invoice-form {
    max-width: 900px;
    margin: auto;
    background: var(--bg);
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: var(--shadow);
  }

  .invoice-form h2 {
    color: var(--primary);
    text-align: center;
    margin-bottom: 20px;
  }

  .invoice-form label {
    font-weight: 600;
    margin-top: 12px;
    display: block;
    color: #444;
  }

  .invoice-form input,
  .invoice-form textarea {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border: 1px solid var(--input-border);
    border-radius: 6px;
    font-size: 1rem;
  }

  .invoice-form textarea {
    resize: vertical;
    min-height: 80px;
  }

  .invoice-form button,
  .btn {
    background: var(--primary);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    margin-top: 15px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
    font-size: 1rem;
    box-shadow: var(--shadow);
  }

  .invoice-form button:hover,
  .btn:hover {
    background: var(--btn-hover);
    transform: translateY(-1px);
  }

  .items-container {
    margin-top: 25px;
  }

  .items-container h3 {
    color: var(--primary);
    margin-bottom: 10px;
  }

  .item-row {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
  }

  .item-row input {
    flex: 1;
  }

  .item-row button {
    background-color: var(--danger);
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .item-row button:hover {
    background-color: #b52d2d;
  }

  .add-item-btn {
    background: #28a745;
  }

  .add-item-btn:hover {
    background: #218838;
  }

  .calculation-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
  }

  .calculation-row label {
    flex: 1;
    font-weight: bold;
  }

  .calculation-row input {
    flex: 2;
    background: #f0f2f7;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px;
  }
</style>

</head>
<body>
<?php include 'includes/header.php'; ?>
<main>
    <div class="invoice-form">
        <h2><?php echo $editMode ? "Edit Invoice" : "Create Invoice"; ?></h2>
        <form action="<?php echo $editMode ? 'update_invoice.php' : 'submit_invoice.php'; ?>" method="post">
            <?php if ($editMode): ?>
                <input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">
            <?php endif; ?>
            <div>
                <label>Business Name:</label>
                <input type="text" name="business_name" required value="<?php echo $editMode ? htmlspecialchars($invoice['business_name']) : ''; ?>">
            </div>
            <div>
                <label>Client Name:</label>
                <input type="text" name="client_name" required value="<?php echo $editMode ? htmlspecialchars($invoice['client_name']) : ''; ?>">
            </div>
            <div>
                <label>Client Email:</label>
                <input type="email" name="client_email" value="<?php echo $editMode ? htmlspecialchars($invoice['client_email']) : ''; ?>">
            </div>
            <div>
                <label>Client Phone:</label>
                <input type="text" name="client_phone" value="<?php echo $editMode ? htmlspecialchars($invoice['client_phone']) : ''; ?>">
            </div>
            <div>
                <label>Client Address:</label>
                <textarea name="client_address"><?php echo $editMode ? htmlspecialchars($invoice['client_address']) : ''; ?></textarea>
            </div>
            <div>
                <label>Invoice Number:</label>
                <input type="text" name="invoice_number" required value="<?php echo $editMode ? htmlspecialchars($invoice['invoice_number']) : ''; ?>">
            </div>
            <div>
                <label>Invoice Date:</label>
                <input type="date" name="invoice_date" required value="<?php echo $editMode ? htmlspecialchars($invoice['invoice_date']) : ''; ?>">
            </div>
            <div>
                <label>Payment Term:</label>
                <input type="text" name="payment_term" value="<?php echo $editMode ? htmlspecialchars($invoice['payment_term']) : ''; ?>">
            </div>
            <div>
                <label>Due Date:</label>
                <input type="date" name="due_date" value="<?php echo $editMode ? htmlspecialchars($invoice['due_date']) : ''; ?>">
            </div>
            <div>
                <label>Discount:</label>
                <input type="number" step="0.01" name="discount" id="discount" value="<?php echo $editMode ? htmlspecialchars($invoice['discount']) : '0'; ?>">
            </div>
            <div>
                <label>Shipping Amount:</label>
                <input type="number" step="0.01" name="shipping_amount" id="shipping_amount" value="<?php echo $editMode ? htmlspecialchars($invoice['shipping_amount']) : '0'; ?>">
            </div>
            <div>
                <label>Amount Paid:</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" value="<?php echo $editMode ? htmlspecialchars($invoice['amount_paid']) : '0'; ?>">
            </div>
            <div>
                <label>Total Amount:</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" required value="<?php echo $editMode ? htmlspecialchars($invoice['total_amount']) : ''; ?>" readonly>
            </div>
            <div>
                <label>Balance Due:</label>
                <input type="number" step="0.01" name="balance_due" id="balance_due" required value="<?php echo $editMode ? htmlspecialchars($invoice['balance_due']) : ''; ?>" readonly>
            </div>
            <div>
                <label>Terms:</label>
                <textarea name="terms"><?php echo $editMode ? htmlspecialchars($invoice['terms']) : ''; ?></textarea>
            </div>
            <!-- Invoice Items Section -->
            <div class="items-container">
                <h3>Invoice Items</h3>
                <div id="item-rows">
                    <?php if ($editMode && !empty($invoice_items)): ?>
                        <?php foreach ($invoice_items as $index => $item): ?>
                            <div class="item-row">
                                <input type="text" name="item_name[]" placeholder="Item Name" required value="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <input type="number" name="quantity[]" placeholder="Quantity" required value="<?php echo htmlspecialchars($item['quantity']); ?>">
                                <input type="number" step="0.01" name="rate[]" placeholder="Rate" required value="<?php echo htmlspecialchars($item['rate']); ?>">
                                <button type="button" onclick="removeItem(this)">Remove</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Provide one blank row by default -->
                        <div class="item-row">
                            <input type="text" name="item_name[]" placeholder="Item Name" required>
                            <input type="number" name="quantity[]" placeholder="Quantity" required>
                            <input type="number" step="0.01" name="rate[]" placeholder="Rate" required>
                            <button type="button" onclick="removeItem(this)">Remove</button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn add-item-btn" onclick="addItem()">Add Item</button>
            </div>
            <br>
            <button type="submit" class="btn"><?php echo $editMode ? "Update Invoice" : "Save Invoice"; ?></button>
        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>

<script>
// Function to recalculate totals
function recalcTotals() {
    let itemsTotal = 0;
    // Iterate over each item row
    document.querySelectorAll("#item-rows .item-row").forEach(function(row) {
        let qty = parseFloat(row.querySelector("input[name='quantity[]']").value) || 0;
        let rate = parseFloat(row.querySelector("input[name='rate[]']").value) || 0;
        itemsTotal += qty * rate;
    });
    
    // Get additional inputs
    let shipping = parseFloat(document.getElementById("shipping_amount").value) || 0;
    let discount = parseFloat(document.getElementById("discount").value) || 0;
    let amountPaid = parseFloat(document.getElementById("amount_paid").value) || 0;
    
    // Calculate total and balance
    let computedTotal = itemsTotal + shipping - discount;
    let balanceDue = computedTotal - amountPaid;
    
    // Update read-only fields
    document.getElementById("total_amount").value = computedTotal.toFixed(2);
    document.getElementById("balance_due").value = balanceDue.toFixed(2);
}

// Attach event listeners to dynamic inputs
function attachCalcListeners() {
    // For each quantity and rate input, add an input event listener
    document.querySelectorAll("input[name='quantity[]'], input[name='rate[]']").forEach(function(input) {
        input.addEventListener("input", recalcTotals);
    });
    // Also for discount, shipping, and amount paid
    document.getElementById("discount").addEventListener("input", recalcTotals);
    document.getElementById("shipping_amount").addEventListener("input", recalcTotals);
    document.getElementById("amount_paid").addEventListener("input", recalcTotals);
}

// Call recalcTotals on page load
window.addEventListener("load", function() {
    recalcTotals();
    attachCalcListeners();
});

// Functions for adding and removing item rows
function addItem() {
    const container = document.getElementById('item-rows');
    const row = document.createElement('div');
    row.className = 'item-row';
    row.innerHTML = `
        <input type="text" name="item_name[]" placeholder="Item Name" required>
        <input type="number" name="quantity[]" placeholder="Quantity" required>
        <input type="number" step="0.01" name="rate[]" placeholder="Rate" required>
        <button type="button" onclick="removeItem(this)">Remove</button>
    `;
    container.appendChild(row);
    attachCalcListeners(); // Attach listener to new inputs
    recalcTotals();
}
function removeItem(button) {
    button.parentElement.remove();
    recalcTotals();
}
</script>
</body>
</html>
