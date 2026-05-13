<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // --- 1. Check if username already exists ---
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: register1.php?error=username_taken");
        exit;
    }
    $check->close();

    // --- 2. Check if email already exists ---
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        header("Location: register1.php?error=email_taken");
        exit;
    }
    $checkEmail->close();

    // --- 3. Insert new user ---
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: user_login.php?success=registered");
        exit;
    } else {
        header("Location: register1.php?error=server_error");
        exit;
    }

} else {
    header("Location: register1.php");
    exit;
}
?>