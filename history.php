<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="data/css/style.css">
    <link rel="stylesheet" href="data/css/history.css">
    <style>
        /* Tambahan CSS sederhana untuk tanggal header */
        .date-row {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #4c5baf;
            color: white;
        }
        table td.time-col {
            text-align: center;
        }
    </style>
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
            <h1 id="headerH">HISTORY</h1>
            <div>
                <?php
                // ambil data history + customer name
                $sql = "SELECT h.*, c.customer_name
                        FROM history h
                        JOIN customers c ON h.customer_id = c.id
                        ORDER BY h.history_date DESC";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    // ambil tanggal terbaru untuk di atas
                    $latest_row = $result->fetch_assoc();
                    $latest_date = date('d M Y', strtotime($latest_row['history_date']));
                    echo "<p class='dateH'>Latest date: $latest_date</p>";

                    // reset pointer agar bisa loop dari awal lagi
                    $result->data_seek(0);
                } else {
                    echo "<p class='dateH'>Latest date: -</p>";
                }
                ?>

                <table>
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
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $current_date = '';
                            $no = 1;

                            while($row = $result->fetch_assoc()) {
                                $row_date = date('d M Y', strtotime($row['history_date']));
                                $row_time = date('H:i:s', strtotime($row['history_date']));

                                // jika tanggal berbeda, tampilkan baris tanggal baru
                                if ($row_date != $current_date) {
                                    $current_date = $row_date;
                                    echo "<tr class='date-row'>
                                            <td colspan='15'>Date: $current_date</td>
                                          </tr>";
                                    $no = 1; // reset nomor urut per tanggal
                                }

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
                                    <td>".$row['status']."</td>
                                    <td class='time-col'>".$row_time."</td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='15' align='center'>Belum ada data history</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>