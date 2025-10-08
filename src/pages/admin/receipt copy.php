<?php
require_once "../../config/dbconn.php";
include_once "../../api/admin_query.php";

$student_id = $_GET["student_id"];
$receipt_id = $_GET["receipt_id"];
$transaction_id = $_GET["transaction_id"];

$student = get_students_data_for_receipt($conn, $student_id);
$transaction = get_transaction_data_for_receipt($conn, $transaction_id);
$student_fees_id = $transaction["student_fees_id"];
$receipt = get_receipt_number_for_receipt($conn, $receipt_id);
$fee = get_student_fee_details_for_receipt($conn, $student_id, $student_fees_id);
var_dump($fee);

// STUDENT INFO
$id = $student["student_id"]; //sample value 22-16908
$student_name = $student["student_name"]; //sample value John karl Bulalacao
$student_year = $student["student_year"]; //sample value 4
$student_course = $student["course"]; //sample value BSIT

// TRANSACTION INFO
$transaction_date = $transaction["date"]; // sample value 2025-10-4 Will work later to convert into more readable date
$amount_paid = $transaction["amount_paid"]; // sample value 500
$processed_by = $transaction["processed_by"]; // sample value Vanessa Cerdan

// RECEIPT NUMBER
$receipt_number = $receipt["receipt_number"]; // sample value CTC29A9293

// FEE INFO
$amount_due = $fee["amount_due"]; // sample value 1000
$current_balance = $fee["current_balance"]; // 500
$status = $fee["status"]; // sample value partial or paid

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt</title>
</head>

<body>
    <img src="/financore/assets/system-images/ctc-logo.png" alt="Ceguera Logo">
</body>

</html>