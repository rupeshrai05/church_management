<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=donations.csv');

$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, array('Date', 'Donor Name', 'Amount', 'Transaction ID', 'Phone', 'Note'));

// Data rows
$result = $conn->query("SELECT * FROM donations ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        date('d M Y h:i A', strtotime($row['created_at'])),
        $row['full_name'],
        $row['amount'],
        $row['transaction_id'],
        $row['phone'],
        $row['note']
    ));
}

fclose($output);
exit;