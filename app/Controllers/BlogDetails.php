<?php

namespace App\Controllers;

class BlogDetails extends BaseController
{
    public function view($postSlug)
    {
        try {
            $db = \Config\Database::connect();

            // ==== Main Post (Query by post_name only, no category filter) ====
            $query = $db->query("
                SELECT p.ID, p.post_title, p.post_content, p.post_date, p.post_author, p.post_name 
                FROM wp_posts p
                WHERE p.post_name = ? AND p.post_status = 'publish'
                LIMIT 1
            ", [$postSlug]);
            $post = $query->getRowArray();

            if (!$post) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            
            //  CRITICAL: Process video embeds safely BEFORE passing to view
            $post['post_content'] = $this->processVideoEmbeds($post['post_content']);

            // ==== Author Info ====
            $author_query = $db->query("SELECT ID, display_name, user_login, user_email FROM wp_users WHERE ID = ?", [$post['post_author']]);
            $author = $author_query->getRow();

            if ($author) {
                $post['author_name'] = $author->display_name ?? 'Unknown';
                $post['author_user_login'] = $author->user_login ?? '';
                $post['author_profile_url'] = base_url('author/' . urlencode($author->user_login));
            } else {
                $post['author_name'] = 'Unknown';
                $post['author_profile_url'] = '#';
            }

            // ==== Featured Image ====
            $thumbnail_query = $db->query("
                SELECT p.guid as thumbnail_url
                FROM wp_postmeta pm 
                JOIN wp_posts p ON p.ID = CAST(pm.meta_value AS UNSIGNED)
                WHERE pm.post_id = ? AND pm.meta_key = '_thumbnail_id'
                LIMIT 1
            ", [$post['ID']]);
            $thumbnail = $thumbnail_query->getRow();
            $post['featured_image'] = $thumbnail->thumbnail_url ?? '';
            if (!empty($post['featured_image'])) {
                $post['featured_image'] = str_replace('http://', 'https://', $post['featured_image']);
            }

            // ==== Categories ====
            $categories_query = $db->query("
                SELECT t.term_id, t.name, t.slug
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tt.taxonomy = 'category' AND tr.object_id = ?
            ", [$post['ID']]);
            $categories = $categories_query->getResultArray();
            $post['category_name'] = !empty($categories) ? $categories[0]['name'] : '';

            // ==== FAQ Schema Data (UPDATED FOR NEW AND OLD FORMATS) ====
            $faq_data = [];
            
            // Naye update hue FAQs dhoondega
            $faq_query = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = '_flypped_faqs' ORDER BY meta_id DESC LIMIT 1", [$post['ID']]);
            $faq_row = $faq_query->getRow();
            
            if ($faq_row && !empty($faq_row->meta_value)) {
                $faq_data = json_decode($faq_row->meta_value, true);
            } else {
                // Agar naye nahi mile toh purane formats dhoondega
                $alt_query = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key IN ('faqs', 'faq_schema', '_faq_schema') LIMIT 1", [$post['ID']]);
                $alt_row = $alt_query->getRow();
                if ($alt_row && !empty($alt_row->meta_value)) {
                    $faq_data = @json_decode($alt_row->meta_value, true) ?: @unserialize($alt_row->meta_value);
                }
            }
            
            // Format check
            $final_faqs = [];
            if (is_array($faq_data)) {
                foreach ($faq_data as $item) {
                    if (is_array($item)) {
                        $q = $item['question'] ?? $item['q'] ?? array_values($item)[0] ?? '';
                        $a = $item['answer'] ?? $item['a'] ?? array_values($item)[1] ?? '';
                        if (!empty($q)) {
                            $final_faqs[] = ['question' => $q, 'answer' => $a];
                        }
                    }
                }
            }
            $post['faq_data'] = $final_faqs;

            // ==== Related Blogs ====
            $category_ids = array_column($categories, 'term_id');
            $related_blogs = [];
            if (!empty($category_ids)) {
                $related_query = $db->query("
                    SELECT p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value AS thumbnail_id
                    FROM wp_posts p
                    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                    LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                    LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE p.post_status = 'publish'
                      AND tt.term_id IN ?
                      AND p.ID != ?
                    GROUP BY p.ID
                    ORDER BY p.post_date DESC
                    LIMIT 3;
                ", [$category_ids, $post['ID']]);
                $related_blogs = $related_query->getResultArray();

                foreach ($related_blogs as &$r) {
                    $r['blog_detail_url'] = base_url($r['post_name']);
                    if (!empty($r['thumbnail_id'])) {
                        $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$r['thumbnail_id']])->getRow();
                        $r['thumbnail_url'] = $thumb->guid ?? '';
                    }
                    $r['formatted_date'] = date('F d, Y', strtotime($r['post_date']));
                }
                unset($r);
            }

            // ==== Exclusive Blogs (fallback for footer/news) ====
            $exclusive_query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, p.post_date, pm.meta_value AS thumbnail_id
                FROM wp_posts p
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                WHERE p.post_status = 'publish'
                ORDER BY p.post_date DESC
                LIMIT 3;
            ");
            $exclusive_news_posts = $exclusive_query->getResultArray();

            foreach ($exclusive_news_posts as &$n) {
                $n['blog_detail_url'] = base_url($n['post_name']);
                if (!empty($n['thumbnail_id'])) {
                    $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$n['thumbnail_id']])->getRow();
                    $n['thumbnail_url'] = $thumb->guid ?? '';
                }
                $n['formatted_date'] = date('F d, Y', strtotime($n['post_date']));
            }
            unset($n);

            // ==== Tags ====
            $tags_query = $db->query("
                SELECT t.name
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tt.taxonomy = 'post_tag' AND tr.object_id = ?
            ", [$post['ID']]);
            $tags = $tags_query->getResultArray();
            $post['tags'] = array_column($tags, 'name');

            // ==== SEO ====
            $seo_query = $db->query("
                SELECT meta_key, meta_value 
                FROM wp_postmeta 
                WHERE post_id = ? 
                AND meta_key IN ('_meta_title','_meta_description','_focus_keywords')
            ", [$post['ID']]);
            $seo_meta = [];
            foreach ($seo_query->getResultArray() as $row) {
                $seo_meta[$row['meta_key']] = $row['meta_value'];
            }
            $seoTitle = $seo_meta['_meta_title'] ?? $post['post_title'];
            $seoDescription = $seo_meta['_meta_description'] ?? substr(strip_tags($post['post_content']), 0, 155);
            $seoKeywords = $seo_meta['_focus_keywords'] ?? '';

            // ==== Footer Latest Blogs ====
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
                $blog['category_slug'] = $category_slug;
                $blog['blog_detail_url'] = base_url($blog['post_name']);
                if (!empty($blog['thumbnail_id'])) {
                    $thumb = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$blog['thumbnail_id']])->getRow();
                    $blog['thumbnail_url'] = $thumb->guid ?? '';
                } else {
                    $blog['thumbnail_url'] = base_url('public/assets/images/default-thumbnail.jpg');
                }
                $blog['formatted_date'] = date('F d, Y', strtotime($blog['post_date']));
            }
            unset($blog);

            // ==== Footer Categories ====
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

            $excluded_categories = ['stories', 'week post', 'weekly post', 'story','latest', 'other'];
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
            
            // ===== Count for 'Other' =====
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

            // ==== Render View ====
            return view('blog_detail', [
                'post' => $post,
                'related_blogs' => $related_blogs,
                'exclusive_news_posts' => $exclusive_news_posts,
                'categories' => $categories,
                'tags' => $post['tags'],
                'latest_footer_blogs' => $latest_footer_blogs,
                'categories_with_post_count' => $categories_with_post_count,
                'categories_column_1' => $categories_column_1,
                'categories_column_2' => $categories_column_2,
                'other_count'         => $other_count, 
                'seoTitle' => $seoTitle,
                'seoDescription' => $seoDescription,
                'seoKeywords' => $seoKeywords
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in BlogDetails controller: ' . $e->getMessage());
            return redirect()->to(base_url('error_page'));
        }
        
    }
    
    /**
     *  ENHANCED: Safely Process video embeds
     */
    private function processVideoEmbeds($content)
    {
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 1️⃣ Remove <figure class="media"> wrapper if it exists around oembed
        $content = preg_replace('/<figure[^>]*class="[^"]*media[^"]*"[^>]*>(.*?)<\/figure>/is', '$1', $content);
        
        // 2️⃣ Convert <oembed> tags to responsive iframes natively using our secure CSS class
        $content = preg_replace_callback(
            '/<oembed\s+url=["\']([^"\']+)["\']\s*><\/oembed>/i',
            function($matches) {
                $url = $matches[1];
                
                // YouTube
                if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $idMatch)) {
                    $videoId = $idMatch[1];
                    return '<div class="video-responsive-wrapper"><iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                }
                
                // Vimeo support
                if (preg_match('/vimeo\.com\/(\d+)/i', $url, $idMatch)) {
                    $videoId = $idMatch[1];
                    return '<div class="video-responsive-wrapper"><iframe src="https://player.vimeo.com/video/' . $videoId . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe></div>';
                }
                
                return $matches[0];
            },
            $content
        );
        
        // 3️⃣ Convert bare YouTube URLs strictly ONLY if they stand alone (Not inside hrefs or attributes). 
        // This fixed the page-breaking issue.
        $content = preg_replace_callback(
            '/(?<!["\'=])\bhttps?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)\b(?!["\'])/i',
            function($matches) {
                $videoId = $matches[1];
                return '<div class="video-responsive-wrapper"><iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
            },
            $content
        );
        
        // 4️⃣ Wrap any remaining bare iframes securely inside our class
        $content = preg_replace_callback(
            '/<iframe([^>]+)>.*?<\/iframe>/is',
            function($matches) {
                $attrs = $matches[1];
                $fullIframe = $matches[0];
                
                // Skip if already in our wrapper
                if (strpos($fullIframe, 'video-responsive-wrapper') !== false) {
                    return $fullIframe;
                }
                
                // Only wrap Video embeds
                if (stripos($attrs, 'youtube.com') !== false || stripos($attrs, 'vimeo.com') !== false || stripos($attrs, 'dailymotion.com') !== false) {
                    preg_match('/src=["\']([^"\']+)["\']/i', $attrs, $srcMatch);
                    $src = $srcMatch[1] ?? '';
                    if($src) {
                        return '<div class="video-responsive-wrapper"><iframe src="' . htmlspecialchars($src) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                    }
                }
                
                return $fullIframe;
            },
            $content
        );
        
        return $content;
    }
}