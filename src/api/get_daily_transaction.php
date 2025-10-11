<?php
// 1. Include the PDO Connection (assuming $conn is available)
require_once '../config/dbconn.php';

// 💡 Timezone Fix for CURDATE/DATE functions
date_default_timezone_set('Asia/Manila');

// 2. Set filters (Current Month/Year by default)
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 3. SQL Query: Daily Totals Across ALL Departments
$sql = "
    SELECT
        DATE(transaction_date) AS payment_date,
        SUM(amount_paid) AS daily_total_paid
    FROM
        payment_transaction pt
    WHERE
        YEAR(pt.transaction_date) = :year
        AND MONTH(pt.transaction_date) = :month
    GROUP BY
        payment_date
    ORDER BY
        payment_date ASC;
";

$daily_totals = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process results into a map: 'YYYY-MM-DD' => amount
    foreach ($results as $row) {
        $daily_totals[$row['payment_date']] = floatval($row['daily_total_paid']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}

// 4. Chart.js Data Preparation (Fill missing days with 0)
$labels = [];
$data_values = [];
$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

for ($day = 1; $day <= $num_days; $day++) {
    $current_date_str = sprintf("%04d-%02d-%02d", $year, $month, $day);

    // Use the day number as the chart label (1, 2, 3, ...)
    $labels[] = $day;

    // Set value to the calculated total, or 0.00 if no entry exists
    $data_values[] = isset($daily_totals[$current_date_str]) ? $daily_totals[$current_date_str] : 0.00;
}

// 5. Prepare the final Chart.js data structure
$chart_data = [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'System Total Daily Revenue',
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