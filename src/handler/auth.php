<?php
session_start();
require_once "../config/dbconn.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    try {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            $token = bin2hex(random_bytes(16));
            $update = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
            $update->execute([$token, $user["id"]]);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["department_id"] = $user["department_id"];
            $_SESSION["profile"] = $user["profile"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["token"] = $token;

            if ($user["role"] === "admin") {
                $redirect = "./admin/dashboard.php";
            } else {
                $redirect = "./superadmin/dashboard.php";
            }

            echo json_encode([
                "status" => true,
                "message" => "Login successful",
                "redirect" => $redirect,
                "role" => $user["role"],
                "token" => $token
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Invalid credentials, please try again."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => false,
            "message" => "System error, please try again later."
        ]);
    }
} else {
    echo json_encode([
        "status" => false,
        "message" => "Invalid request method."
    ]);
}
