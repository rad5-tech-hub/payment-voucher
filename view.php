<?php
// Database connection
require('db.php');

// Fetch voucher ID from query
$voucher_id = $_GET['voucher_id'] ?? '';
if (!$voucher_id) {
  die("No voucher ID provided.");
}

// Query the voucher
$stmt = $pdo->prepare("SELECT * FROM vouchers WHERE voucher_id = :voucher_id");
$stmt->execute(['voucher_id' => $voucher_id]);
$voucher = $stmt->fetch();

if (!$voucher) {
  die("Voucher not found.");
}

// Decode items
$items = json_decode($voucher['items'], true);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Voucher: <?= htmlspecialchars($voucher['voucher_id']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    .voucher {
      border: 2px solid #004080; padding: 20px; max-width: 900px; margin: auto;
    }
    .header {
      display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;
    }
    .logo-container { width: 150px; height: 100px; border: 1px dashed #999; overflow: hidden; }
    .logo-container img { width: 100%; height: 100%; object-fit: contain; }
    .title {
      text-align: center; font-size: 22px; font-weight: bold;
      color: #004080; margin: 15px 0; flex-basis: 100%;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table, th, td { border: 1px solid #004080; }
    th, td { padding: 10px; text-align: left; }
    .signature-section {
      display: flex; justify-content: space-between; margin-top: 30px;
    }
    .signature-box { width: 30%; text-align: center; }
    .signature-label { font-weight: bold; margin-bottom: 10px; }
    .signature-image {
      border-top: 1px solid #000; margin-top: 10px; height: 60px;
    }
    .print-btn {
      display: inline-block; margin: 20px 10px 0 0; padding: 10px 20px;
      background: #004080; color: white; border: none; cursor: pointer;
      font-size: 14px;
    }
    @media print {
      .print-btn { display: none; }
    }
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }

    .voucher {
      border: 2px solid #004080;
      padding: 20px;
      max-width: 900px;
      margin: auto;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .logo-container {
      width: 150px;
      height: 100px;
      border: 1px dashed #999;
      position: relative;
      overflow: hidden;
    }

    .logo-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .logo-container input {
      position: absolute;
      top: 0;
      left: 0;
      width: 150px;
      height: 100px;
      opacity: 0;
      cursor: pointer;
    }

    .title {
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      color: #004080;
      margin: 15px 0;
      flex-basis: 100%;
    }

    .editable {
      border-bottom: 1px dotted #000;
      min-width: 60px;
      display: inline-block;
      padding: 3px;
    }

    .grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 10px;
      margin: 10px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table, th, td {
      border: 1px solid #004080;
    }

    th, td {
      padding: 10px;
      text-align: left;
    }

    .section-title {
      margin-top: 20px;
      font-weight: bold;
    }

    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }

    .signature-box {
      width: 30%;
      text-align: center;
    }

    .signature {
        margin-top: 10px;
    }

    .signature-label {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .print-btn, .action-btn {
      display: inline-block;
      margin: 20px 10px 0 0;
      padding: 10px 20px;
      background: #004080;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    @media print {
        .print-btn, .action-btn {
            display: none !important;
        }
      .logo-container input {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="voucher">
    <div class="header">
      <div class="logo-container">
        <img id="logoImage" src="./rad5hub.png" alt="Company Logo">
        <input type="file" accept="image/*" onchange="uploadLogo(this)">
      </div>
      <div>
        <strong>RAD5 Tech Hub</strong><br>
        3rd Floor, 7 Factory Road,<br>
        Aba, Abia State, Nigeria.<br>
        +234 706 434 3189, +234 818 8155 501<br>
        info@rad5.com.ng<br>
        <a href="https://rad5.com.ng">https://rad5.com.ng</a>
      </div>
      <div style="text-align:right;">
        <strong>Voucher No:</strong> <?= htmlspecialchars($voucher['voucher_id']) ?><br>
        <strong>Date:</strong> <?= date('F j, Y', strtotime($voucher['created_at'])) ?>
      </div>
    </div>


    <div class="title">PAYMENT VOUCHER</div>

    <div class="grid">
      <div>
        Name of Payee: <span><?=$voucher['payee']?></span><br><br>
        Address: <span><?=$voucher['address']?></span>
      </div>
      <div>
        Payment Type: <span><?=$voucher['payment_type'] . (strtolower($voucher['payment_type']) == 'cheque' ? " - " . $voucher['cheque_no'] : "") ?></span><br>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Description</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($item['desc']) ?></td>
            <td>₦<?= number_format($item['amt'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="section-title">Amount in Words:</div><br>
    <div><?=$voucher['amount_words']?></div>

    <div class="signature-section">
      <div class="signature-box">
        <div class="signature-label">Prepared By</div>
        <div><?= htmlspecialchars($voucher['prepared_by_name']) ?></div>
        <?php if ($voucher['prepared_by_signature']): ?>
          <div class="signature-image">
            <img src="<?= $voucher['prepared_by_signature'] ?>" alt="Signature" height="60">
          </div>
        <?php endif; ?>
      </div>

      <div class="signature-box">
        <div class="signature-label">Approved By</div>
        <div><?= htmlspecialchars($voucher['approved_by_name']) ?></div>
        <?php if ($voucher['approved_by_signature']): ?>
          <div class="signature-image">
            <img src="<?= $voucher['approved_by_signature'] ?>" alt="Signature" height="60">
          </div>
        <?php endif; ?>
      </div>

      <div class="signature-box">
        <div class="signature-label">Received By</div>
        <div><?= htmlspecialchars($voucher['received_by_name']) ?></div>
        <?php if ($voucher['received_by_signature']): ?>
          <div class="signature-image">
            <img src="<?= $voucher['received_by_signature'] ?>" alt="Signature" height="60">
          </div>
        <?php endif; ?>
      </div>
    </div>

    <button class="print-btn" onclick="window.print()">Print Voucher</button>
  </div>
</body>
</html>
