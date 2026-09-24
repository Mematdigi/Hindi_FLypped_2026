<?php

namespace App\Controllers;

class PrivacyPolicyController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        try {
            // ====================================================
            //  1. FETCH DATA FOR SIDEBAR (Latest 5 Exclusive Posts)
            // ====================================================
            $exclusive_query = $db->query("
                SELECT DISTINCT p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value AS thumbnail_id
                FROM wp_posts p
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                WHERE p.post_status = 'publish' 
                  AND p.post_type = 'post'
                ORDER BY p.post_date DESC
                LIMIT 5 
            ");
            $exclusive_news_posts = $exclusive_query->getResultArray();

            // Process Images & Links
            foreach ($exclusive_news_posts as &$blog) {
                $blog['blog_detail_url'] = base_url($blog['post_name']);

                if (!empty($blog['thumbnail_id'])) {
                    $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$blog['thumbnail_id']])->getRow();
                    $blog['thumbnail_url'] = $thumb->guid ?? base_url('public/assets/images/default-thumbnail.jpg');
                } else {
                    $blog['thumbnail_url'] = base_url('public/assets/images/default-thumbnail.jpg');
                }
            }
            unset($blog); 

            // ====================================================
            //  2. FETCH FOOTER DATA
            // ====================================================
            
            // Latest Footer Blogs
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
                    WHERE tr.object_id = ? AND tt.taxonomy = 'category'
                    LIMIT 1
                ", [$blog['ID']])->getRow();

                $category_slug = $catRow->slug ?? 'uncategorized';
                $blog['category_slug'] = $category_slug;
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

            // Footer Categories
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

            foreach ($categories_with_post_count as &$cat) {
                if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                    $cat['name'] = 'Tech & Gadgets';
                    $cat['slug'] = 'tech-gadgets';
                }
            }
            unset($cat);

            $total_categories = count($categories_with_post_count);
            $half = ceil($total_categories / 2);
            $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
            $categories_column_2 = array_slice($categories_with_post_count, $half);

            $other_count_row = $db->query("
                SELECT COUNT(DISTINCT p.ID) AS total
                FROM wp_posts p
                JOIN wp_term_relationships tr ON p.ID = tr.object_id
                JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category'
                JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                  AND p.post_type = 'post'
                  AND (
                      LOWER(t.slug) IN ('other','anya','others','misc','uncategorized-other')
                      OR t.name IN ('अन्य','अन्‍य')
                  )
            ")->getRow();
            $other_count = (int) ($other_count_row->total ?? 0);

            //  Return View: privacy_policy
            return view('privacy_policy', [
                'is_contact_page' => false, 
                'exclusive_news_posts' => $exclusive_news_posts,
                'latest_footer_blogs' => $latest_footer_blogs,
                'categories_with_post_count' => $categories_with_post_count,
                'categories_column_1' => $categories_column_1,
                'categories_column_2' => $categories_column_2,
                'other_count'         => $other_count,   
            ]);

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return "An error occurred: " . $e->getMessage();
        }
    }
}