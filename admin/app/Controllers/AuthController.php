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
        try {
            $json = $this->request->getJSON(true);

            if (!$json) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid JSON format']);
            }

            $mobile = trim($json['mobile'] ?? '');

            // Validate mobile (E.164 format: +[country code][number])
            if (empty($mobile) || !preg_match('/^\+[1-9]\d{9,14}$/', $mobile)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Valid mobile number required (e.g., +919136797555)']);
            }

            $db = \Config\Database::connect();

            // Check rate limiting (prevent spam - max 2 requests per 2 mins)
            $recentOtpQuery = $db->query("
                SELECT COUNT(*) as count
                FROM mobile_users_otp_verifications
                WHERE mobile = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)
            ", [$mobile]);

            $recentCount = (int)($recentOtpQuery->getRowArray()['count'] ?? 0);

            // if ($recentCount >= 2) {
            //     return $this->response->setJSON(['success' => false, 'message' => 'Too many OTP requests. Please wait 2 minutes.']);
            // }

            // 1. Find user in wp_users table
            $user = $db->table('wp_users')->where('mobile', $mobile)->get()->getRowArray();

            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'No account linked to this mobile number.']);
            }

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

            // 4. Send OTP via Twilio SMS
            $smsResult = $this->sendOtpViaTwilio($mobile, $otp_code);

            // if (!$smsResult['success']) {
            //     log_message('error', "Failed to send SMS to {$mobile} - " . $smsResult['error']);
            //     return $this->response->setJSON([
            //         'success' => false, 
            //         'message' => 'Failed to send SMS. Please contact support.'
            //     ]);
            // }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'OTP sent successfully to your mobile',
                'dev_otp' => ENVIRONMENT === 'development' ? $otp_code : $otp_code
            ]);

        } catch (\Exception $e) {
            log_message('critical', 'sendOtp: Unexpected exception - ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An unexpected error occurred']);
        }
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

        $user = $db->table('wp_users')->where('mobile', $mobile)->get()->getRowArray();

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
     * TWILIO SMS HELPER
     * Copy the exact Twilio credentials from your English Flypped code here.
     */
    /**
     * SMS HELPER (Fortius API)
     */
     private function sendOtpViaTwilio(string $mobile, string $otp_code): array
    {
        try {
            // Your Twilio Credentials
            $sid    = getenv('TWILIO_SID');
            $token  = getenv('TWILIO_TOKEN');
            $from   = getenv('TWILIO_FROM');
            // Note: Keep the '+' sign for Twilio, so we use $mobile directly
            $message = " Hindi Flypped OTP: {$otp_code} Valid for 10 minutes. Please do not share this code with anyone";

            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

            log_message('info', "sendOtpViaTwilio: Sending OTP to {$mobile}");

            $client = \Config\Services::curlrequest();
            $response = $client->post($url, [
                'auth'        => [$sid, $token], // Twilio uses Basic Auth
                'form_params' => [
                    'To'   => $mobile,
                    'From' => $from,
                    'Body' => $message,
                ],
                'timeout'         => 10,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ]);

            $statusCode   = $response->getStatusCode();
            $responseBody = trim($response->getBody());

            log_message('info', "sendOtpViaTwilio: Response [{$statusCode}] - {$responseBody}");

            // Twilio returns 201 Created on success
            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success'  => true,
                    'response' => $responseBody,
                ];
            }

            return [
                'success' => false,
                'error'   => "Twilio API returned HTTP {$statusCode}: {$responseBody}",
            ];

             } catch (\Exception $e) {
            log_message('error', 'sendOtpViaTwilio: Exception - ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}