<?php
include 'config.php'; 

$query = "
    SELECT c.customer_name, SUM(s.total) AS total_pengiriman
    FROM shipments s
    JOIN customers c ON s.customer_id = c.id
    GROUP BY c.customer_name
    ORDER BY total_pengiriman DESC
";

$result = $conn->query($query);

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['customer_name'];
    $data[] = $row['total_pengiriman'];
}
?>
<!DOCTYPE html>
<html lang="en">
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
        <h2 class="headerH">Grafik Total Pengiriman per Customer</h2>
        <canvas id="shipmentChart"></canvas>

        <script>
            window.onload = function() {
                const ctx = document.getElementById('shipmentChart').getContext('2d');
                const shipmentChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Total Pengiriman',
                            data: <?php echo json_encode($data); ?>,
                            backgroundColor: 'rgba(76, 91, 175, 0.7)',
                            borderColor: 'rgba(76, 91, 175, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        </script>
    </main>
    <footer>
            <p>&copy; Shaka Banuasta V2.0</p>
    </footer>
</body>
</html>
