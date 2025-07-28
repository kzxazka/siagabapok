<?php
session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->userModel->cleanExpiredSessions();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitizeInput($_POST['username']);
            $password = $_POST['password'];
            
            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username dan password harus diisi';
                header('Location: /siagabapok/public/login.php');
                exit;
            }
            
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                $token = $this->userModel->createSession($user['id']);
                
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['market_assigned'] = $user['market_assigned'];
                $_SESSION['session_token'] = $token;
                
                // Set cookie for remember me
                setcookie('session_token', $token, time() + (24 * 60 * 60), '/');
                
                $_SESSION['success'] = 'Login berhasil! Selamat datang, ' . $user['full_name'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /siagabapok/public/admin/dashboard.php');
                        break;
                    case 'uptd':
                        header('Location: /siagabapok/public/uptd/dashboard.php');
                        break;
                    case 'masyarakat':
                        header('Location: /siagabapok/public/index.php');
                        break;
                    default:
                        header('Location: /siagabapok/public/index.php');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header('Location: /siagabapok/public/login.php');
                exit;
            }
        }
        
        // Show login form
        include __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        if (isset($_SESSION['session_token'])) {
            $this->userModel->destroySession($_SESSION['session_token']);
        }
        
        // Clear session
        session_destroy();
        
        // Clear cookie
        setcookie('session_token', '', time() - 3600, '/');
        
        header('Location: /siagabapok/public/login.php?logged_out=1');
        exit;
    }
    
    public function checkAuth() {
        // Check session first
        if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
            $user = $this->userModel->getBySession($_SESSION['session_token']);
            if ($user) {
                return $user;
            }
        }
        
        // Check cookie
        if (isset($_COOKIE['session_token'])) {
            $user = $this->userModel->getBySession($_COOKIE['session_token']);
            if ($user) {
                // Restore session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['market_assigned'] = $user['market_assigned'];
                $_SESSION['session_token'] = $_COOKIE['session_token'];
                
                return $user;
            }
        }
        
        return false;
    }
    
    public function requireAuth($allowedRoles = []) {
        $user = $this->checkAuth();
        
        if (!$user) {
            $_SESSION['error'] = 'Anda harus login terlebih dahulu';
            header('Location: /siagabapok/public/login.php');
            exit;
        }
        
        if (!empty($allowedRoles) && !in_array($user['role'], $allowedRoles)) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
            header('Location: /siagabapok/public/unauthorized.php');
            exit;
        }
        
        return $user;
    }
    
    public function requireRole($role) {
        return $this->requireAuth([$role]);
    }
    
    public function isLoggedIn() {
        return $this->checkAuth() !== false;
    }
    
    public function getCurrentUser() {
        return $this->checkAuth();
    }
    
    public function redirectIfLoggedIn() {
        $user = $this->checkAuth();
        if ($user) {
            switch ($user['role']) {
                case 'admin':
                    header('Location: /siagabapok/public/admin/dashboard.php');
                    break;
                case 'uptd':
                    header('Location: /siagabapok/public/uptd/dashboard.php');
                    break;
                case 'masyarakat':
                    header('Location: /siagabapok/public/index.php');
                    break;
                default:
                    header('Location: /siagabapok/public/index.php');
            }
            exit;
        }
    }
}

// Helper function to get auth controller instance
function getAuth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new AuthController();
    }
    return $auth;
}
?>