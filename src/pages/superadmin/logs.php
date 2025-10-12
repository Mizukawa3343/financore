<?php
session_start();
require_once "../../config/dbconn.php";
include_once "../../api/superadmin_query.php";
include_once "../../util/helper.php";
include_once "../../includes/header.php";

$logs = get_all_logs($conn);
?>
<div class="section-heade">
    <h2 class="title">Logs</h2>
</div>

<div class="table-wrapper">
    <table id="table-logs">
        <thead>
            <tr>
                <th></th>
                <th>Action</th>
                <th>User</th>
                <th>Department</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td></td>
                    <td><?= $log["action"] ?></td>
                    <td><?= $log["user_fullname"] ?></td>
                    <td><?= $log["department_acronym"] ?></td>
                    <td><?= format_readable_date($log["date"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#table-logs').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            // ✅ Show only 5 entries by default
            columnDefs: [
                { orderable: false, targets: -1 },
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                }
            ]
        });
    })
</script>
<?php include_once "../../includes/footer.php" ?>