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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Official Receipt - <?= $receipt_number ?></title>

    <style>
        /* ---------- BASE STYLES (SCREEN) ---------- */
        body {
            background-color: #ffffff;
            font-family: 'Inter', monospace;
            font-size: 10px;
            color: #000;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 80mm;
            padding: 5mm;
            margin: 20px auto;
            background-color: #fff;
            border: 1px dashed #000;
            filter: grayscale(100%);
            line-height: 1.2;
            box-sizing: border-box;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .strong {
            font-weight: bold;
        }

        .large {
            font-size: 11px;
        }

        .header img {
            max-width: 20mm;
            height: auto;
            margin-bottom: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .header h1 {
            font-size: 12px;
            margin: 0;
            line-height: 1.1;
        }

        .header p {
            margin: 0;
            font-size: 9px;
            line-height: 1.1;
        }

        .detail-row,
        .fee-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            word-break: break-all;
        }

        .detail-key {
            flex: 0 0 40%;
            white-space: nowrap;
        }

        .detail-value {
            flex: 1;
            text-align: right;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 5px 0 10px 0;
            line-height: 1.4;
        }

        .disclaimer {
            font-style: italic;
            margin-top: 10px;
            font-size: 9px;
            text-transform: none;
        }
    </style>

    <!-- ---------- STRONG PRINT RULES (separate block) ---------- -->
    <style media="print">
        /* 1. Page & Paper Setup */
        @page {
            /* Set the width and use auto height */
            size: 80mm auto;
            /* Crucial: remove default browser print margins */
            margin: 0;
        }

        /* 2. Body/HTML Reset */
        html,
        body {
            /* Force the content width to match the paper */
            width: 80mm !important;
            /* Allow height to flow naturally */
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            /* Ensure colors (especially black) print correctly */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            background: #fff !important;
            /* Ensure font size consistency in print */
            font-size: 10px !important;
        }

        /* 3. Receipt Container Focus */
        .receipt-container {
            /* Remove the screen's dashed border and grayscale filter */
            border: none !important;
            box-shadow: none !important;
            filter: none !important;
            /* Keep this to ensure the *container* isn't gray */

            /* Apply the *exact* print width an
d            a minimal padding for content */
            width: 80mm !important;
            padding: 2mm 3mm !importan t;
            /* Slightly less padding for full content visibility */
            margin: 0 !important;

            /* Ensure the box model is correct */
            box-sizing: border-box !important;

            /* Prevent a page break from cutting through the middle of the receipt */
            page-break-inside: avoid !important;

            /* Overwrite the screen styles on print */
            position: relative !important;
            left: 0 !important;
            top: 0 !important;
        }

        /* 4. Color & Detail Enforcement */
        * {
            /* Force all elements to print their colors/borders/backgrounds exactly */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            color: #000 !important;
        }


        /* Ensure the dashed lines/borders print black and dashed */
        .divider,
        .summary-box {
            border-color: #000 !important;
        }

        /* 5. Hide the screen-only wrapper border */
        body {
            /* Re-apply background color */
            background-color: #ffffff !important;
        }

        /* 6. Logo & Text Adjustments */
        .header img {
            max-width: 25mm !important;
            margin: 0 auto 10px auto !important;
            /* --- NEW: Make the logo grayscale for print --- */
            filter: grayscale(100%) !important;
        }

        /* Ensure print text size stays correct */
        .large {
            font-size: 11px !important;
        }

        /* Reset screen margin to 0 for print container. */
        .receipt-container {
            margin: 0 auto !important;
        }
    </style>

</head>

<body>

    <div class="receipt-container">
        <div class="header text-center">
            <img src="/financore/assets/system-images/ctc-logo.png" alt="Ceguera Logo">
            <h1 class="strong">CEGUERA TECHNOLOGICAL COLLEGES, INC.</h1>
            <p>Highway 1, Francia 4431, Iriga City, Camarines Sur</p>
        </div>

        <div class="divider"></div>
        <div class="text-center strong large" style="margin:5px 0;">Official Receipt</div>
        <div class="divider"></div>

        <div class="details">
            <div class="detail-row"><span class="detail-key strong">No.</span><span
                    class="detail-value"><?= $receipt_number ?></span></div>
            <div class="detail-row"><span class="detail-key strong">Date:</span><span
                    class="detail-value"><?= $formatted_date ?></span></div>
            <div class="detail-row"><span class="detail-key strong">ID:</span><span
                    class="detail-value"><?= $id ?></span></div>
            <div class="detail-row"><span class="detail-key strong">Student:</span><span
                    class="detail-value"><?= $student_name ?></span></div>
            <div class="detail-row"><span class="detail-key strong">Course / Year:</span><span
                    class="detail-value"><?= $student_course ?> - <?= $student_year ?></span></div>
        </div>

        <div class="divider"></div>

        <div class="fee-details">
            <div class="detail-row strong">
                <span class="detail-key">Description</span>
                <span class="detail-value">Amount Paid</span>
            </div>
            <div class="divider"></div>

            <div class="detail-row">
                <span class="detail-key" style="text-transform:none;"><?= $fee_name ?></span>
                <span class="detail-value">P<?= number_format($amount_paid, 2) ?></span>
            </div>

            <div class="divider"></div>

            <div class="detail-row strong large" style="margin-top:5px;">
                <span class="detail-key">TOTAL PAID</span>
                <span class="detail-value">P<?= number_format($amount_paid, 2) ?></span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="text-center strong large" style="margin:8px 0;">
            *** <?= $amount_paid_in_words ?> ***
        </div>

        <div class="summary-box">
            <div class="text-center strong large" style="margin-bottom:5px;">Fee Summary</div>
            <div class="detail-row">
                <span class="detail-key">Amount Due:</span>
                <span class="detail-value">P<?= number_format($amount_due, 2) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Current Balance:</span>
                <span class="detail-value strong">P<?= number_format($current_balance, 2) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Payment Status:</span>
                <span class="detail-value strong">
                    <?= $status ?>
                </span>
            </div>
        </div>

        <div class="divider"></div>
        <div class="received-by text-center" style="margin-top:15px;">
            <span class="strong">Received By: <?= $processed_by ?></span>
        </div>

        <div class="text-center disclaimer">
            This is a system-generated receipt. Thank you for your payment.
        </div>

        <div class="divider" style="margin-top:15px;"></div>
        <div class="text-center" style="font-size:8px;">-- END OF RECEIPT --</div>
    </div>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>