<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
$fee_id = $_GET['fee_id'];
if (!isset($fee_id)) {
    header("Location: ./fees.php");
    exit;
}
$fee = get_fee_details_by_id($conn, $fee_id, $_SESSION["department_id"]);
$courses = get_courses_by_department_id($conn, $_SESSION["department_id"]);
$students = get_students_assigned_to_fee($conn, $fee_id, $_SESSION["department_id"]);

?>
<div class="section-header">
    <div class="fee-details-wrapper">
        <div class="left">
            <a href="./fees.php">Back to fees</a>
            <i class="bi bi-chevron-right"></i>
            <h1 class="title"><?= $fee["fee_name"] ?></h1>
            <i class="bi bi-chevron-right"></i>
            <span><?= $fee["due_date"] ?></span>
        </div>
        <div class="right">
            <button class="btn btn-sm btn-icon btn-secondary">
                <i class="bi bi-pencil-square"></i>
                <span>update fee</span>
            </button>
            <button class="btn btn-sm btn-icon  btn-primary" id="btn-assign">
                <i class="bi bi-person-add"></i>
                <span>assign fee</span>
            </button>
            <button class="btn btn-sm btn-icon  btn-success">
                <i class="bi bi-check2"></i>
                <span>mark as collected</span>
            </button>
        </div>
    </div>
</div>



<div class="fee-dashboard">
    <div class="metric-cards">
        <div class="card">
            <div class="card-left">
                <p>Amount</p>
                <h2><?= $fee["fee_unit_amount"] ?></h2>
            </div>
            <div class="card-right amount-icon">
                <i class="bi bi-cash"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total collected</p>
                <h2><?= $fee["total_collected"] ?></h2>
            </div>
            <div class="card-right collected-icon">
                <i class="bi bi-piggy-bank"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total to collect</p>
                <h2><?= $fee["total_to_collect"] ?></h2>
            </div>
            <div class="card-right to-collect-icon">
                <i class="bi bi-bank"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total students</p>
                <h2><?= $fee["total_students_assigned"] ?></h2>
            </div>
            <div class="card-right students-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>

    </div>


    <div class="table-wrapper">
        <table id="students-fee-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>student id</th>
                    <th>lastname</th>
                    <th>firstname</th>
                    <th>course</th>
                    <th>year</th>
                    <th style="text-align: center;">status</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td></td>
                        <td><?= $student["student_id"] ?></td>
                        <td><?= $student["last_name"] ?></td>
                        <td><?= $student["first_name"] ?></td>
                        <td><?= $student["course_name"] ?></td>
                        <td><?= $student["year"] ?></td>
                        <td>
                            <p class="fee-status <?= $student["status"] ?>"><?= $student["status"] ?></p>
                        </td>
                        <td style="display: flex; align-items: center; justify-content: center; ">
                            <a href="./student_profile.php?student_id=<?= $student["id"] ?>" class="btn btn-icon
                            btn-sm btn-secondary">
                                <i class="bi bi-person-circle"></i>
                                <span>view profile</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>



            </tbody>
        </table>
    </div>

</div>

<div class="modal-overlay assign-fee-modal hide">
    <div class="modal">
        <form id="assign-fee-form">
            <input type="hidden" name="fee_id" value="<?= $fee_id ?>">

            <div class="row-col">
                <label for="assign-fee-type">Assign fee to:</label>
                <select name="action" id="assign-fee-type">
                    <option value="">Select action</option>
                    <option value="all">all students</option>
                    <option value="year">all students by year</option>
                    <option value="course">all students by course</option>
                    <option value="year_course">all students by course and year</option>
                </select>
            </div>

            <div class="row-col" id="year-input-control">
                <label for="year_select">Year</label>
                <select name="year" id="year_select" disabled>
                    <option value="">Select year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>

            <div class="row-col" id="course-input-control">
                <label for="course_select">Course</label>
                <select name="course" id="course_select" disabled>
                    <option value="">Select course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course["id"] ?>"><?= $course["name"] ?></option>
                    <?php endforeach; ?>
                </select>
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
        $('#students-fee-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 10, // ✅ Show only 5 entries by default
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

        // ... (existing modal open/close logic)

        // Initial setup: Rename select elements for clarity
        const $actionSelect = $("#assign-fee-type");
        const $yearControl = $("#year-input-control");
        const $courseControl = $("#course-input-control");
        const $yearSelect = $("#year_select");
        const $courseSelect = $("#course_select"); // Corrected ID

        // Initial state
        $yearControl.hide();
        $courseControl.hide();
        $yearSelect.prop('disabled', true);
        $courseSelect.prop('disabled', true);

        // When user clicks "Assign fee"
        $("#btn-assign").on("click", function () {
            $(".assign-fee-modal").removeClass("hide");
            resetAssignForm();
        });

        // Handle dropdown selection
        $actionSelect.on("change", function () {
            const action = $(this).val();
            toggleAssignInputs(action);
        });

        // Close modal logic
        $(".close-modal, .btn-cancel").on("click", function () {
            closeAssignModal();
        });

        // Clicking outside closes modal
        $(".assign-fee-modal").on("click", function (e) {
            if ($(e.target).is(".assign-fee-modal")) {
                closeAssignModal();
            }
        });

        // Helper: Reset the assign form
        function resetAssignForm() {
            $("#assign-fee-form")[0].reset();
            $yearControl.hide();
            $courseControl.hide();
            $yearSelect.prop('disabled', true);
            $courseSelect.prop('disabled', true);
        }

        // Helper: Toggle input visibility and DYNAMICALLY ENABLE/DISABLE
        function toggleAssignInputs(action) {

            // 1. Hide/Disable All
            $yearControl.fadeOut(0);
            $courseControl.fadeOut(0);
            $yearSelect.prop('disabled', true);
            $courseSelect.prop('disabled', true);

            // 2. Show/Enable based on action
            switch (action) {
                case "year":
                    $yearControl.fadeIn();
                    $yearSelect.prop('disabled', false);
                    break;
                case "course":
                    $courseControl.fadeIn();
                    $courseSelect.prop('disabled', false);
                    break;
                case "year_course":
                    $yearControl.fadeIn();
                    $courseControl.fadeIn();
                    $yearSelect.prop('disabled', false);
                    $courseSelect.prop('disabled', false);
                    break;
            }
        }

        // Helper: Close modal
        function closeAssignModal() {
            $(".assign-fee-modal").addClass("hide");
            resetAssignForm();
        }

        // --- AJAX Submission ---
        $("#assign-fee-form").on("submit", function (e) {
            e.preventDefault();

            // Only serialize the currently enabled fields
            const formData = $(this).serialize();

            $.ajax({
                url: "/financore/src/handler/assign_fee.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        window.location.reload();
                    } else {
                        // If status is false, show a message before reloading
                        console.error("Assignment failed:", response.message);
                        window.location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    // Add a toastr error here if needed
                    window.location.reload();
                }
            })
        });
    });
</script>

<?php if (isset($_SESSION['toastr'])): ?>
    <script>
        toastr["<?= $_SESSION['toastr']['type'] ?>"]("<?= $_SESSION['toastr']['message'] ?>");
    </script>
    <?php unset($_SESSION['toastr']); // <-- reset happens here ?>
<?php endif; ?>
<?php include_once "../../includes/footer.php" ?>