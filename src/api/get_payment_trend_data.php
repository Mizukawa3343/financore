<?php
// 1. Include the PDO Connection
// This file is assumed to initialize the $conn PDO object.
require_once '../config/dbconn.php';

// 2. GET THE FILTERS AND SET DATE
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

// Set Year and Month to CURRENT SERVER DATE
$year = date('Y');
$month = date('m');

// Basic authorization check
if ($department_id <= 0) {
    http_response_code(401);
    echo json_encode(["error" => "Authorization failed. Missing department ID."]);
    exit();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 3. SQL Query with Named Placeholders
$sql = "
    SELECT
        DATE(transaction_date) AS payment_date,
        SUM(amount_paid) AS daily_total_paid
    FROM
        payment_transaction pt
    WHERE
        pt.department_id = :dept_id
        AND YEAR(pt.transaction_date) = :year
        AND MONTH(pt.transaction_date) = :month
    GROUP BY
        payment_date
    ORDER BY
        payment_date ASC;
";

$daily_totals = [];

try {
    // 4. Prepare and Execute the Statement
    $stmt = $conn->prepare($sql);

    // Bind parameters securely
    $stmt->bindParam(':dept_id', $department_id, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);

    $stmt->execute();

    // 5. Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process results into the map
    foreach ($results as $row) {
        $daily_totals[$row['payment_date']] = floatval($row['daily_total_paid']);
    }

} catch (PDOException $e) {
    // Handle PDO errors
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}


// 6. FILL MISSING DAYS WITH ZERO (Pure PHP logic, no change needed)
$labels = [];
$data_values = [];

$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

for ($day = 1; $day <= $num_days; $day++) {
    $current_date_str = sprintf("%04d-%02d-%02d", $year, $month, $day);

    $labels[] = $day;

    // Set value to the calculated total, or 0.00 if no entry exists
    $data_values[] = isset($daily_totals[$current_date_str]) ? $daily_totals[$current_date_str] : 0.00;
}

// 7. Prepare the final Chart.js data structure
$chart_data = [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Total Paid Amount',
            'data' => $data_values,
            'borderColor' => '#007bff',
            'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
            'fill' => true,
            'tension' => 0.4,
            'pointRadius' => 3
        ]
    ]
];

// Output the JSON
echo json_encode($chart_data);
?>