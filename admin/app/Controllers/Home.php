<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends BaseController
{
    public function index(): string
    {
        try {
            // Load the database
            $db = \Config\Database::connect();

            // Query to fetch the latest 50 posts with thumbnails for the main blog slider
            $query = $db->query("
                SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS thumbnail_id
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                WHERE p.post_status = 'publish'
                ORDER BY p.post_date DESC
                LIMIT 50;
            ");
            $latest_posts = $query->getResultArray();
            $posts_with_thumbnails = [];

            // Fetch the actual thumbnail URLs from wp_posts
            foreach ($latest_posts as &$post) {
                if ($post['thumbnail_id']) {
                    $thumbnail_query = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$post['thumbnail_id']]);
                    $thumbnail = $thumbnail_query->getRow();
                    if ($thumbnail && !empty($thumbnail->guid)) {
                        $post['thumbnail_url'] = $thumbnail->guid;
                        $post['blog_detail_url'] = base_url('blog_detail/' . $post['ID']);
                        $posts_with_thumbnails[] = $post;
                    }
                }
            }
            $posts_with_thumbnails = array_slice($posts_with_thumbnails, 0, 5);

            // Entertainment Section Query
            $entertainment_query = $db->query("
                SELECT p.ID, p.post_title, pm.meta_value AS thumbnail_id
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                AND t.slug = 'entertainment'
                GROUP BY p.post_title
                ORDER BY p.post_date DESC
                LIMIT 15;
            ");
            $entertainment_posts_with_thumbnails = $entertainment_query->getResultArray();

            foreach ($entertainment_posts_with_thumbnails as &$entertainment_post) {
                if ($entertainment_post['thumbnail_id']) {
                    $thumbnail_query = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$entertainment_post['thumbnail_id']]);
                    $thumbnail = $thumbnail_query->getRow();
                    if ($thumbnail && !empty($thumbnail->guid)) {
                        $entertainment_post['thumbnail_url'] = $thumbnail->guid;
                        $entertainment_post['blog_detail_url'] = base_url('blog_detail/' . $entertainment_post['ID']);
                    }
                }
            }

            // Latest News Query
            $latest_news_query = $db->query("
                SELECT p.ID, p.post_title, pm.meta_value AS thumbnail_id, p.post_date
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                WHERE p.post_status = 'publish'
                ORDER BY p.post_date DESC
                LIMIT 6;
            ");
            $latest_news_posts = $latest_news_query->getResultArray();
            $latest_news_with_thumbnails = [];

            foreach ($latest_news_posts as &$news_post) {
                if ($news_post['thumbnail_id']) {
                    $thumbnail_query = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$news_post['thumbnail_id']]);
                    $thumbnail = $thumbnail_query->getRow();
                    if ($thumbnail && !empty($thumbnail->guid)) {
                        $news_post['thumbnail_url'] = $thumbnail->guid;
                        $news_post['blog_detail_url'] = base_url('blog_detail/' . $news_post['ID']);
                        $latest_news_with_thumbnails[] = $news_post;
                    }
                }
            }

            // Sports News Query
            $sports_news_query = $db->query("
                SELECT p.ID, p.post_title, pm.meta_value AS thumbnail_id, p.post_date
                FROM wp_posts p 
                LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
                LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE p.post_status = 'publish'
                AND t.slug = 'sports'
                GROUP BY p.post_title
                ORDER BY p.post_date DESC
                LIMIT 8;
            ");
            $sports_news_posts = $sports_news_query->getResultArray();
            $sports_posts_with_thumbnails = [];

            foreach ($sports_news_posts as &$sports_post) {
                if ($sports_post['thumbnail_id']) {
                    $thumbnail_query = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$sports_post['thumbnail_id']]);
                    $thumbnail = $thumbnail_query->getRow();
                    if ($thumbnail && !empty($thumbnail->guid)) {
                        $sports_post['thumbnail_url'] = $thumbnail->guid;
                        $sports_post['blog_detail_url'] = base_url('blog_detail/' . $sports_post['ID']);
                        $sports_posts_with_thumbnails[] = $sports_post;
                    }
                }
            }

            // Fetch posts for other categories
            $entertainment_tab_posts = $this->fetchCategoryPosts($db, 'entertainment', 6);
            $crazy_fact_tab_posts = $this->fetchCategoryPosts($db, 'crazy-facts', 6);
            $relationship_tab_posts = $this->fetchCategoryPosts($db, 'relationship', 6);
            $lifestyle_tab_posts = $this->fetchCategoryPosts($db, 'lifestyle', 6);
            $health_fitness_tab_posts = $this->fetchCategoryPosts($db, 'health-fitness', 6);

            $politics_tab_posts = $this->fetchCategoryPosts($db, 'news', 6);
            $sports_tab_posts = $this->fetchCategoryPosts($db, 'sports', 6);
            $business_tab_posts = $this->fetchCategoryPosts($db, 'business', 6);
            $tech_tab_posts = $this->fetchCategoryPosts($db, 'tech-gadgets', 6);
            $world_tab_posts = $this->fetchCategoryPosts($db, 'world', 6);

            $footer_blog_query = $db->query("
                SELECT p.ID, p.post_title, p.post_date
                FROM wp_posts p
                WHERE p.post_status = 'publish'
                ORDER BY p.post_date DESC
                LIMIT 8;
            ");
            $latest_footer_blogs = $footer_blog_query->getResultArray();

            $categories_query = $db->query("
                SELECT t.name, COUNT(p.ID) as post_count
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                JOIN wp_posts p ON tr.object_id = p.ID
                WHERE p.post_status = 'publish' AND tt.taxonomy = 'category'
                GROUP BY t.name
                ORDER BY post_count DESC;
            ");
            $categories_with_post_count = $categories_query->getResultArray();

            return view('index', [
                'latest_posts' => $posts_with_thumbnails,
                'trending_posts' => $entertainment_posts_with_thumbnails,
                'latest_news' => $latest_news_with_thumbnails,
                'sports_posts' => $sports_posts_with_thumbnails,
                'entertainment_tab_posts' => $entertainment_tab_posts,
                'crazy_fact_tab_posts' => $crazy_fact_tab_posts,
                'relationship_tab_posts' => $relationship_tab_posts,
                'lifestyle_tab_posts' => $lifestyle_tab_posts,
                'health_fitness_tab_posts' => $health_fitness_tab_posts,
                'politics_tab_posts' => $politics_tab_posts,
                'sports_tab_posts' => $sports_tab_posts,
                'business_tab_posts' => $business_tab_posts,
                'tech_tab_posts' => $tech_tab_posts,
                'world_tab_posts' => $world_tab_posts,
                'latest_footer_blogs' => $latest_footer_blogs,
                'categories_with_post_count' => $categories_with_post_count,
            ]);

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return "An error occurred: " . $e->getMessage();
        }
    }

    private function fetchCategoryPosts($db, $category_slug, $limit)
    {
        $query = $db->query("
            SELECT p.ID, p.post_title, p.post_date, pm.meta_value AS thumbnail_id
            FROM wp_posts p 
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
            AND t.slug = ?
            GROUP BY p.post_title
            ORDER BY p.post_date DESC
            LIMIT ?;
        ", [$category_slug, $limit]);

        $posts = $query->getResultArray();
        $posts_with_thumbnails = [];

        foreach ($posts as &$post) {
            if ($post['thumbnail_id']) {
                $thumbnail_query = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$post['thumbnail_id']]);
                $thumbnail = $thumbnail_query->getRow();
                if ($thumbnail && !empty($thumbnail->guid)) {
                    $post['thumbnail_url'] = $thumbnail->guid;
                    $post['blog_detail_url'] = base_url('blog_detail/' . $post['ID']);
                    $posts_with_thumbnails[] = $post;
                }
            }
        }

        return $posts_with_thumbnails;
    }

    public function add_post()
    {
        $db = \Config\Database::connect();

        $author_query = $db->query("SELECT ID, display_name FROM wp_users ORDER BY display_name ASC");
        $authors = $author_query->getResultArray();

        $category_query = $db->query("
            SELECT t.term_id, t.name 
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'category'
            ORDER BY t.name ASC
        ");
        $categories = $category_query->getResultArray();

        return view('index', [
            'authors' => $authors,
            'categories' => $categories,
        ]);
    }

    public function savePost()
    {
        $db = \Config\Database::connect();
 
        try {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'postTitle'   => 'required|min_length[3]',
                'postContent' => 'required',
                'visibility'  => 'required|in_list[public,private,draft,scheduled]',
                'author'      => 'required|integer',
                'categories'  => 'required',
                'tags'        => 'permit_empty',
                'publishTime' => 'permit_empty'
            ]);
 
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
 
            $title = $this->request->getPost('postTitle');

            // --- ADDED: PREVENT DOUBLE SUBMISSION CHECK ---
            // Check if a post with the exact same title was created in the last 1 minute
            $recentDuplicate = $db->table('wp_posts')
                ->where('post_title', $title)
                ->where('post_date >=', date('Y-m-d H:i:s', strtotime('-1 minute')))
                ->get()
                ->getRow();

            if ($recentDuplicate) {
                // If found, skip creation and just return success to avoid duplication
                return redirect()->to('/add_post')->with('success', 'Post published successfully!');
            }
            // ----------------------------------------------

            $contentRaw = trim($this->request->getPost('postContent') ?? '');
            $visibility = $this->request->getPost('visibility');
            $author = (int) $this->request->getPost('author');
            $tags = $this->request->getPost('tags') ?? '';
            $categories = (array) $this->request->getPost('categories');
 
            $publishTimeRaw = $this->request->getPost('publishTime');
            $currentTime = date('Y-m-d H:i:s');
            $currentTimeGmt = gmdate('Y-m-d H:i:s');
            
            if (!empty($publishTimeRaw)) {
                $publishTime = date('Y-m-d H:i:s', strtotime($publishTimeRaw));
                $publishTimeGmt = gmdate('Y-m-d H:i:s', strtotime($publishTimeRaw));
            } else {
                $publishTime = $currentTime;
                $publishTimeGmt = $currentTimeGmt;
            }

            if ($visibility === 'draft') {
                $postStatus = 'draft';
                $successMessage = 'Post saved as draft successfully!';
            } elseif ($visibility === 'private') {
                $postStatus = 'private';
                $successMessage = 'Post saved as private successfully!';
            } elseif ($visibility === 'scheduled' || $publishTime > $currentTime) {
                $postStatus = 'future';
                $successMessage = 'Post scheduled successfully for ' . date('F j, Y \a\t g:i A', strtotime($publishTime));
            } else {
                $postStatus = 'publish';
                $successMessage = 'Post published successfully!';
            }
 
            $focusKeywords = $this->request->getPost('focusKeywords') ?: '';
            $metaTitle = $this->request->getPost('metaTitle') ?: $title;
            $metaDescription = $this->request->getPost('metaDescription') ?: '';
            $urlSlug = $this->request->getPost('urlSlug');
 
            $slug = $this->ensureUniqueSlug($db, $this->generateSlug($urlSlug ?: $title));
            $processedContent = $this->embedTweet($contentRaw);
 
            $data = [
                'post_author' => $author,
                'post_date' => $publishTime,
                'post_date_gmt' => $publishTimeGmt,
                'post_title' => $title,
                'post_content' => $processedContent,
                'post_excerpt' => substr(strip_tags($contentRaw), 0, 155),
                'post_status' => $postStatus,
                'post_type' => 'post',
                'post_name' => $slug,
                'guid' => '', 
                'post_modified' => $currentTime,
                'post_modified_gmt' => $currentTimeGmt 
            ];
 
            $db->table('wp_posts')->insert($data);
            $postID = (int) $db->insertID();
 
            $db->table('wp_posts')->where('ID', $postID)->update([
                'guid' => base_url('blog_detail/' . $postID)
            ]);
 
            if (!empty($categories)) {
                foreach ($categories as $termId) {
                    $tax = $db->query(
                        "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = ? AND taxonomy = 'category'",
                        [$termId]
                    )->getRow();
                    
                    if ($tax) {
                        $db->table('wp_term_relationships')->insert([
                            'object_id' => $postID,
                            'term_taxonomy_id' => $tax->term_taxonomy_id
                        ]);
                    }
                }
            }
 
            if (!empty($tags)) {
                foreach (array_filter(array_map('trim', explode(',', $tags))) as $tag) {
                    $tag = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $tag);
                    $tag = preg_replace('/^value/i', '', $tag);
                    $tag = trim($tag);
                    
                    if ($tag === '') continue;
 
                    $term = $db->query("SELECT term_id FROM wp_terms WHERE name = ?", [$tag])->getRow();
                    
                    if (!$term) {
                        $db->table('wp_terms')->insert([
                            'name' => $tag,
                            'slug' => $this->generateSlug($tag)
                        ]);
                        $termId = (int) $db->insertID();
                        
                        $db->table('wp_term_taxonomy')->insert([
                            'term_id' => $termId,
                            'taxonomy' => 'post_tag',
                            'count' => 1
                        ]);
                        $ttId = (int) $db->insertID();
                    } else {
                        $tt = $db->query(
                            "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = ? AND taxonomy = 'post_tag'",
                            [$term->term_id]
                        )->getRow();
                        $ttId = $tt ? (int) $tt->term_taxonomy_id : null;
                        
                        if ($ttId) {
                            $db->query("UPDATE wp_term_taxonomy SET count = count + 1 WHERE term_taxonomy_id = ?", [$ttId]);
                        }
                    }
 
                    if (!empty($ttId)) {
                        $db->table('wp_term_relationships')->insert([
                            'object_id' => $postID,
                            'term_taxonomy_id' => $ttId
                        ]);
                    }
                }
            }
 
            // ---- PROPER ATTACHMENT UPLOAD FOR FEATURED IMAGE ----
            $featuredImage = $this->request->getFile('featureImage');
            if ($featuredImage && $featuredImage->isValid()) {
                $uploadPath = FCPATH . 'wp-content/uploads/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // --- Keep original name and make it SEO friendly ---
                $originalName = $featuredImage->getName();
                $ext = $featuredImage->getExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                
                $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
                
                if (empty($cleanName)) {
                    $cleanName = 'feature-' . time();
                }
                
                $imageName = $cleanName . '.' . $ext;
                
                $counter = 1;
                while (file_exists($uploadPath . $imageName)) {
                    $imageName = $cleanName . '-' . $counter . '.' . $ext;
                    $counter++;
                }
                // --------------------------------------------------

                if (!$featuredImage->move($uploadPath, $imageName)) {
                    log_message('error', 'Image upload failed: ' . $featuredImage->getErrorString());
                    throw new \Exception("Image upload failed: " . $featuredImage->getErrorString());
                }
 
                $imageUrl = base_url('wp-content/uploads/' . $imageName);
                
                $db->table('wp_posts')->update(['guid' => $imageUrl], ['ID' => $postID]);

                // Create Attachment Post
                $db->table('wp_posts')->insert([
                    'post_author'    => $author,
                    'post_date'      => $currentTime,
                    'post_date_gmt'  => $currentTimeGmt,
                    'post_title'     => pathinfo($imageName, PATHINFO_FILENAME),
                    'post_content'   => '',
                    'post_excerpt'   => '',
                    'post_status'    => 'inherit',
                    'post_type'      => 'attachment',
                    'post_mime_type' => $featuredImage->getClientMimeType(),
                    'guid'           => $imageUrl,
                    'post_parent'    => $postID
                ]);
                $attachmentID = (int) $db->insertID();

                $db->table('wp_postmeta')->insert([
                    'post_id'    => $attachmentID,
                    'meta_key'   => '_wp_attached_file',
                    'meta_value' => 'uploads/' . $imageName
                ]);
 
                $db->table('wp_postmeta')->insert([
                    'post_id' => $postID,
                    'meta_key' => '_thumbnail_id',
                    'meta_value' => $attachmentID
                ]);
            }
            
            // ---- SEO Metadata ----
            $seoData = [
                'focus_keywords' => $focusKeywords,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription
            ];
 
            foreach ($seoData as $key => $value) {
                if (!empty($value)) {
                    $db->table('wp_postmeta')->insert([
                        'post_id' => $postID,
                        'meta_key' => '_' . $key,
                        'meta_value' => $value
                    ]);
                }
            }
        
            $this->saveFaqs($db, $postID);

            if ($postStatus === 'publish') {
                $post_url = "https://flyppedhindi.com/" . $slug;
                // $this->submitToIndexNow([$post_url]); // Commented if submitToIndexNow doesn't exist
                log_message('info', 'IndexNow triggered for NEW post: ' . $post_url);
            }

            return redirect()->to('/add_post')->with('success', $successMessage);
        } catch (\Exception $e) {
            log_message('error', 'Post Publish Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to publish post. Please try again.');
        }
    }

    private function ensureUniqueSlug($db, $slug)
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $query = $db->query("SELECT ID FROM wp_posts WHERE post_name = ?", [$slug]);
            if (!$query->getRow()) {
                break;
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function generateSlug($title)
    {
        $slug = strtolower($title);
        $slug = str_replace([' ', '_'], '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    private function embedTweet($content)
    {
        $pattern = '/https?:\/\/(www\.)?twitter\.com\/[A-Za-z0-9_]+\/status\/[0-9]+/';
        return preg_replace_callback($pattern, function ($matches) {
            $tweet_url = trim($matches[0]);
            return '<div class="twitter-tweet"><a href="' . htmlspecialchars($tweet_url, ENT_QUOTES, 'UTF-8') . '"></a></div>';
        }, $content);
    }

    private function saveFaqs($db, int $postId): void
    {
        $questions = $this->request->getPost('faq_questions') ?? [];
        $answers   = $this->request->getPost('faq_answers')   ?? [];

        if (empty($questions) || !is_array($questions)) {
            return;
        }

        $db->query("
            DELETE FROM wp_postmeta
            WHERE post_id = ?
            AND (
                  meta_key = '_faq_schema'
               OR meta_key = '_faq_count'
               OR meta_key LIKE '_faq_question_%'
               OR meta_key LIKE '_faq_answer_%'
            )
        ", [$postId]);

        $seenQuestions = [];
        $cleanFaqs     = [];
        $order         = 0;

        foreach ($questions as $i => $question) {
            $question = trim((string)($question ?? ''));
            $answer   = trim((string)($answers[$i] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $key = strtolower($question);
            if (isset($seenQuestions[$key])) {
                continue;
            }
            $seenQuestions[$key] = true;

            $db->table('wp_postmeta')->insert([
                'post_id'    => $postId,
                'meta_key'   => '_faq_question_' . $order,
                'meta_value' => $question,
            ]);

            $db->table('wp_postmeta')->insert([
                'post_id'    => $postId,
                'meta_key'   => '_faq_answer_' . $order,
                'meta_value' => $answer,
            ]);

            $cleanFaqs[] = ['question' => $question, 'answer' => $answer];
            $order++;
        }

        if (empty($cleanFaqs)) {
            return;
        }

        $db->table('wp_postmeta')->insert([
            'post_id'    => $postId,
            'meta_key'   => '_faq_count',
            'meta_value' => count($cleanFaqs),
        ]);

        $db->table('wp_postmeta')->insert([
            'post_id'    => $postId,
            'meta_key'   => '_faq_schema',
            'meta_value' => json_encode([
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(function ($faq) {
                    return [
                        '@type'          => 'Question',
                        'name'           => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => $faq['answer'],
                        ],
                    ];
                }, $cleanFaqs),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    private function validateSEOGuidelines($title, $content, $metaDescription, $focusKeywords, $metaTitle)
    {
        if (empty($focusKeywords) && empty($metaDescription)) {
            return true;
        }
        
        $keywords = array_map('trim', explode(',', $focusKeywords));
        $keywords = array_filter($keywords);
        
        if (!empty($focusKeywords) && (count($keywords) < 1 || count($keywords) > 10)) {
            return false;
        }
        
        return true; 
    }

    private function calculateSEOScore($title, $content, $metaDescription, $focusKeywords, $metaTitle)
    {
        if (empty($focusKeywords)) {
            return 50; 
        }
        
        $score = 0;
        $maxScore = 6;
        
        $keywords = array_map('trim', explode(',', $focusKeywords));
        $keywords = array_filter($keywords);
        
        foreach ($keywords as $keyword) {
            if (stripos($title, $keyword) !== false) {
                $score++;
                break;
            }
        }
        
        if (!empty($metaDescription)) {
            foreach ($keywords as $keyword) {
                if (stripos($metaDescription, $keyword) !== false) {
                    $score++;
                    break;
                }
            }
        }
        
        $wordCount = $this->getWordCount($content);
        if ($wordCount >= 100) { 
            $score++;
        }
        
        if ($this->checkFirstParagraphKeyword($content, $keywords)) {
            $score++;
        }
        
        if ($this->checkHeadingStructure($content)) {
            $score++;
        }
        
        if (!empty($metaTitle) && strlen($metaTitle) >= 10) {
            $score++;
        }
        
        return round(($score / $maxScore) * 100);
    }

    private function saveSEOData($db, $postID, $seoData)
    {
        foreach ($seoData as $key => $value) {
            if (!empty($value)) {
                $db->table('wp_postmeta')->insert([
                    'post_id' => $postID,
                    'meta_key' => '_' . $key,
                    'meta_value' => $value
                ]);
            }
        }
    }

    private function getWordCount($content)
    {
        $plainText = strip_tags($content);
        $plainText = trim($plainText);
        if (empty($plainText)) return 0;
        
        return str_word_count($plainText);
    }

    private function checkHeadingStructure($content)
    {
        return preg_match('/<h[2-6][^>]*>.*?<\/h[2-6]>/i', $content);
    }

    private function checkFirstParagraphKeyword($content, $keywords)
    {
        if (empty($keywords)) return false;
        
        $firstParagraph = '';
        
        if (preg_match('/<p[^>]*>(.*?)<\/p>/i', $content, $matches)) {
            $firstParagraph = $matches[1];
        } else {
            $plainText = strip_tags($content);
            $firstParagraph = substr($plainText, 0, 200);
        }
        
        $firstParagraph = strip_tags($firstParagraph);
        
        foreach ($keywords as $keyword) {
            if (stripos($firstParagraph, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function previewPost()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        try {
            $previewId = (int)$this->request->getGet('id');
            if (!$previewId) throw new \Exception('Preview ID is required');

            $post = $db->query("
                SELECT p.ID, p.post_title, p.post_content, p.post_date, p.post_author
                FROM wp_posts p
                WHERE p.ID = ? AND p.post_title LIKE '[PREVIEW]%'
                LIMIT 1
            ", [$previewId])->getRowArray();
            
            if (!$post) throw new \Exception('Preview post not found');

            $post['post_title'] = preg_replace('/^\[PREVIEW\]\s*/', '', $post['post_title']);

            $authorRow = $db->query("SELECT display_name FROM wp_users WHERE ID = ?", [$post['post_author']])->getRow();
            $post['author_name'] = $authorRow->display_name ?? 'Unknown';

            $thumb = $db->query("
                SELECT a.guid
                FROM wp_postmeta m
                JOIN wp_posts a ON a.ID = m.meta_value
                WHERE m.post_id = ? AND m.meta_key = '_thumbnail_id'
                LIMIT 1
            ", [$post['ID']])->getRow();

            $post['featured_image'] = '';
            if ($thumb && !empty($thumb->guid)) {
                $post['featured_image'] = $this->normalizeWpAssetUrl($thumb->guid);
            }

            $categories = $db->query("
                SELECT t.term_id, t.name, t.slug
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tt.taxonomy = 'category' AND tr.object_id = ?
            ", [$post['ID']])->getResultArray();

            $tags = $db->query("
                SELECT t.name
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tt.taxonomy = 'post_tag' AND tr.object_id = ?
            ", [$post['ID']])->getResultArray();
            $post['tags'] = array_column($tags, 'name');

            $seo = $db->query("
                SELECT meta_key, meta_value
                FROM wp_postmeta
                WHERE post_id = ? AND meta_key IN ('_meta_title','_meta_description')
            ", [$post['ID']])->getResultArray();
            
            foreach ($seo as $row) {
                $key = ltrim($row['meta_key'], '_');
                $post[$key] = $row['meta_value'];
            }

            return view('preview_post', [
                'post' => $post,
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Preview Error: ' . $e->getMessage());
            return redirect()->to('/add_post')->with('error', 'Failed to load preview: ' . $e->getMessage());
        }
    }

    private function normalizeWpAssetUrl(string $url): string
    {
        $base = rtrim(base_url(), '/');

        if (stripos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 7);
        }

        if (!preg_match('~^https?://~i', $url)) {
            if (strpos($url, '/wp-content/') === 0) {
                $url = $base . $url;
            } else {
                $url = $base . '/' . ltrim($url, '/');
            }
        }

        $pi = @parse_url($url);
        $baseHost = parse_url($base, PHP_URL_HOST);
        
        if ($pi && !empty($pi['host']) && $pi['host'] !== $baseHost && strpos($url, '/wp-content/') !== false) {
            if (preg_match('~(/wp-content/.*)$~i', $url, $mm)) {
                $url = $base . $mm[1];
            }
        }
        
        return $url;
    }

    public function createPreview()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        try {
            $title      = (string)$this->request->getPost('postTitle');
            $content    = (string)$this->request->getPost('postContent');
            $author     = (int)$this->request->getPost('author');
            $tags       = (string)$this->request->getPost('tags');
            $categories = (array)$this->request->getPost('categories');

            if ($title === '' || $content === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Title and content required']);
            }
            if (empty($categories)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Select at least one category']);
            }

            $metaTitle       = $this->request->getPost('metaTitle') ?: $title;
            $metaDescription = (string)$this->request->getPost('metaDescription');
            $urlSlug         = (string)$this->request->getPost('urlSlug');

            $baseSlug    = $this->generateSlug($urlSlug ?: $title);
            $previewSlug = $this->ensureUniqueSlug($db, 'preview-' . time() . '-' . $baseSlug);

            $processedContent = $this->embedTweet($content);
            $now    = date('Y-m-d H:i:s');
            $nowGmt = gmdate('Y-m-d H:i:s');

            $db->table('wp_posts')->insert([
                'post_author'       => $author,
                'post_date'         => $now,
                'post_date_gmt'     => $nowGmt,
                'post_title'        => '[PREVIEW] ' . $title,
                'post_content'      => $processedContent,
                'post_excerpt'      => substr(strip_tags($content), 0, 155),
                'post_status'       => 'draft',
                'post_type'         => 'post',
                'post_name'         => $previewSlug,
                'guid'              => base_url($previewSlug),
                'post_modified'     => $now,
                'post_modified_gmt' => $nowGmt
            ]);
            
            $postID = (int)$db->insertID();
            if (!$postID) throw new \Exception('Failed to create preview post');

            $db->table('wp_posts')->where('ID', $postID)->update([
                'guid' => base_url($previewSlug)
            ]);

            $attachmentID  = null;
            
            if (isset($_FILES['featureImage']) && $_FILES['featureImage']['error'] === UPLOAD_ERR_OK) {
                $tmpFilePath = $_FILES['featureImage']['tmp_name'];
                
                if (file_exists($tmpFilePath)) {
                    $extension = pathinfo($_FILES['featureImage']['name'], PATHINFO_EXTENSION);
                    $imageName = uniqid('img_', true) . '.' . $extension;
                    $uploadPath = FCPATH . 'wp-content/uploads/';
                    
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    $finalPath = $uploadPath . $imageName;
                    
                    if (move_uploaded_file($tmpFilePath, $finalPath)) {
                        if (file_exists($finalPath)) {
                            $imageUrl = base_url('wp-content/uploads/' . $imageName);
                            
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $finalPath);
                            finfo_close($finfo);

                            $db->table('wp_posts')->insert([
                                'post_author'      => $author,
                                'post_date'        => $now,
                                'post_date_gmt'    => $nowGmt,
                                'post_title'       => pathinfo($imageName, PATHINFO_FILENAME),
                                'post_content'     => '',
                                'post_excerpt'     => '',
                                'post_status'      => 'inherit',
                                'post_type'        => 'attachment',
                                'post_mime_type'   => $mimeType,
                                'guid'             => $imageUrl,
                                'post_parent'      => $postID,
                                'comment_status'   => 'closed',
                                'ping_status'      => 'closed'
                            ]);
                            
                            $attachmentID = (int)$db->insertID();

                            $db->table('wp_postmeta')->insert([
                                'post_id'    => $attachmentID,
                                'meta_key'   => '_wp_attached_file',
                                'meta_value' => 'uploads/' . $imageName
                            ]);

                            $db->table('wp_postmeta')->insert([
                                'post_id'    => $postID,
                                'meta_key'   => '_thumbnail_id',
                                'meta_value' => $attachmentID
                            ]);
                        }
                    }
                }
            }

            foreach ($categories as $termId) {
                $tax = $db->query(
                    "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = ? AND taxonomy = 'category'",
                    [(int)$termId]
                )->getRow();
                
                if ($tax) {
                    $db->table('wp_term_relationships')->insert([
                        'object_id'        => $postID,
                        'term_taxonomy_id' => $tax->term_taxonomy_id
                    ]);
                }
            }

            if (!empty($tags)) {
                foreach (array_filter(array_map('trim', explode(',', $tags))) as $tag) {
                    if ($tag === '') continue;
                    
                    $term = $db->query("SELECT term_id FROM wp_terms WHERE name = ?", [$tag])->getRow();
                    
                    if (!$term) {
                        $db->table('wp_terms')->insert([
                            'name' => $tag,
                            'slug' => $this->generateSlug($tag)
                        ]);
                        $termId = (int)$db->insertID();
                        
                        $db->table('wp_term_taxonomy')->insert([
                            'term_id'  => $termId,
                            'taxonomy' => 'post_tag',
                            'count'    => 0
                        ]);
                        $ttId = (int)$db->insertID();
                    } else {
                        $tt = $db->query(
                            "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = ? AND taxonomy = 'post_tag'",
                            [$term->term_id]
                        )->getRow();
                        $ttId = $tt ? (int)$tt->term_taxonomy_id : null;
                    }
                    
                    if (!empty($ttId)) {
                        $db->table('wp_term_relationships')->insert([
                            'object_id'        => $postID,
                            'term_taxonomy_id' => $ttId
                        ]);
                    }
                }
            }

            if (!empty($metaTitle)) {
                $db->table('wp_postmeta')->insert([
                    'post_id'    => $postID,
                    'meta_key'   => '_meta_title',
                    'meta_value' => $metaTitle
                ]);
            }
            if (!empty($metaDescription)) {
                $db->table('wp_postmeta')->insert([
                    'post_id'    => $postID,
                    'meta_key'   => '_meta_description',
                    'meta_value' => $metaDescription
                ]);
            }

            $previewUrl = base_url('preview-post?id=' . $postID);

            return $this->response->setJSON([
                'success'       => true,
                'preview_url'   => $previewUrl, 
                'post_id'       => $postID,
                'attachment_id' => $attachmentID
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create preview: ' . $e->getMessage()
            ]);
        }
    }

    public function getFaqsForPost(int $postId): array
    {
        $db = \Config\Database::connect();

        $countRow = $db->query(
            "SELECT meta_value FROM wp_postmeta 
             WHERE post_id = ? AND meta_key = '_faq_count' LIMIT 1",
            [$postId]
        )->getRow();

        if (!$countRow || (int)$countRow->meta_value === 0) {
            return [];
        }

        $count = (int)$countRow->meta_value;
        $faqs  = [];

        for ($i = 0; $i < $count; $i++) {
            $meta = $db->query(
                "SELECT meta_key, meta_value FROM wp_postmeta
                 WHERE post_id = ?
                 AND meta_key IN (?, ?)
                 LIMIT 2",
                [$postId, '_faq_question_' . $i, '_faq_answer_' . $i]
            )->getResultArray();

            $question = '';
            $answer   = '';

            foreach ($meta as $row) {
                if ($row['meta_key'] === '_faq_question_' . $i) {
                    $question = $row['meta_value'];
                }
                if ($row['meta_key'] === '_faq_answer_' . $i) {
                    $answer = $row['meta_value'];
                }
            }

            if ($question !== '' && $answer !== '') {
                $faqs[] = ['question' => $question, 'answer' => $answer];
            }
        }

        return $faqs;
    }

    // ============================================================
    // CKEditor Image Upload (Exactly like Featured Image)
    // ============================================================
    public function uploadEditorImage()
    {
        $editorImage = $this->request->getFile('upload');

        if ($editorImage && $editorImage->isValid()) {
            
            $uploadPath = FCPATH . 'wp-content/uploads/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // --- Keep original name and make it SEO friendly ---
            $originalName = $editorImage->getName();
            $ext = $editorImage->getExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
            
            if (empty($cleanName)) {
                $cleanName = 'editor-' . time();
            }
            
            $imageName = $cleanName . '.' . $ext;
            
            $counter = 1;
            while (file_exists($uploadPath . $imageName)) {
                $imageName = $cleanName . '-' . $counter . '.' . $ext;
                $counter++;
            }
            // --------------------------------------------------
            
            if ($editorImage->move($uploadPath, $imageName)) {
                
                $imagePath = 'wp-content/uploads/' . $imageName;
                $imageUrl = base_url($imagePath);

                $db = \Config\Database::connect();
                $currentTime = date('Y-m-d H:i:s');
                $currentTimeGmt = gmdate('Y-m-d H:i:s');
                
                $data = [
                    'post_author'    => session()->get('user_id') ?? 1,
                    'post_date'      => $currentTime,
                    'post_date_gmt'  => $currentTimeGmt,
                    'post_title'     => pathinfo($imageName, PATHINFO_FILENAME),
                    'post_content'   => '',
                    'post_excerpt'   => '',
                    'post_status'    => 'inherit',
                    'post_type'      => 'attachment',
                    'post_mime_type' => $editorImage->getClientMimeType(),
                    'guid'           => $imageUrl
                ];

                $db->table('wp_posts')->insert($data);
                $attachmentID = $db->insertID();

                $db->table('wp_postmeta')->insert([
                    'post_id'    => $attachmentID,
                    'meta_key'   => '_wp_attached_file',
                    'meta_value' => 'uploads/' . $imageName
                ]);

                return $this->response->setJSON([
                    'url' => $imageUrl
                ]);
            }
        }

        return $this->response->setJSON([
            'error' => ['message' => 'Image upload failed.']
        ]);
    }
}