<?php
require_once "../../config/dbconn.php";
include_once "../../api/superadmin_query.php";
include_once "../../includes/header.php";

$users = get_all_users($conn);
$departments = get_all_department($conn);
?>
<div class="section-header">
    <h2 class="title">Manage users</h2>
    <button class="btn btn-md btn-primary btn-icon" id="btn-add-user">
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

<div class="modal-overlay hide">
    <div class="modal">
        <button class="close-modal"><i class="bi bi-x"></i></button>
        <h2 class="modal-title">add student</h2>
        <form id="add-user-form">

            <div class="row-co">
                <label for="full_name">Fullname</label>
                <input type="text" name="full_name" id="full_name">
            </div>

            <div class="row-co">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>

            <div class="col-2">
                <div class="row-col">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username">
                </div>

                <div class="row-col">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>
            </div>

            <div class="col-2">
                <div class="row-col">
                    <label for="role">Role</label>
                    <select name="role" id="role">
                        <option value="">Select role</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="row-col">
                    <label for="department">Department</label>
                    <select name="department" id="department">
                        <option value="">Select department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department["id"] ?>"><?= $department["acronym"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>



            <div class="form-btn">
                <button type="submit" class="btn btn-md btn-primary">Save</button>
                <button type="button" class="btn btn-md btn-secondary btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
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

        const openModalBtn = $("#btn-add-user");
        const closeModalBtn = $(".close-modal");
        const modalOverlay = $(".modal-overlay");

        // Open Modal
        openModalBtn.on("click", function () {
            modalOverlay.removeClass("hide");
        });

        // Close Modal
        closeModalBtn.on("click", function () {
            modalOverlay.addClass("hide");
        });

        $(".btn-cancel").on("click", function () {
            modalOverlay.addClass("hide");
            $("#add-user-form")[0].reset();
        });

        modalOverlay.on("click", function (e) {
            if ($(e.target).is(modalOverlay)) {
                modalOverlay.addClass("hide");
            }
        });

        $("#add-user-form").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            console.log(formData);

            $.ajax({
                url: "/financore/src/handler/add_user.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        window.location.reload();
                        // alert(response.message)
                    } else {
                        window.location.reload();
                        // alert(response.message)
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            })
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