<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ErrorController extends Controller
{
    public function error404()
    {
        $this->response->setStatusCode(404);

        $db = \Config\Database::connect();

        // ========================================
        //  SECTION 1: FOOTER CATEGORIES
        // ========================================
        
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

        //  Exclude unwanted categories
        $excluded_categories = ['stories', 'week post', 'weekly post', 'story', 'latest', 'other'];
        $categories_with_post_count = array_filter($categories_with_post_count, function ($cat) use ($excluded_categories) {
            $name = strtolower(trim($cat['name']));
            $slug = strtolower(trim($cat['slug']));
            foreach ($excluded_categories as $excluded) {
                if (strpos($name, $excluded) !== false || strpos($slug, $excluded) !== false) {
                    return false;
                }
            }
            return true;
        });
        $categories_with_post_count = array_values($categories_with_post_count);

        //  Normalize Tech & Gadgets
        foreach ($categories_with_post_count as &$cat) {
            if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                $cat['name'] = 'Tech & Gadgets';
                $cat['slug'] = 'tech-gadgets';
            }
        }
        unset($cat);

        //  Split into 2 columns for footer
        $total_categories = count($categories_with_post_count);
        $half = ceil($total_categories / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        // ========================================
        //  SECTION 2: LATEST 3 FOOTER BLOGS
        // ========================================
        
        $latest_footer_blogs_query = $db->query("
            SELECT DISTINCT 
                p.ID, 
                p.post_title, 
                p.post_name, 
                p.post_date, 
                pm.meta_value AS thumbnail_id, 
                t.slug AS category_slug
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug NOT IN ('stories', 'week-post', 'weekly-post', 'story')
            ORDER BY p.post_date DESC
            LIMIT 3
        ");
        $latest_footer_blogs = $latest_footer_blogs_query->getResultArray();

        foreach ($latest_footer_blogs as &$blog) {
            if (!empty($blog['thumbnail_id'])) {
                $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$blog['thumbnail_id']])->getRow();
                $blog['thumbnail_url'] = $thumb->guid ?? base_url('public/assets/images/default-thumbnail.jpg');
            } else {
                $blog['thumbnail_url'] = base_url('public/assets/images/default-thumbnail.jpg');
            }
            $blog['blog_detail_url'] = base_url($blog['category_slug'] . '/' . $blog['post_name']);
            $blog['formatted_date'] = date('F d, Y', strtotime($blog['post_date']));
        }
        unset($blog);

        // ========================================
        //  SECTION 3: LATEST 5 BLOGS FOR 404 PAGE
        // ========================================
        
        $latest_blogs_query = $db->query("
            SELECT DISTINCT 
                p.ID, 
                p.post_title, 
                p.post_name, 
                p.post_date, 
                pm.meta_value AS thumbnail_id, 
                t.slug AS category_slug
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug NOT IN ('stories', 'week-post', 'weekly-post', 'story')
            ORDER BY p.post_date DESC
            LIMIT 4
        ");
        $latestBlogs = $latest_blogs_query->getResultArray();

        //  Process the 5 latest blogs data
        foreach ($latestBlogs as &$blog) {
            // Get thumbnail
            if (!empty($blog['thumbnail_id'])) {
                $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$blog['thumbnail_id']])->getRow();
                $blog['thumbnail_url'] = $thumb->guid ?? base_url('public/assest/images/default-thumbnail.jpg');
            } else {
                $blog['thumbnail_url'] = base_url('public/assest/images/default-thumbnail.jpg');
            }
            
            // Create blog URL
            $blog['blog_url'] = base_url( '/' . $blog['post_name']);
            
            // Format date
            $blog['formatted_date'] = date('F d, Y', strtotime($blog['post_date']));
            
            // Map fields for view compatibility
            $blog['title'] = $blog['post_title'];
            $blog['slug'] = $blog['post_name'];
        }
        unset($blog);

        // ========================================
        //  SECTION 4: RETURN VIEW WITH ALL DATA
        // ========================================
        
        return view('errors/html/error_404', [
            // Footer categories (for footer component)
            'categories_column_1' => $categories_column_1,
            'categories_column_2' => $categories_column_2,
            
            // Footer blogs (for footer component)
            'latest_footer_blogs' => $latest_footer_blogs,
            
            // 404 Page blogs (for main content)
            'latestBlogs' => $latestBlogs,
        ]);
    }
}