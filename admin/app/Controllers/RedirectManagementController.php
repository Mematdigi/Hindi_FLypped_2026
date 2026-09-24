<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class RedirectManagementController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Extract clean path from URL — strips hi.flypped.com domain
     */

    private function extractPath($url)
    {
        $url = trim($url);
        $url = preg_replace('#^https?://(www\.)?hindi\.flypped\.com/?#i', '', $url);
        $url = preg_replace('#^https?://(www\.)?hi\.flypped\.com/?#i', '', $url);  
        $url = preg_replace('#^(www\.)?hindi\.flypped\.com/?#i', '', $url);
        $url = preg_replace('#^(www\.)?hi\.flypped\.com/?#i', '', $url);  
        $url = preg_replace('#^hindi\.flypped/?#i', '', $url);
        $url = preg_replace('#^hi\.flypped/?#i', '', $url);  
        $url = preg_replace('#^flypped/?#i', '', $url);
        $url = preg_replace('#^index\.php/?#i', '', $url);
        $url = preg_replace('/[?#].*$/', '', $url);
        $url = strtolower(trim($url, '/'));
        return $url;
    }

    /**
     * Extract ONLY the post slug (last segment)
     * Hindi has NO category in URLs
     * "technology/my-post" → "my-post"
     * "my-post"           → "my-post"
     */
    private function extractPostSlug($fullPath)
    {
        $parts = explode('/', trim($fullPath, '/'));
        return end($parts);
    }

    /**
     * Display redirect management page
     */
    public function index()
    {
        $redirects = $this->db->table('url_redirects')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        return view('admin/redirect_management', ['redirects' => $redirects]);
    }

    /**
     * Update Post URL & Create Redirect
     * Source: old-slug OR category/old-slug (whatever user types)
     * Destination: always just new-slug (NO category)
     */
    public function addRedirect()
    {
        $oldSlug    = $this->extractPath($this->request->getPost('old_url'));
        $newSlugRaw = $this->extractPath($this->request->getPost('new_url'));
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (empty($oldSlug) || empty($newSlugRaw)) {
            return redirect()->back()->with('error', 'Both Old URL and New URL are required')->withInput();
        }

        $newPostSlug = $this->extractPostSlug($newSlugRaw);
        $oldPostSlug = $this->extractPostSlug($oldSlug);

        if ($oldSlug === $newPostSlug) {
            return redirect()->back()->with('error', 'Old URL and New URL cannot be the same')->withInput();
        }

        try {
            $this->db->transStart();

            $post = $this->db->query("
                SELECT ID, post_name FROM wp_posts
                WHERE post_name = ?
                AND post_status = 'publish'
                AND post_type = 'post'
                LIMIT 1
            ", [$oldPostSlug])->getRow();

            if ($post) {
                $this->db->query("UPDATE wp_posts SET post_name = ? WHERE ID = ?", [$newPostSlug, $post->ID]);
                log_message('info', " Updated post ID {$post->ID}: '{$oldPostSlug}' → '{$newPostSlug}'");
            } else {
                log_message('warning', "⚠️ No post found with slug: '{$oldPostSlug}'. Redirect only.");
            }

            $existing = $this->db->table('url_redirects')->where('old_slug', $oldSlug)->get()->getRow();
            if ($existing) {
                throw new \Exception('A redirect for this Old URL already exists');
            }

            //  Source = user input (can have category), Destination = just slug
            $this->db->table('url_redirects')->insert([
                'old_slug'    => $oldSlug,
                'new_slug'    => $newPostSlug,
                'status_code' => $statusCode,
                'hits'        => 0,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) throw new \Exception('Transaction failed');

            $message = $post
                ? ' Post slug updated and redirect created successfully!'
                : ' Redirect created (no matching post found to update)';

            return redirect()->to('redirects')->with('success', $message);

        } catch (\Exception $e) {
            log_message('error', '❌ Add Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Change Category in DB + Create Redirect
     * Source: old-category/post-slug
     * Destination: post-slug ONLY (no category in Hindi URL)
     */
    public function addCategoryRedirect()
    {
        $postSlug    = $this->extractPostSlug(strtolower(trim($this->request->getPost('post_slug'), '/')));
        $newCategory = strtolower(trim($this->request->getPost('category'), '/'));
        $statusCode  = $this->request->getPost('redirect_type') ?? '301';

        if (empty($postSlug) || empty($newCategory)) {
            return redirect()->back()->with('error', 'Both fields are required')->withInput();
        }

        try {
            $post = $this->db->query("
                SELECT ID, post_name FROM wp_posts
                WHERE post_name = ? AND post_status = 'publish' AND post_type = 'post' LIMIT 1
            ", [$postSlug])->getRow();

            if (!$post) {
                return redirect()->back()->with('error', "❌ No post found with slug: '{$postSlug}'")->withInput();
            }

            $currentCat = $this->db->query("
                SELECT t.slug, tt.term_taxonomy_id
                FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ? AND tt.taxonomy = 'category' LIMIT 1
            ", [$post->ID])->getRow();

            $currentCategorySlug   = $currentCat ? $currentCat->slug : null;
            $currentTermTaxonomyId = $currentCat ? $currentCat->term_taxonomy_id : null;

            if ($currentCategorySlug === $newCategory) {
                return redirect()->back()->with('error', "❌ Post is already in '{$newCategory}'")->withInput();
            }

            $newCat = $this->db->query("
                SELECT tt.term_taxonomy_id FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                WHERE t.slug = ? AND tt.taxonomy = 'category' LIMIT 1
            ", [$newCategory])->getRow();

            if (!$newCat) {
                return redirect()->back()->with('error', "❌ Category '{$newCategory}' not found")->withInput();
            }

            //  Source has old category prefix, destination is just slug
            $oldSlug = ($currentCategorySlug ? $currentCategorySlug . '/' : '') . $postSlug;
            $newSlug = $postSlug; //  NO category in Hindi destination

            $this->db->query("DELETE FROM url_redirects WHERE old_slug = ? OR old_slug = ? OR new_slug = ? OR new_slug = ?",
                [$oldSlug, $newSlug, $oldSlug, $newSlug]);

            $this->db->query("DELETE FROM wp_term_relationships WHERE object_id = ? AND term_taxonomy_id = ?",
                [$post->ID, $currentTermTaxonomyId]);

            $this->db->query("INSERT IGNORE INTO wp_term_relationships (object_id, term_taxonomy_id, term_order) VALUES (?, ?, 0)",
                [$post->ID, $newCat->term_taxonomy_id]);

            $verify = $this->db->query("
                SELECT t.slug FROM wp_terms t
                JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ? AND tt.taxonomy = 'category' LIMIT 1
            ", [$post->ID])->getRow();

            if (!$verify || $verify->slug !== $newCategory) {
                return redirect()->back()->with('error', "❌ Category update FAILED. Got: '" . ($verify->slug ?? 'null') . "'")->withInput();
            }

            $this->db->query("INSERT INTO url_redirects (old_slug, new_slug, status_code, hits, created_at) VALUES (?, ?, ?, 0, NOW())",
                [$oldSlug, $newSlug, $statusCode]);

            return redirect()->to('redirects')
                ->with('success', " Category changed: '{$currentCategorySlug}' → '{$newCategory}' | Redirect: {$oldSlug} → {$newSlug}");

        } catch (\Exception $e) {
            log_message('error', '❌ Category Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Full redirect — any source to any destination
     * Hindi: destination = just slug
     */
    public function addFullRedirect()
    {
        $fromUrl    = $this->extractPath($this->request->getPost('from_url'));
        $toPostSlug = $this->extractPostSlug($this->extractPath($this->request->getPost('to_url')));
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (empty($fromUrl) || empty($toPostSlug)) {
            return redirect()->back()->with('error', 'Both From and To URLs are required')->withInput();
        }
        if ($fromUrl === $toPostSlug) {
            return redirect()->back()->with('error', 'From and To URLs cannot be the same')->withInput();
        }

        try {
            $existing = $this->db->table('url_redirects')->where('old_slug', $fromUrl)->get()->getRow();
            if ($existing) {
                return redirect()->back()->with('error', 'A redirect for this URL already exists')->withInput();
            }

            $this->db->table('url_redirects')->insert([
                'old_slug'    => $fromUrl,
                'new_slug'    => $toPostSlug,
                'status_code' => $statusCode,
                'hits'        => 0,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('redirects')->with('success', " Redirect created: {$fromUrl} → {$toPostSlug}");

        } catch (\Exception $e) {
            log_message('error', '❌ Full Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Redirect any URL to homepage
     */
    public function addHomepageRedirect()
    {
        $fromUrl    = $this->extractPath($this->request->getPost('from_url'));
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (empty($fromUrl)) {
            return redirect()->back()->with('error', 'From URL is required')->withInput();
        }

        try {
            $existing = $this->db->table('url_redirects')->where('old_slug', $fromUrl)->get()->getRow();
            if ($existing) {
                return redirect()->back()->with('error', 'A redirect for this URL already exists')->withInput();
            }

            $this->db->table('url_redirects')->insert([
                'old_slug'    => $fromUrl,
                'new_slug'    => '/',
                'status_code' => $statusCode,
                'hits'        => 0,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('redirects')->with('success', " {$fromUrl} will now redirect to homepage");

        } catch (\Exception $e) {
            log_message('error', '❌ Homepage Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }


    public function addUrlMapping()
    {
        $fromUrl    = $this->extractPath($this->request->getPost('from_url'));
        $toPostSlug = $this->extractPostSlug($this->extractPath($this->request->getPost('to_url')));
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (empty($fromUrl) || empty($toPostSlug)) {
            return redirect()->back()->with('error', 'Both From and To URLs are required')->withInput();
        }
        if ($fromUrl === $toPostSlug) {
            return redirect()->back()->with('error', 'From and To URLs cannot be the same')->withInput();
        }

        try {

            $destPost = $this->db->query("
                SELECT ID FROM wp_posts
                WHERE post_name = ? AND post_status = 'publish' AND post_type = 'post' LIMIT 1
            ", [$toPostSlug])->getRow();

            if (!$destPost) {
                return redirect()->back()->with('error', "❌ Destination post '{$toPostSlug}' does NOT exist in database.")->withInput();
            }

            $loopCheck = $this->db->query("SELECT id FROM url_redirects WHERE old_slug = ? AND new_slug = ? LIMIT 1",
                [$toPostSlug, $fromUrl])->getRow();
            if ($loopCheck) {
                return redirect()->back()->with('error', "❌ Loop detected! Delete the existing reverse redirect first.")->withInput();
            }

            $existing = $this->db->query("SELECT id FROM url_redirects WHERE old_slug = ? LIMIT 1", [$fromUrl])->getRow();
            if ($existing) {
                return redirect()->back()->with('error', "❌ A redirect for '{$fromUrl}' already exists.")->withInput();
            }

            $this->db->query("INSERT INTO url_redirects (old_slug, new_slug, status_code, hits, created_at) VALUES (?, ?, ?, 0, NOW())",
                [$fromUrl, $toPostSlug, $statusCode]);

            return redirect()->to('redirects')
                ->with('success', " URL Mapped: '{$fromUrl}' → '{$toPostSlug}' (Post ID: {$destPost->ID})");

        } catch (\Exception $e) {
            log_message('error', '❌ URL Mapping Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Bulk upload redirects via Excel
     * Hindi: destination always just slug (no category)
     */
    public function bulkUpload()
    {
        $file       = $this->request->getFile('excel_file');
        $toUrl      = trim($this->request->getPost('to_url')) ?: '/';
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid Excel file')->withInput();
        }

        // --- Keep original name and sanitize ---
        $originalName = $file->getName();
        $ext = $file->getExtension();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
        
        if (empty($cleanName)) {
            $cleanName = 'upload-' . time();
        }
        
        $newName = $cleanName . '.' . $ext;
        
        $counter = 1;
        while (file_exists(WRITEPATH . 'uploads/' . $newName)) {
            $newName = $cleanName . '-' . $counter . '.' . $ext;
            $counter++;
        }
        // --------------------------------------

        $file->move(WRITEPATH . 'uploads/', $newName);
        $tempPath = WRITEPATH . 'uploads/' . $newName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $inserted = 0;
            $skipped  = 0;
            $errors   = 0;

            if ($statusCode === '410') {
                $destSlug = '__410__';
            } elseif ($toUrl === '/' || empty($toUrl)) {
                $destSlug = '/';  
            } else {
                $destSlug = $this->extractPostSlug($toUrl);
            }

            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $fullUrl = trim($row[0] ?? '');
                if (empty($fullUrl)) continue;

                $fromUrl = $this->extractPath($fullUrl);
                if (empty($fromUrl)) { $skipped++; continue; }

                $existing = $this->db->table('url_redirects')->where('old_slug', $fromUrl)->get()->getRow();

                if ($existing) {
                    $this->db->table('url_redirects')->where('id', $existing->id)->update([
                        'new_slug'    => $destSlug,
                        'status_code' => $statusCode,
                    ]);
                    $skipped++;
                    continue;
                }

                try {
                    $this->db->table('url_redirects')->insert([
                        'old_slug'    => $fromUrl,
                        'new_slug'    => $destSlug,
                        'status_code' => $statusCode,
                        'hits'        => 0,
                        'created_at'  => date('Y-m-d H:i:s')
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    $errors++;
                    log_message('error', 'Bulk insert error row ' . $index . ': ' . $e->getMessage());
                }
            }

            @unlink($tempPath);

            return redirect()->to('redirects')
                ->with('success', " Bulk upload complete! Inserted: {$inserted} | Updated (existing): {$skipped} | Errors: {$errors}");

        } catch (\Exception $e) {
            @unlink($tempPath);
            log_message('error', 'Bulk Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process Excel: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Single post delete
     * Hindi: find by slug only (no category)
     */
    public function deleteSinglePost()
    {
        $fullUrl  = trim($this->request->getPost('delete_url'));

        if (empty($fullUrl)) {
            return redirect()->back()->with('error', 'URL is required')->withInput();
        }

        $path     = $this->extractPath($fullUrl);
        $postSlug = $this->extractPostSlug($path);

        try {
            $post = $this->db->query("
                SELECT ID FROM wp_posts
                WHERE post_name = ? AND post_status = 'publish' AND post_type = 'post' LIMIT 1
            ", [$postSlug])->getRow();

            if (!$post) {
                return redirect()->back()->with('error', "❌ No post found with slug: '{$postSlug}'")->withInput();
            }

            $postId = $post->ID;

            $thumbMeta = $this->db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = '_thumbnail_id' LIMIT 1", [$postId])->getRow();
            if ($thumbMeta && !empty($thumbMeta->meta_value)) {
                $attachId = (int)$thumbMeta->meta_value;
                $attach   = $this->db->query("SELECT guid FROM wp_posts WHERE ID = ? AND post_type = 'attachment' LIMIT 1", [$attachId])->getRow();
                if ($attach && !empty($attach->guid)) {
                    $filePath = str_replace(base_url(), ROOTPATH . 'public/', $attach->guid);
                    if (file_exists($filePath)) @unlink($filePath);
                    $this->db->query("DELETE FROM wp_postmeta WHERE post_id = ?", [$attachId]);
                    $this->db->query("DELETE FROM wp_posts WHERE ID = ?", [$attachId]);
                }
            }

            $this->db->query("DELETE FROM wp_postmeta WHERE post_id = ?", [$postId]);
            $this->db->query("DELETE FROM wp_term_relationships WHERE object_id = ?", [$postId]);
            $this->db->query("DELETE FROM wp_posts WHERE ID = ?", [$postId]);
            $this->db->query("DELETE FROM url_redirects WHERE old_slug = ? OR new_slug = ?", [$path, $postSlug]);

            return redirect()->to('redirects')->with('success', " Post deleted: '{$postSlug}' (ID: {$postId})");

        } catch (\Exception $e) {
            log_message('error', 'Single Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Bulk delete posts via Excel
     * Hindi: find by slug only (no category)
      */
    // public function bulkDeletePosts()
    // {
    //     $file = $this->request->getFile('delete_excel_file');

    //     if (!$file || !$file->isValid()) {
    //         return redirect()->back()->with('error', 'Please upload a valid Excel file')->withInput();
    //     }

    //     // --- Keep original name and sanitize ---
    //     $originalName = $file->getName();
    //     $ext = $file->getExtension();
    //     $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    //     $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
        
    //     if (empty($cleanName)) {
    //         $cleanName = 'delete-' . time();
    //     }
        
    //     $newName = $cleanName . '.' . $ext;
        
    //     $counter = 1;
    //     while (file_exists(WRITEPATH . 'uploads/' . $newName)) {
    //         $newName = $cleanName . '-' . $counter . '.' . $ext;
    //         $counter++;
    //     }
    //     // --------------------------------------

    //     $file->move(WRITEPATH . 'uploads/', $newName);
    //     $tempPath = WRITEPATH . 'uploads/' . $newName;

    //     try {

    //             //  Read file — supports both CSV and Excel (if PhpSpreadsheet installed)
    //             $rows = [];

    //             if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
    //                 require_once ROOTPATH . 'vendor/autoload.php';
    //                 $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempPath);
    //                 $sheet       = $spreadsheet->getActiveSheet();
    //                 $rows        = $sheet->toArray();
    //             } else {
    //                 //  CSV fallback — no composer needed
    //                 // Convert xlsx to readable format using PHP's built-in
    //                 // Ask user to upload CSV instead
    //                 @unlink($tempPath);
    //                 return redirect()->back()
    //                     ->with('error', 'PhpSpreadsheet not installed. Please upload a CSV file (.csv) instead of Excel.')
    //                     ->withInput();
    //             }

    //         $deleted = 0;
    //         $skipped = 0;
    //         $errors  = 0;

    //         foreach ($rows as $index => $row) {
    //             if ($index === 0) continue;

    //             $fullUrl  = trim($row[0] ?? '');
    //             if (empty($fullUrl)) continue;

    //             $path     = $this->extractPath($fullUrl);
    //             $postSlug = $this->extractPostSlug($path);

    //             if (empty($postSlug)) { $skipped++; continue; }

    //             try {
    //                 $post = $this->db->query("
    //                     SELECT ID FROM wp_posts
    //                     WHERE post_name = ? AND post_status = 'publish' AND post_type = 'post' LIMIT 1
    //                 ", [$postSlug])->getRow();

    //                 if (!$post) { $skipped++; continue; }

    //                 $postId = $post->ID;

    //                 $thumbMeta = $this->db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = '_thumbnail_id' LIMIT 1", [$postId])->getRow();
    //                 if ($thumbMeta && !empty($thumbMeta->meta_value)) {
    //                     $attachId = (int)$thumbMeta->meta_value;
    //                     $attach   = $this->db->query("SELECT guid FROM wp_posts WHERE ID = ? AND post_type = 'attachment' LIMIT 1", [$attachId])->getRow();
    //                     if ($attach && !empty($attach->guid)) {
    //                         $filePath = str_replace(base_url(), ROOTPATH . 'public/', $attach->guid);
    //                         if (file_exists($filePath)) @unlink($filePath);
    //                         $this->db->query("DELETE FROM wp_postmeta WHERE post_id = ?", [$attachId]);
    //                         $this->db->query("DELETE FROM wp_posts WHERE ID = ?", [$attachId]);
    //                     }
    //                 }

    //                 $this->db->query("DELETE FROM wp_postmeta WHERE post_id = ?", [$postId]);
    //                 $this->db->query("DELETE FROM wp_term_relationships WHERE object_id = ?", [$postId]);
    //                 $this->db->query("DELETE FROM wp_posts WHERE ID = ?", [$postId]);
    //                 $this->db->query("DELETE FROM url_redirects WHERE old_slug = ? OR new_slug = ?", [$path, $postSlug]);

    //                 $deleted++;

    //             } catch (\Exception $e) {
    //                 $errors++;
    //                 log_message('error', 'Bulk Delete row error: ' . $e->getMessage());
    //             }
    //         }

    //         @unlink($tempPath);

    //         return redirect()->to('redirects')
    //             ->with('success', " Bulk Delete Complete! Deleted: {$deleted} | Skipped: {$skipped} | Errors: {$errors}");

    //     } catch (\Exception $e) {
    //         @unlink($tempPath);
    //         log_message('error', 'Bulk Delete Fatal: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
    //     }
    // }

    /**
     * Update existing redirect
     * Hindi: destination is always just slug
     */
    public function updateRedirect()
    {
        $id         = $this->request->getPost('meta_id');
        $oldSlug    = $this->extractPath($this->request->getPost('old_url'));
        $newSlug    = $this->extractPostSlug($this->extractPath($this->request->getPost('new_url')));
        $statusCode = $this->request->getPost('redirect_type') ?? '301';

        if (empty($id) || empty($oldSlug) || empty($newSlug)) {
            return redirect()->back()->with('error', 'All fields are required');
        }

        try {
            $this->db->table('url_redirects')->where('id', $id)->update([
                'old_slug'    => $oldSlug,
                'new_slug'    => $newSlug,
                'status_code' => $statusCode
            ]);

            return redirect()->to('redirects')->with('success', ' Redirect updated successfully');

        } catch (\Exception $e) {
            log_message('error', '❌ Update Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update redirect');
        }
    }

    /**
     * Delete redirect
     */
    public function deleteRedirect($id)
    {
        try {
            $redirect = $this->db->table('url_redirects')->where('id', $id)->get()->getRow();
            if ($redirect) {
                $this->db->table('url_redirects')->where('id', $id)->delete();
                return redirect()->to('redirects')->with('success', '🗑️ Redirect deleted successfully');
            } else {
                return redirect()->back()->with('error', 'Redirect not found');
            }
        } catch (\Exception $e) {
            log_message('error', '❌ Delete Redirect Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting redirect');
        }
    }
}