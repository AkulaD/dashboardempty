<?php
include "config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM shipments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$shipment = $result->fetch_assoc();

if (!$shipment) {
    die("Data tidak ditemukan!");
}

$sql_delete = "DELETE FROM shipments WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id);
$stmt_delete->execute();

$sql_history = "DELETE FROM history WHERE shipment_id = ?";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $id);
$stmt_history->execute();

header("Location: index.php");
exit;
?>
