<?php
session_start();
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
include_once "../../util/helper.php";
include "../../includes/student_header.php";

$student_id = $_SESSION["student_id"];

$student = get_student_info_by_id($conn, $student_id);
$fees = get_outstanding_fees_by_student_id($conn, $student_id);
$transaction_history = get_student_transaction_history($conn, $student_id);

?>
<div class="desktop-content">
    <h1 class="title">Overview</h1>
    <div class="tab">
        <button>Fees</button>
        <button>History</button>
    </div>
    <div>
        <div class="fees">
            <h3>Student Fees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fee</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Due date</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fees as $fee): ?>
                        <tr>
                            <td><?= $fee["fee_name"] ?></td>
                            <td><?= $fee["amount_due"] ?></td>
                            <td><?= $fee["current_balance"] ?></td>
                            <td><?= $fee["due_date"] ?></td>
                            <td>
                                <p class="fee-status <?= $fee["status"] ?>"><?= $fee["status"] ?></p>
                            </td>

                        </tr>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
    <div>
        <div class="history">
            <h3>Table for Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fee</th>
                        <th>Amount Paid</th>
                        <th>Received by</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaction_history as $th): ?>
                        <tr>
                            <td><?= format_readable_datetime($th["transaction_date"]) ?></td>
                            <td><?= get_fee_by_id($conn, $th["student_fees_id"])["description"] ?></td>
                            <td><?= number_format($th["amount_paid"], 2) ?></td>
                            <td><?= get_user_by_id($conn, $th["recorded_by_user_id"])["full_name"] ?></td>
                            <td>
                                <a href="./digital_receipt.php?student_id=<?= $student_id ?>&transaction_id=<?= $th["transaction_id"] ?>&receipt_id=<?= $th["receipt_id"] ?>"
                                    class="btn btn-sm btn-icon btn-secondary">
                                    <i class="bi bi-credit-card-2-front-fill"></i>
                                    <span>Receipt</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mobile-content">
    <h1>Overview</h1>
    <div class="tab">
        <button>Fees</button>
        <button>History</button>
    </div>
    <div>
        <div class="fees">
            <h3 class="title">Student Fees</h3>
            <?php foreach ($fees as $fee): ?>
                <div class="fee-card">
                    <div class="fee-card-heading">
                        <h3><?= $fee["fee_name"] ?></h3>
                        <span class="fee-status <?= $fee["status"] ?>"><?= $fee["status"] ?></span>
                    </div>
                    <div class="fee-info">
                        <span>Due date</span>
                        <span><?= format_readable_date($fee["due_date"]) ?></span>
                    </div>
                    <div class="fee-info">
                        <span>Amount due</span>
                        <span><?= $fee["amount_due"] ?></span>
                    </div>
                    <div class="fee-info">
                        <span>Current balance</span>
                        <span><?= $fee["current_balance"] ?></span>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div class="history">
            <h3>Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fee</th>
                        <th>Amount Paid</th>
                        <th>Received by</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaction_history as $th): ?>
                        <tr>
                            <td><?= format_readable_datetime($th["transaction_date"]) ?></td>
                            <td><?= get_fee_by_id($conn, $th["student_fees_id"])["description"] ?></td>
                            <td><?= number_format($th["amount_paid"], 2) ?></td>
                            <td><?= get_user_by_id($conn, $th["recorded_by_user_id"])["full_name"] ?></td>
                            <td>
                                <a href="./digital_receipt.php?student_id=<?= $student_id ?>&transaction_id=<?= $th["transaction_id"] ?>&receipt_id=<?= $th["receipt_id"] ?>"
                                    class="btn btn-sm btn-icon btn-secondary">
                                    <i class="bi bi-credit-card-2-front-fill"></i>
                                    <span>View Receipt</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // -----------------------------
        // DESKTOP TAB LOGIC
        // -----------------------------
        const $desktopTabs = $(".desktop-content .tab button");
        const $desktopFees = $(".desktop-content .fees");
        const $desktopHistory = $(".desktop-content .history");

        // Initialize desktop view
        $desktopHistory.hide();
        $desktopTabs.first().addClass("active");

        // Handle tab switching for desktop
        $desktopTabs.on("click", function () {
            const index = $(this).index();

            // Toggle active state
            $desktopTabs.removeClass("active");
            $(this).addClass("active");

            // Show corresponding section
            if (index === 0) {
                $desktopFees.fadeIn(200);
                $desktopHistory.hide();
            } else {
                $desktopFees.hide();
                $desktopHistory.fadeIn(200);
            }
        });

        // -----------------------------
        // MOBILE TAB LOGIC
        // -----------------------------
        const $mobileTabs = $(".mobile-content .tab button");
        const $mobileFees = $(".mobile-content .fees");
        const $mobileHistory = $(".mobile-content .history");

        // Initialize mobile view
        $mobileHistory.hide();
        $mobileTabs.first().addClass("active");

        // Handle tab switching for mobile
        $mobileTabs.on("click", function () {
            const index = $(this).index();

            // Toggle active state
            $mobileTabs.removeClass("active");
            $(this).addClass("active");

            // Show corresponding section
            if (index === 0) {
                $mobileFees.fadeIn(200);
                $mobileHistory.hide();
            } else {
                $mobileFees.hide();
                $mobileHistory.fadeIn(200);
            }
        });
    });
</script>

<?php include "../../includes/student_footer.php"; ?>