<?php
// Assume you start the session and get the admin's department ID here
session_start();
require_once "../config/dbconn.php"; // Replace with your actual connection file
header('Content-Type: application/json');

// --- CRITICAL: Get the logged-in admin's department ID ---
// For demonstration, we'll hardcode Department ID 1 (CBMIT)
// In production, use $_SESSION['user_department_id'] or similar
$admin_department_id = $_SESSION["department_id"];

try {
    // Combine two queries using UNION ALL: System Events and Fee Due Dates
    $sql = "
        (
            -- 1. Fetch Custom System Events
            SELECT
                id,
                title,
                start_date AS start,
                end_date AS end,
                color,
                'system' AS event_type
            FROM
                system_events
            WHERE
                department_id = :dept_id
        )
        UNION ALL
        (
            -- 2. Fetch Fee Due Dates
            SELECT
                id,
                CONCAT('Fee Due: ', description) AS title,
                DATE(due_date) AS start, -- Use DATE() to ignore time
                DATE_ADD(DATE(due_date), INTERVAL 1 DAY) AS end, -- Full-day event end is next day
                '#dc3545' AS color, -- Distinct color for financial deadlines
                'fee' AS event_type
            FROM
                fees_type
            WHERE
                department_id = :dept_id
        )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':dept_id', $admin_department_id, PDO::PARAM_INT);
    $stmt->execute();

    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // FullCalendar expects a specific structure
        $events[] = [
            'title' => $row['title'],
            'start' => $row['start'],
            'end' => $row['end'],
            'color' => $row['color'],
            'allDay' => ($row['event_type'] == 'fee' || $row['end'] == NULL), // Fees are always allDay
            'extendedProps' => [ // Custom data to pass for event clicks
                'type' => $row['event_type'],
                'db_id' => $row['id']
            ]
        ];
    }

    echo json_encode($events);

} catch (PDOException $e) {
    // Error handling
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>