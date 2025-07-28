<?php
require_once '../includes/db.php';

$base_url = '../';
$page_title = 'Dashboard';

// Ambil data untuk grafik 7 hari terakhir
$query_chart = "
    SELECT 
        k.nama_komoditas,
        p.nama_pasar,
        h.tanggal,
        h.harga_per_kg
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    JOIN pasar p ON h.id_pasar = p.id_pasar
    WHERE h.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ORDER BY h.tanggal ASC, k.nama_komoditas, p.nama_pasar
";

$result_chart = $conn->query($query_chart);
$chart_data = [];
if ($result_chart && $result_chart->num_rows > 0) {
    while ($row = $result_chart->fetch_assoc()) {
        $chart_data[] = $row;
    }
}

// Ambil statistik umum
$query_stats = "
    SELECT 
        COUNT(DISTINCT k.id_komoditas) as total_komoditas,
        COUNT(DISTINCT p.id_pasar) as total_pasar,
        COUNT(DISTINCT h.tanggal) as total_hari_data
    FROM komoditas k, pasar p, harga h
    WHERE h.tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
";

$result_stats = $conn->query($query_stats);
$stats = $result_stats->fetch_assoc();

// Ambil data harga tertinggi dan terendah hari ini
$query_extremes = "
    SELECT 
        k.nama_komoditas,
        p.nama_pasar,
        h.harga_per_kg,
        'tertinggi' as jenis
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    JOIN pasar p ON h.id_pasar = p.id_pasar
    WHERE h.tanggal = CURDATE()
    ORDER BY h.harga_per_kg DESC
    LIMIT 5
";

$result_tertinggi = $conn->query($query_extremes);
$harga_tertinggi = [];
if ($result_tertinggi && $result_tertinggi->num_rows > 0) {
    while ($row = $result_tertinggi->fetch_assoc()) {
        $harga_tertinggi[] = $row;
    }
}

$query_terendah = "
    SELECT 
        k.nama_komoditas,
        p.nama_pasar,
        h.harga_per_kg,
        'terendah' as jenis
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    JOIN pasar p ON h.id_pasar = p.id_pasar
    WHERE h.tanggal = CURDATE()
    ORDER BY h.harga_per_kg ASC
    LIMIT 5
";

$result_terendah = $conn->query($query_terendah);
$harga_terendah = [];
if ($result_terendah && $result_terendah->num_rows > 0) {
    while ($row = $result_terendah->fetch_assoc()) {
        $harga_terendah[] = $row;
    }
}

include '../includes/header.php';
?>

<div class="container my-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Dashboard Analytics
                    </h1>
                    <p class="text-muted mb-0">Analisis pergerakan harga komoditas 7 hari terakhir</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Update terakhir:</small><br>
                    <strong><?php echo date('d M Y, H:i'); ?> WIB</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-basket fs-1"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?php echo $stats['total_komoditas'] ?? '0'; ?></h3>
                        <p class="mb-0">Total Komoditas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-shop fs-1"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?php echo $stats['total_pasar'] ?? '0'; ?></h3>
                        <p class="mb-0">Pasar Terpantau</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-calendar-check fs-1"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?php echo $stats['total_hari_data'] ?? '0'; ?></h3>
                        <p class="mb-0">Hari Data (30 hari)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts -->
    <div class="row mb-4">
        <!-- Line Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Grafik Pergerakan Harga (7 Hari)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="commoditySelect" class="form-label">Pilih Komoditas:</label>
                        <select class="form-select" id="commoditySelect">
                            <option value="all">Semua Komoditas</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Rata-rata Harga Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Extremes -->
    <div class="row mb-4">
        <!-- Harga Tertinggi -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-up-circle me-2"></i>
                        Harga Tertinggi Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($harga_tertinggi)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($harga_tertinggi as $index => $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['nama_komoditas']); ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?php echo htmlspecialchars($item['nama_pasar']); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger fs-6">
                                            <?php echo format_rupiah($item['harga_per_kg']); ?>/kg
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Tidak ada data untuk hari ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Harga Terendah -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-down-circle me-2"></i>
                        Harga Terendah Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($harga_terendah)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($harga_terendah as $index => $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['nama_komoditas']); ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?php echo htmlspecialchars($item['nama_pasar']); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success fs-6">
                                            <?php echo format_rupiah($item['harga_per_kg']); ?>/kg
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Tidak ada data untuk hari ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="card bg-light">
                <div class="card-body py-4">
                    <h5 class="mb-3">Butuh Data Lebih Detail?</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="komoditas.php" class="btn btn-primary">
                            <i class="bi bi-table me-2"></i>Lihat Tabel Harga
                        </a>
                        <a href="../index.php" class="btn btn-outline-primary">
                            <i class="bi bi-house me-2"></i>Kembali ke Beranda
                        </a>
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_scripts = "
<script>
// Data untuk grafik
const chartData = " . json_encode($chart_data) . ";

// Siapkan data untuk Chart.js
const dates = [...new Set(chartData.map(item => item.tanggal))].sort();
const commodities = [...new Set(chartData.map(item => item.nama_komoditas))];

// Populate commodity select
const commoditySelect = document.getElementById('commoditySelect');
commodities.forEach(commodity => {
    const option = document.createElement('option');
    option.value = commodity;
    option.textContent = commodity;
    commoditySelect.appendChild(option);
});

// Function to create line chart
function createLineChart(selectedCommodity = 'all') {
    const ctx = document.getElementById('lineChart').getContext('2d');
    
    let filteredCommodities = selectedCommodity === 'all' ? commodities : [selectedCommodity];
    
    const datasets = filteredCommodities.map((commodity, index) => {
        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        
        // Group data by commodity and calculate average per date
        const commodityData = chartData.filter(item => item.nama_komoditas === commodity);
        const avgData = dates.map(date => {
            const dayData = commodityData.filter(item => item.tanggal === date);
            if (dayData.length === 0) return null;
            const avg = dayData.reduce((sum, item) => sum + parseFloat(item.harga_per_kg), 0) / dayData.length;
            return avg;
        });
        
        return {
            label: commodity,
            data: avgData,
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + '20',
            tension: 0.1,
            fill: false
        };
    });

    // Destroy existing chart if it exists
    if (window.lineChartInstance) {
        window.lineChartInstance.destroy();
    }

    window.lineChartInstance = new Chart(ctx, {
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
                    text: 'Pergerakan Harga Komoditas (Rata-rata per Hari)'
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

// Create bar chart for today's average prices
function createBarChart() {
    const ctx = document.getElementById('barChart').getContext('2d');
    
    // Calculate today's average prices
    const today = dates[dates.length - 1]; // Latest date
    const todayData = chartData.filter(item => item.tanggal === today);
    
    const avgPrices = commodities.map(commodity => {
        const commodityToday = todayData.filter(item => item.nama_komoditas === commodity);
        if (commodityToday.length === 0) return 0;
        return commodityToday.reduce((sum, item) => sum + parseFloat(item.harga_per_kg), 0) / commodityToday.length;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: commodities.map(name => name.length > 10 ? name.substring(0, 10) + '...' : name),
            datasets: [{
                label: 'Rata-rata Harga (Rp/kg)',
                data: avgPrices,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
                borderColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

// Event listener for commodity selection
commoditySelect.addEventListener('change', function() {
    createLineChart(this.value);
});

// Initialize charts
createLineChart();
createBarChart();
</script>
";

include '../includes/footer.php';
?>