<?php
session_start();
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
$student_id = $_GET['student_id'] ?? "";
if (!isset($student_id) || $student_id == "") {
    header("Location: ./students.php");
    exit;
}
include_once "../../includes/header.php";

$student = get_student_info_by_id($conn, $student_id);


?>
<div class="section-header">
    <div class="fee-details-wrapper">
        <div class="left">
            <a href="./students.php">Back to students</a>
            <i class="bi bi-chevron-right"></i>
            <h1 class="title">Student profile</h1>
        </div>
        <div class="right">
            <button class="btn btn-md btn-icon btn-secondary">
                <i class="bi bi-pencil-square"></i>
                <span>Edit</span>
            </button>
            <button class="btn btn-md btn-icon btn-danger">
                <i class="bi bi-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    </div>
</div>

<div class="student-container">
    <div class="student-wrapper">
        <div class="student-profile">
            <div class="top">
                <div class="background"></div>
                <img src="/financore/assets/system-images/profile.png" alt="">
            </div>
            <h2><?= $student["student_name"] ?></h2>
            <div class=" student-info">
                <div class="info">
                    <i class="bi bi-person-vcard"></i>
                    <div class="detail">
                        <span>Student id</span>
                        <h3><?= $student["student_id"] ?></h3>
                    </div>
                </div>

                <div class="info">
                    <i class="bi bi-journal-code"></i>
                    <div class="detail">
                        <span>Course</span>
                        <h3><?= $student["course"] ?></h3>
                    </div>
                </div>

                <div class="info">
                    <i class="bi bi-calendar"></i>
                    <div class="detail">
                        <span>Year</span>
                        <h3><?= $student["year"] ?></h3>
                    </div>
                </div>

                <div class="info">
                    <i class="bi bi-building"></i>
                    <div class="detail">
                        <span>department</span>
                        <h3><?= $student["department_acronym"] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="student-fees-wrapper">

            <div class="student-tabs">
                <div class="student-fees-tab">
                    <button class="btn-tab active" data-target="fees">Fees</button>
                    <button class="btn-tab" data-target="history">History</button>
                </div>
            </div>


            <div class="tab-content">
                <div class="tab-pane active" id="fees">
                    <h3 class="table-title">Student Fees</h3>
                    <div class="student-table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fee</th>
                                    <th>Amount</th>
                                    <th>Due date</th>
                                    <th>Status</th>
                                    <th>ACtion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Bicol Youth for Technology Expo</td>
                                    <td>1000</td>
                                    <td>October 15, 2025</td>
                                    <td>unpaid</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-success">
                                            <i class="bi bi-credit-card-2-front-fill"></i>
                                            <span>Pay</span>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="history">
                    <h3 class="table-title">Payment history</h3>
                    <div class="student-table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Fee</th>
                                    <th>Amount Paid</th>
                                    <th>Received by</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>October 15, 2025</td>
                                    <td>Bicol Youth for Technology Expo</td>
                                    <td>1000</td>
                                    <td>Vanessa cerdan</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-secondary">
                                            <i class="bi bi-credit-card-2-front-fill"></i>
                                            <span>Receipt</span>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>





<script>
    $(document).ready(function () {
        $(".btn-tab").on("click", function () {
            const target = $(this).data("target");

            // Remove active class from all buttons
            $(".btn-tab").removeClass("active");
            $(this).addClass("active");


            $(".tab-pane").removeClass("active");
            $("#" + target).addClass("active");
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