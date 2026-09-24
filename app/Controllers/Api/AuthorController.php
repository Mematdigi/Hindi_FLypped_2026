<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class AuthorController extends ResourceController
{
    protected $db;
    protected $format = 'json';

    public function __construct()
    {
        $this->db = Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Get all users with their statistics
     * GET /api/v1/authors
     */
    public function index()
    {
        try {
            $builder = $this->db->table('wp_users u');
            $builder->select('u.ID, u.user_login, u.user_email, u.display_name, u.user_registered, COUNT(p.ID) as post_count, m.meta_value as role_meta');
            $builder->join('wp_usermeta m', 'u.ID = m.user_id AND m.meta_key = "wp_capabilities"', 'left');
            $builder->join('wp_posts p', 'u.ID = p.post_author AND p.post_status = "publish"', 'left');
            $builder->groupBy('u.ID, u.user_login, u.user_email, u.display_name, u.user_registered, m.meta_value');
            $builder->orderBy('u.ID', 'DESC');
            
            $authors = $builder->get()->getResult();

            // Extract role from serialized data and get additional meta
            foreach ($authors as $author) {
                $author->role = $this->extractRole($author->role_meta);
                unset($author->role_meta);
                
                // Get additional meta data
                $meta = $this->getUserMeta($author->ID);
                $author->first_name = $meta['first_name'] ?? '';
                $author->last_name = $meta['last_name'] ?? '';
                $author->bio = $meta['description'] ?? '';
                $author->mobile = $meta['mobile'] ?? '';
                $author->profile_image = $meta['profile_image'] ?? null;
                
                if ($author->profile_image) {
                    $author->profile_image_url = base_url('uploads/profile_images/' . $author->profile_image);
                }
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Users retrieved successfully',
                'data' => $authors,
                'total' => count($authors)
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching authors: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user by ID
     * GET /api/v1/authors/{id}
     */
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('User ID is required');
            }

            $author = $this->db->table('wp_users')
                ->where('ID', $id)
                ->get()
                ->getRow();

            if (!$author) {
                return $this->failNotFound('User not found');
            }

            // Get role
            $roleMeta = $this->db->table('wp_usermeta')
                ->where('user_id', $id)
                ->where('meta_key', 'wp_capabilities')
                ->get()
                ->getRow();

            $author->role = $this->extractRole($roleMeta->meta_value ?? '');

            // Get additional meta
            $meta = $this->getUserMeta($id);
            $author->first_name = $meta['first_name'] ?? '';
            $author->last_name = $meta['last_name'] ?? '';
            $author->bio = $meta['description'] ?? '';
            $author->mobile = $meta['mobile'] ?? '';
            $author->profile_image = $meta['profile_image'] ?? null;
            
            if ($author->profile_image) {
                $author->profile_image_url = base_url('uploads/profile_images/' . $author->profile_image);
            }

            // Get post count
            $postCount = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'publish')
                ->countAllResults();

            $author->post_count = $postCount;

            return $this->respond([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => $author
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching author: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new user
     * POST /api/v1/authors
     */
    public function create()
    {
        try {
            // Get JSON input
            $json = $this->request->getJSON(true);
            
            // Validate required fields
            $validation = \Config\Services::validation();
            $validation->setRules([
                'first_name' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email|is_unique[wp_users.user_email]',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[subscriber,contributor,author,editor,administrator,seo_editor,seo_manager]'
            ]);

            if (!$validation->run($json)) {
                return $this->failValidationErrors($validation->getErrors());
            }

            $firstName = $json['first_name'];
            $lastName  = $json['last_name'] ?? '';
            $email     = $json['email'];
            $password  = $json['password'];
            $role      = $json['role'];
            $bio       = $json['bio'] ?? '';
            $mobile    = $json['mobile'] ?? '';

            // Handle profile image upload (if sent as base64)
            $profileImage = null;
            if (!empty($json['profile_image_base64'])) {
                $profileImage = $this->saveBase64Image($json['profile_image_base64']);
            }

            // Create unique username
            $username = strtolower($firstName . $lastName . rand(100, 999));

            // Insert user into wp_users
            $userData = [
                'user_login'      => $username,
                'user_email'      => $email,
                'display_name'    => trim($firstName . ' ' . $lastName),
                'user_pass'       => password_hash($password, PASSWORD_BCRYPT),
                'user_registered' => date('Y-m-d H:i:s'),
                'user_status'     => 0
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

            // Get created user data
            $newUser = $this->db->table('wp_users')->where('ID', $user_id)->get()->getRow();
            $newUser->role = $role;
            $newUser->first_name = $firstName;
            $newUser->last_name = $lastName;
            $newUser->bio = $bio;
            $newUser->mobile = $mobile;
            $newUser->profile_image = $profileImage;
            
            if ($profileImage) {
                $newUser->profile_image_url = base_url('uploads/profile_images/' . $profileImage);
            }

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $newUser
            ]);

        } catch (\Exception $e) {
            log_message('error', 'User creation error: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     * PUT/PATCH /api/v1/authors/{id}
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('User ID is required');
            }

            // Get JSON input
            $json = $this->request->getJSON(true);

            // Check if user exists
            $existingUser = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$existingUser) {
                return $this->failNotFound('User not found');
            }

            // Validate input
            $validation = \Config\Services::validation();
            $rules = [
                'email' => 'required|valid_email',
                'display_name' => 'required|min_length[2]'
            ];

            if (!empty($json['password'])) {
                $rules['password'] = 'min_length[6]';
            }

            $validation->setRules($rules);

            if (!$validation->run($json)) {
                return $this->failValidationErrors($validation->getErrors());
            }

            // Check for duplicate email (excluding current user)
            $emailCheck = $this->db->table('wp_users')
                ->where('user_email', $json['email'])
                ->where('ID !=', $id)
                ->get()->getRow();
            
            if ($emailCheck) {
                return $this->failValidationError('Email already exists');
            }

            // Prepare update data
            $updateData = [
                'user_email'   => $json['email'],
                'display_name' => $json['display_name'],
            ];

            // Update password if provided
            if (!empty($json['password'])) {
                $updateData['user_pass'] = password_hash($json['password'], PASSWORD_BCRYPT);
            }

            // Update user table
            $this->db->table('wp_users')->where('ID', $id)->update($updateData);

            // Update role if provided
            if (!empty($json['role'])) {
                $role = $json['role'];
                $role_meta = 'a:1:{s:' . strlen($role) . ':"' . $role . '";b:1;}';
                
                $capExists = $this->db->table('wp_usermeta')
                    ->where('user_id', $id)
                    ->where('meta_key', 'wp_capabilities')
                    ->get()->getRow();

                if ($capExists) {
                    $this->db->table('wp_usermeta')
                        ->where('user_id', $id)
                        ->where('meta_key', 'wp_capabilities')
                        ->update(['meta_value' => $role_meta]);
                } else {
                    $this->db->table('wp_usermeta')->insert([
                        'user_id' => $id,
                        'meta_key' => 'wp_capabilities',
                        'meta_value' => $role_meta
                    ]);
                }

                // Update user level
                $userLevel = $this->getRoleLevel($role);
                $this->updateOrInsertMeta($id, 'wp_user_level', $userLevel);
            }

            // Update other meta fields if provided
            if (isset($json['first_name'])) {
                $this->updateOrInsertMeta($id, 'first_name', $json['first_name']);
            }
            if (isset($json['last_name'])) {
                $this->updateOrInsertMeta($id, 'last_name', $json['last_name']);
            }
            if (isset($json['bio'])) {
                $this->updateOrInsertMeta($id, 'description', $json['bio']);
            }
            if (isset($json['mobile'])) {
                $this->updateOrInsertMeta($id, 'mobile', $json['mobile']);
            }

            // Handle profile image update
            if (!empty($json['profile_image_base64'])) {
                $profileImage = $this->saveBase64Image($json['profile_image_base64']);
                if ($profileImage) {
                    // Delete old image if exists
                    $oldImage = $this->getUserMeta($id)['profile_image'] ?? null;
                    if ($oldImage) {
                        $oldPath = FCPATH . 'uploads/profile_images/' . $oldImage;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $this->updateOrInsertMeta($id, 'profile_image', $profileImage);
                }
            }

            // Get updated user data
            $updatedUser = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            $meta = $this->getUserMeta($id);
            $roleMeta = $this->db->table('wp_usermeta')
                ->where('user_id', $id)
                ->where('meta_key', 'wp_capabilities')
                ->get()->getRow();

            $updatedUser->role = $this->extractRole($roleMeta->meta_value ?? '');
            $updatedUser->first_name = $meta['first_name'] ?? '';
            $updatedUser->last_name = $meta['last_name'] ?? '';
            $updatedUser->bio = $meta['description'] ?? '';
            $updatedUser->mobile = $meta['mobile'] ?? '';
            $updatedUser->profile_image = $meta['profile_image'] ?? null;
            
            if ($updatedUser->profile_image) {
                $updatedUser->profile_image_url = base_url('uploads/profile_images/' . $updatedUser->profile_image);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $updatedUser
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'User update error: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     * DELETE /api/v1/authors/{id}
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('User ID is required');
            }

            // Check if user exists
            $user = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Don't allow deletion of current user
            if (session()->get('user_id') == $id) {
                return $this->failForbidden('You cannot delete your own account');
            }

            // Get profile image before deletion
            $meta = $this->getUserMeta($id);
            $profileImage = $meta['profile_image'] ?? null;

            // Delete user and related data
            $this->db->table('wp_users')->where('ID', $id)->delete();
            $this->db->table('wp_usermeta')->where('user_id', $id)->delete();

            // Delete profile image file if exists
            if ($profileImage) {
                $imagePath = FCPATH . 'uploads/profile_images/' . $profileImage;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'User deletion error: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user posts
     * GET /api/v1/authors/{id}/posts
     */
    public function getUserPosts($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('User ID is required');
            }

            $user = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Get pagination parameters
            $page = $this->request->getGet('page') ?? 1;
            $limit = $this->request->getGet('limit') ?? 20;
            $offset = ($page - 1) * $limit;

            // Get posts with pagination
            $builder = $this->db->table('wp_posts');
            $builder->where('post_author', $id);
            $builder->where('post_status', 'publish');
            $builder->orderBy('post_date', 'DESC');
            $builder->limit($limit, $offset);
            
            $posts = $builder->get()->getResult();

            // Get total count
            $totalPosts = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'publish')
                ->countAllResults();

            return $this->respond([
                'status' => 'success',
                'message' => 'Posts retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->ID,
                        'name' => $user->display_name,
                        'email' => $user->user_email
                    ],
                    'posts' => $posts,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'per_page' => (int)$limit,
                        'total' => $totalPosts,
                        'total_pages' => ceil($totalPosts / $limit)
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching user posts: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Failed to retrieve posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     * GET /api/v1/authors/{id}/stats
     */
    public function getUserStats($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('User ID is required');
            }

            $user = $this->db->table('wp_users')->where('ID', $id)->get()->getRow();
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Total posts
            $totalPosts = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'publish')
                ->countAllResults();

            // Draft posts
            $draftPosts = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'draft')
                ->countAllResults();

            // Posts this month
            $postsThisMonth = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'publish')
                ->where('MONTH(post_date)', date('m'))
                ->where('YEAR(post_date)', date('Y'))
                ->countAllResults();

            // Latest post
            $latestPost = $this->db->table('wp_posts')
                ->where('post_author', $id)
                ->where('post_status', 'publish')
                ->orderBy('post_date', 'DESC')
                ->limit(1)
                ->get()->getRow();

            // Posts by month (last 6 months)
            $postsByMonth = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i month"));
                $count = $this->db->table('wp_posts')
                    ->where('post_author', $id)
                    ->where('post_status', 'publish')
                    ->where('DATE_FORMAT(post_date, "%Y-%m")', $month)
                    ->countAllResults();
                
                $postsByMonth[] = [
                    'month' => date('M Y', strtotime($month . '-01')),
                    'count' => $count
                ];
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Statistics retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->ID,
                        'name' => $user->display_name,
                        'email' => $user->user_email
                    ],
                    'stats' => [
                        'total_posts' => $totalPosts,
                        'draft_posts' => $draftPosts,
                        'posts_this_month' => $postsThisMonth,
                        'latest_post' => $latestPost,
                        'posts_by_month' => $postsByMonth
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching user stats: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users
     * GET /api/v1/authors/search
     */
    public function search()
    {
        try {
            $searchTerm = $this->request->getGet('q');
            $role = $this->request->getGet('role');

            if (empty($searchTerm) && empty($role)) {
                return $this->failValidationError('Search term or role is required');
            }

            $builder = $this->db->table('wp_users u');
            $builder->select('u.ID, u.user_login, u.user_email, u.display_name, u.user_registered, COUNT(p.ID) as post_count, m.meta_value as role_meta');
            $builder->join('wp_usermeta m', 'u.ID = m.user_id AND m.meta_key = "wp_capabilities"', 'left');
            $builder->join('wp_posts p', 'u.ID = p.post_author AND p.post_status = "publish"', 'left');

            if (!empty($searchTerm)) {
                $builder->groupStart();
                $builder->like('u.display_name', $searchTerm);
                $builder->orLike('u.user_email', $searchTerm);
                $builder->orLike('u.user_login', $searchTerm);
                $builder->groupEnd();
            }

            $builder->groupBy('u.ID, u.user_login, u.user_email, u.display_name, u.user_registered, m.meta_value');
            $results = $builder->get()->getResult();

            // Filter by role if specified
            if (!empty($role)) {
                $results = array_filter($results, function($user) use ($role) {
                    $userRole = $this->extractRole($user->role_meta);
                    return $userRole === $role;
                });
                $results = array_values($results); // Re-index array
            }

            // Add meta data
            foreach ($results as $user) {
                $user->role = $this->extractRole($user->role_meta);
                unset($user->role_meta);
                
                $meta = $this->getUserMeta($user->ID);
                $user->first_name = $meta['first_name'] ?? '';
                $user->last_name = $meta['last_name'] ?? '';
                $user->profile_image = $meta['profile_image'] ?? null;
                
                if ($user->profile_image) {
                    $user->profile_image_url = base_url('uploads/profile_images/' . $user->profile_image);
                }
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Search completed successfully',
                'data' => $results,
                'total' => count($results)
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Error searching users: ' . $e->getMessage());
            return $this->fail([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========== HELPER METHODS ==========

    /**
     * Get user meta data
     */
    private function getUserMeta($userId)
    {
        $metaRows = $this->db->table('wp_usermeta')
            ->where('user_id', $userId)
            ->get()->getResult();

        $meta = [];
        foreach ($metaRows as $row) {
            $meta[$row->meta_key] = $row->meta_value;
        }

        return $meta;
    }

    /**
     * Extract role from WordPress serialized capabilities
     */
    private function extractRole($roleMeta)
    {
        if (empty($roleMeta)) {
            return 'subscriber';
        }

        // Unserialize WordPress capabilities
        $capabilities = @unserialize($roleMeta);
        if ($capabilities && is_array($capabilities)) {
            return array_key_first($capabilities);
        }

        return 'subscriber';
    }

    /**
     * Get role level for WordPress compatibility
     */
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

    /**
     * Update or insert user meta
     */
    private function updateOrInsertMeta($userId, $metaKey, $metaValue)
    {
        $exists = $this->db->table('wp_usermeta')
            ->where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->get()->getRow();

        if ($exists) {
            $this->db->table('wp_usermeta')
                ->where('user_id', $userId)
                ->where('meta_key', $metaKey)
                ->update(['meta_value' => $metaValue]);
        } else {
            $this->db->table('wp_usermeta')->insert([
                'user_id' => $userId,
                'meta_key' => $metaKey,
                'meta_value' => $metaValue
            ]);
        }
    }

    /**
     * Save base64 image
     */
    private function saveBase64Image($base64String)
    {
        try {
            // Remove data URI prefix if present
            if (strpos($base64String, 'data:image') === 0) {
                $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            }

            $imageData = base64_decode($base64String);
            if ($imageData === false) {
                return null;
            }

            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/profile_images/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $filename = uniqid('profile_') . '.jpg';
            $filepath = $uploadPath . $filename;

            // Save file
            if (file_put_contents($filepath, $imageData)) {
                return $filename;
            }

            return null;

        } catch (\Exception $e) {
            log_message('error', 'Error saving base64 image: ' . $e->getMessage());
            return null;
        }
    }
}