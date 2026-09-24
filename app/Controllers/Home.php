<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends BaseController
{
    public function index(): string
    {
        // ===== REDIS CACHE CHECK =====
        try {
            $redis = new \Redis();
            if ($redis->connect('127.0.0.1', 6379)) {
                $cachedHTML = $redis->get('homepage_html_v1');
                if ($cachedHTML) {
                    return $cachedHTML;
                }
            }
        } catch (\Exception $e) {
            // Cache failed, continue normally
            log_message('error', 'Redis cache read error: ' . $e->getMessage());
        }
        // ===== END CACHE CHECK =====
        
        try {
            $db = \Config\Database::connect();

            // ===== Latest Posts (Slider) =====
            $query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id, 
                       t.slug AS category_slug
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish' AND tt.taxonomy = 'category'
                ORDER BY p.post_date DESC
                LIMIT 10;
            ");
            $latest_posts = $this->processPosts($db, $query->getResultArray(), 5);

            // ===== Trending Entertainment (15) =====
            $entertainment_query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id, 
                       t.slug AS category_slug
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                AND t.slug = 'entertainment'
                GROUP BY p.ID
                ORDER BY p.post_date DESC
                LIMIT 6;
            ");
            $trending_posts = $this->processPosts($db, $entertainment_query->getResultArray());

            // ===== Latest News (3 unique) =====
            $latest_news_query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id, 
                       t.slug AS category_slug, p.post_date
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                GROUP BY p.ID
                ORDER BY p.post_date DESC
                LIMIT 3;
            ");
            $latest_news = $this->processPosts($db, $latest_news_query->getResultArray(), 3);

            // ===== Sports News (8) =====
            $sports_news_query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id, 
                       t.slug AS category_slug, p.post_date
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                AND t.slug = 'sports'
                GROUP BY p.ID
                ORDER BY p.post_date DESC
                LIMIT 3;
            ");
            $sports_posts_with_thumbnails = $this->processPosts($db, $sports_news_query->getResultArray());

            // ===== Tab Sections =====
            $entertainment_tab_posts   = $this->fetchCategoryPosts($db, 'entertainment', 6);
            $crazy_fact_tab_posts      = $this->fetchCategoryPosts($db, 'crazy-facts', 6);
            $relationship_tab_posts    = $this->fetchCategoryPosts($db, 'relationship', 6);
            $lifestyle_tab_posts       = $this->fetchCategoryPosts($db, 'lifestyle', 6);
            $health_fitness_tab_posts  = $this->fetchCategoryPosts($db, 'health-fitness', 6);

            // ===== Global News Tabs =====
            $politics_tab_posts = $this->fetchCategoryPosts($db, 'news', 6);
            $sports_tab_posts   = $this->fetchCategoryPosts($db, 'sports', 6);
            $business_tab_posts = $this->fetchCategoryPosts($db, 'business', 6);
            $tech_tab_posts     = $this->fetchCategoryPosts($db, 'technology', 6);  //  CORRECT SLUG
            $world_tab_posts    = $this->fetchCategoryPosts($db, 'world', 6);
            
            //  ADDED NEW CATEGORIES
            $education_tab_posts       = $this->fetchCategoryPosts($db, 'education', 6); 
            $spiritual_tab_posts       = $this->fetchCategoryPosts($db, 'spiritual', 6);
            $travel_tab_posts   = $this->fetchCategoryPosts($db, 'travel', 6);


            // ===== Footer Blogs =====
            $footer_blog_query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, p.post_date, 
                       t.name AS category_name, t.slug AS category_slug
                FROM wp_posts p
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                GROUP BY p.ID
                ORDER BY p.post_date DESC
                LIMIT 2;
            ");
            $latest_footer_blogs = $this->processPosts($db, $footer_blog_query->getResultArray(), 3);

            // ===== Categories with post counts ======
            $categories_query = $db->query("
                SELECT t.name, t.slug, COUNT(p.ID) as post_count
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                JOIN wp_posts p ON tr.object_id = p.ID
                WHERE p.post_status = 'publish' AND tt.taxonomy = 'category'
                GROUP BY t.term_id
                ORDER BY post_count DESC;
            ");
            $categories_with_post_count = $categories_query->getResultArray();

            // тЬЕ FILTER OUT unwanted categories (Stories, Week Post, etc.)
            $excluded_categories = ['stories', 'week post', 'weekly post', 'story', 'latest', 'other'];

            $categories_with_post_count = array_filter($categories_with_post_count, function($cat) use ($excluded_categories) {
                $cat_name_lower = strtolower(trim($cat['name']));
                $cat_slug_lower = strtolower(trim($cat['slug']));
                
                foreach ($excluded_categories as $excluded) {
                    if (stripos($cat_name_lower, $excluded) !== false || stripos($cat_slug_lower, $excluded) !== false) {
                        return false;
                    }
                }
                return true;
            });

            // Re-index array after filtering
            $categories_with_post_count = array_values($categories_with_post_count);

            // тЬЕ Normalize Tech & Gadgets category
            foreach ($categories_with_post_count as &$cat) {
                if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                    $cat['name'] = 'Tech & Gadgets';
                    $cat['slug'] = 'tech-gadgets';
                }
            }

            // тЬЕ Split categories into two columns
            $total_categories   = count($categories_with_post_count);
            $half               = ceil($total_categories / 2);
            $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
            $categories_column_2 = array_slice($categories_with_post_count, $half);

            // ===== ЁЯФв Count for тАЬOther / рдЕрдиреНрдптАЭ (footer row added manually in view) =====
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
                        OR t.name IN ('рдЕрдиреНрдп','рдЕрдиреНтАНрдп')
                      )
            ")->getRow();
            $other_count = (int) ($other_count_row->total ?? 0);

            // Generate HTML
            $html = view('index', [
                'latest_posts'                 => $latest_posts,
                'trending_posts'               => $trending_posts,
                'latest_news'                  => $latest_news,
                'sports_posts_with_thumbnails' => $sports_posts_with_thumbnails,
                'entertainment_tab_posts'      => $entertainment_tab_posts,
                'crazy_fact_tab_posts'         => $crazy_fact_tab_posts,
                'relationship_tab_posts'       => $relationship_tab_posts,
                'lifestyle_tab_posts'          => $lifestyle_tab_posts,
                'health_fitness_tab_posts'     => $health_fitness_tab_posts,
                'education_tab_posts'          => $education_tab_posts, 
                'spiritual_tab_posts'       => $spiritual_tab_posts,
                'travel_tab_posts'          => $travel_tab_posts,
                'politics_tab_posts'           => $politics_tab_posts,
                'sports_tab_posts'             => $sports_tab_posts,
                'business_tab_posts'           => $business_tab_posts,
                'tech_tab_posts'               => $tech_tab_posts,
                'world_tab_posts'              => $world_tab_posts,
                'latest_footer_blogs'          => $latest_footer_blogs,
                'categories_column_1'          => $categories_column_1,  // тЬЕ First column
                'categories_column_2'          => $categories_column_2,  // тЬЕ Second column
                'other_count'                  => $other_count,          // тЬЕ <- added
            ]);

            // ===== SAVE TO REDIS CACHE =====
            try {
                $redis = new \Redis();
                if ($redis->connect('127.0.0.1', 6379)) {
                    // Cache for 5 minutes (300 seconds)
                    $redis->setex('homepage_html_v1', 300, $html);
                }
            } catch (\Exception $e) {
                log_message('error', 'Redis cache save error: ' . $e->getMessage());
            }
            // ===== END CACHE SAVE =====
            
            return $html;

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return "An error occurred: " . $e->getMessage();
        }
    }
    
    private function getFooterData($db): array
    {
        // Latest 5 -> weтАЩll show top 3 in UI
        $footer_blog_query = $db->query("
            SELECT p.ID, p.post_title, p.post_name, p.post_date, 
                   t.name AS category_name, t.slug AS category_slug,
                   pm.meta_value AS thumbnail_id
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish' AND p.post_type = 'post'
            GROUP BY p.ID
            ORDER BY p.post_date DESC
            LIMIT 3;
        ");
        $latest_footer_blogs = $this->processPosts($db, $footer_blog_query->getResultArray(), 3);

        // Categories with counts
        $categories_query = $db->query("
            SELECT t.name, t.slug, COUNT(p.ID) as post_count
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_posts p ON tr.object_id = p.ID
            WHERE p.post_status = 'publish' AND p.post_type = 'post' AND tt.taxonomy = 'category'
            GROUP BY t.term_id
            HAVING post_count > 0
            ORDER BY post_count DESC;
        ");
        $categories_with_post_count = $categories_query->getResultArray();

        // Exclusions + normalize
        $excluded = ['stories', 'week post', 'weekly post', 'story', 'latest', 'other'];
        $categories_with_post_count = array_values(array_filter($categories_with_post_count, function($cat) use ($excluded) {
            $n = strtolower(trim($cat['name'] ?? ''));
            $s = strtolower(trim($cat['slug'] ?? ''));
            foreach ($excluded as $e) {
                if (stripos($n, $e) !== false || stripos($s, $e) !== false) return false;
            }
            return true;
        }));
        foreach ($categories_with_post_count as &$cat) {
            if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                $cat['name'] = 'Tech & Gadgets';
                $cat['slug'] = 'tech-gadgets';
            }
        }

        // Split into two columns (if a page needs it)
        $total = count($categories_with_post_count);
        $half  = (int) ceil($total / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        return [
            'latest_footer_blogs'  => $latest_footer_blogs,
            'footer_latest_blogs'  => $latest_footer_blogs,
            'categories_with_post_count' => $categories_with_post_count,
            'categories_column_1'        => $categories_column_1,
            'categories_column_2'        => $categories_column_2,
        ];
    }

    // Helper to process posts (add thumbnail + url safely)
    private function processPosts($db, $posts, $limit = null)
    {
        $processed = [];
        foreach ($posts as &$post) {
            $post['category_slug'] = $post['category_slug'] ?? 'uncategorized';

            if (!empty($post['thumbnail_id'])) {
                $thumbRow = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$post['thumbnail_id']])->getRow();
                $post['thumbnail_url'] = $thumbRow->guid ?? base_url('assets/images/default-thumbnail.jpg');
            } else {
                $post['thumbnail_url'] = base_url('assets/images/default-thumbnail.jpg');
            }

            // blog detail url
            $post['blog_detail_url'] = base_url($post['post_name']);
            $processed[] = $post;
        }

        return $limit ? array_slice($processed, 0, $limit) : $processed;
    }

    // Helper function to fetch posts by category
    private function fetchCategoryPosts($db, $category_slug, $limit)
    {
        $query = $db->query("
            SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id, 
                   t.slug AS category_slug, p.post_date
            FROM wp_posts p 
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
            AND t.slug = ?
            GROUP BY p.ID
            ORDER BY p.post_date DESC
            LIMIT ?;
        ", [$category_slug, $limit]);

        return $this->processPosts($db, $query->getResultArray());
    }
   public function search()
    {
        $db = \Config\Database::connect();

        $q       = trim((string) ($this->request->getGet('q') ?? $this->request->getPost('q') ?? ''));
        $tagRaw  = trim((string) ($this->request->getGet('tag') ?? ''));
        $tag     = strtolower($tagRaw);
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(20, (int) ($this->request->getGet('per_page') ?? 10)));
        $offset  = ($page - 1) * $perPage;

        $isTagMode = $tag !== '';

        // If you have a method for footer data
        $footer = method_exists($this, 'getFooterData') ? $this->getFooterData($db) : [];

        if (!$isTagMode && mb_strlen($q) < 2) {
            return view('search_results', array_merge([
                'query'     => $q,
                'tag'       => $tagRaw,
                'results'   => [],
                'total'     => 0,
                'page'      => $page,
                'perPage'   => $perPage
            ], $footer));
        }

        if ($isTagMode) {
            $countSql = "
                SELECT COUNT(DISTINCT p.ID) AS total
                FROM wp_posts p
                JOIN wp_term_relationships tr ON p.ID = tr.object_id
                JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'post_tag'
                JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                  AND p.post_type = 'post'
                  AND (LOWER(t.slug) = ? OR LOWER(t.name) = ?)
            ";
            $total = (int) ($db->query($countSql, [$tag, $tag])->getRow()->total ?? 0);

            $sql = "
                SELECT DISTINCT
                    p.ID, p.post_title, p.post_name, p.post_date,
                    pm.meta_value AS thumbnail_id,
                    MAX(ct.slug) AS category_slug
                FROM wp_posts p
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                JOIN wp_term_relationships tr ON p.ID = tr.object_id
                JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'post_tag'
                JOIN wp_terms t ON tt.term_id = t.term_id
                LEFT JOIN wp_term_relationships ctr ON p.ID = ctr.object_id
                LEFT JOIN wp_term_taxonomy ctt ON ctr.term_taxonomy_id = ctt.term_taxonomy_id AND ctt.taxonomy = 'category'
                LEFT JOIN wp_terms ct ON ctt.term_id = ct.term_id
                WHERE p.post_status = 'publish'
                  AND p.post_type = 'post'
                  AND (LOWER(t.slug) = ? OR LOWER(t.name) = ?)
                /* FIXED: Strict MySQL requires all selected columns in GROUP BY */
                GROUP BY p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value
                ORDER BY p.post_date DESC
                LIMIT ? OFFSET ?
            ";
            $rows = $db->query($sql, [$tag, $tag, $perPage, $offset])->getResultArray();
            $results = method_exists($this, 'processPosts') ? $this->processPosts($db, $rows) : $rows;

            return view('search_results', array_merge([
                'query'   => '',
                'tag'     => $tagRaw,
                'results' => $results,
                'total'   => $total,
                'page'    => $page,
                'perPage' => $perPage
            ], $footer));
        }

        // normal keyword search
        $like = '%' . $db->escapeLikeString($q) . '%';

        $countSql = "
            SELECT COUNT(DISTINCT p.ID) AS total
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
              AND (
                    p.post_title LIKE ? OR
                    p.post_content LIKE ? OR
                    t.name LIKE ?
                  )
        ";
        $total = (int) ($db->query($countSql, [$like, $like, $like])->getRow()->total ?? 0);

        $sql = "
            SELECT DISTINCT
                p.ID, p.post_title, p.post_name, p.post_date,
                pm.meta_value AS thumbnail_id,
                MAX(t.slug) AS category_slug,
                (
                    (CASE WHEN p.post_title   LIKE ? THEN 3 ELSE 0 END) +
                    (CASE WHEN t.name         LIKE ? THEN 2 ELSE 0 END) +
                    (CASE WHEN p.post_content LIKE ? THEN 1 ELSE 0 END)
                ) AS relevance
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
              AND (
                    p.post_title LIKE ? OR
                    p.post_content LIKE ? OR
                    t.name LIKE ?
                  )
            /* FIXED: Strict MySQL requires all selected columns in GROUP BY */
            GROUP BY p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value
            ORDER BY relevance DESC, p.post_date DESC
            LIMIT ? OFFSET ?
        ";
        $params  = [$like, $like, $like, $like, $like, $like, $perPage, $offset];
        $rows    = $db->query($sql, $params)->getResultArray();
        $results = method_exists($this, 'processPosts') ? $this->processPosts($db, $rows) : $rows;

        return view('search_results', array_merge([
            'query'   => $q,
            'tag'     => '',
            'results' => $results,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage
        ], $footer));
    }

    public function searchRecentPosts()
    {
        $db = \Config\Database::connect();

        try {
            $sql = "
                SELECT p.ID, p.post_title, p.post_name, p.post_date
                FROM wp_posts p
                WHERE p.post_status = 'publish'
                AND p.post_type = 'post'
                ORDER BY p.post_date DESC
                LIMIT 3
            ";

            $rows = $db->query($sql)->getResultArray();

            $out = [];
            foreach ($rows as $r) {
                $slug = trim((string)($r['post_name'] ?? ''), '/');
                if (empty($slug)) continue;

                $out[] = [
                    'title' => $r['post_title'],
                    'url'   => base_url($slug), // Clean URL structure
                ];
            }

            return $this->response->setContentType('application/json')->setJSON($out);

        } catch (\Exception $e) {
            log_message('error', 'searchRecentPosts error: ' . $e->getMessage());
            return $this->response->setContentType('application/json')->setJSON([]);
        }
    }

    public function searchSuggestions()
    {
        $db = \Config\Database::connect();
        $q  = trim((string) ($this->request->getGet('q') ?? ''));

        if (mb_strlen($q) < 2) {
            return $this->response->setContentType('application/json')->setJSON([]);
        }

        $like = '%' . $db->escapeLikeString($q) . '%';

        // Fetch Posts
        $postSql = "
            SELECT p.ID, p.post_title, p.post_name
            FROM wp_posts p
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
              AND p.post_title LIKE ?
            ORDER BY p.post_date DESC
            LIMIT 4
        ";
        $posts = $db->query($postSql, [$like])->getResultArray();

        // Fetch Categories
        $catSql = "
            SELECT t.term_id, t.name, t.slug
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'category'
              AND t.name LIKE ?
            ORDER BY t.name ASC
            LIMIT 2
        ";
        $cats = $db->query($catSql, [$like])->getResultArray();

        $out = [];
        
        // Add Categories to output (Clean URL)
        foreach ($cats as $c) {
            $out[] = [
                'title' => $c['name'],
                'type'  => 'category',
                'url'   => base_url(trim($c['slug'], '/')) // FIXED: No "category/" prefix
            ];
        }
        
        // Add Posts to output (Clean URL)
        foreach ($posts as $p) {
            $slug = trim((string)($p['post_name'] ?? ''), '/');
            if (empty($slug)) continue;

            $out[] = [
                'title' => $p['post_title'],
                'type'  => 'article',
                'url'   => base_url($slug) // FIXED: Removed category prefix mapping
            ];
        }

        return $this->response->setContentType('application/json')->setJSON($out);
    }
    
  
}
