<?php

namespace App\Controllers;

class PostController extends BaseController
{
    // Method to display all posts with filtering
    public function allPosts()
    {
        try {
            // Connect to the database
            $db = \Config\Database::connect();
            
            // Get filter parameters from GET request
            $titleSearch = $this->request->getGet('title_search');
            $authorFilter = $this->request->getGet('author_filter');
            $postDate = $this->request->getGet('post_date'); // Single date instead of range
            
            // Build the base query
            $sql = "SELECT ID, post_title, post_date, post_author FROM wp_posts WHERE post_type = 'post'";
            $binds = [];
            
            // Add title filter if provided
            if (!empty($titleSearch)) {
                $sql .= " AND post_title LIKE ?";
                $binds[] = '%' . $titleSearch . '%';
            }
            
            // Add author filter if provided
            if (!empty($authorFilter)) {
                $sql .= " AND post_author = ?";
                $binds[] = $authorFilter;
            }
            
            // Add single date filter if provided
            if (!empty($postDate)) {
                $sql .= " AND DATE(post_date) = ?";
                $binds[] = $postDate;
            }
            
            $status = $this->request->getGet('post_status');
            if (!empty($status)) {
                $sql .= " AND post_status = ?";
                $binds[] = $status;
            }

            // Add ordering
            $sql .= " ORDER BY post_date DESC";
            
            // Execute the query with or without binds
            if (!empty($binds)) {
                $query = $db->query($sql, $binds);
            } else {
                $query = $db->query($sql);
            }
            
            // Fetch all posts
            $posts = $query->getResult();
            
            // Fetch all authors for the dropdown
            $authorsQuery = $db->query("SELECT ID, display_name FROM wp_users ORDER BY display_name ASC");
            $authors = $authorsQuery->getResult();
            
            // Pass posts and authors to the view
            return view('all_posts', [
                'posts' => $posts,
                'authors' => $authors
            ]);

        } catch (\Exception $e) {
            // Catch and display the error
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Method to get post title suggestions for autocomplete
    public function getPostSuggestions()
    {
        try {
            $query = $this->request->getGet('q');
            
            if (empty($query) || strlen($query) < 2) {
                return $this->response->setJSON([]);
            }
            
            $db = \Config\Database::connect();
            
            // Search for posts with matching titles
            $sql = "SELECT p.post_title, u.display_name as author_name, 
                           DATE_FORMAT(p.post_date, '%M %d, %Y') as post_date
                    FROM wp_posts p
                    LEFT JOIN wp_users u ON p.post_author = u.ID
                    WHERE p.post_status = 'publish' 
                    AND p.post_type = 'post' 
                    AND p.post_title LIKE ?
                    ORDER BY p.post_date DESC
                    LIMIT 10";
            
            $suggestions = $db->query($sql, ['%' . $query . '%'])->getResult();
            
            return $this->response->setJSON($suggestions);
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching suggestions: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }

    // Method to Delete the Post From database 
    public function confirmDeletePost($id)
    {
        $db = \Config\Database::connect();

        // Fetch the post details
        $post = $db->table('wp_posts')->where('ID', $id)->get()->getRow();

        // Check if the post exists
        if (!$post) {
            return redirect()->to('/all-posts')->with('error', 'Post not found.');
        }

        // Load the confirmation view
        return view('confirm_delete_post', ['post' => $post]);
    }

    public function processDeletePost($id)
    {
        $db = \Config\Database::connect();

        // =======================================================
        //  1. PEHLE POST URL NIKALEIN (DELETE HONE SE PEHLE)
        // =======================================================
        $post = $db->table('wp_posts')->select('post_name')->where('ID', $id)->get()->getRow();
        $post_slug = $post ? $post->post_name : '';
        $post_url = "https://flyppedhindi.com/" . $post_slug;

        // Try deleting the post
        try {
            $delete_post_query = $db->table('wp_posts')->delete(['ID' => $id]);

            if ($delete_post_query) {
                // Delete associated category and tag relationships
                $db->table('wp_term_relationships')->delete(['object_id' => $id]);
                
                // Delete SEO meta data
                $db->table('wp_postmeta')->delete(['post_id' => $id]);

                // =======================================================
                //  2. DELETE HONE KE BAAD INDEXNOW KO TRIGGER KAREIN
                // =======================================================
                if (!empty($post_slug)) {
                    $this->submitToIndexNow([$post_url]);
                    log_message('info', 'IndexNow triggered for deleted post: ' . $post_url);
                }

                // Redirect with a success message
                return redirect()->to('/all-posts')->with('success', 'Post deleted successfully.');
            } else {
                throw new \Exception('Failed to delete the post.');
            }
        } catch (\Exception $e) {
            // Log the error and return an error message
            log_message('error', 'Post Deletion Error: ' . $e->getMessage());
            return redirect()->to('/all-posts')->with('error', 'An error occurred while trying to delete the post.');
        }
    }

    // Method to display the Edit Post form with SEO data
    public function editPost($id)
    {
        try {
            $db = \Config\Database::connect();
            
            // Fetch the post details
            $post = $db->query("SELECT * FROM wp_posts WHERE ID = ?", [$id])->getRow();

            if ($post) {
                // ========================================
                //  FETCH CORRECT FEATURED IMAGE
                // ========================================
                
                $featured_image_query = $db->query("
                    SELECT meta_value AS thumbnail_id
                    FROM wp_postmeta
                    WHERE post_id = ? AND meta_key = '_thumbnail_id'
                ", [$id]);
                
                $featured_image_data = $featured_image_query->getRow();
                
                $post->featured_image_url = null;
                
                if ($featured_image_data && !empty($featured_image_data->thumbnail_id)) {
                    $thumb_id = (int)$featured_image_data->thumbnail_id; //  int cast fixes mismatch
                    $attachment_query = $db->query("
                        SELECT ID, guid 
                        FROM wp_posts 
                        WHERE ID = ?
                        LIMIT 1
                    ", [$thumb_id]); //  removed post_type = 'attachment' filter
                    
                    $attachment = $attachment_query->getRow();
                    
                    if ($attachment && !empty($attachment->guid)) {
                        //  fix http → https so image doesn't load broken
                        $post->featured_image_url = str_replace('http://', 'https://', $attachment->guid);
                        log_message('info', "Featured image found for post {$id}: {$post->featured_image_url}");
                    }
                }

                log_message('debug', '=== FEATURED IMAGE DEBUG for post ' . $id . ' ===');
                log_message('debug', 'thumbnail_id row: ' . print_r($featured_image_data, true));
                log_message('debug', 'attachment row: ' . print_r($attachment ?? 'NOT FOUND', true));
                log_message('debug', 'final url: ' . ($post->featured_image_url ?? 'NULL'));

                // Fetch all categories
                $categories_query = $db->query("
                    SELECT t.term_id, t.name 
                    FROM wp_terms t
                    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                    WHERE tt.taxonomy = 'category'
                ");
                $categories = $categories_query->getResult();

                //  was fetching term_taxonomy_id but checkbox value uses term_id — fixed
                $selected_categories_query = $db->query("
                    SELECT t.term_id
                    FROM wp_terms t
                    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                    JOIN wp_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tt.taxonomy = 'category' AND tr.object_id = ?", [$id]);
                $selected_categories = array_column(
                    $selected_categories_query->getResultArray(),
                    'term_id'
                );

                // Fetch tags associated with the post
                $tags = $db->query("
                    SELECT t.name 
                    FROM wp_terms t
                    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                    JOIN wp_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tt.taxonomy = 'post_tag' AND tr.object_id = ?", [$id])->getResult();

                // Fetch all authors (users)
                $authors_query = $db->query("
                    SELECT ID, display_name 
                    FROM wp_users
                ");
                $authors = $authors_query->getResult();

                // Fetch SEO data from wp_postmeta
                $seo_data = $this->getSEOData($db, $id);

                // ── Load existing FAQs from wp_postmeta ──────────────────
                $existingFaqs = [];
                $faqCountRow  = $db->query(
                    "SELECT meta_value FROM wp_postmeta
                    WHERE post_id = ? AND meta_key = '_faq_count' LIMIT 1",
                    [$id]
                )->getRow();

                if ($faqCountRow && (int)$faqCountRow->meta_value > 0) {
                    $faqCount = (int)$faqCountRow->meta_value;
                    for ($i = 0; $i < $faqCount; $i++) {
                        $faqMeta = $db->query(
                            "SELECT meta_key, meta_value FROM wp_postmeta
                            WHERE post_id = ?
                            AND meta_key IN (?, ?)
                            LIMIT 2",
                            [$id, '_faq_question_' . $i, '_faq_answer_' . $i]
                        )->getResultArray();

                        $q = '';
                        $a = '';
                        foreach ($faqMeta as $row) {
                            if ($row['meta_key'] === '_faq_question_' . $i) $q = $row['meta_value'];
                            if ($row['meta_key'] === '_faq_answer_'   . $i) $a = $row['meta_value'];
                        }
                        if ($q !== '' && $a !== '') {
                            $existingFaqs[] = ['question' => $q, 'answer' => $a];
                        }
                    }
                }

                return view('edit_post', [
                    'post'                => $post,
                    'categories'          => $categories,
                    'selected_categories' => $selected_categories,
                    'tags'                => $tags,
                    'authors'             => $authors,
                    'seo_data'            => $seo_data,
                    'existing_faqs'       => $existingFaqs,   //  FAQ
                ]);
            } else {
                return redirect()->to('/all-posts')->with('error', 'Post not found.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Edit Post Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Get SEO data from wp_postmeta
    private function getSEOData($db, $post_id)
    {
        // Check for both 'flypped' keys AND standard keys
        $search_map = [
            'title'       => ['_flypped_seo_title', '_meta_title', 'seo_title'],
            'description' => ['_flypped_meta_description', '_meta_description', 'meta_description'],
            'keywords'    => ['_flypped_focus_keywords', '_focus_keywords', 'focus_keywords'],
            'slug'        => ['_flypped_custom_slug', 'post_slug']
        ];

        $seo_data = [
            'title' => '', 'description' => '', 'keywords' => '', 'slug' => ''
        ];
        
        foreach ($search_map as $data_key => $possible_keys) {
            foreach ($possible_keys as $meta_key) {
                $query = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = ? LIMIT 1", [$post_id, $meta_key]);
                $result = $query->getRow();
                
                if ($result && !empty($result->meta_value)) {
                    $seo_data[$data_key] = $result->meta_value;
                    break; // Stop looking once found
                }
            }
        }

        // Fallback for slug if still empty
        if (empty($seo_data['slug'])) {
            $post = $db->query("SELECT post_name FROM wp_posts WHERE ID = ?", [$post_id])->getRow();
            $seo_data['slug'] = $post ? $post->post_name : '';
        }

        return $seo_data;
    }

    // Save SEO data to wp_postmeta
    private function saveSEOData($db, $post_id, $seo_data)
    {
        $seo_fields = [
            'seo_title' => '_flypped_seo_title',
            'meta_description' => '_flypped_meta_description',
            'focus_keywords' => '_flypped_focus_keywords',
            'post_slug' => '_flypped_custom_slug'
        ];

        foreach ($seo_fields as $field => $meta_key) {
            if (isset($seo_data[$field])) {
                $value = trim($seo_data[$field]);
                
                // Check if meta already exists
                $existing = $db->query("
                    SELECT meta_id 
                    FROM wp_postmeta 
                    WHERE post_id = ? AND meta_key = ?
                ", [$post_id, $meta_key])->getRow();

                if ($existing) {
                    // Update existing meta
                    $db->query("
                        UPDATE wp_postmeta 
                        SET meta_value = ? 
                        WHERE post_id = ? AND meta_key = ?
                    ", [$value, $post_id, $meta_key]);
                } else {
                    // Insert new meta
                    $db->query("
                        INSERT INTO wp_postmeta (post_id, meta_key, meta_value) 
                        VALUES (?, ?, ?)
                    ", [$post_id, $meta_key, $value]);
                }
            }
        }
    }

    // ── Save FAQs into wp_postmeta (same logic as Home::saveFaqs) ──
    private function updateFaqs($db, int $postId): void
    {
        $questions = $this->request->getPost('faq_questions') ?? [];
        $answers   = $this->request->getPost('faq_answers')   ?? [];

        // ── Always wipe old FAQ meta first (prevents duplicates) ──
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

        // If no FAQs submitted, just stop here (all deleted above)
        if (empty($questions) || !is_array($questions)) {
            return;
        }

        $seenQuestions = [];
        $cleanFaqs     = [];
        $order         = 0;

        foreach ($questions as $i => $question) {
            $question = trim((string)($question ?? ''));
            $answer   = trim((string)($answers[$i] ?? ''));

            if ($question === '' || $answer === '') continue;

            $key = strtolower($question);
            if (isset($seenQuestions[$key])) continue;
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

        if (empty($cleanFaqs)) return;

        // ── Save count ────────────────────────────────────────────
        $db->table('wp_postmeta')->insert([
            'post_id'    => $postId,
            'meta_key'   => '_faq_count',
            'meta_value' => count($cleanFaqs),
        ]);

        // ── Save compiled FAQ Schema JSON ─────────────────────────
        $db->table('wp_postmeta')->insert([
            'post_id'    => $postId,
            'meta_key'   => '_faq_schema',
            'meta_value' => json_encode([
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(function($faq) {
                    return [
                        '@type'          => 'Question',
                        'name'           => $faq['question'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
                    ];
                }, $cleanFaqs),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        log_message('info', "✓ FAQs saved for post {$postId}: " . count($cleanFaqs) . " FAQs");
    }

    public function deleteFeaturedImage($postId)
    {
        try {
            $db = \Config\Database::connect();
            $db->query("
                DELETE FROM wp_postmeta 
                WHERE post_id = ? AND meta_key = '_thumbnail_id'
            ", [(int)$postId]);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updatePost($id)
    {
        $db = \Config\Database::connect();
        $data = $this->request->getPost();

        log_message('info', "==========================================");
        log_message('info', "POST UPDATE STARTED FOR POST ID: {$id}");
        log_message('info', "==========================================");

        // Verify post exists
        $post = $db->table('wp_posts')->where('ID', $id)->get()->getRow();
        if (!$post) {
            log_message('error', "Post {$id} not found");
            return redirect()->to('/all-posts')->with('error', 'Post not found.');
        }

        // ========================================
        // FEATURED IMAGE UPLOAD - WITH WEBP SUPPORT
        // ========================================

        $imageFile = $this->request->getFile('featured_image');
        $imageUploaded = false;

        log_message('info', "Checking for featured image upload...");

        if ($imageFile !== null) {
            log_message('info', "File object exists");
            log_message('info', "File name: " . $imageFile->getName());
            log_message('info', "File valid: " . ($imageFile->isValid() ? 'YES' : 'NO'));
            log_message('info', "File moved: " . ($imageFile->hasMoved() ? 'YES' : 'NO'));

            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                try {
                    log_message('info', "Starting image upload process...");

                    // Get file info
                    $originalName = $imageFile->getName();
                    $fileSize = $imageFile->getSize();
                    $mimeType = $imageFile->getMimeType();

                    log_message('info', "Original: {$originalName}, Size: {$fileSize}, Type: {$mimeType}");

                    // Validate file size (5MB max)
                    if ($fileSize > 10 * 1024 * 1024) {
                        throw new \Exception("File too large. Maximum size is 5MB.");
                    }

                    //  FIXED: Added WebP support
                    $allowedTypes = [
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/gif',
                        'image/webp'  //  WebP support added
                    ];

                    if (!in_array($mimeType, $allowedTypes)) {
                        throw new \Exception("Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.");
                    }

                    log_message('info', "✓ File type validated: {$mimeType}");

                    // Set upload path FIRST so we can check for file existence
                    $uploadPath = ROOTPATH . 'admin/wp-content/uploads/';
                    log_message('info', "Upload path: {$uploadPath}");

                    // --- Keep original name and make it SEO friendly ---
                    $ext = $imageFile->getExtension();
                    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                    
                    $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
                    
                    if (empty($cleanName)) {
                        $cleanName = 'post-image-' . time();
                    }
                    
                    $newFilename = $cleanName . '.' . $ext;
                    
                    $counter = 1;
                    while (file_exists($uploadPath . $newFilename)) {
                        $newFilename = $cleanName . '-' . $counter . '.' . $ext;
                        $counter++;
                    }
                    // --------------------------------------------------

                    log_message('info', "New SEO-friendly filename: {$newFilename}");

                    // Create directory if needed
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                        log_message('info', "Created directory: {$uploadPath}");
                    }

                    // Verify directory is writable
                    if (!is_writable($uploadPath)) {
                        throw new \Exception("Upload directory is not writable: {$uploadPath}");
                    }

                    // Move the file
                    log_message('info', "Moving file...");
                    if ($imageFile->move($uploadPath, $newFilename)) {
                        log_message('info', "✓ File moved successfully");

                        // Verify file exists
                        $fullPath = $uploadPath . $newFilename;
                        if (!file_exists($fullPath)) {
                            throw new \Exception("File not found after move: {$fullPath}");
                        }

                        log_message('info', "✓ File verified at: {$fullPath}");

                        // Generate URL
                        $imageUrl = base_url('admin/wp-content/uploads/' . $newFilename);
                        log_message('info', "Image URL: {$imageUrl}");

                        //  Get image dimensions (WebP compatible)
                        $dimensions = @getimagesize($fullPath);
                        $width = 0;
                        $height = 0;

                        if ($dimensions !== false) {
                            $width = $dimensions[0];
                            $height = $dimensions[1];
                            log_message('info', "Dimensions: {$width}x{$height}");
                        } else {
                            log_message('warning', "Could not get image dimensions for: {$mimeType}");
                        }

                        // Delete old featured image if exists
                        $oldThumbQuery = $db->table('wp_postmeta')
                            ->where('post_id', $id)
                            ->where('meta_key', '_thumbnail_id')
                            ->get();

                        $oldThumb = $oldThumbQuery->getRow();

                        if ($oldThumb && !empty($oldThumb->meta_value)) {
                            $oldAttachId = $oldThumb->meta_value;
                            log_message('info', "Deleting old attachment ID: {$oldAttachId}");

                            // Get old attachment
                            $oldAttach = $db->table('wp_posts')
                                ->where('ID', $oldAttachId)
                                ->where('post_type', 'attachment')
                                ->get()
                                ->getRow();

                            if ($oldAttach) {
                                // Delete old file
                                $oldFile = str_replace(base_url(), ROOTPATH, $oldAttach->guid);
                                if (file_exists($oldFile)) {
                                    @unlink($oldFile);
                                    log_message('info', "✓ Deleted old file: {$oldFile}");
                                }

                                // Delete from database
                                $db->table('wp_posts')->delete(['ID' => $oldAttachId]);
                                $db->table('wp_postmeta')->delete(['post_id' => $oldAttachId]);
                                log_message('info', "✓ Deleted old attachment records");
                            }
                        }

                        // Insert new attachment in wp_posts
                        $attachmentData = [
                            'post_author' => $data['post_author'],
                            'post_date' => date('Y-m-d H:i:s'),
                            'post_date_gmt' => gmdate('Y-m-d H:i:s'),
                            'post_content' => '',
                            'post_title' => pathinfo($originalName, PATHINFO_FILENAME),
                            'post_excerpt' => '',
                            'post_status' => 'inherit',
                            'comment_status' => 'closed',
                            'ping_status' => 'closed',
                            'post_name' => pathinfo($newFilename, PATHINFO_FILENAME),
                            'post_modified' => date('Y-m-d H:i:s'),
                            'post_modified_gmt' => gmdate('Y-m-d H:i:s'),
                            'post_parent' => $id,
                            'guid' => $imageUrl,
                            'post_type' => 'attachment',
                            'post_mime_type' => $mimeType,
                        ];

                        $db->table('wp_posts')->insert($attachmentData);
                        $attachmentId = $db->insertID();

                        if (!$attachmentId) {
                            throw new \Exception("Failed to create attachment record");
                        }

                        log_message('info', "✓ Created attachment ID: {$attachmentId}");

                        // Insert attachment metadata
                        $db->table('wp_postmeta')->insert([
                            'post_id' => $attachmentId,
                            'meta_key' => '_wp_attached_file',
                            'meta_value' => 'admin/wp-content/uploads/' . $newFilename
                        ]);

                        $metaData = serialize([
                            'width' => $width,
                            'height' => $height,
                            'file' => 'admin/wp-content/uploads/' . $newFilename
                        ]);

                        $db->table('wp_postmeta')->insert([
                            'post_id' => $attachmentId,
                            'meta_key' => '_wp_attachment_metadata',
                            'meta_value' => $metaData
                        ]);

                        log_message('info', "✓ Inserted attachment metadata");

                        // Set as featured image
                        if ($oldThumb) {
                            // Update existing
                            $db->table('wp_postmeta')
                                ->where('post_id', $id)
                                ->where('meta_key', '_thumbnail_id')
                                ->update(['meta_value' => $attachmentId]);
                            log_message('info', "✓ Updated _thumbnail_id");
                        } else {
                            // Insert new
                            $db->table('wp_postmeta')->insert([
                                'post_id' => $id,
                                'meta_key' => '_thumbnail_id',
                                'meta_value' => $attachmentId
                            ]);
                            log_message('info', "✓ Inserted _thumbnail_id");
                        }

                        $imageUploaded = true;
                        log_message('info', "========== IMAGE UPLOAD SUCCESS ==========");

                    } else {
                        throw new \Exception("Failed to move file: " . $imageFile->getErrorString());
                    }

                } catch (\Exception $e) {
                    log_message('error', "IMAGE UPLOAD FAILED: " . $e->getMessage());
                    log_message('error', $e->getTraceAsString());
                    return redirect()->back()
                        ->with('error', 'Image upload failed: ' . $e->getMessage())
                        ->withInput();
                }
            } else {
                if (!$imageFile->isValid()) {
                    log_message('warning', "Invalid file: " . $imageFile->getErrorString());
                }
            }
        } else {
            log_message('info', "No image file uploaded");
        }

        // ========================================
        // UPDATE POST DATA & DATE HANDLING
        // ========================================

        log_message('info', "Updating post data...");

        $postSlug = !empty($data['post_slug']) ? 
            $this->sanitizeSlug($data['post_slug']) : 
            $this->generateSlug($data['post_title']);

        $currentTime = date('Y-m-d H:i:s');
        $currentTimeGmt = gmdate('Y-m-d H:i:s');
        
        // 1. Date Handling (Using the standard post_date input)
        if (!empty($data['post_date'])) {
            // Use the calendar date but retain the original time so it doesn't reset to midnight
            $originalTime = date('H:i:s', strtotime($post->post_date));
            $publishTime = date('Y-m-d', strtotime($data['post_date'])) . ' ' . $originalTime;
            $publishTimeGmt = gmdate('Y-m-d H:i:s', strtotime($publishTime));
        } else {
            // Fallback to exactly what is in the database
            $publishTime = $post->post_date;
            $publishTimeGmt = $post->post_date_gmt;
        }

        // 2. Validate and set the Status (Restricted to just publish and draft)
        $submittedStatus = $data['post_status'] ?? 'publish';
        $allowedStatuses = ['publish', 'draft'];
        $postStatus = in_array($submittedStatus, $allowedStatuses) ? $submittedStatus : 'publish';

        // 3. Safeguard: If they select "Publish" but pick a date in the future, auto-switch to "future"
        if ($postStatus === 'publish' && $publishTime > $currentTime) {
            $postStatus = 'future';
        }

        $updateData = [
            'post_title' => $data['post_title'],
            'post_content' => $data['post_content'],
            'post_author' => $data['post_author'],
            'post_date' => $publishTime,
            'post_date_gmt' => $publishTimeGmt,
            'post_modified' => $currentTime,
            'post_modified_gmt' => $currentTimeGmt,
            'post_status' => $postStatus,
            'post_name' => $postSlug,
        ];

        $db->table('wp_posts')->where('ID', $id)->update($updateData);
        log_message('info', "✓ Post data updated");

        // Save SEO data
        $this->saveSEOData($db, $id, $data);

        // ── Save FAQs ─────────────────────────────────────────────
        $this->updateFaqs($db, $id);
        log_message('info', "✓ FAQs saved");

        // ========================================
        // UPDATE CATEGORIES - FIXED VERSION
        // ========================================

        log_message('info', "Updating categories...");

        // Step 1: Delete ALL existing category relationships for this post
        try {
            $db->query("
                DELETE FROM wp_term_relationships 
                WHERE object_id = ? 
                AND term_taxonomy_id IN (
                    SELECT term_taxonomy_id 
                    FROM wp_term_taxonomy 
                    WHERE taxonomy = 'category'
                )
            ", [$id]);
            
            log_message('info', "✓ Deleted existing category relationships");
            
        } catch (\Exception $e) {
            log_message('error', "Error deleting categories: " . $e->getMessage());
        }

        // Step 2: Insert fresh categories (no duplicates possible)
        if (!empty($data['categories'])) {
            $insertCount = 0;
            foreach ($data['categories'] as $categoryId) {
                try {
                    $db->query("
                        INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
                        VALUES (?, ?, 0)
                    ", [$id, $categoryId]);
                    $insertCount++;
                } catch (\Exception $e) {
                    log_message('error', "Error inserting category {$categoryId}: " . $e->getMessage());
                }
            }
            log_message('info', "✓ Inserted {$insertCount} categories");
        }

        // Step 3: Update category post counts
        try {
            $db->query("
                UPDATE wp_term_taxonomy tt
                SET count = (
                    SELECT COUNT(*) 
                    FROM wp_term_relationships tr 
                    WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
                )
                WHERE taxonomy = 'category'
            ");
            log_message('info', "✓ Category counts updated");
        } catch (\Exception $e) {
            log_message('error', "Error updating category counts: " . $e->getMessage());
        }

        // ========================================
        // UPDATE TAGS
        // ========================================

        if (isset($data['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', $data['tags'])));

            $db->query("
                DELETE FROM wp_term_relationships 
                WHERE object_id = ? 
                AND term_taxonomy_id IN (
                    SELECT term_taxonomy_id 
                    FROM wp_term_taxonomy 
                    WHERE taxonomy = 'post_tag'
                )
            ", [$id]);

            foreach ($tags as $tagName) {
                if (empty($tagName)) continue;

                $existingTag = $db->table('wp_terms')->where('name', $tagName)->get()->getRow();

                if ($existingTag) {
                    $tagId = $existingTag->term_id;
                } else {
                    $db->table('wp_terms')->insert([
                        'name' => $tagName,
                        'slug' => $this->sanitizeSlug($tagName)
                    ]);
                    $tagId = $db->insertID();

                    $db->table('wp_term_taxonomy')->insert([
                        'term_id' => $tagId,
                        'taxonomy' => 'post_tag',
                        'description' => '',
                        'parent' => 0,
                        'count' => 0
                    ]);
                }

                $termTax = $db->table('wp_term_taxonomy')
                    ->where('term_id', $tagId)
                    ->where('taxonomy', 'post_tag')
                    ->get()
                    ->getRow();

                if ($termTax) {
                    $db->query("
                        INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
                        VALUES (?, ?, 0)
                    ", [$id, $termTax->term_taxonomy_id]);
                }
            }
            log_message('info', "✓ Tags updated");
        }

        log_message('info', "==========================================");
        log_message('info', "POST UPDATE COMPLETED FOR POST ID: {$id}");
        log_message('info', "Image uploaded: " . ($imageUploaded ? 'YES' : 'NO'));
        log_message('info', "==========================================");

        $successMessage = 'Post updated successfully!';
        if ($imageUploaded) {
            $successMessage .= ' Featured image has been updated.';
        }

        // =======================================================
        //  3. INDEXNOW TRIGGER HONE PAR YAHAN CHALEGA
        // =======================================================
        if (!empty($postSlug) && $postStatus === 'publish') {
            $updated_url = "https://flyppedhindi.com/" . $postSlug;
            $this->submitToIndexNow([$updated_url]);
            log_message('info', 'IndexNow triggered for updated post: ' . $updated_url);
        }

        return redirect()->to('/all-posts')->with('success', $successMessage);
    }

    // Generate slug from title
    private function generateSlug($title)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    // Sanitize slug input
    private function sanitizeSlug($slug)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug), '-'));
    }

    // AJAX endpoint to get SEO analysis
    public function getSEOAnalysis()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $post_id = $this->request->getPost('post_id');
            $content = $this->request->getPost('content');
            $title = $this->request->getPost('title');
            $keywords = $this->request->getPost('keywords');

            if (empty($post_id) || empty($content)) {
                return $this->response->setJSON(['error' => 'Missing required parameters']);
            }

            // Analyze content for SEO
            $analysis = $this->analyzeSEOContent($content, $title, $keywords);
            
            return $this->response->setJSON([
                'success' => true,
                'analysis' => $analysis
            ]);

        } catch (\Exception $e) {
            log_message('error', 'SEO Analysis Error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Analysis failed']);
        }
    }

    // Analyze content for SEO factors
    private function analyzeSEOContent($content, $title, $keywords)
    {
        $analysis = [
            'word_count' => 0,
            'keyword_density' => [],
            'readability_score' => 0,
            'suggestions' => []
        ];

        // Clean content and count words
        $clean_content = strip_tags($content);
        $words = str_word_count($clean_content);
        $analysis['word_count'] = $words;

        // Analyze keywords if provided
        if (!empty($keywords)) {
            $keyword_list = array_map('trim', explode(',', $keywords));
            
            foreach ($keyword_list as $keyword) {
                if (empty($keyword)) continue;
                
                $keyword_lower = strtolower($keyword);
                $content_lower = strtolower($clean_content);
                $title_lower = strtolower($title);
                
                // Count occurrences
                $content_count = substr_count($content_lower, $keyword_lower);
                $title_count = substr_count($title_lower, $keyword_lower);
                
                // Calculate density (percentage)
                $density = $words > 0 ? ($content_count / $words) * 100 : 0;
                
                $analysis['keyword_density'][$keyword] = [
                    'count' => $content_count,
                    'title_count' => $title_count,
                    'density' => round($density, 2),
                    'status' => $this->getKeywordStatus($density, $title_count)
                ];
            }
        }

        // Generate suggestions
        $analysis['suggestions'] = $this->generateSEOSuggestions($analysis, $title, $clean_content);

        return $analysis;
    }

    // Get keyword status based on density and title presence
    private function getKeywordStatus($density, $title_count)
    {
        if ($title_count == 0) {
            return 'warning'; // Keyword not in title
        }
        
        if ($density < 0.5) {
            return 'low'; // Too low density
        } elseif ($density > 2.5) {
            return 'high'; // Too high density (keyword stuffing)
        } else {
            return 'good'; // Good density
        }
    }

    // Generate SEO suggestions
    private function generateSEOSuggestions($analysis, $title, $content)
    {
        $suggestions = [];

        // Word count suggestions
        if ($analysis['word_count'] < 300) {
            $suggestions[] = "Consider adding more content. Aim for at least 300 words.";
        } elseif ($analysis['word_count'] > 2000) {
            $suggestions[] = "Content is quite long. Consider breaking it into smaller sections.";
        }

        // Keyword suggestions
        foreach ($analysis['keyword_density'] as $keyword => $data) {
            if ($data['title_count'] == 0) {
                $suggestions[] = "Include the keyword '{$keyword}' in your title for better SEO.";
            }
            
            if ($data['density'] < 0.5) {
                $suggestions[] = "Consider using the keyword '{$keyword}' more frequently in your content.";
            } elseif ($data['density'] > 2.5) {
                $suggestions[] = "The keyword '{$keyword}' may be overused. Consider reducing its frequency.";
            }
        }

        // Structure suggestions
        if (substr_count($content, '<h') < 2) {
            $suggestions[] = "Add more headings (H2, H3) to improve content structure.";
        }

        return $suggestions;
    }

    // Get SEO preview data for a post
    public function getSEOPreview($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400);
            }

            $db = \Config\Database::connect();
            
            // Get post data
            $post = $db->query("SELECT * FROM wp_posts WHERE ID = ?", [$id])->getRow();
            
            if (!$post) {
                return $this->response->setJSON(['error' => 'Post not found']);
            }

            // Get SEO data
            $seo_data = $this->getSEOData($db, $id);
            
            // Prepare preview data
            $preview = [
                'title' => !empty($seo_data['title']) ? $seo_data['title'] : $post->post_title,
                'description' => !empty($seo_data['description']) ? $seo_data['description'] : substr(strip_tags($post->post_content), 0, 160),
                'url' => base_url() . '/' . (!empty($seo_data['slug']) ? $seo_data['slug'] : $post->post_name),
                'keywords' => $seo_data['keywords'] ?? ''
            ];

            return $this->response->setJSON([
                'success' => true,
                'preview' => $preview
            ]);

        } catch (\Exception $e) {
            log_message('error', 'SEO Preview Error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Preview generation failed']);
        }
    }
}