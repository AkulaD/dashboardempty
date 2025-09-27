<?php
include "config.php"; // koneksi ke database

// Ambil data mutasi per hari
$sql = "SELECT
            shipment_date,
            COUNT(DISTINCT customer_id) AS total_customer,
            SUM(spx) AS total_spx,
            SUM(anter) AS total_anter,
            SUM(sicepat) AS total_sicepat,
            SUM(jnt) AS total_jnt,
            SUM(jne) AS total_jne,
            SUM(jnt_cargo) AS total_jnt_cargo,
            SUM(jne_cargo) AS total_jne_cargo,
            SUM(lazada) AS total_lazada,
            SUM(pos) AS total_pos,
            SUM(id_express) AS total_id_express,
            SUM(total) AS total_all
        FROM shipments
        GROUP BY shipment_date
        ORDER BY shipment_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutation</title>
    <link rel="stylesheet" href="data/css/style.css">
    <link rel="stylesheet" href="data/css/mutation.css">
    <script src="data/js/script.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="history.php">History</a>
            <a href="mutation.php">Mutation</a>
            <a href="setting.php">Settings</a>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h1 class="headerH">MUTATION</h1>
            <div class="mutation">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Customer</th>
                            <th>Total Spx</th>
                            <th>Total Anter</th>
                            <th>Total Sicepat</th>
                            <th>Total J&T</th>
                            <th>Total JNE</th>
                            <th>Total JNT Cargo</th>
                            <th>Total JNE Cargo</th>
                            <th>Total Lazada</th>
                            <th>Total Pos</th>
                            <th>Total ID Express</th>
                            <th>Total All</th>
                            <th>Download Excel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['shipment_date'] ?></td>
                                    <td><?= $row['total_customer'] ?></td>
                                    <td><?= $row['total_spx'] ?></td>
                                    <td><?= $row['total_anter'] ?></td>
                                    <td><?= $row['total_sicepat'] ?></td>
                                    <td><?= $row['total_jnt'] ?></td>
                                    <td><?= $row['total_jne'] ?></td>
                                    <td><?= $row['total_jnt_cargo'] ?></td>
                                    <td><?= $row['total_jne_cargo'] ?></td>
                                    <td><?= $row['total_lazada'] ?></td>
                                    <td><?= $row['total_pos'] ?></td>
                                    <td><?= $row['total_id_express'] ?></td>
                                    <td><?= $row['total_all'] ?></td>
                                    <td><a href="downloadMutation.php?date=<?= $row['shipment_date'] ?>"><button class="btnDownload">Download</button></a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" style="text-align:center;">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>