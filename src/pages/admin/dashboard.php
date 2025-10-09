<?php
session_start();
require_once "../../config/dbconn.php";
include_once "../../includes/header.php";
include_once "../../api/admin_query.php";
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
                <h2><?= $transaction_today["total_transactions_today"] ?> <span>1000 total revenue</span> </h2>
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
</div>

<?php include_once "../../includes/footer.php" ?>