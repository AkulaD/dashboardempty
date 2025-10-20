<?php
include 'config.php';

// --- Ambil tanggal dari input
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// --- Jika user pilih "all", tampilkan semua tanggal
if ($selected_date === 'all') {
    $sql = "SELECT h.*, c.customer_name 
            FROM history h
            JOIN customers c ON h.customer_id = c.id
            ORDER BY h.history_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT h.*, c.customer_name 
            FROM history h
            JOIN customers c ON h.customer_id = c.id
            WHERE DATE(h.history_date) = ?
            ORDER BY h.history_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_date);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="data/css/my.css">
    <link rel="stylesheet" href="data/css/historyempty.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="history.php" class="active">History</a>
        <a href="mutation.php">Mutation</a>
        <a href="chart.php">Chart</a>
    </nav>
</header>

<main>
    <section class="hero">
        <h1 class="headerH">HISTORY</h1>

        <div class="filter-box">
            <form method="get" action="">
                <label for="date">Pilih tanggal:</label>
                <input type="date" name="date" id="date"
                    value="<?php echo ($selected_date !== 'all') ? $selected_date : ''; ?>">
                <button type="submit" class="all-btn">Tampilkan</button>
                <button type="submit" name="date" value="all" class="all-btn">Semua Tanggal</button>
            </form>
        </div>


        <p class="dateH">
            <?php
            if ($selected_date === 'all') {
                echo "Menampilkan <b>semua tanggal</b>";
            } else {
                echo "Menampilkan data untuk tanggal: <b>" . date('d M Y', strtotime($selected_date)) . "</b>";
            }
            ?>
        </p>

        <table class="history-table">
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
                    $no = 1;
                    $current_date = '';

                    while($row = $result->fetch_assoc()) {
                        $row_date = date('d M Y', strtotime($row['history_date']));
                        $row_time = date('H:i:s', strtotime($row['history_date']));

                        if ($selected_date === 'all' && $row_date != $current_date) {
                            $current_date = $row_date;
                            echo "<tr class='date-row'>
                                    <td colspan='15'>Date: $current_date</td>
                                  </tr>";
                            $no = 1;
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
                    echo "<tr><td colspan='15' align='center'>Tidak ada data ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>
<footer>
    <p>&copy; Shaka Banuasta V2.0</p></footer>
</body>
</html>
