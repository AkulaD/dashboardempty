<?php
include 'config.php';

// Ambil tanggal dari form (jika ada)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// --- 1️⃣ Grafik total pengiriman per hari ---
$query_perhari = "
    SELECT shipment_date, SUM(total) AS total_harian
    FROM shipments
    GROUP BY shipment_date
    ORDER BY shipment_date ASC
";
$result_perhari = $conn->query($query_perhari);
$labels_harian = [];
$data_harian = [];
while ($row = $result_perhari->fetch_assoc()) {
    $labels_harian[] = $row['shipment_date'];
    $data_harian[] = $row['total_harian'];
}

// --- 2️⃣ Grafik ekspedisi per tanggal terpilih ---
$query_ekspedisi = "
    SELECT 
        SUM(spx) AS spx, SUM(anter) AS anter, SUM(sicepat) AS sicepat,
        SUM(jnt) AS jnt, SUM(jne) AS jne, SUM(jnt_cargo) AS jnt_cargo,
        SUM(jne_cargo) AS jne_cargo, SUM(lazada) AS lazada,
        SUM(pos) AS pos, SUM(id_express) AS id_express, SUM(goto) AS goto
    FROM shipments
    WHERE shipment_date = '$selected_date'
";
$result_ekspedisi = $conn->query($query_ekspedisi)->fetch_assoc();
$labels_ekspedisi = array_keys($result_ekspedisi);
$data_ekspedisi = array_values($result_ekspedisi);

// --- 3️⃣ Grafik total pengiriman per customer (hari terpilih) ---
$query_customer = "
    SELECT c.customer_name, SUM(s.total) AS total_pengiriman
    FROM shipments s
    JOIN customers c ON s.customer_id = c.id
    WHERE shipment_date = '$selected_date'
    GROUP BY c.customer_name
    ORDER BY total_pengiriman DESC
";
$result_customer = $conn->query($query_customer);
$labels_customer = [];
$data_customer = [];
while ($row = $result_customer->fetch_assoc()) {
    $labels_customer[] = $row['customer_name'];
    $data_customer[] = $row['total_pengiriman'];
}

// --- 4️⃣ Tabel detail pengiriman hari terpilih ---
$query_tabel = "
    SELECT s.id, c.customer_name, s.shipment_date, s.total
    FROM shipments s
    JOIN customers c ON s.customer_id = c.id
    WHERE s.shipment_date = '$selected_date'
    ORDER BY s.id DESC
";
$result_tabel = $conn->query($query_tabel);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Pengiriman</title>
    <link rel="stylesheet" href="data/css/my.css">
    <link rel="stylesheet" href="data/css/chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="history.php">History</a>
        <a href="mutation.php">Mutation</a>
        <a href="chart.php" class="active">Chart</a>
    </nav>
</header>

<main>
    <h2 class="headerH">Grafik Total Pengiriman per Hari</h2>
    <canvas id="chartHarian"></canvas>

    <hr>

    <form method="get" class="filter-form">
        <label>Pilih Tanggal: </label>
        <input type="date" name="date" value="<?php echo $selected_date; ?>">
        <button type="submit">Tampilkan</button>
    </form>

    <h2 class="headerH">Grafik Ekspedisi pada <?php echo $selected_date; ?></h2>
    <canvas id="chartEkspedisi"></canvas>

    <h2 class="headerH">Grafik Customer pada <?php echo $selected_date; ?></h2>
    <canvas id="chartCustomer"></canvas>

    <h2 class="headerH">Tabel Detail Pengiriman (<?php echo $selected_date; ?>)</h2>
    <table border="1" class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result_tabel->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo $row['shipment_date']; ?></td>
                <td><?php echo number_format($row['total']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</main>

<script>
    // --- Chart Per Hari ---
    new Chart(document.getElementById('chartHarian'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_harian); ?>,
            datasets: [{
                label: 'Total Pengiriman',
                data: <?php echo json_encode($data_harian); ?>,
                borderColor: 'rgba(76, 91, 175, 1)',
                backgroundColor: 'rgba(76, 91, 175, 0.3)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // --- Chart Ekspedisi ---
    new Chart(document.getElementById('chartEkspedisi'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_ekspedisi); ?>,
            datasets: [{
                label: 'Total per Ekspedisi',
                data: <?php echo json_encode($data_ekspedisi); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // --- Chart Customer ---
    new Chart(document.getElementById('chartCustomer'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_customer); ?>,
            datasets: [{
                label: 'Total Pengiriman per Customer',
                data: <?php echo json_encode($data_customer); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>

<footer>
    <p>&copy; Shaka Banuasta <?php echo $versionWeb ?? ''; ?></p>
</footer>
</body>
</html>
