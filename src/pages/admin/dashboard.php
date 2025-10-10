<?php
session_start();
require_once "../../config/dbconn.php";
include_once "../../includes/header.php";
include_once "../../api/admin_query.php";
include_once "../../util/helper.php";
$department_id = $_SESSION["department_id"];

$total_assigned_fees = get_total_assigned_fees_by_department($conn, $department_id)["total_fees_assigned"];
$total_collected = get_total_collected_by_department($conn, $department_id)["total_revenue_current_month"];
$total_students = get_total_students_by_department($conn, $department_id)["total_students"];
$outstanding_balance = get_outstanding_balance_by_department($conn, $department_id)["total_outstanding_balance"];
$pending_payments = get_pending_payments_by_department($conn, $department_id)["count_pending_fees"];
$transaction_today = get_today_transaction_by_department($conn, $department_id);
$total_fully_paid_students = get_total_fully_paid_students_by_department($conn, $department_id)["count_fully_paid_students"];
$overdue_fees = get_overdue_fees_by_department($conn, $department_id);

$collection_rate = 0;

if ($total_assigned_fees > 0) {
    $collection_rate = number_format(($total_collected / $total_assigned_fees) * 100, 0);
}

$students_collection_rate = 0;

if ($total_students > 0) {
    $students_collection_rate = ($total_fully_paid_students / $total_students) * 100;
}

// second layer of dashboard
$recent_transaction = get_recent_transaction($conn, $department_id);


// echo "Total collected: " . $total_collected . "<br>";
// echo "Total students: " . $total_students . "<br>";
// echo "Outstanding balance: " . $outstanding_balance . "<br>";
// echo "Collection Rate: " . $collection_rate . "<br>";
// echo "Pending Payments: " . $pending_payments . "<br>";
// echo "Transaction today: " . $transaction_today["total_transactions_today"] . "<br>";
// echo "Total Revenue today: " . $transaction_today["total_revenue_today"] . "<br>";
// echo "Total Full paid students: " . $total_fully_paid_students . "<br>";
// echo "students_collection_rate: " . $students_collection_rate . "<br>";
// echo "Overdues Count: " . $overdue_fees["count_overdue_fees"] . "<br>";
// echo "Total overdues amount: " . $overdue_fees["total_overdue_amount"];

?>

<div class="admin-dashboard">
    <div class="admin-metric-cards-wrapper">
        <div class="card">
            <div class="card-data">
                <h2><?= $total_students ?></h2>
                <p>Total students</p>
            </div>
            <i class="bi bi-people students-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $total_collected ?></h2>
                <p>Total collected</p>
            </div>
            <i class="bi bi-bank collected-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $outstanding_balance ?></h2>
                <p>Outstanding balance</p>
            </div>
            <i class="bi bi-exclamation-circle outstanding-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $collection_rate ?>%</h2>
                <p>Collection rate</p>
            </div>
            <i class="bi bi-percent collection-rate-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $pending_payments ?></h2>
                <p>Pending payments</p>
            </div>
            <i class="bi bi-hourglass pending-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $transaction_today["total_transactions_today"] ?>
                    <span><?= $transaction_today["total_revenue_today"] ?> total revenue</span> </h2>
                <p>Transaction today</p>
            </div>
            <i class="bi bi-calendar transaction-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $total_fully_paid_students ?> <span><?= $students_collection_rate ?>% collection rate</span>
                </h2>
                <p>Total fully paid students</p>
            </div>
            <i class="bi bi-credit-card-2-front paid-students-icon"></i>
        </div>
        <div class="card">
            <div class="card-data">
                <h2><?= $overdue_fees["count_overdue_fees"] ?> <span><?= $overdue_fees["total_overdue_amount"] ?> total
                        overdues amount</span>
                </h2>
                <p>Overdues</p>
            </div>
            <i class="bi bi-clock-history overdues-icon"></i>
        </div>
    </div>

    <div class="middle-content">
        <div class="recent-transaction">
            <h2 class="content-title">Recent payments</h2>
            <?php foreach ($recent_transaction as $rt): ?>
                <div class="recent-card">

                    <img src="<?= $rt["student_picture"] ?>" alt="">
                    <h4><?= $rt["student_name"] ?></h4>

                    <h5>₱<?= number_format($rt["amount_paid"], 2) ?></h5>
                    <p><?= format_readable_date($rt["transaction_date"]) ?></p>
                    <a href="./receipt.php?student_id=<?= $rt["student_id"] ?>&receipt_id=<?= $rt["receipt_id"] ?>&transaction_id=<?= $rt["transaction_id"] ?>"
                        class="btn btn-sm btn-secondary"><i class="bi bi-receipt"></i></a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Chart here -->
        <div>
            <canvas id="feeStatusChart"></canvas>
        </div>
    </div>

    <div class="line-chart-container">
        <canvas id="paymentTrendChart"></canvas>
    </div>
</div>

<script>
    $(document).ready(function () {
        // 1. API Endpoint
        const api_url = '/financore/src/api/get_fee_status_data.php?department_id=' + <?= $department_id ?>;

        // 2. Fetch Data using jQuery AJAX
        $.ajax({
            url: api_url,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                // Check for a Chart.js-compatible structure
                if (response && response.datasets && response.labels) {
                    renderChart(response);
                } else if (response.error) {
                    console.error("API Error:", response.error);
                    $('#feeStatusChart').parent().html('<p>Error fetching data: ' + response.error + '</p>');
                } else {
                    console.error("Invalid data format received from API.");
                    $('#feeStatusChart').parent().html('<p>Invalid data format received.</p>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $('#feeStatusChart').parent().html('<p>Failed to connect to the server API.</p>');
            }
        });

        // 3. Render Chart Function
        // 3. Render Chart Function
        function renderChart(chartData) {
            const ctx = document.getElementById('feeStatusChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut', // Stays as 'doughnut'
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // 💡 KEY TO THINNER DOUGHNUT: Adjusting the cutout
                    cutout: '75%', // Increases the inner hole to 70% of the chart radius. Use a higher percentage (e.g., '80%') for an even thinner ring.
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Student Fee Status Breakdown'
                        }
                    }
                }
            });
        }

        // Example: If the user is authorized for Department ID 1

        // Simplified API Endpoint: Only pass the department_id
        const api_url_trend = `/financore/src/api/get_payment_trend_data.php?department_id=${<?= $department_id ?>}`;

        $.ajax({
            url: api_url_trend,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.datasets) {
                    renderLineChart(response);
                } else {
                    console.error("Failed to load trend data.");
                }
            }
            // ... error handling
        });

        // The renderLineChart function can also be slightly updated to reflect the current month
        function renderLineChart(chartData) {
            const date = new Date();
            const currentMonth = date.toLocaleString('default', { month: 'long' });
            const currentYear = date.getFullYear();

            // Apply modern styling options to the dataset
            const modernChartData = {
                labels: chartData.labels,
                datasets: chartData.datasets.map(dataset => ({
                    ...dataset, // Keep original data and label
                    borderColor: '#007bff', // Main color for the line
                    backgroundColor: 'rgba(0, 123, 255, 0.1)', // Very light fill for the area below the line
                    pointRadius: 4, // Slightly larger, visible points
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#007bff', // Match point color to line
                    pointBorderColor: '#fff', // White border on points for definition
                    pointBorderWidth: 2,
                    tension: 0.4, // Smooth curve (less rigid)
                    borderWidth: 3, // Thicker line for emphasis
                    fill: true, // Enable area fill
                }))
            };

            const ctx = document.getElementById('paymentTrendChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: modernChartData, // Use the new styled data object
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Often removed in modern charts for cleaner look
                        },
                        title: {
                            display: true,
                            text: `Daily Payment Trend (${currentMonth} ${currentYear})`,
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Day of the Month'
                            },
                            // 💡 Modern Look: Remove grid lines and axis ticks
                            grid: {
                                display: false, // Remove vertical grid lines
                                drawBorder: false // Remove the x-axis line
                            },
                            ticks: {
                                color: '#666'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Paid Amount (PHP)'
                            },
                            // 💡 Modern Look: Keep only horizontal lines, but make them very light
                            grid: {
                                color: 'rgba(0, 0, 0, 0.08)', // Light gray lines
                                drawBorder: false // Remove the y-axis line
                            },
                            ticks: {
                                color: '#666'
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php include_once "../../includes/footer.php" ?>