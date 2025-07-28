<?php
require_once '../includes/db.php';

$base_url = '../';
$page_title = 'Tentang Siaga Bapok';

include '../includes/header.php';
?>

<div class="container my-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-5">
                    <i class="bi bi-graph-up-arrow display-1 mb-4"></i>
                    <h1 class="display-4 fw-bold mb-3">Siaga Bapok</h1>
                    <p class="lead mb-0">Sistem Informasi Harga Bahan Pokok Kota Bandar Lampung</p>
                </div>
            </div>
        </div>
    </div>

    <!-- About Content -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-primary mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Tentang Sistem
                    </h2>
                    
                    <p class="lead mb-4">
                        Siaga Bapok adalah sistem informasi yang dirancang untuk memantau dan menyajikan data harga 
                        bahan pokok secara real-time di Kota Bandar Lampung. Sistem ini bertujuan untuk memberikan 
                        transparansi informasi harga kepada masyarakat, pedagang, dan pemerintah daerah.
                    </p>

                    <h4 class="text-primary mb-3">Tujuan Sistem</h4>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Menyediakan informasi harga bahan pokok yang akurat dan terkini
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Membantu masyarakat dalam membandingkan harga di berbagai pasar
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Mendukung stabilitas harga melalui transparansi informasi
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Membantu pemerintah dalam monitoring dan pengambilan kebijakan
                        </li>
                    </ul>

                    <h4 class="text-primary mb-3">Fitur Utama</h4>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-graph-up text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6>Dashboard Analytics</h6>
                                    <small class="text-muted">Visualisasi data dengan grafik interaktif</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-table text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6>Tabel Harga</h6>
                                    <small class="text-muted">Data harga detail per pasar dan komoditas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-clock text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6>Update Real-time</h6>
                                    <small class="text-muted">Data diperbarui secara berkala</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-download text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6>Export Data</h6>
                                    <small class="text-muted">Unduh data dalam format CSV</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="text-center mb-4">Cakupan Data</h3>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-shop text-primary fs-1 mb-3"></i>
                    <h4 class="text-primary">12+</h4>
                    <p class="mb-0">Pasar Tradisional</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-basket text-success fs-1 mb-3"></i>
                    <h4 class="text-success">25+</h4>
                    <p class="mb-0">Jenis Komoditas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-calendar-check text-info fs-1 mb-3"></i>
                    <h4 class="text-info">Harian</h4>
                    <p class="mb-0">Frekuensi Update</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people text-warning fs-1 mb-3"></i>
                    <h4 class="text-warning">Publik</h4>
                    <p class="mb-0">Akses Data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Technology Stack -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-gear-fill me-2"></i>
                        Teknologi yang Digunakan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary">Backend</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-code-slash me-2"></i>PHP Native</li>
                                <li><i class="bi bi-database me-2"></i>MySQL Database</li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary">Frontend</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-bootstrap me-2"></i>Bootstrap 5</li>
                                <li><i class="bi bi-bar-chart me-2"></i>Chart.js</li>
                                <li><i class="bi bi-code-square me-2"></i>jQuery</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Sources -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-collection me-2"></i>
                        Sumber Data
                    </h4>
                </div>
                <div class="card-body">
                    <p class="mb-3">Data harga komoditas diperoleh dari survei harian di pasar-pasar tradisional di Kota Bandar Lampung, meliputi:</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">Pasar Utama</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Tugu</li>
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Bambu Kuning</li>
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Smep</li>
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Kangkung</li>
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Pasir Gintung</li>
                                <li><i class="bi bi-geo-alt me-1"></i>Pasar Way Halim</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">Komoditas Utama</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-dot me-1"></i>Beras Premium & Medium</li>
                                <li><i class="bi bi-dot me-1"></i>Cabai Merah & Rawit</li>
                                <li><i class="bi bi-dot me-1"></i>Bawang Merah & Putih</li>
                                <li><i class="bi bi-dot me-1"></i>Minyak Goreng</li>
                                <li><i class="bi bi-dot me-1"></i>Gula Pasir</li>
                                <li><i class="bi bi-dot me-1"></i>Daging Sapi & Ayam</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-light">
                <div class="card-body text-center py-5">
                    <h4 class="mb-4">Informasi Kontak</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <i class="bi bi-geo-alt text-primary fs-2 mb-2"></i>
                            <h6>Alamat</h6>
                            <p class="text-muted mb-0">Kota Bandar Lampung<br>Provinsi Lampung</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="bi bi-envelope text-primary fs-2 mb-2"></i>
                            <h6>Email</h6>
                            <p class="text-muted mb-0">info@siagabapok.com<br>admin@siagabapok.com</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="bi bi-telephone text-primary fs-2 mb-2"></i>
                            <h6>Telepon</h6>
                            <p class="text-muted mb-0">(0721) 123456<br>(0721) 654321</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="card">
                <div class="card-body py-4">
                    <h5 class="mb-3">Jelajahi Data Harga</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="../index.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-house me-2"></i>Beranda
                        </a>
                        <a href="dashboard.php" class="btn btn-success btn-lg">
                            <i class="bi bi-graph-up me-2"></i>Dashboard
                        </a>
                        <a href="komoditas.php" class="btn btn-info btn-lg">
                            <i class="bi bi-table me-2"></i>Tabel Harga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>