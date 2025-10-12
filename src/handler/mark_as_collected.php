<?php
session_start();
require_once "../config/dbconn.php";
include_once "../api/admin_query.php";
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    if (empty($_POST["fee_id"])) {
        throw new Exception("Missing fee ID.");
    }

    $fee_id = $_POST["fee_id"];
    $description = get_fee_by_id($conn, $fee_id)["description"];

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
        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Cannot mark as collected — this fee type is not assigned to any students."
        ];
        echo json_encode(["status" => false]);
        exit;

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

    $date = (new DateTime())->format('Y-m-d H:i:s');

    $logsql = "INSERT INTO logs (action, user_id, department_id, date) VALUES (?, ?, ?, ?)";
    $logs_stmt = $conn->prepare($logsql);
    $logs_stmt->execute([
        "Marked fee as collected: $description",
        $_SESSION["user_id"],
        $_SESSION["department_id"],
        $date
    ]);

    echo json_encode(["status" => true, "redirect" => "./fees.php"]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => $e->getMessage()]);
}
