<?php
// Konfigurasi Database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'siagabapok_db';

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke UTF-8
$conn->set_charset("utf8");

// Fungsi untuk membersihkan input
function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// Fungsi untuk format tanggal Indonesia
function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// Fungsi untuk format rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>