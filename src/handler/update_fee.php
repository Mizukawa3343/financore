<?php
session_start();
require_once "../config/dbconn.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fee_name = $_POST["description"];
    $fee_amount = $_POST["amount"];
    $fee_due_date = $_POST["due_date"];
    $fee_id = $_POST["fee_id"];
    try {
        if (empty($fee_name) || empty($fee_amount) || empty($fee_due_date)) {
            $_SESSION['toastr'] = [
                "type" => "error",
                "message" => "All fields are required."
            ];
            echo json_encode(["status" => false]);
            exit;
        }

        $sql = "UPDATE fees_type SET description = ?, amount = ?, due_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$fee_name, $fee_amount, $fee_due_date, $fee_id])) {
            $_SESSION['toastr'] = [
                "type" => "success",
                "message" => "Fee info successfully updated."
            ];
            echo json_encode(["status" => true]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Failed to add fee."]);
        }
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => false, "message" => "Validation error: " . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}