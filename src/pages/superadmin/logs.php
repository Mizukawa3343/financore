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

<table>
    <thead>
        <tr>
            <th>Action</th>
            <th>User</th>
            <th>Department</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $log["action"] ?></td>
                <td><?= $log["user_fullname"] ?></td>
                <td><?= $log["department_acronym"] ?></td>
                <td><?= format_readable_date($log["date"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include_once "../../includes/footer.php" ?>