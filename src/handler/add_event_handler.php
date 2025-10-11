<?php
session_start();
// IMPORTANT: Ensure this path is correct for your environment
require_once "../config/dbconn.php";
header('Content-Type: application/json');

// Helper function to remove the timezone offset (+HH:MM) from the ISO string
function clean_iso_datetime($datetime_string)
{
    if (!$datetime_string) {
        return null;
    }
    // Search for the timezone pattern (e.g., +08:00 or Z) and strip it and everything after
    if (preg_match('/([+-]\d{2}:\d{2}|Z)$/', $datetime_string, $matches)) {
        return substr($datetime_string, 0, strrpos($datetime_string, $matches[0]));
    }
    return $datetime_string;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Get and sanitize data
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
    $end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);

    // CRITICAL FIX: Clean the timezone offset from the strings
    $start_clean = clean_iso_datetime($start);
    $end_clean = clean_iso_datetime($end);

    // 2. Critical: Get the department ID of the logged-in admin
    $admin_department_id = 1; // Replace '1' with $_SESSION['user_department_id'] in production
    $default_color = '#28a745';

    if (empty($title) || empty($start)) {
        echo json_encode(["status" => "error", "message" => "Title and start date are required."]);
        exit;
    }

    try {
        // SQL Insertion: We are now using the cleaned variables ($start_clean, $end_clean)
        // Since the string is now guaranteed to be in YYYY-MM-DDTHH:MM:SS format (or just YYYY-MM-DD),
        // MySQL can usually handle this directly without STR_TO_DATE, but we keep STR_TO_DATE for maximum safety 
        // using the format that is now guaranteed to be correct.

        $sql = "INSERT INTO system_events (title, start_date, end_date, department_id, color) 
                VALUES (?, 
                        STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%s'), 
                        " . ($end_clean ? "STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%s')" : "NULL") . ", 
                        ?, 
                        ?)";

        $stmt = $conn->prepare($sql);

        // Build the parameter array dynamically
        $params = [$title, $start_clean];
        if ($end_clean) {
            $params[] = $end_clean; // Only include $end_clean if it's not NULL
        }
        $params[] = $admin_department_id;
        $params[] = $default_color;

        if ($stmt->execute($params)) {
            echo json_encode([
                "status" => "success",
                "message" => "Event added successfully.",
                "event_id" => $conn->lastInsertId()
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(["status" => "error", "message" => "Failed to insert event. DB Error: " . $errorInfo[2]]);
        }

    } catch (PDOException $e) {
        error_log("Event insert error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>