<?php
session_start();
require_once "../config/dbconn.php";
include_once "../api/admin_query.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- 1. Collect and Sanitize Input ---
    $action = $_POST["action"] ?? null;
    $year = $_POST["year"] ?? null;
    $course_id = $_POST["course"] ?? null; // Use course_id based on schema
    $fee_id = $_POST["fee_id"] ?? null;
    $department_id = $_SESSION["department_id"] ?? null;

    $fee_name = get_fee_by_id($conn, $fee_id)["description"];

    // --- 2. Basic Validation ---
    if (empty($action) || empty($fee_id) || empty($department_id)) {
        echo json_encode(["status" => false, "message" => "Missing required fields for fee assignment (action, fee ID, or department ID)."]);
        exit;
    }

    $assigned_count = 0;
    $success = false;
    $message = "No students matched the criteria.";

    try {
        // --- 3. Execute Assignment based on Action ---
        switch ($action) {
            case "all":
                // Assign to all students in the department
                $assigned_count = assign_fee_to_all_students($conn, $fee_id, $department_id);
                break;

            case "year":
                // Assign to all students in a specific year and department
                if (empty($year)) {
                    throw new Exception("Year is required for this action.");
                }
                $assigned_count = assign_fee_to_all_students_by_year($conn, $fee_id, $department_id, $year);
                break;

            case "course":
                // Assign to all students in a specific course and department
                if (empty($course_id)) {
                    throw new Exception("Course is required for this action.");
                }
                $assigned_count = assign_fee_to_all_students_by_course($conn, $fee_id, $department_id, $course_id);
                break;

            case "year_course":
                // Assign to all students in a specific year, course, and department
                if (empty($year) || empty($course_id)) {
                    throw new Exception("Year and Course are required for this action.");
                }
                $assigned_count = assign_fee_to_all_students_by_year_and_course($conn, $fee_id, $department_id, $year, $course_id);
                break;

            default:
                throw new Exception("Invalid assignment action specified.");
        }

        // --- 4. Prepare Final Response ---
        if ($assigned_count > 0) {
            $success = true;
            $message = "Fee successfully assigned to **{$assigned_count}** student(s).";

            $date = (new DateTime())->format('Y-m-d H:i:s');

            $logsql = "INSERT INTO logs (action, user_id, department_id, date) VALUES (?, ?, ?, ?)";
            $logs_stmt = $conn->prepare($logsql);
            $logs_stmt->execute([
                "Assign fee: $fee_name to $assigned_count students",
                $_SESSION["user_id"],
                $_SESSION["department_id"],
                $date
            ]);
        } else {
            $message = "Fee assignment query executed, but **0** new fees were assigned. They may already be assigned or no students matched.";
        }

        $_SESSION['toastr'] = [
            "type" => $success ? "success" : "warning",
            "message" => $message
        ];

        echo json_encode(["status" => $success, "count" => $assigned_count]);
        exit;

    } catch (Exception $e) {
        // Handle validation or database execution errors
        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Assignment Error: " . $e->getMessage()
        ];
        echo json_encode(["status" => false, "message" => "Validation error: " . $e->getMessage()]);
        exit;
    }


} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}