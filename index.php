<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="data/css/style.css">
    <script src="data/js/script.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="history.php">History</a>
            <a href="mutation.php">Mutation</a>
            <a href="chart.php">Chart</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1 id="header1">DASHBOARD</h1>
            <div class="add-item">
                <button id="btn" onclick="openPopup()">Add Item</button>
            </div>

            <!-- POPUP -->
            <div class="overlay" id="popupOverlay">
                <div class="popup">
                    <span class="close" onclick="closePopup()">&times;</span>
                    <h2>Tambah Item</h2>
                    <form action="tambah.php" method="POST">
                        <label>Customer:</label>
                        <input type="text" name="customer" placeholder="Masukkan nama" required>

                        <label>Spx:</label>
                        <input type="number" name="spx" placeholder="Masukkan jumlah">

                        <label>Anter:</label>
                        <input type="number" name="anter" placeholder="Masukkan jumlah">

                        <label>SiCepat:</label>
                        <input type="number" name="sicepat" placeholder="Masukkan jumlah">

                        <label>J&T:</label>
                        <input type="number" name="jt" placeholder="Masukkan jumlah">

                        <label>JNE:</label>
                        <input type="number" name="jne" placeholder="Masukkan jumlah">

                        <label>JNT Cargo:</label>
                        <input type="number" name="jntcargo" placeholder="Masukkan jumlah">

                        <label>JNE Cargo:</label>
                        <input type="number" name="jnecargo" placeholder="Masukkan jumlah">

                        <label>Lazada:</label>
                        <input type="number" name="lazada" placeholder="Masukkan jumlah">

                        <label>Pos:</label>
                        <input type="number" name="pos" placeholder="Masukkan jumlah">

                        <label>ID Express:</label>
                        <input type="number" name="id_express" placeholder="Masukkan jumlah">

                        <button type="submit">Simpan</button>
                    </form>
                </div>
            </div>

            <!-- TABEL DATA -->
            <section>
                <h2>Data Pengiriman</h2>
                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Spx</th>
                            <th>Anter</th>
                            <th>Sicepat</th>
                            <th>J&T</th>
                            <th>JNE</th>
                            <th>JNT Cargo</th>
                            <th>JNE Cargo</th>
                            <th>Lazada</th>
                            <th>Pos</th>
                            <th>ID Express</th>
                            <th>Total</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT s.id, c.customer_name,
                                s.spx, s.anter, s.sicepat,
                                s.jnt, s.jne, s.jnt_cargo,
                                s.jne_cargo, s.lazada, s.pos, s.id_express, s.total
                                FROM shipments s
                                JOIN customers c ON s.customer_id = c.id
                                WHERE s.shipment_date = CURDATE()";

                        $result = $conn->query($sql);

                        if (!$result) {
                            die("Query error: " . $conn->error);
                        }

                        $no = 1;
                        $totals = [
                            "spx"=>0, "anter"=>0, "sicepat"=>0, "jnt"=>0, "jne"=>0,
                            "jnt_cargo"=>0, "jne_cargo"=>0, "lazada"=>0, "pos"=>0, "id_express"=>0, "total"=>0
                        ];

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()){
                                echo "<tr>
                                    <td>".$no++."</td>
                                    <td>".$row['customer_name']."</td>
                                    <td>".$row['spx']."</td>
                                    <td>".$row['anter']."</td>
                                    <td>".$row['sicepat']."</td>
                                    <td>".$row['jnt']."</td>
                                    <td>".$row['jne']."</td>
                                    <td>".$row['jnt_cargo']."</td>
                                    <td>".$row['jne_cargo']."</td>
                                    <td>".$row['lazada']."</td>
                                    <td>".$row['pos']."</td>
                                    <td>".$row['id_express']."</td>
                                    <td>".$row['total']."</td>
                                    <td>
                                        <a href='edit.php?id=".$row['id']."'>Edit</a> |
                                        <a href='delete.php?id=".$row['id']."' onclick=\"return confirm('Yakin mau hapus data ini?');\">Delete</a>
                                    </td>
                                </tr>";

                                // hitung total
                                $totals['spx']       += $row['spx'];
                                $totals['anter']     += $row['anter'];
                                $totals['sicepat']   += $row['sicepat'];
                                $totals['jnt']       += $row['jnt'];
                                $totals['jne']       += $row['jne'];
                                $totals['jnt_cargo'] += $row['jnt_cargo'];
                                $totals['jne_cargo'] += $row['jne_cargo'];
                                $totals['lazada']    += $row['lazada'];
                                $totals['pos']       += $row['pos'];
                                $totals['id_express']+= $row['id_express'];
                                $totals['total']     += $row['total'];
                            }
                        } else {
                            echo "<tr><td colspan='14' align='center'>Belum ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><?php echo $totals['spx']; ?></td>
                            <td><?php echo $totals['anter']; ?></td>
                            <td><?php echo $totals['sicepat']; ?></td>
                            <td><?php echo $totals['jnt']; ?></td>
                            <td><?php echo $totals['jne']; ?></td>
                            <td><?php echo $totals['jnt_cargo']; ?></td>
                            <td><?php echo $totals['jne_cargo']; ?></td>
                            <td><?php echo $totals['lazada']; ?></td>
                            <td><?php echo $totals['pos']; ?></td>
                            <td><?php echo $totals['id_express']; ?></td>
                            <td><?php echo $totals['total']; ?></td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </section>
    </main>
    <footer>
            <p style="font-size: 1.0rem; text-align: center; background-color: blue; color: white; padding: 2px;">&copy; Shaka Banuasta V2.0</p>
    </footer>
</body>
</html>
