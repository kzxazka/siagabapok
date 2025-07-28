<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Siaga Bapok - Sistem Informasi Harga Bahan Pokok</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo isset($base_url) ? $base_url : ''; ?>assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #28a745;
            --light-green: #d4edda;
            --dark-green: #155724;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.15s ease-in-out;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .text-primary {
            color: var(--primary-green) !important;
        }
        
        .bg-light-green {
            background-color: var(--light-green) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            padding: 4rem 0;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php">
                <i class="bi bi-graph-up-arrow me-2"></i>
                <span>SIAGA BAPOK</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>index.php">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/dashboard.php">
                            <i class="bi bi-graph-up me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'komoditas.php' ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/komoditas.php">
                            <i class="bi bi-list-ul me-1"></i>Tabel Harga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>pages/about.php">
                            <i class="bi bi-info-circle me-1"></i>Tentang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->