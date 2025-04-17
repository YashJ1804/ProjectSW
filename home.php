<?php
// home.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';
include 'includes/header.php';  // your navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Invoice Generator</title>
  <style>
    /* ─── GLOBAL ─────────────────────────────────────────────────────────────── */
    body {
      margin: 0;
      background: #f1f5ff;             /* soft blue */
      font-family: 'Poppins', sans-serif;
      color: #2c2f4a;                  /* dark grayish */
    }
    a { text-decoration: none; }

    /* ─── TOP DASHBOARD ───────────────────────────────────────────────────────── */
    .dashboard-top {
      max-width: 1000px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.05);
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      padding: 40px;
      align-items: center;
    }
    .dashboard-intro {
      flex: 1 1 300px;
    }
    .dashboard-intro h1 {
      font-size: 2rem;
      color: #4a63d9;                  /* deep indigo */
      margin-bottom: 16px;
    }
    .dashboard-intro p {
      margin-bottom: 24px;
    }
    .dashboard-intro .btn-primary,
    .dashboard-intro .btn-secondary {
      display: inline-block;
      padding: 10px 20px;
      margin-right: 12px;
      border-radius: 6px;
      font-weight: 500;
      transition: transform 0.2s;
    }
    .dashboard-intro .btn-primary {
      background: linear-gradient(135deg, #4a63d9, #6c8bfa);
      color: white;
    }
    .dashboard-intro .btn-primary:hover {
      transform: translateY(-2px);
    }
    .dashboard-intro .btn-secondary {
      background: white;
      color: #4a63d9;
      border: 2px solid #4a63d9;
    }
    .dashboard-intro .btn-secondary:hover {
      background: #4a63d9;
      color: white;
      transform: translateY(-2px);
    }

    .dashboard-stats {
      flex: 1 1 300px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .stats-cards {
      display: flex;
      gap: 20px;
      justify-content: center;
    }
    .stat-card {
      flex: 1;
      background: #f1f5ff;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }
    .stat-value {
      font-size: 2.5rem;
      color: #4a63d9;
      margin: 0;
    }
    .stat-label {
      margin-top: 8px;
      font-weight: 500;
    }

    @media(max-width: 768px) {
      .dashboard-top {
        flex-direction: column;
        padding: 20px;
      }
      .stats-cards {
        flex-direction: column;
      }
    }

    /* ─── BENEFITS SECTION (UNCHANGED) ─────────────────────────────────────────── */
    .firstSectionTwoLilac {
      padding: 60px 20px;
      margin-top: 60px;
    }
    .firstSectionTwoLilac .container {
      max-width: 1000px;
      margin: auto;
    }
    .row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }
    .col-md-4 {
      flex: 0 0 calc(33.333% - 20px);
      display: flex;
      justify-content: center;
    }
    .firstSectionTwoInner {
      background: #fff;
      border-radius: 12px;
      padding: 25px 20px;
      text-align: center;
      box-shadow: 0 6px 16px rgba(0,0,0,0.06);
      width: 100%;
    }
    .firstSectionTwoInner img {
      width: 60px;
      margin-bottom: -20px;
    }
    .firstSectionTwoInner h4 {
      font-size: 1.4rem;
      color: #4a63d9;
      margin-bottom: 5px;
    }
    .firstSectionTwoInner p {
      font-size: 0.95rem;
      color: #555;
    }
    .sec2-h1{
      color: #4a63d9;
      text-align: center;
    }
    @media(max-width: 768px) {
      .col-md-4 {
        flex: 0 0 100%;
      }
    }
    .faq-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.faq-item {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.06);
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-question {
    background-color: #ffffff;
    color: #4a63d9;
    border: none;
    outline: none;
    width: 100%;
    text-align: left;
    padding: 18px 24px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
}

.faq-question:hover {
    background-color: #f1f5ff;
}

.faq-answer {
    padding: 0 24px 18px 24px;
    font-size: 15px;
    color: #333;
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-5px);}
    to {opacity: 1; transform: translateY(0);}
}

@media (max-width: 600px) {
    .faq-question {
        font-size: 15px;
        padding: 14px 20px;
    }

    .faq-answer {
        font-size: 14px;
    }
}

/* ==== Explainer Section Styles ==== */

.explainer-section {
  background-color: #f1f5ff;
  padding: 60px 20px;
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  align-items: flex-start;
}

.explainer-text {
  flex: 1;
  min-width: 280px;
  max-width: 400px;
}

.explainer-text h2 {
  font-size: 1.8rem;
  color: #4a63d9;
  margin-bottom: 15px;
}

.explainer-text p {
  font-size: 1rem;
  line-height: 1.5;
  margin-bottom: 15px;
}

.read-more-toggle {
  color: #4a63d9;
  cursor: pointer;
  font-weight: 500;
}

.read-more-content {
  display: none;
  margin-top: 10px;
  font-size: 0.95rem;
  line-height: 1.4;
}

.explainer-cards {
  flex: 1;
  min-width: 280px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.explainer-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  padding: 20px;
  transition: transform 0.3s ease;
}

.explainer-card:hover {
  transform: translateY(-5px);
}

.explainer-card h3 {
  font-size: 1rem;
  margin-bottom: 8px;
  color: #333;
}

.explainer-card p {
  font-size: 0.9rem;
  color: #555;
}

@media(max-width: 768px) {
  .explainer-section {
    flex-direction: column;
  }
  .explainer-cards {
    grid-template-columns: 1fr;
  }
}

  </style>
</head>
<body>

  <div class="dashboard-top">
    <div class="dashboard-intro">
      <h1>Free Invoice Format | Billing Formats</h1>
      <p>Get 200+ formats & simplify invoicing with us!</p>
      <a href="index.php" class="btn-primary">Create Invoice</a>
      <a href="history.php" class="btn-secondary">View All Invoices</a>
    </div>
    <div class="dashboard-stats">
      <?php
        // fetch stats
        $stmt = $conn->prepare("SELECT COUNT(*) FROM invoices WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($totalInvoices);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($income);
        $stmt->fetch();
        $stmt->close();
      ?>
      <div class="stats-cards">
        <div class="stat-card">
          <p id="invoices-count" class="stat-value">0</p>
          <p class="stat-label">Total Invoices</p>
        </div>
        <div class="stat-card">
          <p id="income-count" class="stat-value">₹0.00</p>
          <p class="stat-label">Total Income</p>
        </div>
      </div>
    </div>
  </div>

  <!-- WHY CHOOSE US? (unchanged) -->
  <div class="firstSectionTwoLilac">
    <div class="container">
      <h1 class="sec2-h1">WHY CHOOSE US?</h1>
      <div class="row">
        <div class="col-md-4">
          <div class="firstSectionTwoInner">
            <img src="assets/img/love-icon-two.png" alt="">
            <div class="text">
              <h4>~5 lacs saving</h4>
              <p>from billing errors</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="firstSectionTwoInner">
            <img src="assets/img/currency-icon-two.png" alt="">
            <div class="text">
              <h4>3x reduction</h4>
              <p>in overdue payments</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="firstSectionTwoInner">
            <img src="assets/img/building-icon-two.png" alt="">
            <div class="text">
              <h4>65% faster</h4>
              <p>order processing</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

 <!-- ==== NEW Explainer Section ==== -->
<div class="explainer-section">
  <div class="explainer-text">
    <h2>Why use a free invoice generator to simplify your billings?</h2>
    <p>
      Free online invoice generators are particularly appealing for entrepreneurs, freelancers,
      and small businesses with limited budgets to create invoice free.
      Here are the key benefits of the free invoice generators.
    </p>
    <span class="read-more-toggle">Read More &gt;</span>
    <div class="read-more-content">
      <p><strong>What is an invoice generator?</strong><br>
      Invoice generator is an online tool designed to help businesses and individuals create professional invoices within minutes...
      </p>
      <!-- …and so on with all the text you provided… -->
      <p><strong>Conclusion:</strong> For modern businesses, online and AI‑powered invoice generators are game‑changers—time‑efficient, error‑free, and professional.</p>
    </div>
  </div>
  <div class="explainer-cards">
    <div class="explainer-card">
      <h3>Hassle‑free invoice maker</h3>
      <p>Generate unlimited invoices instantly at no cost and without any hassle.</p>
    </div>
    <div class="explainer-card">
      <h3>Go Paperless</h3>
      <p>Say goodbye to manual invoicing. Generate, store, and send invoices digitally.</p>
    </div>
    <div class="explainer-card">
      <h3>100% accurate, every time</h3>
      <p>Automated calculations ensure precise totals, taxes, and discounts.</p>
    </div>
    <div class="explainer-card">
      <h3>Simple & intuitive</h3>
      <p>Designed for ease of use—no training needed to create invoices.</p>
    </div>
  </div>
</div>



  <section id="faq" style="background-color: #f1f5ff; padding: 60px 20px; font-family: 'Poppins', sans-serif;">
    <div style="max-width: 900px; margin: auto;">
        <h2 style="text-align: center; font-size: 2rem; color: #4a63d9; margin-bottom: 40px;">Frequently Asked Questions</h2>

        <div class="faq-container">
            <div class="faq-item">
                <button class="faq-question">1. What is an invoice generator?</button>
                <div class="faq-answer">An invoice generator is a tool that helps you create professional invoices for your business or personal needs. You can customize details and download or share invoices quickly.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">2. Is the invoice generator free to use?</button>
                <div class="faq-answer">Yes, our basic invoice formats are free to use. You can also explore premium templates with a 7-day free trial using the Vyapar App.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">3. Can I save and edit invoices later?</button>
                <div class="faq-answer">Absolutely! You can create, save, and manage all your invoices through your dashboard. Editing existing invoices is also supported.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">4. What file formats can I download my invoice in?</button>
                <div class="faq-answer">You can download invoices in PDF format, which is ideal for printing, sharing, and record-keeping.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">5. Is my data secure with this invoice generator?</button>
                <div class="faq-answer">Yes, we ensure your data is stored securely and is never shared with third parties. Your privacy and security are our priority.</div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// animate counters
document.addEventListener("DOMContentLoaded", () => {
  // invoices counter
  const invTarget = <?php echo $totalInvoices; ?>;
  let inv = 0;
  const invElem = document.getElementById("invoices-count");
  const invInt = setInterval(() => {
    inv++;
    invElem.textContent = inv;
    if (inv >= invTarget) clearInterval(invInt);
  }, 40);

  // income counter
  const incTarget = <?php echo $income; ?>;
  let inc = 0;
  const incElem = document.getElementById("income-count");
  const incInt = setInterval(() => {
    inc += Math.ceil(incTarget / 100);
    if (inc > incTarget) inc = incTarget;
    incElem.textContent = "₹" + inc.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (inc >= incTarget) clearInterval(incInt);
  }, 20);
});
document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const answer = button.nextElementSibling;

        // Collapse all other answers
        document.querySelectorAll('.faq-answer').forEach(item => {
            if (item !== answer) item.style.display = 'none';
        });

        // Toggle current one
        if (answer.style.display === 'block') {
            answer.style.display = 'none';
        } else {
            answer.style.display = 'block';
        }
    });
});
</script>
</body>
</html>
