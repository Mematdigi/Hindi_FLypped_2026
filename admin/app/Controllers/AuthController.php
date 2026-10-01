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

    // Find the user by email (preferred) or by mobile (fallback)
    private function findOtpUser($db, string $email, string $mobile): ?array
    {
        if ($email !== '') {
            return $db->table('wp_users')->where('user_email', $email)->get()->getRowArray();
        }

        if ($mobile !== '') {
            return $db->table('wp_users')->where('mobile', $mobile)->get()->getRowArray();
        }

        return null;
    }

    public function sendOtp()
    {
        try {
            $json = $this->request->getJSON(true);

            if (!$json) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid JSON format']);
            }

            $email  = trim($json['email'] ?? '');
            $mobile = trim($json['mobile'] ?? '');

            if ($email === '' && $mobile === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Email address required']);
            }

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Valid email address required']);
            }

            $db = \Config\Database::connect();

            // 1. Find user in wp_users table
            $user = $this->findOtpUser($db, $email, $mobile);

            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'No account found for this email.']);
            }

            $email  = $user['user_email'];
            $mobile = $user['mobile'] ?? '';

            // Check rate limiting (prevent spam - max 2 requests per 2 mins)
            $recentOtpQuery = $db->query("
                SELECT COUNT(*) as count
                FROM mobile_users_otp_verifications
                WHERE user_id = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)
            ", [$user['ID']]);

            $recentCount = (int)($recentOtpQuery->getRowArray()['count'] ?? 0);

            // if ($recentCount >= 2) {
            //     return $this->response->setJSON(['success' => false, 'message' => 'Too many OTP requests. Please wait 2 minutes.']);
            // }

            // 2. Generate 6-digit OTP
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
                    'user_id' => $user['ID'], 
                    'mobile' => $mobile,
                    'otp_code' => $otp_code,
                    'otp_type' => 'login',
                    'is_verified' => 0,
                    'expires_at' => $expires_at,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Temporary bypass: set OTP_BYPASS = true in .env to show OTP on screen
            if (getenv('OTP_BYPASS') === 'true') {
                log_message('warning', "OTP_BYPASS is ON - OTP returned in response for {$email}");

                return $this->response->setJSON([
                    'success' => true,
                    'message' => "TEST MODE - Your OTP is: {$otp_code}",
                    'otp'     => $otp_code,
                ]);
            }

            // 4. Send OTP via email
            $result = $this->sendOtpViaEmail($otp_code, $email);

            if (!$result['success']) {
                log_message('error', "Failed to send OTP email for {$email} - " . $result['error']);

                return $this->response->setJSON([
                    'success' => false, 
                    'message' => $result['error'] 
                ]);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'OTP sent successfully to your email'
            ]);

        } catch (\Throwable $e) {
            log_message('critical', 'sendOtp: Unexpected exception - ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An unexpected error occurred']);
        }
    }

    public function verifyOtp()
    {
        $json = $this->request->getJSON(true);
        $email = trim($json['email'] ?? '');
        $mobile = trim($json['mobile'] ?? '');
        $otp_code = trim($json['otp_code'] ?? '');

        if (($email === '' && $mobile === '') || $otp_code === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Email and OTP required']);
        }

        $db = \Config\Database::connect();

        $user = $this->findOtpUser($db, $email, $mobile);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

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

        // OTP Validated
        $db->table('mobile_users_otp_verifications')
           ->where('id', $otpRecord['id'])
           ->update(['is_verified' => 1, 'verified_at' => date('Y-m-d H:i:s')]);

        $db->table('wp_users')
           ->where('ID', $user['ID'])
           ->update(['last_login' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['success' => true, 'message' => 'OTP verified']);
    }

    
        /**
     * EMAIL OTP HELPER
     * Sends the OTP through a Google Apps Script web app (OTP_GAS_URL in .env),
     * because outbound SMTP ports are blocked on this droplet.
     */
    private function sendOtpViaEmail(string $otp_code, string $requestedBy = ''): array
    {
        try {
            $url    = getenv('OTP_GAS_URL');
            $secret = getenv('OTP_GAS_SECRET');

            if (empty($url) || empty($secret)) {
                log_message('error', 'sendOtpViaEmail: Missing OTP_GAS_URL / OTP_GAS_SECRET in .env');
                return [
                    'success' => false,
                    'error'   => 'Could not send OTP right now. Please try again later.',
                ];
            }

            log_message('info', 'sendOtpViaEmail: Sending OTP via Google Apps Script' . ($requestedBy !== '' ? " (requested by {$requestedBy})" : ''));

            $client = \Config\Services::curlrequest();
            $response = $client->post($url, [
                'json' => [
                    'secret'       => $secret,
                    'otp'          => $otp_code,
                    'requested_by' => $requestedBy,
                ],
                'allow_redirects' => true,
                'timeout'         => 20,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ]);

            $statusCode = $response->getStatusCode();
            $decoded    = json_decode(trim($response->getBody()), true) ?: [];

            log_message('info', "sendOtpViaEmail: Apps Script response [{$statusCode}] success=" . (!empty($decoded['success']) ? 'true' : 'false'));

            if ($statusCode >= 200 && $statusCode < 300 && !empty($decoded['success'])) {
                return [
                    'success'  => true,
                    'response' => 'email_sent',
                ];
            }

            log_message('error', "sendOtpViaEmail: Apps Script failed [{$statusCode}] " . ($decoded['message'] ?? 'no message'));

            return [
                'success' => false,
                'error'   => 'Could not send OTP right now. Please try again later.',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'sendOtpViaEmail: Exception - ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Could not send OTP right now. Please try again later.',
            ];
        }
    }
}