<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class AuthorProfileController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Show all authors page
     */
    public function index()
    {
        try {
            $builder = $this->db->table('wp_users u');
            $builder->select('u.ID, u.user_login, u.user_email, u.display_name, COUNT(p.ID) as post_count, m.meta_value as role');
            $builder->join('wp_usermeta m', 'u.ID = m.user_id');
            $builder->join('wp_posts p', 'u.ID = p.post_author AND p.post_status = "publish"', 'left');
            $builder->where('m.meta_key', 'wp_capabilities');
            $builder->groupBy('u.ID');
            $builder->having('post_count >', 0);
            $authors = $builder->get()->getResult();

            foreach ($authors as $author) {
                $roleArr = @unserialize($author->role);
                $author->role_display = (is_array($roleArr)) ? ucfirst(key($roleArr)) : 'Author';
            }

            //  Unified Footer Data
            $footerData = $this->getFooterData();

            return view('authors_list', [
                'authors'                    => $authors,
                'latest_footer_blogs'        => $footerData['latest_footer_blogs'],
                'categories_with_post_count' => $footerData['categories_with_post_count'],
                'categories_column_1'        => $footerData['categories_column_1'],
                'categories_column_2'        => $footerData['categories_column_2'],
                'other_count'                => $footerData['other_count'],
            ]);

        } catch (\Exception $e) {
            log_message('error', 'AuthorProfileController::index error: ' . $e->getMessage());
            return redirect()->to(base_url('/'));
        }
    }

    /**
     * Show individual author profile
     */
    public function profile($username)
    {
        try {
            $decodedUsername = urldecode($username);
            $cleanUsername = trim($decodedUsername);
            $currentPage = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
            $currentPage = max(1, $currentPage);

            $author = $this->getAuthorByUsername($cleanUsername);
            if (!$author) {
                return redirect()->to(base_url('authors'))->with('error', 'Author not found.');
            }

            $metaData   = $this->getAuthorMeta($author->ID);
            $totalPosts = $this->getAuthorPostsCount($author->ID);
            $perPage    = 12;
            $posts      = $this->getAuthorPosts($author->ID, $currentPage, $perPage);
            $role       = $this->getAuthorRole($author->ID);
            $stats      = $this->getAuthorStats($author->ID);
            $totalPages = $totalPosts > 0 ? ceil($totalPosts / $perPage) : 1;

            //  Unified Footer Data
            $footerData = $this->getFooterData();

            return view('author_profile', [
                'author'                     => $author,
                'meta_data'                  => $metaData,
                'posts'                      => $posts,
                'total_posts'                => $totalPosts,
                'current_page'               => $currentPage,
                'per_page'                   => $perPage,
                'total_pages'                => $totalPages,
                'role'                       => $role,
                'stats'                      => $stats,
                'profile_image'              => (!empty($metaData['profile_image']) && $metaData['profile_image'] !== 'default-avatar.jpg')
                                                ? $metaData['profile_image'] 
                                                : 'default-avatar.jpg',
                'latest_footer_blogs'        => $footerData['latest_footer_blogs'],
                'categories_with_post_count' => $footerData['categories_with_post_count'],
                'categories_column_1'        => $footerData['categories_column_1'],
                'categories_column_2'        => $footerData['categories_column_2'],
                'other_count'                => $footerData['other_count'],
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'AuthorProfileController::profile error: ' . $e->getMessage());
            return redirect()->to(base_url('authors'))->with('error', 'Error loading author profile.');
        }
    }

    // ==================================================================
    //  Unified Footer Data (Used Across Pages)
    // ==================================================================
    private function getFooterData()
    {
        $db = $this->db;

        //  Latest 3 Blogs
        $latest_footer_blogs_query = $db->query("
            SELECT DISTINCT p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value AS thumbnail_id
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug NOT IN ('stories','week-post','weekly-post','story')
            ORDER BY p.post_date DESC
            LIMIT 3;
        ");
        $latest_footer_blogs = $latest_footer_blogs_query->getResultArray();

        foreach ($latest_footer_blogs as &$blog) {
            $catRow = $db->query("
                SELECT t.slug FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ? AND tt.taxonomy = 'category' LIMIT 1
            ", [$blog['ID']])->getRow();

            $category_slug = $catRow->slug ?? 'uncategorized';
            $blog['category_slug']   = $category_slug;
            
            //  FIX: Removed category_slug from URL to prevent 404s
            $blog['blog_detail_url'] = base_url($blog['post_name']);

            if (!empty($blog['thumbnail_id'])) {
                $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$blog['thumbnail_id']])->getRow();
                $blog['thumbnail_url'] = $thumb->guid ?? base_url('public/assets/images/default-thumbnail.jpg');
            } else {
                $blog['thumbnail_url'] = base_url('public/assets/images/default-thumbnail.jpg');
            }

            $blog['formatted_date'] = date('F d, Y', strtotime($blog['post_date']));
        }
        unset($blog);

        //  Footer Categories with Post Counts
        $categories_query = $db->query("
            SELECT t.name, t.slug, COUNT(p.ID) AS post_count
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_posts p ON tr.object_id = p.ID
            WHERE p.post_status = 'publish' AND tt.taxonomy = 'category'
            GROUP BY t.term_id
            ORDER BY post_count DESC;
        ");
        $categories_with_post_count = $categories_query->getResultArray();

        // Exclude unwanted categories
        $excluded_categories = ['stories', 'week post', 'weekly post', 'story', 'latest', 'other'];
        $categories_with_post_count = array_filter($categories_with_post_count, function ($cat) use ($excluded_categories) {
            $name = strtolower(trim($cat['name'] ?? ''));
            $slug = strtolower(trim($cat['slug'] ?? ''));
            foreach ($excluded_categories as $excluded) {
                if (strpos($name, $excluded) !== false || strpos($slug, $excluded) !== false) {
                    return false;
                }
            }
            return true;
        });
        $categories_with_post_count = array_values($categories_with_post_count);

        // Normalize Tech & Gadgets
        foreach ($categories_with_post_count as &$cat) {
            if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                $cat['name'] = 'Tech & Gadgets';
                $cat['slug'] = 'tech-gadgets';
            }
        }
        unset($cat);

        // Split into columns
        $total_categories   = count($categories_with_post_count);
        $half               = ceil($total_categories / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        //  “Other” posts count
        $other_count_row = $db->query("
            SELECT COUNT(DISTINCT p.ID) AS total
            FROM wp_posts p
            JOIN wp_term_relationships tr ON p.ID = tr.object_id
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category'
            JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
              AND (
                    LOWER(t.slug) IN ('other','others','anya','anyaa','misc','miscellaneous')
                    OR t.name IN ('अन्य','अन्‍य')
                  )
        ")->getRow();
        $other_count = (int) ($other_count_row->total ?? 0);

        return [
            'latest_footer_blogs'        => $latest_footer_blogs,
            'categories_with_post_count' => $categories_with_post_count,
            'categories_column_1'        => $categories_column_1,
            'categories_column_2'        => $categories_column_2,
            'other_count'                => $other_count,
        ];
    }

    // ==================================================================
    //  Helper Methods
    // ==================================================================

    private function getAuthorByUsername($username)
    {
        $author = $this->db->table('wp_users')->where('user_login', $username)->get()->getRow();
        if (!$author) {
            $author = $this->db->table('wp_users')->where('display_name', $username)->get()->getRow();
        }
        if (!$author && is_numeric($username)) {
            $author = $this->db->table('wp_users')->where('ID', (int)$username)->get()->getRow();
        }
        return $author;
    }

    private function getAuthorMeta($user_id)
    {
        $meta = $this->db->table('wp_usermeta')
            ->whereIn('meta_key', ['first_name', 'last_name', 'description', 'profile_image', 'mobile'])
            ->where('user_id', $user_id)
            ->get()
            ->getResultArray();

        $metaData = [
            'first_name'    => '',
            'last_name'     => '',
            'description'   => '',
            'profile_image' => 'default-avatar.jpg',
            'mobile'        => ''
        ];

        foreach ($meta as $m) {
            $metaData[$m['meta_key']] = $m['meta_value'];
        }

        return $metaData;
    }

    private function getAuthorPosts($author_id, $page = 1, $perPage = 12)
    {
        $offset = ($page - 1) * $perPage;
        $posts = $this->db->table('wp_posts p')
            ->select('p.ID, p.post_title, p.post_name, p.post_date, p.post_excerpt, p.post_content')
            ->where('p.post_author', $author_id)
            ->where('p.post_status', 'publish')
            ->where('p.post_type', 'post')
            ->orderBy('p.post_date', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        foreach ($posts as &$post) {
            $category = $this->getPostPrimaryCategory($post['ID']);
            $categorySlug = $category['slug'] ?? 'uncategorized';
            
            //  FIX: Removed categorySlug from this URL to prevent 404
            $post['blog_detail_url'] = base_url($post['post_name']);
            
            $post['thumbnail_url']   = $this->getPostThumbnail($post['ID']);
            $post['categories']      = $category['name'] ?? 'General';
            $post['post_excerpt']    = $post['post_excerpt'] ?: substr(strip_tags($post['post_content']), 0, 120);
        }
        return $posts;
    }

    private function getPostPrimaryCategory($post_id)
    {
        $category = $this->db->table('wp_terms t')
            ->select('t.term_id, t.name, t.slug')
            ->join('wp_term_taxonomy tt', 't.term_id = tt.term_id')
            ->join('wp_term_relationships tr', 'tt.term_taxonomy_id = tr.term_taxonomy_id')
            ->where('tt.taxonomy', 'category')
            ->where('tr.object_id', $post_id)
            ->orderBy('t.term_id', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $category ?: ['name' => 'General', 'slug' => 'uncategorized'];
    }

    private function getPostThumbnail($post_id)
    {
        $thumbnail_id = $this->db->table('wp_postmeta')
            ->select('meta_value')
            ->where('post_id', $post_id)
            ->where('meta_key', '_thumbnail_id')
            ->get()
            ->getRow();

        if ($thumbnail_id && !empty($thumbnail_id->meta_value)) {
            $image = $this->db->table('wp_posts')
                ->select('guid')
                ->where('ID', $thumbnail_id->meta_value)
                ->get()
                ->getRow();
            return $image->guid ?? base_url('public/assets/images/default-thumbnail.jpg');
        }
        return base_url('public/assets/images/default-thumbnail.jpg');
    }

    private function getAuthorPostsCount($author_id)
    {
        return $this->db->table('wp_posts')
            ->where('post_author', $author_id)
            ->where('post_status', 'publish')
            ->where('post_type', 'post')
            ->countAllResults();
    }

    private function getAuthorRole($user_id)
    {
        $roleData = $this->db->table('wp_usermeta')
            ->where('user_id', $user_id)
            ->where('meta_key', 'wp_capabilities')
            ->get()
            ->getRow();
        if ($roleData && !empty($roleData->meta_value)) {
            $roleArr = @unserialize($roleData->meta_value);
            if (is_array($roleArr) && !empty($roleArr)) {
                return ucfirst(key($roleArr));
            }
        }
        return 'Author';
    }

    private function getAuthorStats($author_id)
    {
        $totalPosts = $this->getAuthorPostsCount($author_id);
        $totalViews = $totalPosts * rand(50, 500);
        $followers  = rand(100, 10000);
        return [
            'total_posts' => $totalPosts,
            'total_views' => $totalViews,
            'followers'   => $followers
        ];
    }
}