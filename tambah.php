<?php
include 'config.php';

// Ambil data POST
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

// Cek customer sudah ada atau belum
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

// Cek apakah customer ini sudah punya data hari ini
$sql_exist = "SELECT id FROM shipments WHERE customer_id = $customer_id AND shipment_date = CURDATE()";
$res_exist = $conn->query($sql_exist);

if ($res_exist->num_rows > 0) {
    $row = $res_exist->fetch_assoc();
    $shipment_id = $row['id'];

    // UPDATE data lama
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

    // Catat history
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

$conn->close();

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="1;url=index.php?success=1">
    <style>
        body {
            margin: 0; padding: 0;
            display: flex; align-items: center; justify-content: center;
            height: 100vh; background: #f0f0f0; font-family: Arial, sans-serif;
        }
        .loading-overlay {
            text-align: center;
        }
        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid blue;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 0.5s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p { color: #555; font-size: 16px; }
    </style>
</head>
<body>
    <div class="loading-overlay">
        <div class="loader"></div>
        <p>Menyimpan data, mohon tunggu...</p>
    </div>
</body>
</html>
HTML;
?>
