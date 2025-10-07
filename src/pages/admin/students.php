<?php
session_start();
require_once "../../config/dbconn.php";
include_once "../../api/admin_query.php";
include_once "../../includes/header.php";

$courses = get_courses_by_department_id($conn, $_SESSION['department_id']);
$students_list = get_all_students_by_department_id($conn, $_SESSION['department_id']);

?>
<div class="section-header">
    <h1 class="title">Students</h1>
    <button class="btn btn-icon btn-md btn-primary" id="btn-student">
        <i class="bi bi-person-add"></i>
        <span>add student</span>
    </button>
</div>

<div class="students-tab">
    <button class="btn-tab active" data-target="list">List</button>
    <button class="btn-tab" data-target="balances">Balances</button>
    <button class="btn-tab" data-target="overdues">Overdues</button>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="list">
        <div class="table-wrapper">
            <table id="students-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>student id</th>
                        <th>lastname</th>
                        <th>firstname</th>
                        <th>gender</th>
                        <th>course</th>
                        <th>year</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students_list as $student): ?>
                        <tr>
                            <td></td>
                            <td><?= $student["student_id"] ?></td>
                            <td><?= $student["last_name"] ?></td>
                            <td><?= $student["first_name"] ?></td>
                            <td><?= $student["gender"] ?></td>
                            <td><?= $student["course_name"] ?></td>
                            <td><?= $student["year"] ?></td>
                            <td style="display: flex; align-items: center; justify-content: center; ">
                                <a href="./student_profile.php?student_id=<?= $student["id"] ?>" class="btn btn-icon btn-sm
                                btn-secondary">
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

    <div class="tab-pane" id="balances">
        <h1>Table for Balances</h1>
    </div>

    <div class="tab-pane" id="overdues">
        <h1>Table for Overdues</h1>
    </div>
</div>

<!-- Modal -->
<div class="modal-overlay hide">
    <div class="modal">
        <button class="close-modal"><i class="bi bi-x"></i></button>
        <h2 class="modal-title">add student</h2>
        <form id="add-student-form">
            <div class="row-col">
                <label for="student_id">student id</label>
                <input type="text" id="student_id" name="student_id" placeholder="Enter student id" />
            </div>
            <div class="col-2">
                <div class="row-col">
                    <label for="first_name">firstname</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter firstname" />
                </div>

                <div class="row-col">
                    <label for="last_name">lastname</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter lastname" />
                </div>
            </div>

            <div class="col-3">
                <div class="row-col">
                    <label for="course">course</label>
                    <select name="course" id="course">
                        <option value="">select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id']; ?>"><?= $course['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row-col">
                    <label for="year">year</label>
                    <select name="year" id="year">
                        <option value="">select year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="row-col">
                    <label for="gender">gender</label>
                    <select name="gender" id="gender">
                        <option value="">select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
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
        $('#students-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 6, // ✅ Show only 5 entries by default
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


        const openModalBtn = $("#btn-student");
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
            $("#add-student-form")[0].reset();
        });

        modalOverlay.on("click", function (e) {
            if ($(e.target).is(modalOverlay)) {
                modalOverlay.addClass("hide");
            }
        });

        // Tab Functionality
        $(".btn-tab").on("click", function () {
            const target = $(this).data("target");

            // Remove active class from all buttons
            $(".btn-tab").removeClass("active");
            $(this).addClass("active");


            $(".tab-pane").removeClass("active");
            $("#" + target).addClass("active");
        });

        $("#add-student-form").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "/financore/src/handler/add_student.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
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