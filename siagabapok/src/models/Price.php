<?php
require_once __DIR__ . '/../../config/database.php';

class Price {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO prices (commodity_name, price, market_name, uptd_user_id, notes) 
                VALUES (?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $data['commodity_name'],
            $data['price'],
            $data['market_name'],
            $data['uptd_user_id'],
            $data['notes'] ?? null
        ]);
    }
    
    public function getAll($status = null, $limit = null) {
        $sql = "SELECT p.*, u.full_name as uptd_name, admin.full_name as approved_by_name 
                FROM prices p 
                JOIN users u ON p.uptd_user_id = u.id 
                LEFT JOIN users admin ON p.approved_by = admin.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE p.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, u.full_name as uptd_name, admin.full_name as approved_by_name 
                FROM prices p 
                JOIN users u ON p.uptd_user_id = u.id 
                LEFT JOIN users admin ON p.approved_by = admin.id 
                WHERE p.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getByUptd($uptdId, $status = null) {
        $sql = "SELECT p.*, u.full_name as uptd_name 
                FROM prices p 
                JOIN users u ON p.uptd_user_id = u.id 
                WHERE p.uptd_user_id = ?";
        
        $params = [$uptdId];
        
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function approve($id, $adminId, $notes = null) {
        $sql = "UPDATE prices SET status = 'approved', approved_by = ?, approved_at = NOW(), notes = ? WHERE id = ?";
        return $this->db->execute($sql, [$adminId, $notes, $id]);
    }
    
    public function reject($id, $adminId, $notes = null) {
        $sql = "UPDATE prices SET status = 'rejected', approved_by = ?, approved_at = NOW(), notes = ? WHERE id = ?";
        return $this->db->execute($sql, [$adminId, $notes, $id]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE prices SET commodity_name = ?, price = ?, market_name = ?, notes = ? WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['commodity_name'],
            $data['price'],
            $data['market_name'],
            $data['notes'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM prices WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function getApprovedPrices($days = 30) {
        $sql = "SELECT * FROM view_approved_prices 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, [$days]);
    }
    
    public function getPriceTrends($days = 30) {
        $sql = "SELECT 
                    commodity_name,
                    DATE(created_at) as price_date,
                    AVG(price) as avg_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price,
                    COUNT(*) as market_count
                FROM prices 
                WHERE status = 'approved' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY commodity_name, DATE(created_at)
                ORDER BY commodity_name, price_date DESC";
        
        return $this->db->fetchAll($sql, [$days]);
    }
    
    public function getLatestPrices() {
        $sql = "SELECT 
                    commodity_name,
                    market_name,
                    price,
                    created_at
                FROM (
                    SELECT *,
                           ROW_NUMBER() OVER (PARTITION BY commodity_name, market_name ORDER BY created_at DESC) as rn
                    FROM prices 
                    WHERE status = 'approved'
                ) ranked
                WHERE rn = 1
                ORDER BY commodity_name, market_name";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getPriceComparison($commodityName, $days = 7) {
        $sql = "SELECT 
                    market_name,
                    price,
                    created_at
                FROM prices 
                WHERE commodity_name = ? AND status = 'approved' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY created_at DESC, market_name";
        
        return $this->db->fetchAll($sql, [$commodityName, $days]);
    }
    
    public function getTopIncreasingPrices($days = 7, $limit = 5) {
        $sql = "SELECT 
                    commodity_name,
                    AVG(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) THEN price END) as current_avg,
                    AVG(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                             AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY) THEN price END) as previous_avg,
                    COUNT(*) as data_count
                FROM prices 
                WHERE status = 'approved' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY commodity_name
                HAVING current_avg IS NOT NULL AND previous_avg IS NOT NULL
                ORDER BY (current_avg - previous_avg) / previous_avg DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$days, $days * 2, $days, $days * 2, $limit]);
    }
    
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_prices,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count,
                    COUNT(DISTINCT commodity_name) as total_commodities,
                    COUNT(DISTINCT market_name) as total_markets
                FROM prices";
        
        return $this->db->fetchOne($sql);
    }
    
    public function getCommodityList() {
        $sql = "SELECT DISTINCT commodity_name FROM prices WHERE status = 'approved' ORDER BY commodity_name";
        return $this->db->fetchAll($sql);
    }
    
    public function getMarketList() {
        $sql = "SELECT DISTINCT market_name FROM prices WHERE status = 'approved' ORDER BY market_name";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPrices($filters) {
        $sql = "SELECT p.*, u.full_name as uptd_name 
                FROM prices p 
                JOIN users u ON p.uptd_user_id = u.id 
                WHERE p.status = 'approved'";
        
        $params = [];
        
        if (!empty($filters['commodity'])) {
            $sql .= " AND p.commodity_name = ?";
            $params[] = $filters['commodity'];
        }
        
        if (!empty($filters['market'])) {
            $sql .= " AND p.market_name = ?";
            $params[] = $filters['market'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(p.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(p.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
}
?>