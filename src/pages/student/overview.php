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
    <h1 class="title">OVERVIEW</h1>

    <div class="overview-container">
        <div class="table-wrapper">
            <h3>Student Fees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fee</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Due date</th>
                        <th>Status</th>
                        <th>ACtion</th>
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
                            <td>
                                <button class="btn btn-sm btn-icon btn-success btn-pay"
                                    data-fees-id="<?= $fee["fees_id"] ?>" data-fees-amount="<?= $fee["current_balance"] ?>">
                                    <i class="bi bi-credit-card-2-front-fill"></i>
                                    <span>Pay</span>
                                </button>
                            </td>
                        </tr>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>
        <div class="table-wrapper">
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
                                <a href="./receipt.php?student_id=<?= $student_id ?>&transaction_id=<?= $th["transaction_id"] ?>&receipt_id=<?= $th["receipt_id"] ?>"
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
            <h3>Student Fees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fee name</th>
                        <th>Amount</th>
                        <th>Due date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                    </tr>
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
                        <th>Fee name</th>
                        <th>Amount</th>
                        <th>Due date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                    <tr>
                        <td>Bicol Youth for Technology Expo</td>
                        <td>1000</td>
                        <td>October 17, 2025</td>
                        <td>View Receipt</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Hide all sections except Fees by default
        $(".history").hide();
        $(".tab button:first").addClass("active");

        // Handle tab button click
        $(".tab button").on("click", function () {
            const index = $(this).index();

            // Remove 'active' class from all buttons
            $(".tab button").removeClass("active");
            // Add 'active' to the clicked one
            $(this).addClass("active");

            // Hide all sections
            $(".fees, .history").hide();

            // Show the selected section based on button index
            if (index === 0) {
                $(".fees").fadeIn(200);
            } else if (index === 1) {
                $(".history").fadeIn(200);
            }
        });
    });
</script>
<?php include "../../includes/student_footer.php"; ?>