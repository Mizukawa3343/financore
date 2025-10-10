<?php
// 1. Include the PDO Connection
require_once '../config/dbconn.php';

// 2. GET THE DEPARTMENT ID
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

if ($department_id <= 0) {
    http_response_code(401);
    echo json_encode(["error" => "Authorization failed. Missing or invalid department ID."]);
    exit();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 3. REVISED SQL Query with status filter
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
        ft.department_id = :dept_id
        AND ft.status = 0; -- *** NEW FILTER: Only display fees where the administrative status is 0 ***
";

try {
    // 4. Prepare and Execute the Statement
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':dept_id', $department_id, PDO::PARAM_INT);
    $stmt->execute();

    // 5. Fetch the single row of counts
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $labels = ['Paid', 'Partially Paid', 'Pending', 'Overdue'];
        $data_values = [
            (int) $row['Paid_Count'],
            (int) $row['Partially_Paid_Count'],
            (int) $row['Pending_Count'],
            (int) $row['Overdue_Count']
        ];

        $chart_data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Fee Status',
                    'data' => $data_values,
                    'backgroundColor' => ['#4CAF50', '#FFC107', '#2196F3', '#F44336'],
                    'hoverOffset' => 4
                ]
            ]
        ];

        echo json_encode($chart_data);
    } else {
        // Return 0 counts if no data is found after filtering
        echo json_encode(['labels' => ['Paid', 'Partially Paid', 'Pending', 'Overdue'], 'datasets' => [['data' => [0, 0, 0, 0]]]]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>