<?php
require 'db.php'; // include your database connection

function sanitize($input) {
  return htmlspecialchars(trim($input));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucherId = sanitize($_POST['voucherId']);
    $role = sanitize($_POST['role']);
    $name = ucwords(sanitize($_POST['by_name']));
    $signature = ($_POST['by_signature']);

    // Query the voucher
    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE voucher_id = :voucher_id");
    $stmt->execute(['voucher_id' => $voucherId]);
    $voucher = $stmt->fetch();

    if (!$voucher) {
    die("Voucher not found.");
    }

    // Decode items
    $items = json_decode($voucher['items'], true);

    if ($role == 'approve') {
        $stmt = $pdo->prepare("UPDATE vouchers SET approved_by_name = :name, approved_by_signature = :signature, status = :status WHERE voucher_id = :voucher_id");
        $stmt->execute(['name' => $name, 'signature' => $signature, 'status' => 'approved', 'voucher_id' => $voucherId]);
    } else {
        $stmt = $pdo->prepare("UPDATE vouchers SET received_by_name = :name, received_by_signature = :signature, status = :status WHERE voucher_id = :voucher_id");
        $stmt->execute(['name' => $name, 'signature' => $signature, 'status' => 'received', 'voucher_id' => $voucherId]);
    }

    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE voucher_id = :voucher_id");
    $stmt->execute(['voucher_id' => $voucherId]);
    $voucher = $stmt->fetch();

    // check if all have signed
    if ($voucher['prepared_by_signature'] and $voucher['approved_by_signature'] and $voucher['received_by_signature']) {
        $message = "Hello,<br><br>

        All Signatories have completed the signing of the Payment Voucher - " . $voucher['voucher_id'] . ".<br><br>

        <a href='" . $domain . "view.php?voucher_id=" . $voucherId . "'>View and Print Voucher</a>";

        // Preferably use SMTP to send mail
        $emails = $voucher['preparer_email'] . ", " . $voucher['approver_email'] . ", " . $voucher['receiver_email'];
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: RAD5 Tech Hub <no-reply@rad5.com.ng>";
        mail($emails, "Payment Voucher: " . $voucher['voucher_id'] . " Completed", $message, $headers);
    }

    echo json_encode(['success' => true, 'id' => $voucherId]);
}
