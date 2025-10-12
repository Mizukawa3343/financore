<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
require_once "../../api/superadmin_query.php";
include_once "../../util/helper.php";

$department_performance = department_performance_report($conn);
$performing_fees = performing_fees_report($conn);
$monthly_audit = get_monthly_audit_trail($conn);
?>

<div class="section-header">
    <h1 class="title">Reports</h1>
    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem;">
        <div>
            <select id="report-select" class="form-select">
                <option value="department_performance" selected>Department Performance Report</option>
                <option value="performing_fees">Performing Fees Report</option>
                <option value="monthly_audit">Monthly Audit Trail Report</option>
            </select>
        </div>
        <button id="export-excel" class="btn btn-md btn-success">Export Excel</button>
        <button id="export-pdf" class="btn btn-md btn-danger">Export PDF</button>
    </div>
</div>

<!-- Department Performance -->
<div id="department_performance" class="report-table">
    <table id="table-department" class="table table-bordered">
        <thead>
            <tr>
                <th>Department name</th>
                <th>Total assigned fees</th>
                <th>Total collected</th>
                <th>Collection rate</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($department_performance as $dp): ?>
                <tr>
                    <td><?= htmlspecialchars($dp["department_name"]) ?></td>
                    <td><?= htmlspecialchars($dp["total_assigned"]) ?></td>
                    <td><?= htmlspecialchars($dp["total_collected"]) ?></td>
                    <td><?= htmlspecialchars($dp["collection_rate"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Performing Fees -->
<div id="performing_fees" class="report-table" style="display: none;">
    <table id="table-fee-summary" class="table table-bordered">
        <thead>
            <tr>
                <th>Fee</th>
                <th>Department</th>
                <th>Amount</th>
                <th>Students assigned</th>
                <th>Total collected</th>
                <th>Outstanding balance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($performing_fees as $pf): ?>
                <tr>
                    <td><?= htmlspecialchars($pf["fee_name"]) ?></td>
                    <td><?= htmlspecialchars($pf["department_name"]) ?></td>
                    <td><?= htmlspecialchars($pf["amount"]) ?></td>
                    <td><?= htmlspecialchars($pf["student_assigned"]) ?></td>
                    <td><?= htmlspecialchars($pf["total_collected"]) ?></td>
                    <td><?= htmlspecialchars($pf["outstanding_balance"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Monthly Audit Trail -->
<div id="monthly_audit" class="report-table" style="display: none;">
    <table id="table-audit" class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Fullname</th>
                <th>Department</th>
                <th>Action Performed</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthly_audit as $ma): ?>
                <tr>
                    <td><?= htmlspecialchars(format_readable_date($ma["transaction_date"])) ?></td>
                    <td><?= htmlspecialchars($ma["user_fullname"]) ?></td>
                    <td><?= htmlspecialchars($ma["department_acronym"]) ?></td>
                    <td><?= htmlspecialchars($ma["action_performed"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ✅ Export Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    $(document).ready(function () {
        // 🔄 Show/hide reports
        $('#report-select').on('change', function () {
            const selected = $(this).val();
            $('.report-table').hide();
            $('#' + selected).show();
        });

        // 🟢 Export Excel
        $('#export-excel').click(function () {
            const selected = $('#report-select').val();
            const table = document.querySelector(`#${selected} table`);
            const wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
            XLSX.writeFile(wb, `${selected}.xlsx`);
        });

        // 🔴 Export PDF
        // 🔴 Export to PDF (full table, auto multi-page)
        $('#export-pdf').click(async function () {
            const selected = $('#report-select').val();
            const table = document.querySelector(`#${selected} table`);
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4'); // landscape orientation

            // Convert entire table into canvas
            await html2canvas(table, {
                scale: 2,          // better quality
                useCORS: true,
                scrollY: -window.scrollY  // ensure full capture, not viewport only
            }).then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                const imgWidth = pageWidth - 20; // margins
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                let heightLeft = imgHeight;
                let position = 10;

                // Add the first page
                doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                // Add extra pages if needed
                while (heightLeft > 0) {
                    position = heightLeft - imgHeight + 10;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                doc.save(`${selected}.pdf`);
            });
        });

    });
</script>

<?php include_once "../../includes/footer.php"; ?>