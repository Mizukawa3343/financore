<?php
session_start();
require_once "../config/dbconn.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_row_id = trim($_POST["row_id"]) ?? "";
    $student_id = trim($_POST["student_id"] ?? "");
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    try {
        // Fixed path - assuming this file is in financore/src/handler/
        // Go up 2 levels to reach financore root, then into uploads
        $targetDir = dirname(__DIR__, 2) . "/uploads/";
        $publicPath = "/financore/uploads/";

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                echo json_encode([
                    "status" => false,
                    "message" => "Failed to create uploads directory"
                ]);
                exit;
            }
        }

        // Check if directory is writable
        if (!is_writable($targetDir)) {
            echo json_encode([
                "status" => false,
                "message" => "Uploads directory is not writable. Path: " . $targetDir
            ]);
            exit;
        }

        $profilePath = null;

        // Check if a new file is uploaded
        if (!empty($_FILES["student_profile"]["name"])) {
            // Check for upload errors
            if ($_FILES["student_profile"]["error"] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize in php.ini",
                    UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE in HTML form",
                    UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                    UPLOAD_ERR_NO_FILE => "No file was uploaded",
                    UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                    UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
                ];

                $error = $_FILES["student_profile"]["error"];
                $message = $errorMessages[$error] ?? "Unknown upload error";

                echo json_encode(["status" => false, "message" => $message]);
                exit;
            }

            $fileName = basename($_FILES["student_profile"]["name"]);
            $newFile = uniqid() . "_" . $fileName;
            $targetFile = $targetDir . $newFile;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode([
                    "status" => false,
                    "message" => "Only JPG, PNG, GIF allowed"
                ]);
                exit;
            }

            // Validate size (max 2MB)
            if ($_FILES["student_profile"]["size"] > 2 * 1024 * 1024) {
                echo json_encode([
                    "status" => false,
                    "message" => "File size exceeds 2MB"
                ]);
                exit;
            }

            // Additional validation: check if file is actually an image
            $check = getimagesize($_FILES["student_profile"]["tmp_name"]);
            if ($check === false) {
                echo json_encode([
                    "status" => false,
                    "message" => "File is not a valid image"
                ]);
                exit;
            }

            // Try to move the uploaded file
            if (!move_uploaded_file($_FILES["student_profile"]["tmp_name"], $targetFile)) {
                echo json_encode([
                    "status" => false,
                    "message" => "File upload failed. Target: " . $targetFile
                ]);
                exit;
            }

            $profilePath = $publicPath . $newFile;
        }

        // Conditional update query
        if ($profilePath) {
            $sql = "UPDATE students
                    SET student_id = ?, last_name = ?, first_name = ?, course = ?, year = ?, gender = ?, picture = ?
                    WHERE id = ?";
            $params = [$student_id, $last_name, $first_name, $course, $year, $gender, $profilePath, $student_row_id];
        } else {
            $sql = "UPDATE students
                    SET student_id = ?, last_name = ?, first_name = ?, course = ?, year = ?, gender = ?
                    WHERE id = ?";
            $params = [$student_id, $last_name, $first_name, $course, $year, $gender, $student_row_id];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $_SESSION['toastr'] = [
            "type" => "success",
            "message" => "Successfully updated student info"
        ];

        echo json_encode(["status" => true]);

    } catch (PDOException $e) {
        echo json_encode([
            "status" => false,
            "message" => "System error: " . $e->getMessage()
        ]);
    }
}