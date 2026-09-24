<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class HomeApiController extends ResourceController
{
    protected $format = 'json';
    private $db;
    private $cache;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->cache = \Config\Services::cache();
    }
    
    /**
     * Helper function to clean image URLs
     * Removes /admin/ from paths and fixes protocol
     */
    private function cleanImageUrl($url)
    {
        if (empty($url)) {
            return base_url('assets/images/default-thumbnail.jpg');
        }
        
        // Remove /admin/ from path
        $url = str_replace('/admin/wp-content/', '/wp-content/', $url);
        
        // Ensure HTTPS
        $url = str_replace('http://flyppedhindi.com', 'https://flyppedhindi.com', $url);
        
        return $url;
    }
    
    /**
     * GET /api/v1/home
     * Get all homepage data
     */
    public function index()
    {
        try {
            $cacheKey = 'api_homepage_data_v5'; // Changed version to clear old cache
            $cachedData = $this->cache->get($cacheKey);
            
            if ($cachedData !== null) {
                return $this->respond([
                    'success' => true,
                    'data' => $cachedData,
                    'cached' => true
                ]);
            }
            
            $data = [
                'latest_posts' => $this->getPostsWithThumbnails(null, 5),
                'trending_posts' => $this->getPostsWithThumbnails('entertainment', 15),
                'latest_news' => $this->getPostsWithThumbnails(null, 3),
                'sports_posts' => $this->getPostsWithThumbnails('sports', 8),
                'entertainment_tab_posts' => $this->getPostsWithThumbnails('entertainment', 6),
                'crazy_fact_tab_posts' => $this->getPostsWithThumbnails('crazy-facts', 6),
                'relationship_tab_posts' => $this->getPostsWithThumbnails('relationship', 6),
                'lifestyle_tab_posts' => $this->getPostsWithThumbnails('lifestyle', 6),
                'health_fitness_tab_posts' => $this->getPostsWithThumbnails('health-fitness', 6),
                'travel_tab_posts' => $this->getPostsWithThumbnails('travel', 6),
                'politics_tab_posts' => $this->getPostsWithThumbnails('news', 6),
                'sports_tab_posts' => $this->getPostsWithThumbnails('sports', 6),
                'business_tab_posts' => $this->getPostsWithThumbnails('business', 6),
                'tech_tab_posts' => $this->getPostsWithThumbnails('tech', 6),
                'world_tab_posts' => $this->getPostsWithThumbnails('world', 6),
                'categories' => $this->getCategories()
            ];
            
            $this->cache->save($cacheKey, $data, 300);
            
            return $this->respond([
                'success' => true,
                'data' => $data,
                'cached' => false
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API HomeApiController::index() Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/posts
     * GET /api/v1/posts?category=sports&limit=10&page=1
     */
    public function posts()
    {
        try {
            $category = $this->request->getGet('category');
            $limit = min(50, max(1, (int) ($this->request->getGet('limit') ?? 10)));
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            
            // Create unique cache key based on parameters
            $cacheKey = 'api_posts_v2_' . md5(($category ?? 'all') . '_' . $limit . '_' . $offset);
            $cachedData = $this->cache->get($cacheKey);
            
            if ($cachedData !== null) {
                return $this->respond([
                    'success' => true,
                    'data' => $cachedData,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'count' => count($cachedData)
                    ],
                    'cached' => true
                ]);
            }
            
            $posts = $this->getPostsWithThumbnails($category, $limit, $offset);
            
            // Cache for 5 minutes
            $this->cache->save($cacheKey, $posts, 300);
            
            return $this->respond([
                'success' => true,
                'data' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'count' => count($posts)
                ],
                'cached' => false
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API posts Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    
/**
 * GET /api/v1/url?q=your-post-url-slug
 */
public function postByUrl()
{
    try {
        $url = $this->request->getGet('q');
        
        if (empty($url)) {
            return $this->respond([
                'success' => false,
                'message' => 'URL parameter is required',
                'error_code' => 'URL_REQUIRED'
            ], 400);
        }
        
        $post = $this->getPostByUrl($url);
        
        if (!$post) {
            return $this->respond([
                'success' => false,
                'message' => 'Post not found',
                'error_code' => 'POST_NOT_FOUND'
            ], 404);
        }
        
        return $this->respond([
            'success' => true,
            'data' => $post
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'API postByUrl Exception: ' . $e->getMessage());
        return $this->failServerError('Internal server error');
    }
}

/**
 * Helper function to get post by URL
 */
private function getPostByUrl($url)
{
    try {
        $db = \Config\Database::connect();
        
        // Query to get post by URL
        $query = $db->query("
            SELECT 
                p.ID,
                p.post_title,
                p.post_content,
                p.post_excerpt,
                p.post_date,
                p.post_modified,
                p.post_name as url,
                p.post_status,
                GROUP_CONCAT(DISTINCT t.name) as categories,
                (SELECT meta_value FROM wp_postmeta 
                 WHERE post_id = p.ID AND meta_key = '_thumbnail_id' 
                 LIMIT 1) as thumbnail_id
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                AND tt.taxonomy = 'category'
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_name = ?
                AND p.post_type = 'post'
                AND p.post_status = 'publish'
            GROUP BY p.ID
            LIMIT 1
        ", [$url]);
        
        $post = $query->getRowArray();
        
        if (!$post) {
            return null;
        }
        
        // Get thumbnail URL if thumbnail_id exists
        $thumbnailUrl = base_url('assets/images/default-thumbnail.jpg');
        if (!empty($post['thumbnail_id'])) {
            $thumbQuery = $db->query("
                SELECT meta_value as thumbnail_url 
                FROM wp_postmeta 
                WHERE post_id = ? AND meta_key = '_wp_attached_file'
                LIMIT 1
            ", [$post['thumbnail_id']]);
            
            $thumbResult = $thumbQuery->getRowArray();
            if ($thumbResult) {
                //  Clean path - remove any /admin/ prefix
                $uploadPath = str_replace('admin/', '', $thumbResult['thumbnail_url']);
                $thumbnailUrl = $this->cleanImageUrl(base_url('wp-content/uploads/' . $uploadPath));
            }
        }
        
        // Format the response
        return [
            'id' => (int) $post['ID'],
            'title' => $post['post_title'],
            'content' => $post['post_content'],
            'excerpt' => $post['post_excerpt'] ?? '',
            'date' => $post['post_date'],
            'modified' => $post['post_modified'],
            'url' => $post['url'],
            'categories' => $post['categories'] ?? '',
            'thumbnail' => $thumbnailUrl
        ];
        
    } catch (\Exception $e) {
        log_message('error', 'getPostByUrl Exception: ' . $e->getMessage());
        return null;
    }
}
    
    /**
     * GET /api/v1/categories
     */
    public function categories()
    {
        try {
            $cacheKey = 'api_categories_list';
            $cachedData = $this->cache->get($cacheKey);
            
            if ($cachedData !== null) {
                return $this->respond([
                    'success' => true,
                    'data' => $cachedData,
                    'cached' => true
                ]);
            }
            
            $categories = $this->getCategories();
            
            $this->cache->save($cacheKey, $categories, 600);
            
            return $this->respond([
                'success' => true,
                'data' => $categories,
                'cached' => false
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API categories Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/search?q=search+terms&limit=10&page=1
     */
    public function search()
    {
        try {
            $query = $this->request->getGet('q');
            $limit = min(50, max(1, (int) ($this->request->getGet('limit') ?? 10)));
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            
            if (empty($query)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }
            
            $searchTerm = '%' . $query . '%';
            
            $sql = "
                SELECT 
                    p.ID, 
                    p.post_title, 
                    p.post_name, 
                    p.post_date,
                    p.post_content,
                    p.post_excerpt,
                    t.slug AS category_slug,
                    thumb.guid AS thumbnail_url,
                    author.display_name AS author_name,
                    author.ID AS author_id
                FROM wp_posts p
                INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                    AND tt.taxonomy = 'category'
                INNER JOIN wp_terms t ON tt.term_id = t.term_id
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
                LEFT JOIN wp_users author ON p.post_author = author.ID
                WHERE p.post_status = 'publish'
                  AND p.post_type = 'post'
                  AND (p.post_title LIKE ? OR p.post_content LIKE ? OR p.post_excerpt LIKE ?)
                GROUP BY p.ID 
                ORDER BY p.post_date DESC 
                LIMIT ? OFFSET ?
            ";
            
            $query = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
            $posts = $query->getResultArray();
            $processed = [];
            
            foreach ($posts as $post) {
                $cat = strtolower(trim((string)($post['category_slug'] ?? ''), '/'));
                $slug = trim((string)($post['post_name'] ?? ''), '/');
                
                if (empty($cat)) {
                    continue;
                }
                
                //  Clean image URL
                $thumbUrl = $this->cleanImageUrl($post['thumbnail_url'] ?? '');
                $detailUrl = base_url($slug);
                
                $processed[] = [
                    'ID' => (int)($post['ID'] ?? 0),
                    'post_title' => $post['post_title'] ?? '',
                    'post_name' => $slug,
                    'post_date' => $post['post_date'] ?? '',
                    'post_content' => $post['post_content'] ?? '',
                    'post_excerpt' => $post['post_excerpt'] ?? '',
                    'author_name' => $post['author_name'] ?? 'Unknown',
                    'author_id' => $post['author_id'] ?? '',
                    'category_slug' => $cat,
                    'thumbnail_url' => $thumbUrl,
                    'url' => $detailUrl,
                ];
            }
            
            return $this->respond([
                'success' => true,
                'data' => $processed,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'count' => count($processed)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API search Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/suggestions?q=search+term&limit=5
     */
    public function suggestions()
    {
        try {
            $query = $this->request->getGet('q');
            $limit = min(10, max(1, (int) ($this->request->getGet('limit') ?? 5)));
            
            if (empty($query) || strlen($query) < 2) {
                return $this->respond([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $searchTerm = '%' . $query . '%';
            
            $sql = "
                SELECT 
                    p.ID, 
                    p.post_title, 
                    p.post_name,
                    t.slug AS category_slug
                FROM wp_posts p
                INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                    AND tt.taxonomy = 'category'
                INNER JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                  AND p.post_type = 'post'
                  AND p.post_title LIKE ?
                GROUP BY p.ID 
                ORDER BY p.post_date DESC 
                LIMIT ?
            ";
            
            $query = $this->db->query($sql, [$searchTerm, $limit]);
            $posts = $query->getResultArray();
            $suggestions = [];
            
            foreach ($posts as $post) {
                $cat = strtolower(trim((string)($post['category_slug'] ?? ''), '/'));
                $slug = trim((string)($post['post_name'] ?? ''), '/');
                
                if (empty($cat)) {
                    continue;
                }
                
                $suggestions[] = [
                    'title' => $post['post_title'] ?? '',
                    'url' => base_url($slug)
                ];
            }
            
            return $this->respond([
                'success' => true,
                'data' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API suggestions Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/post/{id}
     */
    public function post($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Post ID is required', 400);
            }
            
            $sql = "
                SELECT 
                    p.ID, 
                    p.post_title, 
                    p.post_content,
                    p.post_excerpt,
                    p.post_name, 
                    p.post_date,
                    p.post_modified,
                    t.name AS category_name,
                    t.slug AS category_slug,
                    thumb.guid AS thumbnail_url,
                    author.display_name AS author_name,
                    author.ID AS author_id
                FROM wp_posts p
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                    AND tt.taxonomy = 'category'
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
                LEFT JOIN wp_users author ON p.post_author = author.ID
                WHERE p.ID = ?
                  AND p.post_status = 'publish'
                  AND p.post_type = 'post'
                LIMIT 1
            ";
            
            $post = $this->db->query($sql, [$id])->getRowArray();
            
            if (!$post) {
                return $this->failNotFound('Post not found');
            }
            
            // Get tags
            $tagsSql = "
                SELECT t.name, t.slug
                FROM wp_terms t
                INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = 'post_tag'
                INNER JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ?
            ";
            $tags = $this->db->query($tagsSql, [$id])->getResultArray();
            
            //  Clean thumbnail URL
            $thumbnailUrl = $this->cleanImageUrl($post['thumbnail_url'] ?? '');
            
            $data = [
                'ID' => (int)$post['ID'],
                'post_title' => $post['post_title'],
                'post_content' => $post['post_content'],
                'post_excerpt' => $post['post_excerpt'] ?? '',
                'post_date' => $post['post_date'],
                'post_modified' => $post['post_modified'],
                'author_name' => $post['author_name'] ?? 'Unknown',
                'category' => [
                    'slug' => $post['category_slug'] ?? '',
                    'name' => $post['category_name'] ?? ''
                ],
                'tags' => $tags,
                'thumbnail_url' => $thumbnailUrl,
                'url' => base_url($post['category_slug'] . '/' . $post['post_name'])
            ];
            
            return $this->respond([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API post Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/post/{category}/{slug}
     */
    public function postBySlug($category = null, $slug = null)
    {
        try {
            if (!$category || !$slug) {
                return $this->fail('Category and slug are required', 400);
            }
            
            $sql = "
                SELECT 
                    p.ID, 
                    p.post_title, 
                    p.post_content,
                    p.post_excerpt,
                    p.post_name, 
                    p.post_date,
                    p.post_modified,
                    t.name AS category_name,
                    t.slug AS category_slug,
                    thumb.guid AS thumbnail_url,
                    author.display_name AS author_name,
                    author.ID AS author_id
                FROM wp_posts p
                INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                    AND tt.taxonomy = 'category'
                INNER JOIN wp_terms t ON tt.term_id = t.term_id
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
                LEFT JOIN wp_users author ON p.post_author = author.ID
                WHERE p.post_name = ?
                  AND t.slug = ?
                  AND p.post_status = 'publish'
                  AND p.post_type = 'post'
                LIMIT 1
            ";
            
            $post = $this->db->query($sql, [$slug, $category])->getRowArray();
            
            if (!$post) {
                return $this->failNotFound('Post not found');
            }
            
            // Get tags
            $tagsSql = "
                SELECT t.name, t.slug
                FROM wp_terms t
                INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = 'post_tag'
                INNER JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ?
            ";
            $tags = $this->db->query($tagsSql, [$post['ID']])->getResultArray();
            
            //  Clean thumbnail URL
            $thumbnailUrl = $this->cleanImageUrl($post['thumbnail_url'] ?? '');
            
            $data = [
                'ID' => (int)$post['ID'],
                'post_title' => $post['post_title'],
                'post_content' => $post['post_content'],
                'post_excerpt' => $post['post_excerpt'] ?? '',
                'post_date' => $post['post_date'],
                'post_modified' => $post['post_modified'],
                'author_name' => $post['author_name'] ?? 'Unknown',
                'category' => [
                    'slug' => $category,
                    'name' => $post['category_name'] ?? ''
                ],
                'tags' => $tags,
                'thumbnail_url' => $thumbnailUrl,
                'url' => base_url($category . '/' . $slug)
            ];
            
            return $this->respond([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API postBySlug Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    /**
     * GET /api/v1/related/{id}?limit=5
     */
    public function relatedPosts($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Post ID is required', 400);
            }
            
            $limit = min(20, max(1, (int) ($this->request->getGet('limit') ?? 5)));
            
            // Get category of current post
            $catSql = "
                SELECT t.slug
                FROM wp_terms t
                INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = 'category'
                INNER JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ?
                LIMIT 1
            ";
            $category = $this->db->query($catSql, [$id])->getRow();
            
            if (!$category) {
                return $this->respond([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $posts = $this->getPostsWithThumbnails($category->slug, $limit, 0, $id);
            
            return $this->respond([
                'success' => true,
                'data' => $posts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API relatedPosts Exception: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
    
    // ========== HELPER METHODS ==========
    
    /**
     * Get posts with thumbnails including post_content, post_excerpt, and author_name
     */
    private function getPostsWithThumbnails($category_slug = null, $limit = 10, $offset = 0, $excludeId = null)
    {
        $sql = "
            SELECT 
                p.ID, 
                p.post_title, 
                p.post_name, 
                p.post_date,
                p.post_content,
                p.post_excerpt,
                t.slug AS category_slug,
                thumb.guid AS thumbnail_url,
                author.display_name AS author_name,
                author.ID AS author_id
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                AND tt.taxonomy = 'category'
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
            LEFT JOIN wp_users author ON p.post_author = author.ID
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
        ";
        
        $params = [];
        
        if ($category_slug) {
            $sql .= " AND t.slug = ?";
            $params[] = $category_slug;
        }
        
        if ($excludeId) {
            $sql .= " AND p.ID != ?";
            $params[] = $excludeId;
        }
        
        $sql .= " GROUP BY p.ID ORDER BY p.post_date DESC LIMIT ?";
        $params[] = $limit;
        
        if ($offset > 0) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }
        
        $query = $this->db->query($sql, $params);
        
        if (!$query) {
            log_message('error', 'Query failed for category: ' . ($category_slug ?? 'all'));
            return [];
        }
        
        $posts = $query->getResultArray();
        $processed = [];
        
        foreach ($posts as $post) {
            $cat = strtolower(trim((string)($post['category_slug'] ?? ''), '/'));
            $slug = trim((string)($post['post_name'] ?? ''), '/');
            
            if (empty($cat)) {
                continue;
            }
            
            //  Clean image URL - Remove /admin/ and fix protocol
            $thumbUrl = $this->cleanImageUrl($post['thumbnail_url'] ?? '');
            
            $detailUrl = base_url($slug);
            
            $processed[] = [
                'ID' => (int)($post['ID'] ?? 0),
                'post_title' => $post['post_title'] ?? '',
                'post_name' => $slug,
                'post_date' => $post['post_date'] ?? '',
                'post_content' => $post['post_content'] ?? '',
                'post_excerpt' => $post['post_excerpt'] ?? '',
                'author_name' => $post['author_name'] ?? 'Unknown',
                'author_id' => $post['author_id'] ?? '',
                'category_slug' => $cat,
                'thumbnail_url' => $thumbUrl,
                'url' => $detailUrl,
            ];
        }
        
        return $processed;
    }
    
    /**
     * Get categories with post counts
     */
    private function getCategories()
    {
        $categories_query = $this->db->query("
            SELECT t.name, t.slug, COUNT(DISTINCT p.ID) as post_count
            FROM wp_terms t
            INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = 'category'
            INNER JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN wp_posts p ON tr.object_id = p.ID 
                AND p.post_status = 'publish' 
                AND p.post_type = 'post'
            WHERE t.slug NOT IN (
                'stories', 'week-post', 'weekly-post', 'story', 'latest', 
                'other', 'others', 'anya', 'anyaa', 'misc', 'miscellaneous', 
                'uncategorized'
            )
            AND LOWER(t.name) NOT IN ('other', 'others', 'अन्य', 'अन्‍य', 'misc', 'miscellaneous')
            GROUP BY t.term_id, t.name, t.slug
            HAVING post_count > 0
            ORDER BY post_count DESC
            LIMIT 50
        ");
        
        $categories = $categories_query ? $categories_query->getResultArray() : [];
        
        foreach ($categories as &$cat) {
            $cat['slug'] = strtolower($cat['slug']);
            $cat['post_count'] = (int)$cat['post_count'];
        }
        unset($cat);
        
        return $categories;
    }

public function exportPostsExcel()
{
    try {
        $db = \Config\Database::connect();

        // Fetch all posts with first category slug for URL building
        $posts = $db->query("
            SELECT 
                p.ID,
                p.post_title AS title,
                p.post_name AS slug,
                p.post_status AS status,
                p.post_date AS published_date,
                u.display_name AS author_name,
                GROUP_CONCAT(DISTINCT t.name ORDER BY t.term_id ASC SEPARATOR ', ') AS categories,
                MIN(t.slug) AS first_category_slug
            FROM wp_posts p
            LEFT JOIN wp_users u ON u.ID = p.post_author
            LEFT JOIN wp_term_relationships tr ON tr.object_id = p.ID
            LEFT JOIN wp_term_taxonomy tt 
                ON tt.term_taxonomy_id = tr.term_taxonomy_id 
                AND tt.taxonomy = 'category'
            LEFT JOIN wp_terms t ON t.term_id = tt.term_id
            WHERE p.post_type = 'post'
            AND p.post_status IN ('publish', 'draft', 'pending')
            GROUP BY p.ID
            ORDER BY p.post_date DESC
        ")->getResult();

        // ── Fixed base URL with https:// ──────────────────────────────────
        $baseUrl = 'https://flyppedhindi.com';

        $filename = 'posts_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        // ── Try PhpSpreadsheet first ──────────────────────────────────────
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {

            // Clear any previous output buffer
            if (ob_get_length()) ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0, no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: public');
            header('Expires: 0');

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Posts');

            // ── Header Row ────────────────────────────────────────────────
            $headers = ['#', 'Post ID', 'Title', 'Post Link', 'Categories', 'Author', 'Status', 'Published Date'];
            $headerStyle = [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 12,
                    'name'  => 'Arial',
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D6A4F'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];

            foreach ($headers as $colIndex => $header) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $cell = $colLetter . '1';
                $sheet->setCellValue($cell, $header);
                $sheet->getStyle($cell)->applyFromArray($headerStyle);
                $sheet->getRowDimension(1)->setRowHeight(25);
            }

            // ── Data Rows ─────────────────────────────────────────────────
            foreach ($posts as $i => $post) {
                $row = $i + 2;

                // ── URL without category ──────────────────────────────────
                $postUrl = $baseUrl . '/' . $post->slug;

                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $post->ID);
                $sheet->setCellValue('C' . $row, $post->title);

                // Clickable hyperlink with correct https:// URL
                $sheet->setCellValue('D' . $row, $postUrl);
                $sheet->getCell('D' . $row)->getHyperlink()->setUrl($postUrl);
                $sheet->getStyle('D' . $row)->getFont()
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0563C1'))
                    ->setUnderline(true);

                $sheet->setCellValue('E' . $row, $post->categories ?? 'Uncategorized');
                $sheet->setCellValue('F' . $row, $post->author_name ?? '-');
                $sheet->setCellValue('G' . $row, ucfirst($post->status));
                $sheet->setCellValue('H' . $row, date('d M Y', strtotime($post->published_date)));

                // Alternate row background
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F0F7F4');
                }

                // Center align columns A, B, G, H
                foreach (['A', 'B', 'G', 'H'] as $col) {
                    $sheet->getStyle($col . $row)->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }

            // ── Column Widths ─────────────────────────────────────────────
            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(10);
            $sheet->getColumnDimension('C')->setWidth(55);
            $sheet->getColumnDimension('D')->setWidth(65);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(18);

            // Freeze header row
            $sheet->freezePane('A2');

            // Total posts count at bottom
            $totalRow = count($posts) + 2;
            $sheet->setCellValue('A' . $totalRow, 'Total Posts:');
            $sheet->setCellValue('B' . $totalRow, count($posts));
            $sheet->getStyle('A' . $totalRow . ':B' . $totalRow)
                ->getFont()->setBold(true);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');

        } else {
            // ── CSV Fallback (no composer needed) ─────────────────────────
            if (ob_get_length()) ob_end_clean();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="posts_export_' . date('Y-m-d') . '.csv"');
            header('Cache-Control: max-age=0, no-store, no-cache, must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            $output = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens correctly
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['#', 'Post ID', 'Title', 'Post Link', 'Categories', 'Author', 'Status', 'Published Date']);

            foreach ($posts as $i => $post) {
                // ── URL without category ──────────────────────────────────
                $postUrl = $baseUrl . '/' . $post->slug;

                fputcsv($output, [
                    $i + 1,
                    $post->ID,
                    $post->title,
                    $postUrl,
                    $post->categories ?? 'Uncategorized',
                    $post->author_name ?? '-',
                    ucfirst($post->status),
                    date('d M Y', strtotime($post->published_date)),
                ]);
            }

            fputcsv($output, ['', '', '', '', '', '', 'Total Posts:', count($posts)]);
            fclose($output);
        }

        exit;

    } catch (\Exception $e) {
        log_message('error', 'Export Posts Excel Error: ' . $e->getMessage());
        return $this->failServerError('Export failed: ' . $e->getMessage());
    }
}

}