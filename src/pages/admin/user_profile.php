<?php
session_start();
require_once "../../config/dbconn.php";
include_once "../../includes/header.php";
include_once "../../util/helper.php";
include_once "../../api/admin_query.php";

$user = get_user_by_id($conn, $_SESSION["user_id"]);
$department = get_department_by_id($conn, $_SESSION["department_id"]);

?>
<div class="section-header">
    <h1 class="title">Profile settings</h1>
</div>

<div class="user-profile-wrapper">

    <div class="student-profile">
        <div class="top">
            <div class="background"></div>
            <img src="/financore/assets/system-images/student-default-profile.png" alt="">
        </div>
        <h2><?= $user["full_name"] ?></h2>
        <div class=" student-info">
            <div class="info">
                <i class="bi bi-person-vcard"></i>
                <div class="detail">
                    <span>Email</span>
                    <h3><?= $user["email"] ?></h3>
                </div>
            </div>

            <div class="info">
                <i class="bi bi-journal-code"></i>
                <div class="detail">
                    <span>username</span>
                    <h3><?= $user["username"] ?></h3>
                </div>
            </div>

            <div class="info">
                <i class="bi bi-calendar"></i>
                <div class="detail">
                    <span>role</span>
                    <h3><?= $user["role"] ?></h3>
                </div>
            </div>

            <div class="info">
                <i class="bi bi-building"></i>
                <div class="detail">
                    <span>department</span>
                    <h3><?= $department["acronym"] ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="user-settings">
            <div>
                <h3 class="box-title">update user data</h3>
                <form>
                    <div class="col-2">
                        <div class="row-col">
                            <label for="full_name">Fullname</label>
                            <input type="text" name="full_name" id="full_name" value="<?= $user["full_name"] ?>">
                        </div>
                        <div class="row-col">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="<?= $user["email"] ?>">
                        </div>
                    </div>
                    <div class="row-col">
                        <label for="user_profile">add profile picture</label>
                        <input type="file" name="user_profile" id="user_profile">
                    </div>
                    <div class="form-button-container">
                        <button class="btn btn-primary btn-icon btn-md">
                            update account
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="user-password">
            <h3 class="box-title">update password</h3>
            <form>
                <div class="row-col">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>

                <div class="col-2">
                    <div class="row-col">
                        <label for="new_password">New password</label>
                        <input type="password" name="new_password" id="new_password">
                    </div>

                    <div class="row-col">
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password">
                    </div>
                </div>
                <div class="form-button-container">
                    <button class="btn btn-primary btn-icon btn-md">
                        update password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once "../../includes/footer.php"; ?>