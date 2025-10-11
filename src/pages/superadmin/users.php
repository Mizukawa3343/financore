<?php
require_once "../../config/dbconn.php";
include_once "../../api/superadmin_query.php";
include_once "../../includes/header.php";

$users = get_all_users($conn);
?>
<div class="section-header">
    <h2 class="title">Manage users</h2>
    <button class="btn btn-md btn-primary btn-icon">
        <i class="bi bi-person-add"></i>
        <span>Add user</span>
    </button>
</div>

<div class="table-wrapper">
    <table id="payment-history-table">
        <thead>
            <th>#</th>
            <th>Picture</th>
            <th>Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Role</th>
            <th>Department</th>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td></td>
                    <td>
                        <div>
                            <img src="<?= $user["profile"] ?>" alt="">
                        </div>
                    </td>
                    <td><?= $user["full_name"] ?></td>
                    <td><?= $user["email"] ?></td>
                    <td><?= $user["username"] ?></td>
                    <td><?= $user["role"] ?></td>
                    <td><?= $user["department_id"] ?></td>
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
<?php include_once "../../includes/footer.php" ?>