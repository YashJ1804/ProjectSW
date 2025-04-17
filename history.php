<?php
// history.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice History</title>
  <style>
    :root {
      --primary: #4a63d9;
      --accent: #f1f5ff;
      --danger: #e74c3c;
      --danger-dark: #c0392b;
      --btn-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--accent);
      margin: 0;
      padding: 40px 20px;
      color: #333;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      text-align: center;
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 30px;
    }

    .table-container {
      max-width: 1100px;
      margin: auto;
      background-color: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 14px 18px;
      text-align: center;
      font-size: 0.95rem;
    }

    th {
      background-color: #e8edff;
      color: #333;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: #f9faff;
    }

    .btn-group {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 6px;
    }

    .btn {
      padding: 8px 14px;
      background-color: var(--primary);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.3s ease, transform 0.2s ease;
      box-shadow: var(--btn-shadow);
    }

    .btn:hover {
      background-color: #364dc7;
      transform: translateY(-1px);
    }

    .btn.delete {
      background-color: var(--danger);
    }

    .btn.delete:hover {
      background-color: var(--danger-dark);
    }

    a {
      color: var(--primary);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    p {
      color: #d10000;
      font-weight: 500;
      text-align: center;
    }
  </style>
</head>
<body>

<h2>📄 Your Invoice History</h2>

<div class="table-container">
<?php
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT invoice_id, invoice_number, client_name, client_email, client_phone, due_date, total_amount FROM invoices WHERE user_id = ? ORDER BY invoice_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No invoices found! Create one <a href='index.php'>here</a>.</p>";
} else {
    echo "<table>";
    echo "<tr>
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
        echo "<td>" . htmlspecialchars($row['invoice_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
        echo "<td>₹" . htmlspecialchars($row['total_amount']) . "</td>";
        echo "<td>
                <div class='btn-group'>
                    <a href='generate_pdf.php?id=" . $row['invoice_id'] . "' class='btn'>Download PDF</a>
                    <a href='index.php?edit_id=" . $row['invoice_id'] . "' class='btn'>Edit</a>
                    <a href='delete_invoice.php?id=" . $row['invoice_id'] . "' class='btn delete' onclick='return confirm(\"Are you sure you want to delete this invoice?\")'>Delete</a>
                </div>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}
$stmt->close();
$conn->close();
?>
</div>

</body>
</html>
