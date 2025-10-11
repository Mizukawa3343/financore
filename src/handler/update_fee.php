<?php
// session_start();
// require_once "../config/dbconn.php";
// header('Content-Type: application/json');

// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//     $fee_name = $_POST["description"];
//     $fee_amount = $_POST["amount"];
//     $fee_due_date = $_POST["due_date"];
//     $fee_id = $_POST["fee_id"];
//     try {
//         if (empty($fee_name) || empty($fee_amount) || empty($fee_due_date)) {
//             $_SESSION['toastr'] = [
//                 "type" => "error",
//                 "message" => "All fields are required."
//             ];
//             echo json_encode(["status" => false]);
//             exit;
//         }

//         $sql = "UPDATE fees_type SET description = ?, amount = ?, due_date = ? WHERE id = ?";
//         $stmt = $conn->prepare($sql);

//         if ($stmt->execute([$fee_name, $fee_amount, $fee_due_date, $fee_id])) {
//             $_SESSION['toastr'] = [
//                 "type" => "success",
//                 "message" => "Fee info successfully updated."
//             ];
//             echo json_encode(["status" => true]);
//             exit;
//         } else {
//             echo json_encode(["status" => false, "message" => "Failed to add fee."]);
//         }
//         exit;

//     } catch (Exception $e) {
//         echo json_encode(["status" => false, "message" => "Validation error: " . $e->getMessage()]);
//         exit;
//     }
// } else {
//     echo json_encode(["status" => "error", "message" => "Invalid request method."]);
// }


session_start();
require_once "../config/dbconn.php"; // Assuming $conn is your PDO connection object
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize inputs
    $fee_name = $_POST["description"];
    $fee_amount = (float) $_POST["amount"];
    $fee_due_date = $_POST["due_date"];
    $fee_id = $_POST["fee_id"];

    try {
        if (empty($fee_name) || empty($fee_amount) || empty($fee_due_date)) {
            $_SESSION['toastr'] = ["type" => "error", "message" => "All fields are required."];
            echo json_encode(["status" => false]);
            exit;
        }

        // --- 1. START THE TRANSACTION ---
        $conn->beginTransaction();

        // 2. UPDATE the fees_type template
        $sql_type_update = "UPDATE fees_type SET description = ?, amount = ?, due_date = ? WHERE id = ?";
        $stmt_type = $conn->prepare($sql_type_update);
        $stmt_type->execute([$fee_name, $fee_amount, $fee_due_date, $fee_id]);

        // 3. PROPAGATE the amount change to student_fees (Only for UNPAID/PARTIAL students)
        // new_balance = old_balance + (new_amount - old_amount_due)
        // new_amount_due = new_amount

        $sql_fees_update = "
            UPDATE student_fees sf
            SET 
                sf.current_balance = sf.current_balance + (? - sf.amount_due),
                sf.amount_due = ?
            WHERE
                sf.fees_id = ?
                AND sf.status IN ('unpaid', 'partial');
        ";

        $stmt_fees = $conn->prepare($sql_fees_update);
        // Parameters: [new_amount], [new_amount], [fee_id]
        $stmt_fees->execute([$fee_amount, $fee_amount, $fee_id]);

        // --- 4. COMMIT THE TRANSACTION ---
        // If we reached this point without an exception, both updates succeeded.
        $conn->commit();

        $_SESSION['toastr'] = [
            "type" => "success",
            "message" => "Fee info and student balances successfully updated."
        ];
        echo json_encode(["status" => true]);

    } catch (PDOException $e) {
        // --- 5. ROLLBACK ON FAILURE ---
        // If any query failed, or an exception was thrown, revert all changes.
        $conn->rollback();

        // Log the actual error for debugging
        error_log("Fee Update Failed: " . $e->getMessage());

        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "An error occurred. No changes were saved. (Error: " . $e->getCode() . ")"
        ];
        echo json_encode(["status" => false, "message" => "Database error."]);

    } catch (Exception $e) {
        // Handle non-PDO exceptions (e.g., general PHP errors)
        echo json_encode(["status" => false, "message" => "Application error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>