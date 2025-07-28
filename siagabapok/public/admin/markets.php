<?php
require_once __DIR__ . '/../../src/controllers/AuthController.php';
require_once __DIR__ . '/../../src/models/Price.php';

$auth = new AuthController();
$user = $auth->requireRole('admin');
$priceModel = new Price();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'create') {
        $nama_pasar = sanitizeInput($_POST['nama_pasar']);
        $alamat = sanitizeInput($_POST['alamat']);
        $keterangan = sanitizeInput($_POST['keterangan'] ?? '');
        
        $sql = "INSERT INTO markets (nama_pasar, alamat, keterangan) VALUES (?, ?, ?)";
        try {
            $priceModel->db->execute($sql, [$nama_pasar, $alamat, $keterangan]);
            $_SESSION['success'] = 'Pasar berhasil ditambahkan';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menambahkan pasar: ' . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $nama_pasar = sanitizeInput($_POST['nama_pasar']);
        $alamat = sanitizeInput($_POST['alamat']);
        $keterangan = sanitizeInput($_POST['keterangan'] ?? '');
        
        $sql = "UPDATE markets SET nama_pasar = ?, alamat = ?, keterangan = ? WHERE id = ?";
        try {
            $priceModel->db->execute($sql, [$nama_pasar, $alamat, $keterangan, $id]);
            $_SESSION['success'] = 'Pasar berhasil diperbarui';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui pasar: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        $sql = "DELETE FROM markets WHERE id = ?";
        try {
            $priceModel->db->execute($sql, [$id]);
            $_SESSION['success'] = 'Pasar berhasil dihapus';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menghapus pasar: ' . $e->getMessage();
        }
    }
    
    header('Location: markets.php');
    exit;
}

// Create markets table if not exists
$createTableSql = "CREATE TABLE IF NOT EXISTS markets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_pasar VARCHAR(100) NOT NULL,
    alamat TEXT,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$priceModel->db->query($createTableSql);

// Get all markets
$markets = $priceModel->db->fetchAll("SELECT * FROM markets ORDER BY nama_pasar");

// Get market being edited
$editMarket = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editMarket = $priceModel->db->fetchOne("SELECT * FROM markets WHERE id = ?", [$editId]);
}

$pageTitle = 'Kelola Pasar - Admin Siaga Bapok';
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
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-3">
            <h4 class="text-center mb-4">
                <i class="bi bi-graph-up-arrow me-2"></i>
                SIAGA BAPOK
            </h4>
            <small class="text-center d-block mb-3 opacity-75">Admin Panel</small>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="approvals.php">
                    <i class="bi bi-check-circle me-2"></i>
                    Persetujuan Data
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="bi bi-people me-2"></i>
                    Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <small class="text-uppercase text-muted px-3 mt-3 mb-2">Data Master</small>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="markets.php">
                    <i class="bi bi-shop me-2"></i>
                    Pasar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="commodities.php">
                    <i class="bi bi-basket me-2"></i>
                    Komoditas
                </a>
            </li>
            <li class="nav-item">
                <small class="text-uppercase text-muted px-3 mt-3 mb-2">Lainnya</small>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="bi bi-house me-2"></i>
                    Lihat Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile Menu Toggle -->
        <button class="btn btn-primary d-md-none mb-3" type="button" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h1 class="h3 mb-0">
                            <i class="bi bi-shop me-2"></i>
                            Kelola Data Pasar
                        </h1>
                        <p class="mb-0 mt-2 opacity-75">
                            Manajemen data pasar tradisional di Bandar Lampung
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Add/Edit -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            <?php echo $editMarket ? 'Edit Pasar' : 'Tambah Pasar Baru'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $editMarket ? 'update' : 'create'; ?>">
                            <?php if ($editMarket): ?>
                                <input type="hidden" name="id" value="<?php echo $editMarket['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="nama_pasar" class="form-label">
                                    <i class="bi bi-shop me-1"></i>Nama Pasar *
                                </label>
                                <input type="text" class="form-control" id="nama_pasar" name="nama_pasar" 
                                       value="<?php echo $editMarket ? htmlspecialchars($editMarket['nama_pasar']) : ''; ?>" 
                                       placeholder="Contoh: Pasar Tugu" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Alamat Lengkap *
                                </label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                          placeholder="Alamat lengkap pasar..." required><?php echo $editMarket ? htmlspecialchars($editMarket['alamat']) : ''; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">
                                    <i class="bi bi-info-circle me-1"></i>Keterangan (Opsional)
                                </label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                          placeholder="Keterangan tambahan..."><?php echo $editMarket ? htmlspecialchars($editMarket['keterangan']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>
                                    <?php echo $editMarket ? 'Update Pasar' : 'Simpan Pasar'; ?>
                                </button>
                                <?php if ($editMarket): ?>
                                    <a href="markets.php" class="btn btn-secondary">
                                        <i class="bi bi-x-lg me-2"></i>
                                        Batal Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Markets List -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Daftar Pasar (<?php echo count($markets); ?>)
                        </h5>
                        <div>
                            <button class="btn btn-light btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($markets)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Nama Pasar</th>
                                            <th width="40%">Alamat</th>
                                            <th width="20%">Keterangan</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($markets as $index => $market): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($market['nama_pasar']); ?></strong>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($market['alamat']); ?></small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo $market['keterangan'] ? htmlspecialchars($market['keterangan']) : '-'; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?php echo $market['id']; ?>" 
                                                           class="btn btn-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger" 
                                                                onclick="deleteMarket(<?php echo $market['id']; ?>, '<?php echo addslashes($market['nama_pasar']); ?>')" 
                                                                title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-shop fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data pasar</h5>
                                <p class="text-muted">Tambahkan data pasar menggunakan form di sebelah kiri.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pasar <strong id="marketName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-warning me-2"></i>
                        <strong>Peringatan:</strong> Data yang sudah dihapus tidak dapat dikembalikan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle sidebar for mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
        
        // Delete market function
        function deleteMarket(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('marketName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            });
        }, 5000);
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const namaParar = document.getElementById('nama_pasar').value.trim();
            const alamat = document.getElementById('alamat').value.trim();
            
            if (!namaParar || !alamat) {
                e.preventDefault();
                alert('Nama pasar dan alamat harus diisi!');
                return;
            }
        });
    </script>
</body>
</html>