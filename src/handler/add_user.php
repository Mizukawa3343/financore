<?php
session_start();
require_once "../config/dbconn.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];
    $department_id = $_POST["department"];

    if (!$full_name || !$email || !$username || !$password_hash || !$role || !$department_id) {
        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Fill up all required fields"
        ];
        echo json_encode([
            "status" => false,
            "message" => "fill up all fields"
        ]);
    }

    try {


        $sql = "INSERT INTO users(full_name, email, username, password, role, department_id) VALUES(?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$full_name, $email, $username, $password_hash, $role, $department_id])) {

            $_SESSION['toastr'] = [
                "type" => "success",
                "message" => "Successfully created user"
            ];
            echo json_encode(["status" => true]);
            exit;
        } else {
            $_SESSION['toastr'] = [
                "type" => "error",
                "message" => "Failed to add user"
            ];
            echo json_encode([
                "status" => false,
                "message" => "Failed to add user"
            ]);
        }
        exit;

    } catch (Exception $e) {
        $_SESSION['toastr'] = [
            "type" => "error",
            "message" => "Invalid request method." . "Validation error: " . $e->getMessage(),
        ];
        echo json_encode([
            "status" => false,
            "message" => "Invalid Request",
        ]);
    }

} else {
    $_SESSION['toastr'] = [
        "type" => "error",
        "message" => "Invalid request method."
    ];
    echo json_encode([
        "status" => false,
        "message" => "invalid request method"
    ]);

}