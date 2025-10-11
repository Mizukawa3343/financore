<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
include_once "../../api/superadmin_query.php";

$total_students = total_students($conn)["total_student_count"];

$overall_collected = get_overall_collected_all_departments($conn);
$total_collected = $overall_collected['total_collected_lifetime'];
$total_assigned = $overall_collected['total_assigned_lifetime'];

$collection_rate = 0.00;

if ($total_assigned > 0) {
    $collection_rate = number_format(($total_collected / $total_assigned) * 100, 0);
}


$outstanding_balance = get_overall_balances_all_departments($conn);
$total_outstanding = $outstanding_balance['total_outstanding'];
$total_assigned = $outstanding_balance['total_assigned'];

$outstanding_percentage = 0.00;

if ($total_assigned > 0) {
    $outstanding_percentage = number_format(($total_outstanding / $total_assigned) * 100, 0);
}

$collection = get_collection_rate_all_departments($conn);
$total_collected = $collection['total_collected_lifetime'];
$total_assigned = $collection['total_assigned_lifetime'];

$collection_rate = 0.00;

if ($total_assigned > 0) {
    $collection_rate = number_format(($total_collected / $total_assigned) * 100, 1);
}

$total_department = get_all_active_departments($conn)["total_active_departments"];

$today_transaction = get_all_transaction_all_department($conn);

$overdue = get_overdues_all_department($conn);

$total_receipts = get_total_receipts_count($conn)["total_receipts_generated"];
?>
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
            <h2><?= $total_collected ?> <span><?= $collection_rate ?>%</span></h2>
            <p>Overall collected</p>
        </div>
        <i class="bi bi-bank collected-icon"></i>
    </div>
    <div class="card">
        <div class="card-data">
            <h2><?= $total_outstanding ?> <span><?= $outstanding_percentage ?>%</span> </h2>
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
            <h2><?= $total_department ?></h2>
            <p>Active department</p>
        </div>
        <i class="bi bi-hourglass pending-icon"></i>
    </div>
    <div class="card">
        <div class="card-data">
            <h2><?= $today_transaction["transaction_count_today"] ?>
                <span><?= $today_transaction["total_amount_today"] ?> total revenue</span>
            </h2>
            <p>Transaction today</p>
        </div>
        <i class="bi bi-calendar transaction-icon"></i>
    </div>
    <div class="card">
        <div class="card-data">
            <h2><?= $total_receipts ?>
            </h2>
            <p>Total receipts generated</p>
        </div>
        <i class="bi bi-receipt paid-students-icon"></i>
    </div>
    <div class="card">
        <div class="card-data">
            <h2><?= $overdue["overdue_fee_count"] ?> <span><?= $overdue["total_overdue_amount"] ?> total
                    overdues amount</span>
            </h2>
            <p>Overdues</p>
        </div>
        <i class="bi bi-clock-history overdues-icon"></i>
    </div>

</div>

<div class="department-analytics">
    <div class="">
        <h3 class="title">
            Payment Collection Trend
        </h3>
        <div>
            <canvas id="dailyTransactionChart" height="350"></canvas>
        </div>
    </div>

    <div style="margin-bottom: 5rem;">
        <h3 class="title">Students per department</h3>
        <div>
            <canvas id="studentDistributionChart" height="300"></canvas>
        </div>
    </div>
</div>




<script>
    // CHART 1: Daily Department Trend (Replaces old daily transaction chart)
    function renderDailyDepartmentTrendChart(data) {
        const chartCanvas = $('#dailyTransactionChart');
        if (!chartCanvas.length) return;

        new Chart(chartCanvas[0], {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        align: 'start',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                label = label.replace(' (₱)', '');
                                if (context.parsed.y !== null) {
                                    label += ': ' + new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Day of the Month' },
                        grid: { display: false }
                    },
                    y: {
                        title: { display: true, text: 'Collected Amount (₱)' },
                        beginAtZero: true,
                        ticks: {
                            callback: function (value, index, ticks) {
                                return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0 }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    function renderStudentDistributionChart(data) {
        const chartCanvas = $('#studentDistributionChart');
        if (!chartCanvas.length) return;

        new Chart(chartCanvas[0], {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }

                                // Get value and calculate percentage
                                if (context.parsed !== null) {
                                    label += context.parsed + ' Students';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1) + '%';

                                return label + ' (' + percentage + ')';
                            }
                        }
                    }
                },
                cutout: "75%",
            }
        });
    }

    // --- MAIN EXECUTION BLOCK ---
    $(function () {
        // 1. Daily Department Collection Trend
        const dailyChartCanvas = $('#dailyTransactionChart');
        const dailyApiUrl = '../../api/get_daily_department_collection.php';

        if (dailyChartCanvas.length) {
            $.getJSON(dailyApiUrl)
                .done(function (data) {
                    if (data.error) {
                        // Display error message from API if available
                        dailyChartCanvas.text('Failed to load daily department chart data: ' + data.error);
                        return;
                    }
                    renderDailyDepartmentTrendChart(data);
                })
                .fail(function () {
                    dailyChartCanvas.text('Failed to load daily department trend data (Network Error).');
                });
        }

        // No other chart fetches needed here since you removed them
    });

    $(function () {
        // ... existing fetch calls for daily trend chart ...

        // --- CHART 4: Department Student Distribution ---
        const studentCanvas = $('#studentDistributionChart');
        const studentApiUrl = '../../api/get_department_student_distribution.php';

        if (studentCanvas.length) {
            $.getJSON(studentApiUrl)
                .done(function (data) {
                    if (data.error) {
                        studentCanvas.text('Failed to load student distribution chart: ' + data.error);
                        return;
                    }
                    renderStudentDistributionChart(data);
                })
                .fail(function () {
                    studentCanvas.text('Failed to load department student distribution data.');
                });
        }

        // ... your closing lines for the $(function () { ... }); block ...
    });
</script>
<?php include_once "../../includes/footer.php" ?>