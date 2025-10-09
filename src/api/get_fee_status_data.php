<?php
// 1. Include the PDO Connection
// This file is assumed to initialize the $conn PDO object.
require_once '../config/dbconn.php';

// 2. GET THE DEPARTMENT ID
// Get the department ID from the request.
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

// Basic authorization check: If department_id is not provided or invalid
if ($department_id <= 0) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Authorization failed. Missing or invalid department ID."]);
    exit();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 3. SQL Query with Named Placeholder
$sql = "
    SELECT
        COUNT(CASE WHEN sf.status = 'paid' THEN 1 END) AS Paid_Count,
        COUNT(CASE WHEN sf.status = 'partial' THEN 1 END) AS Partially_Paid_Count,
        COUNT(CASE
            WHEN sf.status = 'unpaid' AND ft.due_date >= CURRENT_DATE() THEN 1
        END) AS Pending_Count,
        COUNT(CASE
            WHEN (sf.status = 'unpaid' OR sf.status = 'partial') AND ft.due_date < CURRENT_DATE() THEN 1
        END) AS Overdue_Count
    FROM
        student_fees sf
    JOIN
        fees_type ft ON sf.fees_id = ft.id
    WHERE
        ft.department_id = :dept_id; 
";

try {
    // 4. Prepare and Execute the Statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter securely
    $stmt->bindParam(':dept_id', $department_id, PDO::PARAM_INT);

    $stmt->execute();

    // 5. Fetch the single row of counts
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // The raw data array
        $labels = ['Paid', 'Partially Paid', 'Pending', 'Overdue'];
        $data_values = [
            (int) $row['Paid_Count'],
            (int) $row['Partially_Paid_Count'],
            (int) $row['Pending_Count'],
            (int) $row['Overdue_Count']
        ];

        // Prepare the final Chart.js data structure
        $chart_data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Fee Status',
                    'data' => $data_values,
                    'backgroundColor' => [
                        '#4CAF50', // Green for Paid
                        '#FFC107', // Amber for Partially Paid
                        '#2196F3', // Blue for Pending
                        '#F44336'  // Red for Overdue
                    ],
                    'hoverOffset' => 4
                ]
            ]
        ];

        // Output the JSON
        echo json_encode($chart_data);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "No data found for the specified department."]);
    }

} catch (PDOException $e) {
    // Handle PDO errors
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

// Note: With PDO, explicitly closing the connection is not always necessary 
// if you allow the script to end and close it automatically, but you can set $conn = null;
// if you want to ensure it closes sooner.

?>