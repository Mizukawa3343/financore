<?php
session_start();
require_once "../config/dbconn.php";
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    if (empty($_POST["fee_id"])) {
        throw new Exception("Missing fee ID.");
    }

    $fee_id = $_POST["fee_id"];

    $sql = "
        SELECT
            COUNT(*) AS total_assigned_count,
            COUNT(CASE WHEN status IN ('partial', 'unpaid') THEN 1 END) AS outstanding_fee_count
        FROM student_fees
        WHERE fees_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fee_id]);
    $fee = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_assigned_count = (int) $fee["total_assigned_count"];
    $total_outstanding = (int) $fee["outstanding_fee_count"];

    if ($total_assigned_count === 0) {
        throw new Exception("Cannot mark as collected — this fee type is not assigned to any students.");
    }

    if ($total_outstanding > 0) {
        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Failed: there are still $total_outstanding students who have not yet fully paid."
        ];
        echo json_encode(["status" => false]);
        exit;
    }

    $update_sql = "UPDATE fees_type SET status = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt->execute([$fee_id])) {
        throw new Exception("Failed to update fee status.");
    }

    $_SESSION['toastr'] = [
        "type" => "success",
        "message" => "Successfully marked as collected."
    ];
    echo json_encode(["status" => true, "redirect" => "./fees.php"]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => $e->getMessage()]);
}
