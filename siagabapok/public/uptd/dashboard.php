<?php
require_once __DIR__ . '/../../src/controllers/AuthController.php';
require_once __DIR__ . '/../../src/models/Price.php';

$auth = new AuthController();
$user = $auth->requireRole('uptd');
$priceModel = new Price();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commodity_name = sanitizeInput($_POST['commodity_name']);
    $price = sanitizeInput($_POST['price']);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    $errors = [];
    
    // Validation
    if (empty($commodity_name)) {
        $errors[] = 'Nama komoditas harus diisi';
    }
    
    if (empty($price)) {
        $errors[] = 'Harga harus diisi';
    } elseif (!validatePrice($price)) {
        $errors[] = 'Harga harus berupa angka maksimal 5 digit (1-99999)';
    }
    
    if (empty($errors)) {
        $data = [
            'commodity_name' => $commodity_name,
            'price' => $price,
            'market_name' => $user['market_assigned'],
            'uptd_user_id' => $user['id'],
            'notes' => $notes
        ];
        
        try {
            $priceModel->create($data);
            $_SESSION['success'] = 'Data harga berhasil diinput dan menunggu persetujuan admin';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menyimpan data: ' . $e->getMessage();
        }
        
        header('Location: dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Get UPTD's price data
$myPrices = $priceModel->getByUptd($user['id']);
$pendingCount = count($priceModel->getByUptd($user['id'], 'pending'));
$approvedCount = count($priceModel->getByUptd($user['id'], 'approved'));
$rejectedCount = count($priceModel->getByUptd($user['id'], 'rejected'));

$pageTitle = 'Dashboard UPTD - Siaga Bapok';
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
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .status-pending {
            color: #ffc107;
        }
        
        .status-approved {
            color: #28a745;
        }
        
        .status-rejected {
            color: #dc3545;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="bi bi-graph-up-arrow me-2"></i>
                SIAGA BAPOK
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?php echo $user['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php">
                            <i class="bi bi-house me-1"></i>Beranda
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h1 class="h3 mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>
                            Dashboard UPTD
                        </h1>
                        <p class="mb-0 mt-2">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?php echo htmlspecialchars($user['market_assigned']); ?>
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-warning fs-2 mb-2"></i>
                        <h3 class="status-pending"><?php echo $pendingCount; ?></h3>
                        <p class="mb-0">Menunggu Persetujuan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-check-circle text-success fs-2 mb-2"></i>
                        <h3 class="status-approved"><?php echo $approvedCount; ?></h3>
                        <p class="mb-0">Disetujui</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-x-circle text-danger fs-2 mb-2"></i>
                        <h3 class="status-rejected"><?php echo $rejectedCount; ?></h3>
                        <p class="mb-0">Ditolak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-list-ul text-info fs-2 mb-2"></i>
                        <h3 class="text-info"><?php echo count($myPrices); ?></h3>
                        <p class="mb-0">Total Input</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Input Form -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            Input Harga Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="priceForm">
                            <div class="mb-3">
                                <label for="commodity_name" class="form-label">
                                    <i class="bi bi-basket me-1"></i>Nama Komoditas *
                                </label>
                                <select class="form-select" id="commodity_name" name="commodity_name" required>
                                    <option value="">Pilih Komoditas</option>
                                    <option value="Beras Premium">Beras Premium</option>
                                    <option value="Beras Medium">Beras Medium</option>
                                    <option value="Cabai Merah">Cabai Merah</option>
                                    <option value="Cabai Rawit">Cabai Rawit</option>
                                    <option value="Bawang Merah">Bawang Merah</option>
                                    <option value="Bawang Putih">Bawang Putih</option>
                                    <option value="Minyak Goreng">Minyak Goreng</option>
                                    <option value="Gula Pasir">Gula Pasir</option>
                                    <option value="Daging Sapi">Daging Sapi</option>
                                    <option value="Daging Ayam">Daging Ayam</option>
                                    <option value="Telur Ayam">Telur Ayam</option>
                                    <option value="Ikan Tongkol">Ikan Tongkol</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class="bi bi-currency-dollar me-1"></i>Harga per Kg *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           placeholder="0" min="1" max="99999" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Masukkan harga dalam rupiah (maksimal 5 digit)
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="market_display" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Pasar
                                </label>
                                <input type="text" class="form-control" id="market_display" 
                                       value="<?php echo htmlspecialchars($user['market_assigned']); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-chat-text me-1"></i>Catatan (Opsional)
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Catatan tambahan..."></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>
                                    Kirim Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Data History -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Riwayat Input Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($myPrices)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Komoditas</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($myPrices, 0, 10) as $price): ?>
                                            <tr>
                                                <td>
                                                    <small><?php echo formatDateTime($price['created_at'], 'd M Y H:i'); ?></small>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($price['commodity_name']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="fw-bold"><?php echo formatRupiah($price['price']); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusIcon = '';
                                                    $statusText = '';
                                                    
                                                    switch ($price['status']) {
                                                        case 'pending':
                                                            $statusClass = 'bg-warning';
                                                            $statusIcon = 'bi-clock-history';
                                                            $statusText = 'Menunggu';
                                                            break;
                                                        case 'approved':
                                                            $statusClass = 'bg-success';
                                                            $statusIcon = 'bi-check-circle';
                                                            $statusText = 'Disetujui';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'bg-danger';
                                                            $statusIcon = 'bi-x-circle';
                                                            $statusText = 'Ditolak';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <i class="bi <?php echo $statusIcon; ?> me-1"></i>
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($price['notes']): ?>
                                                        <small class="text-muted"><?php echo htmlspecialchars($price['notes']); ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">-</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($myPrices) > 10): ?>
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Menampilkan 10 data terbaru dari <?php echo count($myPrices); ?> total data
                                    </small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data</h5>
                                <p class="text-muted">Mulai input data harga menggunakan form di sebelah kiri.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        document.getElementById('priceForm').addEventListener('submit', function(e) {
            const price = document.getElementById('price').value;
            const commodity = document.getElementById('commodity_name').value;
            
            if (!commodity) {
                e.preventDefault();
                alert('Silakan pilih komoditas terlebih dahulu');
                return;
            }
            
            if (!price || price < 1 || price > 99999) {
                e.preventDefault();
                alert('Harga harus berupa angka antara 1 - 99999');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengirim...';
            submitBtn.disabled = true;
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            });
        }, 5000);
        
        // Price input formatting
        document.getElementById('price').addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            this.value = value;
        });
    </script>
</body>
</html>