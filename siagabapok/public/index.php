<?php
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/Price.php';

$auth = new AuthController();
$priceModel = new Price();

// Get current user if logged in (optional for public page)
$currentUser = $auth->getCurrentUser();

// Get price data for different periods
$trends1Day = $priceModel->getPriceTrends(1);
$trends7Days = $priceModel->getPriceTrends(7);
$trends30Days = $priceModel->getPriceTrends(30);

// Get latest prices
$latestPrices = $priceModel->getLatestPrices();

// Get top increasing prices (7 days)
$topIncreasing = $priceModel->getTopIncreasingPrices(7, 5);

// Get statistics
$stats = $priceModel->getStatistics();

$pageTitle = 'Beranda - Siaga Bapok';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #28a745;
            --light-green: #d4edda;
            --dark-green: #155724;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            padding: 4rem 0;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .period-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .price-trend-up {
            color: #dc3545;
        }
        
        .price-trend-down {
            color: #28a745;
        }
        
        .price-trend-stable {
            color: #6c757d;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-graph-up-arrow me-2"></i>
                SIAGA BAPOK
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house me-1"></i>Beranda
                        </a>
                    </li>
                    <?php if ($currentUser): ?>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/dashboard.php">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                                </a>
                            </li>
                        <?php elseif ($currentUser['role'] === 'uptd'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="uptd/dashboard.php">
                                    <i class="bi bi-clipboard-data me-1"></i>Dashboard UPTD
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i><?php echo $currentUser['full_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        Sistem Informasi Harga Bahan Pokok
                    </h1>
                    <p class="lead mb-4">
                        Pantau pergerakan harga komoditas bahan pokok di Kota Bandar Lampung secara real-time. 
                        Data terpercaya untuk kebutuhan sehari-hari Anda.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <button class="btn btn-light btn-lg" onclick="scrollToChart()">
                            <i class="bi bi-graph-up me-2"></i>Lihat Grafik
                        </button>
                        <button class="btn btn-outline-light btn-lg" onclick="scrollToLatestPrices()">
                            <i class="bi bi-list-ul me-2"></i>Harga Terbaru
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white bg-opacity-10 rounded-3 p-4">
                        <h3 class="mb-3">Data Terupdate</h3>
                        <p class="h5 mb-0"><?php echo date('d M Y'); ?></p>
                        <small><?php echo date('H:i'); ?> WIB</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-basket text-primary fs-1 mb-2"></i>
                        <h3 class="text-primary"><?php echo $stats['total_commodities']; ?></h3>
                        <p class="mb-0">Jenis Komoditas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-shop text-success fs-1 mb-2"></i>
                        <h3 class="text-success"><?php echo $stats['total_markets']; ?></h3>
                        <p class="mb-0">Pasar Terpantau</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-check-circle text-info fs-1 mb-2"></i>
                        <h3 class="text-info"><?php echo $stats['approved_count']; ?></h3>
                        <p class="mb-0">Data Terverifikasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-clock text-warning fs-1 mb-2"></i>
                        <h3 class="text-warning">Real-time</h3>
                        <p class="mb-0">Update Data</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Increasing Prices -->
        <?php if (!empty($topIncreasing)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">
                    <i class="bi bi-trending-up text-danger me-2"></i>
                    Komoditas dengan Kenaikan Harga Tertinggi (7 Hari)
                </h2>
            </div>
            <?php foreach (array_slice($topIncreasing, 0, 3) as $index => $item): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <?php
                                $icons = ['bi-trophy-fill text-warning', 'bi-award-fill text-secondary', 'bi-star-fill text-success'];
                                ?>
                                <i class="bi <?php echo $icons[$index]; ?> fs-1"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($item['commodity_name']); ?></h5>
                            <?php if ($item['current_avg'] && $item['previous_avg']): ?>
                                <?php $percentage = (($item['current_avg'] - $item['previous_avg']) / $item['previous_avg']) * 100; ?>
                                <p class="card-text">
                                    <span class="badge bg-danger fs-6">
                                        +<?php echo number_format($percentage, 1); ?>%
                                    </span>
                                </p>
                                <p class="text-muted mb-2">Rata-rata saat ini:</p>
                                <h4 class="text-primary"><?php echo formatRupiah($item['current_avg']); ?>/kg</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Interactive Chart Section -->
        <div class="row mb-5" id="chartSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Grafik Pergerakan Harga Komoditas
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Period Selection -->
                        <div class="period-buttons mb-3">
                            <h6>Pilih Periode:</h6>
                            <button class="btn btn-outline-primary active" data-period="1" onclick="changePeriod(1)">
                                1 Hari Terakhir
                            </button>
                            <button class="btn btn-outline-primary" data-period="7" onclick="changePeriod(7)">
                                7 Hari Terakhir
                            </button>
                            <button class="btn btn-outline-primary" data-period="30" onclick="changePeriod(30)">
                                30 Hari Terakhir
                            </button>
                        </div>
                        
                        <!-- Chart Container -->
                        <div class="chart-container">
                            <canvas id="priceChart"></canvas>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Grafik menampilkan rata-rata harga per hari dari semua pasar
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Prices Table -->
        <div class="row mb-5" id="latestPricesSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Harga Terbaru per Pasar
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($latestPrices)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Komoditas</th>
                                            <th>Pasar</th>
                                            <th>Harga/kg</th>
                                            <th>Terakhir Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($latestPrices, 0, 20) as $index => $price): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($price['commodity_name']); ?></strong>
                                                </td>
                                                <td>
                                                    <i class="bi bi-geo-alt text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($price['market_name']); ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-primary">
                                                        <?php echo formatRupiah($price['price']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo formatDateTime($price['created_at']); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($latestPrices) > 20): ?>
                                <div class="text-center mt-3">
                                    <p class="text-muted">
                                        Menampilkan 20 dari <?php echo count($latestPrices); ?> data terbaru
                                    </p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data harga</h5>
                                <p class="text-muted">Data harga akan ditampilkan setelah diinput oleh petugas UPTD.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="card bg-light">
                    <div class="card-body py-5">
                        <h3 class="mb-3">Butuh Akses Lebih Lengkap?</h3>
                        <p class="lead mb-4">
                            Login sebagai UPTD untuk input data harga atau sebagai Admin untuk mengelola sistem
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="login.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-graph-up-arrow me-2"></i>Siaga Bapok</h5>
                    <p class="mb-0">Sistem Informasi Harga Bahan Pokok Kota Bandar Lampung</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>&copy; <?php echo date('Y'); ?> Siaga Bapok. All rights reserved.</small><br>
                    <small>Data terakhir diperbarui: <?php echo date('d M Y, H:i'); ?> WIB</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Price data for different periods
        const priceData = {
            1: <?php echo json_encode($trends1Day); ?>,
            7: <?php echo json_encode($trends7Days); ?>,
            30: <?php echo json_encode($trends30Days); ?>
        };
        
        let currentChart = null;
        let currentPeriod = 1;
        
        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('priceChart').getContext('2d');
            updateChart(1);
        }
        
        // Update chart based on period
        function updateChart(period) {
            const data = priceData[period];
            
            if (!data || data.length === 0) {
                showNoDataMessage();
                return;
            }
            
            // Group data by commodity
            const commodityData = {};
            data.forEach(item => {
                if (!commodityData[item.commodity_name]) {
                    commodityData[item.commodity_name] = [];
                }
                commodityData[item.commodity_name].push({
                    date: item.price_date,
                    price: parseFloat(item.avg_price)
                });
            });
            
            // Get unique dates and sort them
            const dates = [...new Set(data.map(item => item.price_date))].sort();
            
            // Create datasets for each commodity
            const datasets = Object.keys(commodityData).slice(0, 6).map((commodity, index) => {
                const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
                
                const commodityPrices = dates.map(date => {
                    const item = commodityData[commodity].find(d => d.date === date);
                    return item ? item.price : null;
                });
                
                return {
                    label: commodity,
                    data: commodityPrices,
                    borderColor: colors[index],
                    backgroundColor: colors[index] + '20',
                    tension: 0.1,
                    fill: false
                };
            });
            
            // Destroy existing chart
            if (currentChart) {
                currentChart.destroy();
            }
            
            // Create new chart
            const ctx = document.getElementById('priceChart').getContext('2d');
            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    }),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Pergerakan Harga ${period} Hari Terakhir`
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
        
        // Change period
        function changePeriod(period) {
            currentPeriod = period;
            
            // Update button states
            document.querySelectorAll('.period-buttons .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-period="${period}"]`).classList.add('active');
            
            // Update chart
            updateChart(period);
        }
        
        // Show no data message
        function showNoDataMessage() {
            const ctx = document.getElementById('priceChart').getContext('2d');
            if (currentChart) {
                currentChart.destroy();
            }
            
            ctx.font = '16px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText('Tidak ada data untuk periode ini', ctx.canvas.width / 2, ctx.canvas.height / 2);
        }
        
        // Scroll functions
        function scrollToChart() {
            document.getElementById('chartSection').scrollIntoView({ behavior: 'smooth' });
        }
        
        function scrollToLatestPrices() {
            document.getElementById('latestPricesSection').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });
    </script>
</body>
</html>