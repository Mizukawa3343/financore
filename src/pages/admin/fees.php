<?php
session_start();
include_once "../../includes/header.php";
require_once "../../config/dbconn.php";
require_once "../../api/admin_query.php";
$fees = get_all_fees_by_department_id($conn, $_SESSION['department_id']);
?>
<div class="section-header">
    <h1 class="title">Fees</h1>
    <button class="btn btn-icon btn-md btn-primary" id="btn-fee">
        <i class="bi bi-plus-square"></i>
        <span>add fee</span>
    </button>
</div>

<div class="fees-container">
    <?php foreach ($fees as $fee): ?>
        <div class="fee-card">
            <div class="fee-header">
                <h2><?= $fee["description"] ?></h2>
                <i class="bi bi-folder2"></i>
            </div>
            <div class="fee-summary">
                <h3>
                    <strong>₱9000</strong>
                    <span>/</span>
                    <strong>₱15000</strong>
                </h3>
                <div class="progress-container">
                    <progress class="fee-progress" value="9000" max="15000"></progress>
                    <span>60%</span>
                </div>
            </div>
            <a href="./fee_details.php?fee_id=<?= $fee["id"] ?>" class="btn btn-full btn-icon btn-md btn-primary">
                <i class="bi bi-folder2-open"></i>
                <span>show more info</span>
            </a>
        </div>
    <?php endforeach; ?>
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
<script>
    $(document).ready(function () {
        const openModalBtn = $("#btn-fee");
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
            $("#add-fee-form")[0].reset();
        });

        modalOverlay.on("click", function (e) {
            if ($(e.target).is(modalOverlay)) {
                modalOverlay.addClass("hide");
            }
        });

        $("#add-fee-form").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "/financore/src/handler/add_fee.php",
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