    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-graph-up-arrow me-2"></i>Siaga Bapok</h5>
                    <p class="mb-0">Sistem Informasi Harga Bahan Pokok Kota Bandar Lampung</p>
                    <small class="text-muted">Memantau pergerakan harga komoditas secara real-time</small>
                </div>
                <div class="col-md-3">
                    <h6>Menu</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php" class="text-light text-decoration-none">Beranda</a></li>
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/dashboard.php" class="text-light text-decoration-none">Dashboard</a></li>
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/komoditas.php" class="text-light text-decoration-none">Tabel Harga</a></li>
                        <li><a href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/about.php" class="text-light text-decoration-none">Tentang</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Kontak</h6>
                    <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>Bandar Lampung</p>
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i>info@siagabapok.com</p>
                    <p class="mb-0"><i class="bi bi-telephone me-2"></i>(0721) 123456</p>
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <small>&copy; <?php echo date('Y'); ?> Siaga Bapok. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Data terakhir diperbarui: <?php echo date('d M Y, H:i'); ?> WIB</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo isset($base_url) ? $base_url : ''; ?>assets/js/main.js"></script>
    
    <?php if (isset($additional_scripts)): ?>
        <?php echo $additional_scripts; ?>
    <?php endif; ?>
    
</body>
</html>