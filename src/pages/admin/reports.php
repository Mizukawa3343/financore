<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
include_once "../../util/helper.php";
$fees = get_all_fees_by_department_id($conn, $_SESSION['department_id']);

$department_id = $_SESSION["department_id"];

$monthly_transaction = get_monthly_transaction($conn, $department_id);
$fee_summary = fee_summary_report($conn, $department_id);
$student_with_balances = student_balances_report($conn, $department_id);
?>
<div class="section-header">
    <h1 class="title">Reports</h1>
    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem;">
        <div>
            <select id="report-select">
                <option value="monthly-transaction" selected>Monthly Transaction Report</option>
                <option value="fee-summary">Fee Summary Report</option>
                <option value="student-with-balances">Student with Balances Report</option>
            </select>
        </div>
        <button id="export-excel" class="btn btn-md btn-success">Export Excel</button>
        <button id="export-pdf" class="btn btn-md btn-danger">Export PDF</button>
    </div>
</div>

<!-- Wrap each table for easier show/hide -->
<div id="monthly-transaction" class="report-table">
    <table id="table-monthly">
        <thead>
            <tr>
                <th>Transaction date</th>
                <th>Student name</th>
                <th>Fee</th>
                <th>Amount Paid</th>
                <th>Receipt id</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthly_transaction as $mt): ?>
                <tr>
                    <td><?= format_readable_datetime($mt["transaction_date"]) ?></td>
                    <td><?= get_student_by_id($conn, $mt["student_id"])["first_name"] . " " . get_student_by_id($conn, $mt["student_id"])["last_name"] ?>
                    </td>
                    <td><?= get_student_fee_details_for_receipt($conn, $mt["student_id"], $mt["student_fees_id"])["fee_description"] ?>
                    </td>
                    <td><?= $mt["amount_paid"] ?></td>
                    <td><?= get_receipt_number_for_receipt($conn, $mt["receipt_id"])["receipt_number"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="fee-summary" class="report-table" style="display: none;">
    <table id="table-fee-summary">
        <thead>
            <tr>
                <th>Fee</th>
                <th>Total students assigned</th>
                <th>Due date</th>
                <th>Total collected</th>
                <th>Total due</th>
                <th>Remaining balance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fee_summary as $fs): ?>
                <tr>
                    <td><?= $fs["Fee_Name"] ?></td>
                    <td><?= $fs["Assigned_Students_Count"] ?></td>
                    <td><?= format_readable_date($fs["Due_Date"]) ?></td>
                    <td><?= $fs["Total_Collected"] ?></td>
                    <td><?= $fs["Total_Due_for_Fee"] ?></td>
                    <td><?= $fs["Remaining_Balance"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="student-with-balances" class="report-table" style="display: none;">
    <table id="table-student-balances">
        <thead>
            <tr>
                <th>Student id</th>
                <th>Name</th>
                <th>Course</th>
                <th>Fee name</th>
                <th>Fee amount</th>
                <th>Amount Paid</th>
                <th>Current balance</th>
                <th>Fee duedate</th>
                <th>Fee status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($student_with_balances as $swb): ?>
                <tr>
                    <td><?= $swb["Student_ID"] ?></td>
                    <td><?= $swb["last_name"] . " " . $swb["first_name"] ?></td>
                    <td><?= $swb["Course_Name"] ?></td>
                    <td><?= $swb["Fee_Name"] ?></td>
                    <td><?= $swb["Total_Fee_Amount"] ?></td>
                    <td><?= $swb["Amount_Paid"] ?></td>
                    <td><?= $swb["Current_Balance"] ?></td>
                    <td><?= format_readable_date($swb["Fee_Due_Date"]) ?></td>
                    <td><?= $swb["Fee_Status"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ✅ Required export libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>
    $(document).ready(function () {
        // 🔄 Show/hide tables based on select
        $('#report-select').on('change', function () {
            const selected = $(this).val();
            $('.report-table').hide();
            $('#' + selected).show();
        });

        // 🟢 Export to Excel
        $('#export-excel').click(function () {
            const selected = $('#report-select').val();
            const table = document.querySelector(`#${selected} table`);
            const wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
            XLSX.writeFile(wb, `${selected}.xlsx`);
        });

        // 🔴 Export to PDF
        $('#export-pdf').click(async function () {
            const selected = $('#report-select').val();
            const table = document.querySelector(`#${selected} table`);
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4'); // landscape, points, A4

            // Use html2canvas for better rendering
            await html2canvas(table, { scale: 1.5 }).then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = doc.internal.pageSize.getWidth();
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                doc.addImage(imgData, 'PNG', 10, 10, imgWidth - 20, imgHeight);
                doc.save(`${selected}.pdf`);
            });
        });

    });
</script>



<?php include_once "../../includes/footer.php" ?>