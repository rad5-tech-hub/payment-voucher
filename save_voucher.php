<?php
// save_voucher.php
require 'db.php'; // include your database connection

function sanitize($input) {
  return htmlspecialchars(trim($input));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $voucherId = sanitize($_POST['voucherId']);
  $payee = ucwords(sanitize($_POST['payee']));
  $address = ucfirst(sanitize($_POST['address']));
  $cheque_no = sanitize($_POST['cheque_no']);
  $payment_type = ucfirst(sanitize($_POST['payment_type']));
  $amount_words = ucwords(sanitize($_POST['amount_words']));
  $prepared_by_name = ucwords(sanitize($_POST['prepared_by_name']));
  $prepared_by_signature = $_POST['prepared_by_signature']; // base64 image
  $preparer_email = strtolower(sanitize($_POST['preparer_email']));
  $approver_email = strtolower(sanitize($_POST['approver_email']));
  $receiver_email = strtolower(sanitize($_POST['receiver_email']));

  $descriptions = $_POST['description'];
  $amounts = $_POST['amount'];

  $items = [];
  $total = 0;
  for ($i = 0; $i < count($descriptions); $i++) {
    $items[] = [
      'desc' => sanitize(ucfirst($descriptions[$i])),
      'amt' => floatval($amounts[$i])
    ];
    $total += floatval($amounts[$i]);
  }
  $items_json = json_encode($items);

  // Save to DB
  $stmt = $pdo->prepare("INSERT INTO vouchers (voucher_id, payee, address, cheque_no, payment_type, amount_words, items, total_amount, prepared_by_name, prepared_by_signature, preparer_email, approver_email, receiver_email, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'prepared')");
  $stmt->execute([$voucherId, $payee, $address, $cheque_no, $payment_type, $amount_words, $items_json, $total, $prepared_by_name, $prepared_by_signature, $preparer_email, $approver_email, $receiver_email]);

  $messageApprover = `Hello,

  A Payment Voucher has been prepared and assigned to you as an Approver.

  <a href="$domain . 'sign.php?voucher_id=' . $voucher_id . '&role=approve'">Sign to Approve</a> `;
  $messageReceiver = `Hello,

  A Payment Voucher has been prepared and assigned to you as a Receiver.

  <a href="$domain . 'sign.php?voucher_id=' . $voucher_id . '&role=receive'">Sign to Receive</a> `;

  // Preferably use SMTP to send mail

  mail($approver_email, "Approve Payment Voucher: " . $voucherId, $messageApprover);
  mail($receiver_email, "Receive Payment Voucher: " . $voucherId, $messageReceiver);

  echo json_encode(['success' => true, 'id' => $voucherId]);
} else {
  echo json_encode(['error' => 'Invalid request']);
}
?>
