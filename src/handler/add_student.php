<?php
session_start();
require_once "../config/dbconn.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST["student_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $course = $_POST["course"];
    $year = $_POST["year"];
    $gender = $_POST["gender"];
    $department_id = $_SESSION['department_id'];
    $current_year = date('Y');

    // Calculate the next year (e.g., 2026)
    $next_year = $current_year + 1;

    // Format the school year string
    $school_year = "{$current_year}-{$next_year}";



    try {
        if (empty($student_id) || empty($first_name) || empty($last_name) || empty($course) || empty($year) || empty($gender)) {
            echo json_encode(["status" => false, "message" => "All fields are required."]);
            exit;
        }

        $checkSql = "SELECT COUNT(*) FROM students WHERE student_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$student_id]);
        if ($checkStmt->fetchColumn() > 0) {
            $_SESSION['toastr'] = [
                "type" => "error",
                "message" => "Student ID already exists."
            ];
            echo json_encode([
                "status" => false

            ]);
            exit;
        }

        $sql = "INSERT INTO students (student_id, first_name, last_name, course, year, gender, department_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$student_id, $first_name, $last_name, $course, $year, $gender, $department_id])) {
            $new_student_row_id = $conn->lastInsertId();
            $enrollment_sql = "INSERT INTO enrollments (student_id, course_id, school_year) VALUES (?, ?, ?)";
            $enrollment_stmt = $conn->prepare($enrollment_sql);
            $enrollment_stmt->execute([$new_student_row_id, $course, $school_year]);

            $date = (new DateTime())->format('Y-m-d H:i:s');

            $logsql = "INSERT INTO logs (action, user_id, department_id, date) VALUES (?, ?, ?, ?)";
            $logs_stmt = $conn->prepare($logsql);
            $logs_stmt->execute([
                "Added new student: $first_name $last_name",
                $_SESSION["user_id"],
                $_SESSION["department_id"],
                $date
            ]);


            $_SESSION['toastr'] = [
                "type" => "success",
                "message" => "Student successfully added."
            ];
            echo json_encode(["status" => true]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Failed to add student."]);
        }
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => false, "message" => "Validation error: " . $e->getMessage()]);
        exit;
    }


} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}