<?php
session_start();
date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines
require_once "../config/dbconn.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- 1. Collect and Validate Input ---
    $student_id = $_POST["student_id"] ?? null;
    $student_fees_row_id = $_POST["fees_id"] ?? null; // This is the ID from the student_fees table
    $amount_paid = $_POST["amount"] ?? null;
    $payment_method = $_POST["payment_method"] ?? 'Cash';
    $user_id = $_SESSION["user_id"] ?? 1; // Assuming an admin is logged in (use actual session user ID)
    $transaction_date = new DateTime();
    $philippines_time_string = $transaction_date->format('Y-m-d H:i:s');
    $department_id = $_SESSION["department_id"];

    if (empty($student_id) || empty($student_fees_row_id) || !is_numeric($amount_paid) || $amount_paid <= 0) {
        echo json_encode(["status" => false, "message" => "Invalid payment data."]);
        exit;
    }

    $receiptNumber = strtoupper(uniqid('CTC'));
    $conn->beginTransaction(); // START TRANSACTION

    try {

        // --- STEP 1: INSERT into payment_transaction (Set receipt_id to NULL initially) ---
        $transaction_sql = "INSERT INTO payment_transaction (
            student_id, student_fees_id, amount_paid, payment_method, recorded_by_user_id, transaction_date, department_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $transaction_stmt = $conn->prepare($transaction_sql);
        $transaction_stmt->execute([
            $student_id,
            $student_fees_row_id,
            $amount_paid,
            $payment_method,
            $user_id,
            $philippines_time_string,
            $department_id
        ]);

        $new_transaction_id = $conn->lastInsertId();


        // --- STEP 2: INSERT into receipts (Use the new_transaction_id) ---
        $receipt_sql = "INSERT INTO receipts (
            receipt_number, transaction_id, generated_at
        ) VALUES (?, ?, ?)";

        $receipt_stmt = $conn->prepare($receipt_sql);
        $receipt_stmt->execute([
            $receiptNumber,
            $new_transaction_id, // Use the ID generated in STEP 1
            $philippines_time_string
        ]);

        $new_receipt_id = $conn->lastInsertId();


        // --- STEP 3: UPDATE payment_transaction (Complete the Circular Dependency) ---
        $update_transaction_sql = "UPDATE payment_transaction SET receipt_id = ? WHERE id = ?";
        $update_transaction_stmt = $conn->prepare($update_transaction_sql);
        $update_transaction_stmt->execute([$new_receipt_id, $new_transaction_id]);


        // --- STEP 4: UPDATE student_fees (The Ledger Update) ---
        $status;
        $student_fees = "SELECT * FROM student_fees WHERE student_id = ?";
        $sf_stmt = $conn->prepare($student_fees);
        $sf_stmt->execute([$student_id]);
        $sf = $sf_stmt->fetch(PDO::FETCH_ASSOC);
        if ($sf["current_balance"] == $amount_paid) {
            $status = "paid";
            $update_student_fees_sql = "UPDATE student_fees SET current_balance = ?, status = ? WHERE student_id = ?";
            $update_student_fees_stmt = $conn->prepare($update_student_fees_sql);
            $update_student_fees_stmt->execute([0, $status, $student_id]);
        } else {
            $status = "partial";
            $new_balance = $sf["current_balance"] - $amount_paid;
            $update_student_fees_sql = "UPDATE student_fees SET current_balance = ?, status = ? WHERE student_id = ?";
            $update_student_fees_stmt = $conn->prepare($update_student_fees_sql);
            $update_student_fees_stmt->execute([$new_balance, $status, $student_id]);
        }
        // --- STEP 4: UPDATE student_fees (The Ledger Update) ---
//         $update_fees_sql = "
//     UPDATE student_fees
//     SET 
//         -- Perform the subtraction and update the balance
//         current_balance = current_balance - ?,

        //         -- Check if the remaining balance is functionally zero
//         status = CASE
//             -- We check if the remaining balance is less than 0.01 (a small tolerance)
//             WHEN current_balance - ? < 0.01 THEN 'paid'
//             ELSE 'partial'
//         END
//     WHERE
//         id = ?
// ";

        //         $update_fees_stmt = $conn->prepare($update_fees_sql);
//         // We still bind the amount_paid twice
//         $update_fees_stmt->execute([$amount_paid, $amount_paid, $student_fees_row_id]);

        // ... rest of your code ...


        // --- 5. Finalize ---
        $conn->commit(); // COMMIT TRANSACTION

        $_SESSION['toastr'] = [
            "type" => "success",
            "message" => "Payment successfully recorded. Receipt No: {$receiptNumber}"
        ];
        echo json_encode(["status" => true, "receipt_id" => $new_receipt_id, "student_id" => $student_id, "transaction_id" => $new_transaction_id]);
        exit;

    } catch (Exception $e) {
        $conn->rollBack(); // ROLLBACK TRANSACTION on error

        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Payment failed: " . $e->getMessage()
        ];
        echo json_encode(["status" => false, "message" => "Payment error: " . $e->getMessage()]);
        exit;
    }


} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}