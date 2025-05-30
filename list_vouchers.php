<?php
// Database connection
require('db.php');

// Handle deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
  $deleteId = $_GET['delete'];
  $delStmt = $pdo->prepare("UPDATE vouchers SET deleted_at = NOW() WHERE voucher_id = :id");
  $delStmt->execute([':id' => $deleteId]);

  require 'send_email.php';

  // Query the voucher
  $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE voucher_id = :voucher_id");
  $stmt->execute(['voucher_id' => $deleteId]);
  $voucher = $stmt->fetch();

  $message = "Hello,<br><br>

  Payment Voucher - " . $voucher['voucher_id'] . " has been cancelled, no further action is required";

  // Send Email Alert using SMTP
  $emails = $voucher['preparer_email'] . "," . $voucher['approver_email'] . "," . $voucher['receiver_email'];
  $resp = sendSMTPMail($host, $username, $password, $emails, "Payment Voucher: " . $voucher['voucher_id'] . " Cancelled", $message);
  if (gettype($resp) != "boolean") {
      echo json_encode(['error' => $resp]);
      exit();
  }
  // header("Location: list_vouchers.php?msg=deleted");
}

// Fetch vouchers grouped by month
$stmt = $pdo->prepare("
SELECT 
    id, voucher_id, payee, address, payment_type, total_amount, prepared_by_name, approved_by_name, received_by_name, created_at,
    DATE_FORMAT(created_at, '%M %Y') AS month_year
FROM vouchers WHERE deleted_at IS NULL
ORDER BY created_at DESC
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$vouchers_by_month = [];
foreach ($results as $row) {
    $vouchers_by_month[$row['month_year']][] = $row;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Voucher List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }

    h2 {
      color: #004080;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background: #004080;
      color: white;
    }

    a {
      color: #004080;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>

  <script>
      function confirmDelete(voucherId) {
        if (confirm("Are you sure you want to cancel this voucher?")) {
          window.location.href = "list_vouchers.php?delete=" + voucherId;
        }
      }
  </script>
</head>
<body>

<h1>Payment Vouchers</h1>

<?php if (!empty($vouchers_by_month)): ?>
  <?php 
    $sno = 0;
    foreach ($vouchers_by_month as $month => $vouchers): 
    ?>
    <h2><?= htmlspecialchars($month) ?></h2>
    <table>
      <thead>
        <tr>
          <th>S/No</th>
          <th>Date</th>
          <th>Voucher ID</th>
          <th>Amount</th>
          <th>Payee Name</th>
          <th>Prepared By</th>
          <th>Approver</th>
          <th>Receiver</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vouchers as $voucher): $sno++ ?>
          <tr>
            <td><?=$sno?></td>
            <td><?= date('d M Y', strtotime($voucher['created_at'])) ?></td>
            <td><?= htmlspecialchars($voucher['voucher_id']) ?></td>
            <td style="text-align: right;"><?=number_format($voucher['total_amount'])?></td>
            <td><?= htmlspecialchars($voucher['payee']) ?></td>
            <td><?= htmlspecialchars($voucher['prepared_by_name']) ?></td>
            <td><?= htmlspecialchars($voucher['approved_by_name']) ?></td>
            <td><?= htmlspecialchars($voucher['received_by_name']) ?></td>
            <td>
              <a href="view.php?voucher_id=<?= urlencode($voucher['voucher_id']) ?>" target="_blank">View</a> |
              <a href="javascript:void(0);" class="delete-btn" onclick="confirmDelete('<?= $voucher['voucher_id'] ?>')">Cancel</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
<?php else: ?>
  <p>No vouchers found.</p>
<?php endif; ?>

</body>
</html>
</html>
