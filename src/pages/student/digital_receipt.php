<?php
require_once "../../config/dbconn.php";
include_once "../../api/admin_query.php";
include_once "../../util/helper.php";

$student_id = $_GET["student_id"];
$receipt_id = $_GET["receipt_id"];
$transaction_id = $_GET["transaction_id"];

$student = get_students_data_for_receipt($conn, $student_id);
$transaction = get_transaction_data_for_receipt($conn, $transaction_id);
$student_fees_id = $transaction["student_fees_id"];
$receipt = get_receipt_number_for_receipt($conn, $receipt_id);
$fee = get_student_fee_details_for_receipt($conn, $student_id, $student_fees_id);

// STUDENT INFO
$id = $student["student_id"];
$student_name = $student["student_name"];
$student_year = $student["student_year"];
$student_course = $student["course"];

// TRANSACTION INFO
$transaction_date = $transaction["date"];
$amount_paid = $transaction["amount_paid"];
$processed_by = $transaction["processed_by"];

// RECEIPT NUMBER
$receipt_number = $receipt["receipt_number"];

// FEE INFO
$amount_due = $fee["amount_due"];
$current_balance = $fee["current_balance"];
$status = $fee["status"];
$fee_name = $fee["fee_description"];


function convert_number_to_words($number)
{
    $words = array(
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    );
    $digits = array('', 'Thousand', 'Million', 'Billion', 'Trillion');

    $number = number_format($number, 2, '.', '');
    $parts = explode('.', $number);
    $integerPart = (int) $parts[0];
    $decimalPart = (int) $parts[1];

    $wordsResult = '';
    if ($integerPart === 0) {
        $wordsResult = 'Zero';
    } else {
        $i = 0;
        $temp = $integerPart;
        while ($temp > 0) {
            $chunk = $temp % 1000;
            if ($chunk > 0) {
                $chunkWords = '';
                $hundreds = floor($chunk / 100);
                $tens = $chunk % 100;

                if ($hundreds > 0) {
                    $chunkWords .= $words[$hundreds] . ' Hundred';
                    if ($tens > 0)
                        $chunkWords .= ' ';
                }

                if ($tens > 0) {
                    if ($tens < 20) {
                        $chunkWords .= $words[$tens];
                    } else {
                        $chunkWords .= $words[floor($tens / 10) * 10];
                        if ($tens % 10 > 0)
                            $chunkWords .= ' ' . $words[$tens % 10];
                    }
                }
                $wordsResult = trim($chunkWords) . ' ' . $digits[$i] . ' ' . $wordsResult;
            }
            $temp = floor($temp / 1000);
            $i++;
        }
    }

    $finalWords = trim($wordsResult) . ' PESOS';
    if ($decimalPart > 0) {
        $centavos = $decimalPart;
        $centavosWords = ($centavos < 20) ? $words[$centavos] : $words[floor($centavos / 10) * 10] . (($centavos % 10 > 0) ? ' ' . $words[$centavos % 10] : '');
        $finalWords .= ' AND ' . trim($centavosWords) . ' CENTAVOS ONLY';
    } else {
        $finalWords .= ' ONLY';
    }

    return strtoupper($finalWords);
}

function formatTransactionDate($dateString)
{
    $timestamp = strtotime($dateString);
    return date("M d, Y h:i A", $timestamp);
}

$formatted_date = formatTransactionDate($transaction_date);
$amount_paid_in_words = convert_number_to_words($amount_paid);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/financore/assets/css/digital_receipt.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="receipt">
        <header class="header">
            <a class="close" href="./overview.php">
                <i class="bi bi-x-lg"></i>
            </a>
            <h2><?= $fee_name ?></h2>
        </header>
        <main class="main">
            <div class="check">
                <i class="bi bi-check2"></i>
            </div>
            <div class="info">
                <h2 class="name"><?= $student_name ?></h2>
                <p class="id"><?= $id ?></p>
                <small>CASH</small>
            </div>

            <div class="payment-info">
                <div class="info-card">
                    <h3>Amount Due</h3>
                    <p>₱<?= number_format($amount_due, 2) ?></p>
                </div>
                <div class="info-card">
                    <h3>Amount Paid</h3>
                    <p>₱<?= number_format($amount_paid, 2) ?></p>
                </div>
                <div class="info-card">
                    <h3>Balance</h3>
                    <p>₱<?= number_format($current_balance, 2) ?></p>
                </div>
                <div class="info-card">
                    <h3>Status</h3>
                    <span class="fee-status <?= $status ?>"><?= $status ?></span>
                </div>
                <p class=" word-amount">**<?= $amount_paid_in_words ?>** <br> <small style="font-weight: 500">Total
                        amount paid</small></p>

            </div>

            <div class="receipt-ref">
                <span><strong>Receipt No: </strong> <?= $receipt_number ?></span>
                <span><?= format_readable_datetime($transaction_date) ?></span>
            </div>

            <div class="footer-note">
                <p>This system-generated digital receipt serves as proof of payment.</p>
            </div>
        </main>
    </div>

</body>

</html>