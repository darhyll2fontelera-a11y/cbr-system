<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$admin = $_SESSION['admin'];

$stmt = $conn->prepare("DELETE FROM admins WHERE username = ?");
$stmt->bind_param("s", $admin);
$stmt->execute();

session_destroy();

echo "<script>alert('Account deleted successfully.'); window.location='login.php';</script>";
?>
