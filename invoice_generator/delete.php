<?php
require 'db_connect.php';

echo "Connected successfully<br><br>";

echo "<h2 style='color:#4a63d9;'>📄 Your Invoice History</h2>";

$user_id = 1;

$stmt = $conn->prepare("SELECT invoice_id, invoice_number, client_name, client_email, client_phone, due_date, total_amount FROM invoices WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>No invoices found! Create one <a href='create_invoice.php'>here</a>.</p>";
} else {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr style='background-color:#eee;'>
            <th>Invoice #</th>
            <th>Client Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Due Date</th>
            <th>Total Amount</th>
            <th>Actions</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['invoice_number'] . "</td>";
        echo "<td>" . $row['client_name'] . "</td>";
        echo "<td>" . $row['client_email'] . "</td>";
        echo "<td>" . $row['client_phone'] . "</td>";
        echo "<td>" . $row['due_date'] . "</td>";
        echo "<td>₹" . $row['total_amount'] . "</td>";
        echo "<td>
                <a href='index.php?edit_id=" . $row['invoice_id'] . "' style='margin-right:10px;'>✏️ Edit</a>
                <a href='delete_invoice.php?id=" . $row['invoice_id'] . "' onclick='return confirm(\"Are you sure?\")'>🗑️ Delete</a>
              </td>";
        echo "</tr>";
    }

    echo "</table>";
}

$stmt->close();
$conn->close();
?>
