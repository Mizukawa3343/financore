<?php
session_start();
require_once "../config/dbconn.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $department_id = $_SESSION['department_id'];

    try {
        if (empty($description) || empty($amount) || empty($due_date)) {
            $_SESSION['toastr'] = [
                "type" => "error",
                "message" => "All fields are required."
            ];
            echo json_encode(["status" => false]);
            exit;
        }

        $sql = "INSERT INTO fees_type (description, amount, due_date, department_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$description, $amount, $due_date, $department_id])) {
            $_SESSION['toastr'] = [
                "type" => "success",
                "message" => "Fee successfully added."
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