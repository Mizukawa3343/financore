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
$fees = get_outstanding_fees_by_student_id($conn, $student_id);

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
                                    <th>Balance</th>
                                    <th>Due date</th>
                                    <th>Status</th>
                                    <th>ACtion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fees as $fee): ?>
                                    <tr>
                                        <td><?= $fee["fee_name"] ?></td>
                                        <td><?= $fee["amount_due"] ?></td>
                                        <td><?= $fee["current_balance"] ?></td>
                                        <td><?= $fee["due_date"] ?></td>
                                        <td>
                                            <p class="fee-status <?= $fee["status"] ?>"><?= $fee["status"] ?></p>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-icon btn-success btn-pay"
                                                data-fees-id="<?= $fee["fees_id"] ?>"
                                                data-fees-amount="<?= $fee["current_balance"] ?>">
                                                <i class="bi bi-credit-card-2-front-fill"></i>
                                                <span>Pay</span>
                                            </button>
                                        </td>
                                    </tr>
                                    </tr>
                                <?php endforeach; ?>

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

<div class="modal-overlay payment-modal hide">
    <div class="modal">
        <button class="close-modal"><i class="bi bi-x"></i></button>
        <h2 class="modal-title">Process payment</h2>
        <form id="payment-form">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            <input type="hidden" name="fees_id" id="fees_id">
            <div class="row-col">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" />
            </div>
            <div class="row-col">
                <label for="payment_method">Payment method</label>
                <select name="payment_method" id="payment_method">
                    <option value="Cash">Cash</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>


            <div class="form-btn">
                <button type="submit" class="btn btn-md btn-primary">confirm</button>
                <button type="button" class="btn btn-md btn-secondary btn-cancel">Cancel</button>
            </div>
        </form>
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

        $(".btn-pay").on("click", function () {
            const fees_id = $(this).data("fees-id");
            const fees_amount = $(this).data("fees-amount");
            $(".payment-modal").removeClass("hide");
            $("#amount").val(fees_amount);
            $("#fees_id").val(fees_id);
        });

        // Close Modal
        $(".close-modal").on("click", function () {
            $(".payment-modal").addClass("hide");
        });

        $(".btn-cancel").on("click", function () {
            $(".payment-modal").addClass("hide");

        });

        $(".payment-modal").on("click", function (e) {
            if ($(e.target).is($(".payment-modal"))) {
                $(".payment-modal").addClass("hide");
            }
        });

        $("#payment-form").on("submit", function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: "/financore/src/handler/process_payment.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        window.open(`./receipt.php?receipt_id=${response.receipt_id}`, "_blank");
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            })

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