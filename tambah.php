<?php
include 'config.php';

$customer  = strtoupper(trim($_POST['customer'] ?? ''));
$spx       = (int) ($_POST['spx'] ?? 0);
$anter     = (int) ($_POST['anter'] ?? 0);
$sicepat   = (int) ($_POST['sicepat'] ?? 0);
$jnt       = (int) ($_POST['jt'] ?? 0);
$jne       = (int) ($_POST['jne'] ?? 0);
$jntcargo  = (int) ($_POST['jntcargo'] ?? 0);
$jnecargo  = (int) ($_POST['jnecargo'] ?? 0);
$lazada    = (int) ($_POST['lazada'] ?? 0);
$pos       = (int) ($_POST['pos'] ?? 0);
$id_express= (int) ($_POST['id_express'] ?? 0);

// Pastikan customer ada
$sql_check = "SELECT id FROM customers WHERE customer_name = '$customer'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $customer_id = $row['id'];
} else {
    $customer_code = "CUST" . time();
    $sql_customer = "INSERT INTO customers (customer_code, customer_name) VALUES ('$customer_code', '$customer')";
    if ($conn->query($sql_customer) === TRUE) {
        $customer_id = $conn->insert_id;
    } else {
        die("Error insert customer: " . $conn->error);
    }
}

// Cek apakah shipment untuk customer ini di hari ini sudah ada
$sql_exist = "SELECT id FROM shipments WHERE customer_id = $customer_id AND shipment_date = CURDATE()";
$res_exist = $conn->query($sql_exist);

if ($res_exist->num_rows > 0) {
    $row = $res_exist->fetch_assoc();
    $shipment_id = $row['id'];

    // UPDATE: tambahkan nilai
    $sql_update = "UPDATE shipments
                   SET spx = spx + $spx,
                       anter = anter + $anter,
                       sicepat = sicepat + $sicepat,
                       jnt = jnt + $jnt,
                       jne = jne + $jne,
                       jnt_cargo = jnt_cargo + $jntcargo,
                       jne_cargo = jne_cargo + $jnecargo,
                       lazada = lazada + $lazada,
                       pos = pos + $pos,
                       id_express = id_express + $id_express
                   WHERE id = $shipment_id";
    $conn->query($sql_update);

    // Catat ke history
    $sql_history = "INSERT INTO history
    (shipment_id, customer_id, spx, anter, sicepat, jnt, jne, jnt_cargo, jne_cargo, lazada, pos, id_express, total, status, history_date)
    VALUES
    ($shipment_id, $customer_id, $spx, $anter, $sicepat, $jnt, $jne, $jntcargo, $jnecargo, $lazada, $pos, $id_express,
    ($spx + $anter + $sicepat + $jnt + $jne + $jntcargo + $jnecargo + $lazada + $pos + $id_express), 'Update', NOW())";
    $conn->query($sql_history);

} else {
    // INSERT baru
    $sql_ship = "INSERT INTO shipments
    (customer_id, spx, anter, sicepat, jnt, jne, jnt_cargo, jne_cargo, lazada, pos, id_express, shipment_date)
    VALUES
    ($customer_id, $spx, $anter, $sicepat, $jnt, $jne, $jntcargo, $jnecargo, $lazada, $pos, $id_express, CURDATE())";

    if ($conn->query($sql_ship) === TRUE) {
        $shipment_id = $conn->insert_id;

        // Catat ke history
        $sql_history = "INSERT INTO history
        (shipment_id, customer_id, spx, anter, sicepat, jnt, jne, jnt_cargo, jne_cargo, lazada, pos, id_express, total, status, history_date)
        VALUES
        ($shipment_id, $customer_id, $spx, $anter, $sicepat, $jnt, $jne, $jntcargo, $jnecargo, $lazada, $pos, $id_express,
        ($spx + $anter + $sicepat + $jnt + $jne + $jntcargo + $jnecargo + $lazada + $pos + $id_express), 'Insert', NOW())";
        $conn->query($sql_history);
    }
}

header("Location: index.php?success=1");
$conn->close();
?>