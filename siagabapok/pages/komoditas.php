<?php
require_once '../includes/db.php';

$base_url = '../';
$page_title = 'Tabel Harga Komoditas';

// Ambil filter dari GET
$filter_komoditas = isset($_GET['komoditas']) ? clean_input($_GET['komoditas']) : '';
$filter_tanggal = isset($_GET['tanggal']) ? clean_input($_GET['tanggal']) : date('Y-m-d');

// Query untuk daftar komoditas
$query_komoditas = "SELECT DISTINCT nama_komoditas FROM komoditas ORDER BY nama_komoditas";
$result_komoditas = $conn->query($query_komoditas);
$list_komoditas = [];
if ($result_komoditas && $result_komoditas->num_rows > 0) {
    while ($row = $result_komoditas->fetch_assoc()) {
        $list_komoditas[] = $row['nama_komoditas'];
    }
}

// Query untuk data tabel dengan filter
$where_conditions = [];
$where_conditions[] = "h.tanggal = '$filter_tanggal'";

if (!empty($filter_komoditas)) {
    $where_conditions[] = "k.nama_komoditas = '$filter_komoditas'";
}

$where_clause = implode(' AND ', $where_conditions);

$query_table = "
    SELECT 
        k.nama_komoditas,
        p.nama_pasar,
        h.harga_per_kg,
        h.tanggal
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    JOIN pasar p ON h.id_pasar = p.id_pasar
    WHERE $where_clause
    ORDER BY k.nama_komoditas, p.nama_pasar
";

$result_table = $conn->query($query_table);
$table_data = [];
if ($result_table && $result_table->num_rows > 0) {
    while ($row = $result_table->fetch_assoc()) {
        $table_data[] = $row;
    }
}

// Hitung statistik untuk tanggal yang dipilih
$query_stats = "
    SELECT 
        COUNT(DISTINCT k.nama_komoditas) as total_komoditas,
        COUNT(DISTINCT p.nama_pasar) as total_pasar,
        AVG(h.harga_per_kg) as rata_rata_harga,
        MIN(h.harga_per_kg) as harga_terendah,
        MAX(h.harga_per_kg) as harga_tertinggi
    FROM komoditas k
    JOIN harga h ON k.id_komoditas = h.id_komoditas
    JOIN pasar p ON h.id_pasar = p.id_pasar
    WHERE h.tanggal = '$filter_tanggal'
";

$result_stats = $conn->query($query_stats);
$stats = $result_stats->fetch_assoc();

include '../includes/header.php';
?>

<div class="container my-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="bi bi-table text-primary me-2"></i>
                        Tabel Harga Komoditas
                    </h1>
                    <p class="text-muted mb-0">Data harga bahan pokok per pasar</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Data tanggal:</small><br>
                    <strong><?php echo format_tanggal($filter_tanggal); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel me-2"></i>Filter Data
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?php echo $filter_tanggal; ?>" max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="komoditas" class="form-label">Komoditas</label>
                            <select class="form-select" id="komoditas" name="komoditas">
                                <option value="">Semua Komoditas</option>
                                <?php foreach ($list_komoditas as $komoditas): ?>
                                    <option value="<?php echo htmlspecialchars($komoditas); ?>" 
                                            <?php echo $filter_komoditas === $komoditas ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($komoditas); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="komoditas.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if ($stats): ?>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-basket fs-2 mb-2"></i>
                    <h4 class="mb-0"><?php echo $stats['total_komoditas'] ?? '0'; ?></h4>
                    <p class="mb-0">Komoditas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-shop fs-2 mb-2"></i>
                    <h4 class="mb-0"><?php echo $stats['total_pasar'] ?? '0'; ?></h4>
                    <p class="mb-0">Pasar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-calculator fs-2 mb-2"></i>
                    <h5 class="mb-0"><?php echo $stats['rata_rata_harga'] ? format_rupiah($stats['rata_rata_harga']) : 'Rp 0'; ?></h5>
                    <p class="mb-0">Rata-rata</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up-arrow fs-2 mb-2"></i>
                    <h6 class="mb-1"><?php echo $stats['harga_terendah'] ? format_rupiah($stats['harga_terendah']) : 'Rp 0'; ?></h6>
                    <h6 class="mb-0"><?php echo $stats['harga_tertinggi'] ? format_rupiah($stats['harga_tertinggi']) : 'Rp 0'; ?></h6>
                    <p class="mb-0">Min - Max</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Data Harga <?php echo !empty($filter_komoditas) ? htmlspecialchars($filter_komoditas) : 'Semua Komoditas'; ?>
                    </h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="exportToCSV()">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </button>
                        <button class="btn btn-outline-light btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Cetak
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($table_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="dataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Komoditas</th>
                                        <th width="35%">Pasar</th>
                                        <th width="20%">Harga/kg</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $grouped_data = [];
                                    
                                    // Group data by commodity for better organization
                                    foreach ($table_data as $row) {
                                        $grouped_data[$row['nama_komoditas']][] = $row;
                                    }
                                    ?>
                                    
                                    <?php foreach ($grouped_data as $komoditas => $items): ?>
                                        <?php 
                                        // Calculate average for this commodity
                                        $total_harga = array_sum(array_column($items, 'harga_per_kg'));
                                        $avg_harga = $total_harga / count($items);
                                        ?>
                                        
                                        <?php foreach ($items as $index => $item): ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td>
                                                    <?php if ($index === 0): ?>
                                                        <strong><?php echo htmlspecialchars($item['nama_komoditas']); ?></strong>
                                                        <br><small class="text-muted">Rata-rata: <?php echo format_rupiah($avg_harga); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="bi bi-geo-alt text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($item['nama_pasar']); ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold"><?php echo format_rupiah($item['harga_per_kg']); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $diff_percent = (($item['harga_per_kg'] - $avg_harga) / $avg_harga) * 100;
                                                    if ($diff_percent > 5): ?>
                                                        <span class="badge bg-danger">Tinggi</span>
                                                    <?php elseif ($diff_percent < -5): ?>
                                                        <span class="badge bg-success">Rendah</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Normal</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($grouped_data) > 1 && $komoditas !== array_key_last($grouped_data)): ?>
                                            <tr class="table-light">
                                                <td colspan="5" class="py-1"></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Total <?php echo count($table_data); ?> data ditampilkan
                                </small>
                                <div>
                                    <span class="badge bg-danger me-1">Tinggi</span>
                                    <span class="badge bg-secondary me-1">Normal</span>
                                    <span class="badge bg-success">Rendah</span>
                                    <small class="text-muted ms-2">Status harga vs rata-rata</small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada data</h5>
                            <p class="text-muted">Tidak ada data harga untuk filter yang dipilih.</p>
                            <a href="komoditas.php" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="card bg-light">
                <div class="card-body py-4">
                    <h5 class="mb-3">Lihat Data Lainnya</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="bi bi-graph-up me-2"></i>Dashboard Analytics
                        </a>
                        <a href="../index.php" class="btn btn-outline-primary">
                            <i class="bi bi-house me-2"></i>Kembali ke Beranda
                        </a>
                        <a href="about.php" class="btn btn-info">
                            <i class="bi bi-info-circle me-2"></i>Tentang Siaga Bapok
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_scripts = "
<script>
// Function to export table to CSV
function exportToCSV() {
    const table = document.getElementById('dataTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    const csvContent = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => {
            let text = cell.textContent.trim();
            // Remove extra whitespace and newlines
            text = text.replace(/\\s+/g, ' ');
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',') || text.includes('\"')) {
                text = '\"' + text.replace(/\"/g, '\"\"') + '\"';
            }
            return text;
        }).join(',');
    }).join('\\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'data_harga_komoditas_' + '" . $filter_tanggal . "' + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-submit form when date changes
document.getElementById('tanggal').addEventListener('change', function() {
    this.form.submit();
});
</script>
";

include '../includes/footer.php';
?>