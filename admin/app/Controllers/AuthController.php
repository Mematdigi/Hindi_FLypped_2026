<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class AuthController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Show login form
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        return view('login');
    }

    // Process login
    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate user credentials with role information
        $user = $this->db->query("
            SELECT u.ID, u.user_login, u.user_email, u.display_name, 
                   m.meta_value as capabilities
            FROM wp_users u
            LEFT JOIN wp_usermeta m ON u.ID = m.user_id 
            WHERE u.user_email = ? AND m.meta_key = 'wp_capabilities'
        ", [$email])->getRow();

        if ($user && password_verify($password, $this->getUserPassword($user->ID))) {
            // Extract user role from capabilities
            $role = $this->extractUserRole($user->capabilities);
            
            // Set session data
            $sessionData = [
                'user_id' => $user->ID,
                'user_email' => $user->user_email,
                'user_name' => $user->display_name,
                'user_role' => $role,
                'isLoggedIn' => true
            ];
            
            session()->set($sessionData);
            
            return redirect()->to('/dashboard');
        } else {
            session()->setFlashdata('error', 'Invalid credentials');
            return redirect()->to('/login');
        }
    }

    // Get user password hash
    private function getUserPassword($userId)
    {
        $result = $this->db->query("SELECT user_pass FROM wp_users WHERE ID = ?", [$userId])->getRow();
        return $result ? $result->user_pass : null;
    }

    // Extract role from WordPress capabilities string
    private function extractUserRole($capabilities)
    {
        if (empty($capabilities)) {
            return 'subscriber';
        }

        // WordPress stores capabilities as serialized array
        $caps = @unserialize($capabilities);
        if (is_array($caps)) {
            $roles = array_keys($caps);
            return isset($roles[0]) ? $roles[0] : 'subscriber';
        }
        
        return 'subscriber';
    }

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // Check if user has permission
    public static function hasPermission($requiredRole)
    {
        $userRole = session()->get('user_role');
        
        // Administrator can access everything
        if ($userRole === 'administrator') {
            return true;
        }
        
        // Define role hierarchy
        $roleHierarchy = [
            'subscriber' => 1,
            'contributor' => 2,
            'author' => 3,
            'editor' => 4,
            'seo_editor' => 4,
            'seo_manager' => 5,
            'administrator' => 6
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }

    // Check if user can access admin features
    public static function canAccessAdminFeatures()
    {
        $userRole = session()->get('user_role');
        return in_array($userRole, ['administrator']);
    }

    // Check if user can manage posts
    public static function canManagePosts()
    {
        $userRole = session()->get('user_role');
        return in_array($userRole, ['administrator', 'editor', 'author', 'seo_editor', 'seo_manager']);
    }
}