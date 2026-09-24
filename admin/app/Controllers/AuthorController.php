<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class AuthorController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        helper(['form', 'url']);
    }

    // Show all users (all roles)
    public function index()
    {
        $builder = $this->db->table('wp_users u');
        $builder->select('u.ID, u.user_login, u.user_email, u.display_name, COUNT(p.ID) as post_count, m.meta_value as role');
        $builder->join('wp_usermeta m', 'u.ID = m.user_id AND m.meta_key = "wp_capabilities"', 'left');
        $builder->join('wp_posts p', 'u.ID = p.post_author', 'left');
        $builder->groupBy('u.ID, u.user_login, u.user_email, u.display_name, m.meta_value');
        $authors = $builder->get()->getResult();

        return view('authors', ['authors' => $authors]);
    }

    // Add new user
    public function create()
    {
        // Validate required fields
        $validation = \Config\Services::validation();
        $validation->setRules([
            'first_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[wp_users.user_email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[subscriber,contributor,author,editor,administrator,seo_editor,seo_manager]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', 'Please check your input: ' . implode(', ', $validation->getErrors()));
            return redirect()->back()->withInput();
        }

        try {
            $firstName = $this->request->getPost('first_name');
            $lastName  = $this->request->getPost('last_name') ?? '';
            $email     = $this->request->getPost('email');
            $password  = $this->request->getPost('password');
            $role      = $this->request->getPost('role');
            $bio       = $this->request->getPost('bio') ?? '';
            $mobile    = $this->request->getPost('mobile') ?? '';
// Handle profile image upload
            $profileImage = null;
            $imgFile = $this->request->getFile('profile_image');
            
            if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
                // Create upload directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/profile_images/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // --- Keep original name and make it SEO friendly ---
                $originalName = $imgFile->getName();
                $ext = $imgFile->getExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                
                // Clean the file name (lowercase, alphanumeric, and dashes only)
                $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
                
                // Fallback just in case the image name had only special characters
                if (empty($cleanName)) {
                    $cleanName = 'profile-' . time();
                }
                
                $newName = $cleanName . '.' . $ext;
                
                // Ensure uniqueness (prevents overwriting if a file with the same name exists)
                $counter = 1;
                while (file_exists($uploadPath . $newName)) {
                    $newName = $cleanName . '-' . $counter . '.' . $ext;
                    $counter++;
                }
                // --------------------------------------------------

                if ($imgFile->move($uploadPath, $newName)) {
                    $profileImage = $newName;
                }
            }
            // Create unique username
            $username = strtolower($firstName . $lastName . rand(100, 999));

            // Insert user into wp_users
            $userData = [
                'user_login'     => $username,
                'user_email'     => $email,
                'display_name'   => trim($firstName . ' ' . $lastName),
                'user_pass'      => password_hash($password, PASSWORD_BCRYPT),
                'user_registered' => date('Y-m-d H:i:s'),
                'user_status'    => 0
            ];
            
            $this->db->table('wp_users')->insert($userData);
            $user_id = $this->db->insertID();

            if (!$user_id) {
                throw new \Exception('Failed to create user');
            }

            // Insert user capabilities (role)
            $role_meta = 'a:1:{s:' . strlen($role) . ':"' . $role . '";b:1;}';
            $metaData = [
                [
                    'user_id'    => $user_id,
                    'meta_key'   => 'wp_capabilities',
                    'meta_value' => $role_meta
                ],
                [
                    'user_id'    => $user_id,
                    'meta_key'   => 'wp_user_level',
                    'meta_value' => $this->getRoleLevel($role)
                ],
                [
                    'user_id'    => $user_id,
                    'meta_key'   => 'first_name',
                    'meta_value' => $firstName
                ],
                [
                    'user_id'    => $user_id,
                    'meta_key'   => 'last_name',
                    'meta_value' => $lastName
                ]
            ];

            // Add optional metadata
            if (!empty($bio)) {
                $metaData[] = [
                    'user_id'    => $user_id,
                    'meta_key'   => 'description',
                    'meta_value' => $bio
                ];
            }

            if (!empty($mobile)) {
                $metaData[] = [
                    'user_id'    => $user_id,
                    'meta_key'   => 'mobile',
                    'meta_value' => $mobile
                ];
            }

            if ($profileImage) {
                $metaData[] = [
                    'user_id'    => $user_id,
                    'meta_key'   => 'profile_image',
                    'meta_value' => $profileImage
                ];
            }

            $this->db->table('wp_usermeta')->insertBatch($metaData);

            session()->setFlashdata('success', 'User created successfully!');
            return redirect()->to(base_url('admin/author'));

        } catch (\Exception $e) {
            log_message('error', 'User creation error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error creating user: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Get role level for WordPress compatibility
    private function getRoleLevel($role)
    {
        $levels = [
            'subscriber' => '0',
            'contributor' => '1',
            'author' => '2',
            'editor' => '7',
            'administrator' => '10',
            'seo_editor' => '5',
            'seo_manager' => '8'
        ];
        
        return $levels[$role] ?? '0';
    }

    // Edit user
    public function edit($id)
    {
        $author = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
        
        if (!$author) {
            session()->setFlashdata('error', 'User not found');
            return redirect()->to(base_url('admin/author'));
        }
        
        return view('authors_edit', ['author' => $author]);
    }

    // Update user
    public function update($id)
    {
        try {
            $email    = $this->request->getPost('email');
            $name     = $this->request->getPost('display_name');
            $role     = $this->request->getPost('role');
            $password = $this->request->getPost('password');

            // Validate input
            if (empty($email) || empty($name) || empty($role)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required']);
            }

            // Check if user exists
            $existingUser = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$existingUser) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
            }

            // Check for duplicate email (excluding current user)
            $emailCheck = $this->db->table('wp_users')
                ->where('user_email', $email)
                ->where('ID !=', $id)
                ->get()->getRow();
            
            if ($emailCheck) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Email already exists']);
            }

            // Prepare update data
            $updateData = [
                'user_email'   => $email,
                'display_name' => $name,
            ];

            // Update password if provided
            if (!empty($password)) {
                $updateData['user_pass'] = password_hash($password, PASSWORD_BCRYPT);
            }

            // Update user table
            $this->db->table('wp_users')->where('ID', $id)->update($updateData);

            // Update role in wp_usermeta
            $role_meta = 'a:1:{s:' . strlen($role) . ':"' . $role . '";b:1;}';
            
            // Check if capabilities meta exists
            $capExists = $this->db->table('wp_usermeta')
                ->where('user_id', $id)
                ->where('meta_key', 'wp_capabilities')
                ->get()->getRow();

            if ($capExists) {
                // Update existing
                $this->db->table('wp_usermeta')
                    ->where('user_id', $id)
                    ->where('meta_key', 'wp_capabilities')
                    ->update(['meta_value' => $role_meta]);
            } else {
                // Insert new
                $this->db->table('wp_usermeta')->insert([
                    'user_id' => $id,
                    'meta_key' => 'wp_capabilities',
                    'meta_value' => $role_meta
                ]);
            }

            // Update user level
            $userLevel = $this->getRoleLevel($role);
            $levelExists = $this->db->table('wp_usermeta')
                ->where('user_id', $id)
                ->where('meta_key', 'wp_user_level')
                ->get()->getRow();

            if ($levelExists) {
                $this->db->table('wp_usermeta')
                    ->where('user_id', $id)
                    ->where('meta_key', 'wp_user_level')
                    ->update(['meta_value' => $userLevel]);
            } else {
                $this->db->table('wp_usermeta')->insert([
                    'user_id' => $id,
                    'meta_key' => 'wp_user_level',
                    'meta_value' => $userLevel
                ]);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'User updated successfully']);

        } catch (\Exception $e) {
            log_message('error', 'User update error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error updating user: ' . $e->getMessage()]);
        }
    }

    // Delete user
    public function delete($id)
    {
        try {
            // Check if user exists
            $user = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$user) {
                session()->setFlashdata('error', 'User not found');
                return redirect()->to(base_url('admin/author'));
            }

            // Don't allow deletion of current user
            if (session()->get('user_id') == $id) {
                session()->setFlashdata('error', 'You cannot delete your own account');
                return redirect()->to(base_url('admin/author'));
            }

            // Delete user and related data
            $this->db->table('wp_users')->where('ID', $id)->delete();
            $this->db->table('wp_usermeta')->where('user_id', $id)->delete();

            session()->setFlashdata('success', 'User deleted successfully');
            return redirect()->to(base_url('admin/author'));

        } catch (\Exception $e) {
            log_message('error', 'User deletion error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error deleting user');
            return redirect()->to(base_url('admin/author'));
        }
    }

    // View posts by user
    public function viewPosts($id)
    {
        $user = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
        if (!$user) {
            session()->setFlashdata('error', 'User not found');
            return redirect()->to(base_url('admin/author'));
        }

        $posts = $this->db->table('wp_posts')
            ->where('post_author', $id)
            ->where('post_status', 'publish')
            ->orderBy('post_date', 'DESC')
            ->get()->getResult();

        return view('authors_posts', [
            'posts' => $posts,
            'user' => $user
        ]);
    }
}