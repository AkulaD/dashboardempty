<?php
include "config.php"; 

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']); 

$sql = "SELECT s.*, c.customer_name, c.id as customer_id
        FROM shipments s
        JOIN customers c ON s.customer_id = c.id
        WHERE s.id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Data tidak ditemukan!");
}

$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spx       = intval($_POST['spx']);
    $anter     = intval($_POST['anter']);
    $sicepat   = intval($_POST['sicepat']);
    $jnt       = intval($_POST['jnt']);
    $jne       = intval($_POST['jne']);
    $jnt_cargo = intval($_POST['jnt_cargo']);
    $jne_cargo = intval($_POST['jne_cargo']);
    $lazada    = intval($_POST['lazada']);
    $pos       = intval($_POST['pos']);
    $id_express= intval($_POST['id_express']);

    $total = $spx + $anter + $sicepat + $jnt + $jne + $jnt_cargo + $jne_cargo + $lazada + $pos + $id_express;

    $update = "UPDATE shipments SET
                spx = $spx,
                anter = $anter,
                sicepat = $sicepat,
                jnt = $jnt,
                jne = $jne,
                jnt_cargo = $jnt_cargo,
                jne_cargo = $jne_cargo,
                lazada = $lazada,
                pos = $pos,
                id_express = $id_express
                WHERE id = $id";

    if ($conn->query($update)) {

        $shipment_id = $id;
        $customer_id = $data['customer_id'];
        $sql_history = "INSERT INTO history
            (shipment_id, customer_id, spx, anter, sicepat, jnt, jne, jnt_cargo, jne_cargo, lazada, pos, id_express, total, status, history_date)
            VALUES
            ($shipment_id, $customer_id, $spx, $anter, $sicepat, $jnt, $jne, $jnt_cargo, $jne_cargo, $lazada, $pos, $id_express, $total, 'Edit', NOW())";

        $conn->query($sql_history); 

        echo "<script>alert('Data berhasil diupdate'); window.location='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<link rel="stylesheet" href="data/css/my.css">
<link rel="stylesheet" href="data/css/edit.css">

<main>
    <section class="edit-body">
        <a href="index.php">KELUAR</a>
        <h2>Edit Data Shipment</h2>
        <form method="POST">
            <p><b>Customer:</b> <?php echo $data['customer_name']; ?></p>
            <label>Spx: <input type="number" name="spx" value="<?php echo $data['spx']; ?>"></label><br><br>
            <label>Anter: <input type="number" name="anter" value="<?php echo $data['anter']; ?>"></label><br><br>
            <label>Sicepat: <input type="number" name="sicepat" value="<?php echo $data['sicepat']; ?>"></label><br><br>
            <label>J&T: <input type="number" name="jnt" value="<?php echo $data['jnt']; ?>"></label><br><br>
            <label>JNE: <input type="number" name="jne" value="<?php echo $data['jne']; ?>"></label><br><br>
            <label>JNT Cargo: <input type="number" name="jnt_cargo" value="<?php echo $data['jnt_cargo']; ?>"></label><br><br>
            <label>JNE Cargo: <input type="number" name="jne_cargo" value="<?php echo $data['jne_cargo']; ?>"></label><br><br>
            <label>Lazada: <input type="number" name="lazada" value="<?php echo $data['lazada']; ?>"></label><br><br>
            <label>Pos: <input type="number" name="pos" value="<?php echo $data['pos']; ?>"></label><br><br>
            <label>ID Express: <input type="number" name="id_express" value="<?php echo $data['id_express']; ?>"></label><br><br>

            <button type="submit">Simpan</button>
        </form>
    </section>
</main>