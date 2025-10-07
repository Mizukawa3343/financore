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
$fee = get_fee_by_id($conn, $fee_id);
$courses = get_courses_by_department_id($conn, $_SESSION["department_id"]);
?>
<div class="section-header">
    <div class="fee-details-wrapper">
        <div class="left">
            <a href="./fees.php">Back to fees</a>
            <i class="bi bi-chevron-right"></i>
            <h1 class="title"><?= $fee["description"] ?></h1>
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


<div class="modal-overlay hide">
    <div class="modal">
        <button class="close-modal"><i class="bi bi-x"></i></button>
        <h2 class="modal-title">add student</h2>
        <form id="add-fee-form">
            <div class="row-col">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" />
            </div>
            <div class="col-2">
                <div class="row-col">
                    <label for="amount">Fee amount</label>
                    <input type="number" id="amount" name="amount" placeholder="Enter amount" />
                </div>

                <div class="row-col">
                    <label for="due_date">Due date</label>
                    <input type="date" id="due_date" name="due_date" placeholder="Enter due date" />
                </div>
            </div>


            <div class="form-btn">
                <button type="submit" class="btn btn-md btn-primary">Save</button>
                <button type="button" class="btn btn-md btn-secondary btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>
<div class="fee-dashboard">
    <div class="metric-cards">
        <div class="card">
            <div class="card-left">
                <p>Amount</p>
                <h2>P1000</h2>
            </div>
            <div class="card-right">
                <i class="bi bi-cash"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total collected</p>
                <h2>P8000</h2>
            </div>
            <div class="card-right">
                <i class="bi bi-piggy-bank"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total to collect</p>
                <h2>P15000</h2>
            </div>
            <div class="card-right">
                <i class="bi bi-bank"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-left">
                <p>Total students</p>
                <h2>364</h2>
            </div>
            <div class="card-right">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
</div>



<div class="modal-overlay assign-fee-modal hide">
    <div class="modal">
        <button class="close-modal"><i class="bi bi-x"></i></button>
        <h2 class="modal-title">add student</h2>
        <form id="assign-fee-form">
            <div class="row-col">
                <label for="assign-fee-type">Assign fee to:</label>
                <select name="assign-fee-type" id="assign-fee-type">
                    <option value="">Select action</option>
                    <option value="all">all students</option>
                    <option value="year">all students by year</option>
                    <option value="course">all students by course</option>
                    <option value="course-year">all students by course and year</option>
                </select>
            </div>

            <div class="row-col" id="year-wrapper">
                <label for="year">year</label>
                <input type="text" name="year" id="year">
            </div>

            <div class="row-col" id="course-wrapper">
                <label for="course">course</label>
                <select name="course" id="course">
                    <option value="">Select course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course["id"] ?>"><?= $course["course"] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row-col" id="course-year-wrapper">
                <div class="row-col">
                    <label for="course">course</label>
                    <select name="course" id="course">
                        <option value="">Select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course["id"] ?>"><?= $course["course"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row-col">
                    <label for="year">year</label>
                    <input type="text" name="year" id="year">
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

        // When user clicks "Assign fee"
        $("#btn-assign").on("click", function () {
            $(".assign-fee-modal").removeClass("hide");
            resetAssignForm();
        });

        // Handle dropdown selection
        $("#assign-fee-type").on("change", function () {
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
            $("#year-wrapper, #course-wrapper, #course-year-wrapper").hide();
        }

        // Helper: Toggle input visibility
        function toggleAssignInputs(action) {
            const wrappers = {
                year: $("#year-wrapper"),
                course: $("#course-wrapper"),
                courseYear: $("#course-year-wrapper")
            };

            // Hide all by default
            Object.values(wrappers).forEach($el => $el.hide());

            // Show only what’s needed
            switch (action) {
                case "year":
                    wrappers.year.fadeIn();
                    break;
                case "course":
                    wrappers.course.fadeIn();
                    break;
                case "course-year":
                    wrappers.courseYear.fadeIn();
                    break;
            }
        }

        // Helper: Close modal
        function closeAssignModal() {
            $(".assign-fee-modal").addClass("hide");
            resetAssignForm();
        }

        $("#assign-fee-form").on("submit", function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            console.log(formData);

        })
    });

</script>
<?php if (isset($_SESSION['toastr'])): ?>
    <script>
        toastr["<?= $_SESSION['toastr']['type'] ?>"]("<?= $_SESSION['toastr']['message'] ?>");
    </script>
    <?php unset($_SESSION['toastr']); // <-- reset happens here ?>
<?php endif; ?>
<?php include_once "../../includes/footer.php" ?>