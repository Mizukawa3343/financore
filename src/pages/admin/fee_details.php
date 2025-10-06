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
            <button class="btn btn-sm btn-icon  btn-primary">
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