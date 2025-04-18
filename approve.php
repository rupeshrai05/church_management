<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];
$action = $_GET['action'];
$status_filter = $_GET['status'] ?? 'all';

$status = ($action == 'approve') ? 'approved' : 'rejected';

if ($type == 'appointment') {
    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
} else {
    $stmt = $conn->prepare("UPDATE weddings SET status=? WHERE id=?");
}

$stmt->bind_param("si", $status, $id);
$stmt->execute();

$redirect = $type == 'appointment' ? 'admin_dashboard.php' : 'admin_weddings.php';
header("Location: $redirect?status=$status_filter");
exit();
?>