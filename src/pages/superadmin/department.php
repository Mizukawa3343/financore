<?php
require_once "../../config/dbconn.php";
include_once "../../util/helper.php";
include_once "../../api/superadmin_query.php";
include_once "../../includes/header.php";
?>
<div class="section-header">
    <h2 class="title">Manage Department</h2>
</div>

<div class="table-wrapper">
    <table id="payment-history-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Department name</th>
                <th>Secretary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (display_department($conn) as $department): ?>
                <tr>
                    <td></td>
                    <td>
                        <div>
                            <img src="<?= get_department_logo($department["department_id"]) ?>" alt="">
                        </div>
                    </td>
                    <td><?= $department["department_name"] ?></td>
                    <td><?= $department["secretary"] ?></td>
                    <td>
                        <a href="./department_profile.php?department_id=<?= $department["department_id"] ?>"
                            class="btn btn-md btn-icon btn-secondary">
                            <i class="bi bi-building"></i>
                            <span>view department</span>
                        </a>
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

                // You can add column definitions for other fixed-width columns here if needed
            ]
        });


    })
</script>
<?php if (isset($_SESSION['toastr'])): ?>
    <script>
        toastr["<?= $_SESSION['toastr']['type'] ?>"]("<?= $_SESSION['toastr']['message'] ?>");
    </script>
    <?php unset($_SESSION['toastr']); // <-- reset happens here ?>
<?php endif; ?>
<?php include_once "../../includes/footer.php" ?>