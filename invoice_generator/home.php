<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Generator - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar {
            width: 100%;
            background: #fff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar h1 {
            font-size: 1.8rem;
            color: #007bff;
        }
        .navbar .links a {
            margin-left: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }
        .main {
            padding: 40px;
            text-align: center;
        }
        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            margin-top: 30px;
        }
        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            width: 280px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .card h3 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 15px;
        }
        .card a {
            text-decoration: none;
            color: #fff;
            background: #007bff;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
        }
        footer {
            background: #f0f0f0;
            padding: 30px;
            text-align: center;
            color: #444;
            margin-top: 60px;
        }
        .footer-social a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- 🔝 Header -->
<div class="navbar">
    <h1>Invoice Generator</h1>
    <div class="links">
        <?php if ($is_logged_in): ?>
            <a href="index.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
        <?php endif; ?>
    </div>
</div>

<!-- 🧾 Main Content -->
<div class="main">
    <h2>Welcome to Your Invoice Hub</h2>
    <p>Fast. Simple. Professional invoice creation.</p>

    <div class="cards">
        <div class="card">
            <h3>Create Invoice</h3>
            <p>Quickly generate a professional invoice for your clients.</p>
            <a href="index.php">Create Now</a>
        </div>

        <div class="card">
            <h3>View Invoices</h3>
            <p>See a list of all your created invoices in one place.</p>
            <a href="history.php">View Invoices</a>
        </div>

        <div class="card">
            <h3>Delete Invoices</h3>
            <p>Remove outdated or mistaken invoices from your records.</p>
            <a href="invoices.php">Delete</a>
        </div>

        <div class="card">
            <h3>How It Works?</h3>
            <p>Understand how our system makes billing fast and easy.</p>
            <a href="#">Learn More</a>
        </div>

        <div class="card">
            <h3>Why Use This?</h3>
            <p>Save time, reduce errors, and look professional with every bill.</p>
            <a href="#">See Benefits</a>
        </div>

        <div class="card">
            <h3>When to Use?</h3>
            <p>Ideal for freelancers, small biz, and startups.</p>
            <a href="#">Explore Use Cases</a>
        </div>

        <div class="card">
            <h3>Download as PDF</h3>
            <p>Download invoices in PDF format anytime.</p>
            <a href="#">Coming Soon</a>
        </div>
    </div>
</div>

<!-- 🔚 Footer -->
<footer>
    <p>Created by <strong>Your Name</strong> | Contact: <a href="tel:+911234567890">+91 12345 67890</a></p>
    <div class="footer-social">
        <a href="https://instagram.com/yourusername" target="_blank">Instagram</a>
        <a href="https://linkedin.com/in/yourusername" target="_blank">LinkedIn</a>
        <a href="mailto:youremail@example.com">Email</a>
    </div>
</footer>

</body>
</html>
