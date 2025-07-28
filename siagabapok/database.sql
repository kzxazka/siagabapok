-- Database: siagabapok_db
-- Sistem Informasi Harga Bahan Pokok Kota Bandar Lampung

CREATE DATABASE IF NOT EXISTS siagabapok_db;
USE siagabapok_db;

-- Tabel Pasar
CREATE TABLE pasar (
    id_pasar INT PRIMARY KEY AUTO_INCREMENT,
    nama_pasar VARCHAR(100) NOT NULL,
    alamat TEXT,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Komoditas
CREATE TABLE komoditas (
    id_komoditas INT PRIMARY KEY AUTO_INCREMENT,
    nama_komoditas VARCHAR(100) NOT NULL,
    satuan VARCHAR(20) DEFAULT 'kg',
    kategori VARCHAR(50),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Harga
CREATE TABLE harga (
    id_harga INT PRIMARY KEY AUTO_INCREMENT,
    id_pasar INT NOT NULL,
    id_komoditas INT NOT NULL,
    tanggal DATE NOT NULL,
    harga_per_kg DECIMAL(10,2) NOT NULL,
    stok_tersedia ENUM('tersedia', 'terbatas', 'kosong') DEFAULT 'tersedia',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pasar) REFERENCES pasar(id_pasar) ON DELETE CASCADE,
    FOREIGN KEY (id_komoditas) REFERENCES komoditas(id_komoditas) ON DELETE CASCADE,
    UNIQUE KEY unique_price_per_day (id_pasar, id_komoditas, tanggal)
);

-- Insert Data Pasar
INSERT INTO pasar (nama_pasar, alamat, keterangan) VALUES
('Pasar Tugu', 'Jl. Kartini, Tugu, Bandar Lampung', 'Pasar tradisional terbesar di Bandar Lampung'),
('Pasar Bambu Kuning', 'Jl. Bambu Kuning, Kemiling, Bandar Lampung', 'Pasar tradisional di wilayah Kemiling'),
('Pasar Smep', 'Jl. Teuku Umar, Kedaton, Bandar Lampung', 'Pasar modern dengan fasilitas lengkap'),
('Pasar Kangkung', 'Jl. Kangkung, Teluk Betung, Bandar Lampung', 'Pasar tradisional di Teluk Betung'),
('Pasar Pasir Gintung', 'Jl. Pasir Gintung, Tanjung Karang, Bandar Lampung', 'Pasar tradisional di pusat kota'),
('Pasar Way Halim', 'Jl. Way Halim, Kedaton, Bandar Lampung', 'Pasar tradisional di Way Halim'),
('Pasar Panjang', 'Jl. Panjang, Panjang, Bandar Lampung', 'Pasar tradisional di wilayah Panjang'),
('Pasar Gudang Lelang', 'Jl. Gudang Lelang, Teluk Betung, Bandar Lampung', 'Pasar grosir dan eceran'),
('Pasar Sukarame', 'Jl. Sukarame, Sukarame, Bandar Lampung', 'Pasar tradisional di Sukarame'),
('Pasar Rajabasa', 'Jl. Rajabasa, Rajabasa, Bandar Lampung', 'Pasar tradisional di Rajabasa'),
('Pasar Kemiling', 'Jl. Kemiling Raya, Kemiling, Bandar Lampung', 'Pasar tradisional di Kemiling'),
('Pasar Langkapura', 'Jl. Langkapura, Langkapura, Bandar Lampung', 'Pasar tradisional di Langkapura');

-- Insert Data Komoditas
INSERT INTO komoditas (nama_komoditas, satuan, kategori, keterangan) VALUES
('Beras Premium', 'kg', 'Beras', 'Beras kualitas premium'),
('Beras Medium', 'kg', 'Beras', 'Beras kualitas medium'),
('Cabai Merah', 'kg', 'Sayuran', 'Cabai merah segar'),
('Cabai Rawit', 'kg', 'Sayuran', 'Cabai rawit hijau dan merah'),
('Bawang Merah', 'kg', 'Sayuran', 'Bawang merah lokal'),
('Bawang Putih', 'kg', 'Sayuran', 'Bawang putih impor dan lokal'),
('Minyak Goreng', 'liter', 'Minyak', 'Minyak goreng kemasan'),
('Gula Pasir', 'kg', 'Gula', 'Gula pasir putih'),
('Daging Sapi', 'kg', 'Protein', 'Daging sapi segar'),
('Daging Ayam', 'kg', 'Protein', 'Daging ayam broiler'),
('Telur Ayam', 'kg', 'Protein', 'Telur ayam negeri'),
('Ikan Tongkol', 'kg', 'Protein', 'Ikan tongkol segar'),
('Tomat', 'kg', 'Sayuran', 'Tomat segar'),
('Kentang', 'kg', 'Sayuran', 'Kentang granola'),
('Wortel', 'kg', 'Sayuran', 'Wortel segar'),
('Kacang Tanah', 'kg', 'Kacang-kacangan', 'Kacang tanah kupas'),
('Kacang Kedelai', 'kg', 'Kacang-kacangan', 'Kacang kedelai impor'),
('Jagung', 'kg', 'Serealia', 'Jagung pipilan kering'),
('Tepung Terigu', 'kg', 'Tepung', 'Tepung terigu protein sedang'),
('Garam', 'kg', 'Bumbu', 'Garam dapur'),
('Kecap Manis', 'botol', 'Bumbu', 'Kecap manis 600ml'),
('Mie Instan', 'dus', 'Makanan Instan', 'Mie instan per dus (40 bungkus)'),
('Susu Kental Manis', 'kaleng', 'Susu', 'Susu kental manis 397gr'),
('Mentega', 'kg', 'Lemak', 'Mentega tawar'),
('Keju', 'kg', 'Protein', 'Keju cheddar');

-- Insert Data Harga (7 hari terakhir)
-- Menggunakan stored procedure untuk generate data sample
DELIMITER //

CREATE PROCEDURE GenerateSampleData()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE pasar_id INT;
    DECLARE komoditas_id INT;
    DECLARE base_price DECIMAL(10,2);
    DECLARE current_date DATE;
    DECLARE day_counter INT DEFAULT 0;
    
    -- Cursor untuk pasar
    DECLARE pasar_cursor CURSOR FOR SELECT id_pasar FROM pasar;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Generate data untuk 7 hari terakhir
    WHILE day_counter < 7 DO
        SET current_date = DATE_SUB(CURDATE(), INTERVAL day_counter DAY);
        
        -- Reset cursor
        SET done = FALSE;
        OPEN pasar_cursor;
        
        pasar_loop: LOOP
            FETCH pasar_cursor INTO pasar_id;
            IF done THEN
                LEAVE pasar_loop;
            END IF;
            
            -- Insert harga untuk setiap komoditas di pasar ini
            -- Beras Premium (15000-18000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 1, current_date, 15000 + (RAND() * 3000));
            
            -- Beras Medium (12000-14000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 2, current_date, 12000 + (RAND() * 2000));
            
            -- Cabai Merah (25000-45000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 3, current_date, 25000 + (RAND() * 20000));
            
            -- Cabai Rawit (30000-50000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 4, current_date, 30000 + (RAND() * 20000));
            
            -- Bawang Merah (18000-25000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 5, current_date, 18000 + (RAND() * 7000));
            
            -- Bawang Putih (22000-28000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 6, current_date, 22000 + (RAND() * 6000));
            
            -- Minyak Goreng (16000-18000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 7, current_date, 16000 + (RAND() * 2000));
            
            -- Gula Pasir (13000-15000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 8, current_date, 13000 + (RAND() * 2000));
            
            -- Daging Sapi (120000-140000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 9, current_date, 120000 + (RAND() * 20000));
            
            -- Daging Ayam (28000-35000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 10, current_date, 28000 + (RAND() * 7000));
            
            -- Telur Ayam (24000-28000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 11, current_date, 24000 + (RAND() * 4000));
            
            -- Ikan Tongkol (20000-25000)
            INSERT INTO harga (id_pasar, id_komoditas, tanggal, harga_per_kg) 
            VALUES (pasar_id, 12, current_date, 20000 + (RAND() * 5000));
            
        END LOOP;
        
        CLOSE pasar_cursor;
        SET day_counter = day_counter + 1;
    END WHILE;
END //

DELIMITER ;

-- Execute the procedure to generate sample data
CALL GenerateSampleData();

-- Drop the procedure after use
DROP PROCEDURE GenerateSampleData;

-- Create indexes for better performance
CREATE INDEX idx_harga_tanggal ON harga(tanggal);
CREATE INDEX idx_harga_pasar ON harga(id_pasar);
CREATE INDEX idx_harga_komoditas ON harga(id_komoditas);
CREATE INDEX idx_harga_composite ON harga(tanggal, id_pasar, id_komoditas);

-- Create views for common queries
CREATE VIEW view_harga_terkini AS
SELECT 
    p.nama_pasar,
    k.nama_komoditas,
    h.harga_per_kg,
    h.tanggal,
    h.stok_tersedia
FROM harga h
JOIN pasar p ON h.id_pasar = p.id_pasar
JOIN komoditas k ON h.id_komoditas = k.id_komoditas
WHERE h.tanggal = CURDATE();

CREATE VIEW view_rata_harga_mingguan AS
SELECT 
    k.nama_komoditas,
    AVG(h.harga_per_kg) as rata_harga,
    MIN(h.harga_per_kg) as harga_terendah,
    MAX(h.harga_per_kg) as harga_tertinggi,
    COUNT(*) as jumlah_data
FROM harga h
JOIN komoditas k ON h.id_komoditas = k.id_komoditas
WHERE h.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY k.id_komoditas, k.nama_komoditas
ORDER BY k.nama_komoditas;

-- Show table structure
SHOW TABLES;
SELECT COUNT(*) as total_pasar FROM pasar;
SELECT COUNT(*) as total_komoditas FROM komoditas;
SELECT COUNT(*) as total_data_harga FROM harga;

-- Sample queries to test
SELECT 'Data harga hari ini:' as info;
SELECT * FROM view_harga_terkini LIMIT 10;

SELECT 'Rata-rata harga mingguan:' as info;
SELECT * FROM view_rata_harga_mingguan LIMIT 10;