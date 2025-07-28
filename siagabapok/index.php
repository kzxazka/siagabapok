<?php
require_once 'includes/db.php';

$page_title = 'Beranda';

// Ambil data top 3 komoditas yang naik minggu ini
$query_top3 = "
    SELECT 
        k.nama_komoditas,
        AVG(h1.harga_per_kg) as harga_minggu_ini,
        AVG(h2.harga_per_kg) as harga_minggu_lalu,
        ((AVG(h1.harga_per_kg) - AVG(h2.harga_per_kg)) / AVG(h2.harga_per_kg) * 100) as persentase_kenaikan
    FROM komoditas k
    LEFT JOIN harga h1 ON k.id_komoditas = h1.id_komoditas 
        AND h1.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    LEFT JOIN harga h2 ON k.id_komoditas = h2.id_komoditas 
        AND h2.tanggal >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) 
        AND h2.tanggal < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    WHERE h1.harga_per_kg IS NOT NULL AND h2.harga_per_kg IS NOT NULL
    GROUP BY k.id_komoditas, k.nama_komoditas
    HAVING persentase_kenaikan > 0
    ORDER BY persentase_kenaikan DESC
    LIMIT 3
";

$result_top3 = $conn->query($query_top3);
$top3_komoditas = [];
if ($result_top3 && $result_top3->num_rows > 0) {
    while ($row = $result_top3->fetch_assoc()) {
        $top3_komoditas[] = $row;
    }
}

// Ambil data untuk grafik mingguan (7 hari terakhir)
$query_chart = "
    SELECT 
        k.nama_komoditas,
        h.tanggal,
        AVG(h.harga_per_kg) as rata_harga
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    WHERE h.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        AND k.nama_komoditas IN ('Beras', 'Cabai Merah', 'Bawang Merah', 'Minyak Goreng')
    GROUP BY k.nama_komoditas, h.tanggal
    ORDER BY h.tanggal ASC, k.nama_komoditas
";

$result_chart = $conn->query($query_chart);
$chart_data = [];
if ($result_chart && $result_chart->num_rows > 0) {
    while ($row = $result_chart->fetch_assoc()) {
        $chart_data[] = $row;
    }
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="bi bi-graph-up-arrow me-3"></i>
                    Sistem Informasi Harga Bahan Pokok
                </h1>
                <p class="lead mb-4">
                    Pantau pergerakan harga komoditas bahan pokok di Kota Bandar Lampung secara real-time. 
                    Dapatkan informasi terkini untuk kebutuhan sehari-hari Anda.
                </p>
                <div class="d-flex gap-3">
                    <a href="pages/dashboard.php" class="btn btn-light btn-lg">
                        <i class="bi bi-graph-up me-2"></i>Lihat Dashboard
                    </a>
                    <a href="pages/komoditas.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-list-ul me-2"></i>Tabel Harga
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white bg-opacity-10 rounded-3 p-4">
                    <h3 class="mb-3">Update Terakhir</h3>
                    <p class="h5 mb-0"><?php echo date('d M Y'); ?></p>
                    <small><?php echo date('H:i'); ?> WIB</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    <!-- Top 3 Komoditas Naik -->
    <section class="mb-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">
                    <i class="bi bi-trending-up text-primary me-2"></i>
                    Top 3 Komoditas yang Naik Minggu Ini
                </h2>
            </div>
        </div>
        
        <div class="row">
            <?php if (!empty($top3_komoditas)): ?>
                <?php foreach ($top3_komoditas as $index => $komoditas): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <?php
                                    $icons = ['bi-trophy-fill text-warning', 'bi-award-fill text-info', 'bi-star-fill text-success'];
                                    ?>
                                    <i class="bi <?php echo $icons[$index]; ?> fs-1"></i>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($komoditas['nama_komoditas']); ?></h5>
                                <p class="card-text">
                                    <span class="badge bg-success fs-6">
                                        +<?php echo number_format($komoditas['persentase_kenaikan'], 1); ?>%
                                    </span>
                                </p>
                                <p class="text-muted mb-2">Harga saat ini:</p>
                                <h4 class="text-primary"><?php echo format_rupiah($komoditas['harga_minggu_ini']); ?>/kg</h4>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Tidak ada data komoditas yang mengalami kenaikan minggu ini.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Grafik Mingguan -->
    <section class="mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Grafik Pergerakan Harga 7 Hari Terakhir
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="weeklyChart"></canvas>
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
    </section>

    <!-- Quick Stats -->
    <section class="mb-5">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-shop fs-1 mb-2"></i>
                        <h5>12</h5>
                        <p class="mb-0">Pasar Terpantau</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-basket fs-1 mb-2"></i>
                        <h5>25</h5>
                        <p class="mb-0">Komoditas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check fs-1 mb-2"></i>
                        <h5>Harian</h5>
                        <p class="mb-0">Update Data</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 mb-2"></i>
                        <h5>Real-time</h5>
                        <p class="mb-0">Monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="text-center">
        <div class="card bg-light-green">
            <div class="card-body py-5">
                <h3 class="mb-3">Butuh Informasi Lebih Detail?</h3>
                <p class="lead mb-4">
                    Akses dashboard lengkap untuk melihat analisis mendalam dan tabel harga komprehensif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="pages/dashboard.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-graph-up me-2"></i>Dashboard Lengkap
                    </a>
                    <a href="pages/komoditas.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-table me-2"></i>Lihat Semua Harga
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$additional_scripts = "
<script>
// Data untuk grafik
const chartData = " . json_encode($chart_data) . ";

// Siapkan data untuk Chart.js
const dates = [...new Set(chartData.map(item => item.tanggal))].sort();
const commodities = [...new Set(chartData.map(item => item.nama_komoditas))];

const datasets = commodities.map((commodity, index) => {
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];
    const data = dates.map(date => {
        const item = chartData.find(d => d.tanggal === date && d.nama_komoditas === commodity);
        return item ? parseFloat(item.rata_harga) : null;
    });
    
    return {
        label: commodity,
        data: data,
        borderColor: colors[index % colors.length],
        backgroundColor: colors[index % colors.length] + '20',
        tension: 0.1,
        fill: false
    };
});

// Buat grafik
const ctx = document.getElementById('weeklyChart').getContext('2d');
const weeklyChart = new Chart(ctx, {
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
                text: 'Pergerakan Harga Komoditas (7 Hari Terakhir)'
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
</script>
";

include 'includes/footer.php';
?>