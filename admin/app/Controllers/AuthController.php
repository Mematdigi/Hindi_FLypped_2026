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
public function sendOtp()
    {
        $json = $this->request->getJSON(true);
        $mobile = trim($json['mobile'] ?? '');

        if (empty($mobile) || strlen($mobile) < 10) {
            return $this->response->setJSON(['success' => false, 'message' => 'Valid mobile number required']);
        }

        $db = \Config\Database::connect();
        
        // 1. Find user in wp_users
        $user = $db->table('wp_users')->where('mobile', $mobile)->get()->getRowArray();
        
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'No account linked to this mobile number.']);
        }

        // 2. Generate OTP
        $otp_code = sprintf("%06d", mt_rand(100000, 999999));
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // 3. Save OTP in the separate verifications table
        $existingOtp = $db->table('mobile_users_otp_verifications')
                          ->where(['user_id' => $user['ID'], 'otp_type' => 'login', 'is_verified' => 0])
                          ->get()->getRowArray();

        if ($existingOtp) {
            $db->table('mobile_users_otp_verifications')
               ->where('id', $existingOtp['id'])
               ->update([
                   'otp_code' => $otp_code, 
                   'expires_at' => $expires_at, 
                   'created_at' => date('Y-m-d H:i:s')
               ]);
        } else {
            $db->table('mobile_users_otp_verifications')->insert([
                'user_id' => $user['ID'], // WordPress primary key is uppercase 'ID'
                'mobile' => $mobile,
                'otp_code' => $otp_code,
                'otp_type' => 'login',
                'is_verified' => 0,
                'expires_at' => $expires_at,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // TODO: Call your Twilio/SMS helper here to actually send the text message
        // $this->sendOtpViaTwilio($mobile, $otp_code);

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'OTP sent successfully',
            'dev_otp' => ENVIRONMENT === 'development' ? $otp_code : null
        ]);
    }

    public function verifyOtp()
    {
        $json = $this->request->getJSON(true);
        $mobile = trim($json['mobile'] ?? '');
        $otp_code = trim($json['otp_code'] ?? '');

        if (empty($mobile) || empty($otp_code)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mobile and OTP required']);
        }

        $db = \Config\Database::connect();
        
        // 1. Verify user exists in wp_users
        $user = $db->table('wp_users')->where('mobile', $mobile)->get()->getRowArray();
        
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // 2. Look up the OTP in the separate table
        $otpRecord = $db->table('mobile_users_otp_verifications')
                        ->where(['user_id' => $user['ID'], 'otp_type' => 'login', 'is_verified' => 0])
                        ->orderBy('created_at', 'DESC')
                        ->get()->getRowArray();

        if (!$otpRecord) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid OTP found']);
        }
        
        if (strtotime($otpRecord['expires_at']) < time()) {
            $db->table('mobile_users_otp_verifications')->where('id', $otpRecord['id'])->update(['is_verified' => 2]);
            return $this->response->setJSON(['success' => false, 'message' => 'OTP has expired']);
        }

        if ($otpRecord['otp_code'] !== $otp_code) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid OTP code']);
        }

        // 3. OTP Validated: Mark as verified in OTP table
        $db->table('mobile_users_otp_verifications')
           ->where('id', $otpRecord['id'])
           ->update(['is_verified' => 1, 'verified_at' => date('Y-m-d H:i:s')]);
           
        // 4. Update the last_login column we just created in wp_users
        $db->table('wp_users')
           ->where('ID', $user['ID'])
           ->update(['last_login' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['success' => true, 'message' => 'OTP verified']);
    }
}