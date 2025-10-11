<?php
// Set headers for JSON output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuration & Connection
require_once "../config/dbconn.php";

// SQL Query: Count active students grouped by Department
$sql = "
    SELECT
        d.acronym AS department_name,
        COUNT(s.id) AS total_students
    FROM
        students s
    JOIN
        department d ON s.department_id = d.id
    GROUP BY
        d.acronym
    ORDER BY
        total_students DESC;
";

$labels = [];
$data_values = [];
// Distinct colors for visual appeal
// $base_colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#17a2b8'];
$base_colors = ['#ffc107', '#28a745', '#007bff', '#dc3545', '#6f42c1', '#17a2b8'];
$colors = [];

try {
    $stmt = $conn->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $index => $row) {
        $labels[] = $row['department_name'];
        $data_values[] = intval($row['total_students']);
        // Assign a color, cycling through the array
        $colors[] = $base_colors[$index % count($base_colors)];
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit();
}

$chart_data = [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Total Students',
            'data' => $data_values,
            'backgroundColor' => $colors,
            'hoverOffset' => 10
        ]
    ]
];

echo json_encode($chart_data);
?>