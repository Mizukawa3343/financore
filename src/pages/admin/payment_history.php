<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
include_once "../../util/helper.php";

$payment_history = payment_history($conn, $_SESSION["department_id"]);
?>
<div class="section-header">
    <h1 class="title">Payment History</h1>
</div>

<div class="payment-history-table-wrapper">
    <table id="payment-history-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Picture</th>
                <th>Student name</th>
                <th>Fee</th>
                <th>Amount Paid</th>
                <th>Receipt id</th>
                <th>Transaction date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payment_history as $ph): ?>
                <tr>
                    <td></td>
                    <td>
                        <div>
                            <img src="<?= get_student_by_id($conn, $ph["student_id"])["picture"] ?>" alt="">
                        </div>
                    </td>
                    <td>
                        <?= get_student_by_id($conn, $ph["student_id"])["first_name"] . " " . get_student_by_id($conn, $ph["student_id"])["last_name"] ?>
                    </td>
                    <td>
                        <?= get_fee_by_id($conn, $ph["student_fees_id"])["description"] ?>
                    </td>
                    <td>
                        <?= number_format($ph["amount_paid"], 2) ?>
                    </td>
                    <td>
                        <?= get_receipt_number_for_receipt($conn, $ph["receipt_id"])["receipt_number"] ?>
                    </td>
                    <td>
                        <?= format_readable_date($ph["transaction_date"]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#payment-history-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 10,

            // ✅ CRITICAL FIX: Explicitly disable automatic column width calculation
            autoWidth: false,

            columnDefs: [
                {
                    // Target the # column (index 0)
                    targets: 0,
                    className: 'dt-center',
                    orderable: false,
                    width: '30px',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    // Target the Picture column (index 1)
                    targets: 1,
                    className: 'dt-center',
                    orderable: false,
                    searchable: false,
                    width: '60px' // Enforce a fixed width
                },
                {
                    // Targets the Receipt ID (5) and Transaction Date (6) columns
                    targets: [5, 6],
                    className: 'dt-center' // Center these cells
                },
                // You can add column definitions for other fixed-width columns here if needed
            ]
        });
    })
</script>
<?php include_once "../../includes/footer.php"; ?>