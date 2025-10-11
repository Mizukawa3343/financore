<?php
// Set headers for JSON output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuration & Connection
require_once "../config/dbconn.php";
date_default_timezone_set('Asia/Manila');

// 1. Generate the days of the current month
$current_day = (int) date('j'); // e.g., 10
$days_in_month = (int) date('t'); // e.g., 31

$day_labels = []; // [1, 2, 3, ..., 31]
$day_keys = []; // ['2025-10-01', '2025-10-02', ...]

for ($i = 1; $i <= $days_in_month; $i++) {
    $day_labels[] = "Day " . $i;
    // Format the date key for SQL matching
    $day_keys[] = date('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
}

// 2. SQL Query: Aggregate revenue by day and department for the current month
$start_of_month = date('Y-m-01 00:00:00');
$end_of_month = date('Y-m-t 23:59:59');

$sql = "
    SELECT
        dept.acronym AS department_acronym,
        DATE_FORMAT(pt.transaction_date, '%Y-%m-%d') AS day_key,
        SUM(pt.amount_paid) AS daily_revenue
    FROM
        payment_transaction pt
    JOIN
        department dept ON pt.department_id = dept.id
    WHERE
        pt.transaction_date BETWEEN :start_of_month AND :end_of_month
    GROUP BY
        dept.acronym, day_key
    ORDER BY
        day_key ASC, dept.acronym ASC;
";

$raw_data_by_dept = [];
$all_departments = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':start_of_month', $start_of_month);
    $stmt->bindParam(':end_of_month', $end_of_month);
    $stmt->execute();

    // 3. Process raw SQL results into a structured map: DeptAcronym -> DayKey -> Revenue
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dept_acronym = $row['department_acronym'];
        if (!isset($raw_data_by_dept[$dept_acronym])) {
            $raw_data_by_dept[$dept_acronym] = [];
            if (!in_array($dept_acronym, $all_departments)) {
                $all_departments[] = $dept_acronym;
            }
        }
        $raw_data_by_dept[$dept_acronym][$row['day_key']] = floatval($row['daily_revenue']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}

// 4. Transform structured map into Chart.js datasets
$datasets = [];
// Use distinct colors for lines
$colors = [
    '#ffc107', // Yellow (CRIM)
    '#007bff', // Blue (CBMIT)
    '#dc3545', // Red (SHS)
    '#28a745', // Green (CTE)
];

foreach ($all_departments as $index => $dept_acronym) {
    $data_points = [];
    $color = $colors[$index % count($colors)];

    // Map the daily revenue to the correct index in the full month array
    foreach ($day_keys as $day_key) {
        $revenue = $raw_data_by_dept[$dept_acronym][substr($day_key, 0, 10)] ?? 0.00;
        $data_points[] = $revenue;
    }

    $datasets[] = [
        'label' => $dept_acronym . ' Daily Collection (₱)',
        'data' => $data_points,
        'borderColor' => $color,
        'backgroundColor' => 'transparent', // No fill for clear comparison
        'fill' => false,
        'tension' => 0.4,
        'pointRadius' => 3,
        'borderWidth' => 2,
    ];
}

$chart_data = [
    'labels' => $day_labels,
    'datasets' => $datasets,
];

echo json_encode($chart_data);
?>