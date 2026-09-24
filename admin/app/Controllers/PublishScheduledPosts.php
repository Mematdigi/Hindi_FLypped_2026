<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PublishScheduledPosts extends Controller
{
    /**
     * AUTO-PUBLISHER: Automatically publishes scheduled posts
     * * This controller finds all posts with post_status='future' where:
     * 1. The scheduled date is in the past, OR
     * 2. The scheduled date is today and the scheduled time has already passed
     * * Then it changes them to post_status='publish'
     * * USAGE:
     * 1. Via Cron Job (Recommended):
     * * * * * * cd /path/to/flypped && php index.php PublishScheduledPosts index
     * * 2. Via URL (with online cron service):
     * Add route: $routes->get('cron/publish', 'PublishScheduledPosts::index');
     * Then setup cron-job.org to hit: https://yourdomain.com/cron/publish
     */
    public function index()
    {
        
            // Allow access from localhost/server (cron jobs)
    $allowedIPs = ['127.0.0.1', 'localhost', '::1', $_SERVER['SERVER_ADDR']];
    
    if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
        // For non-local access, check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

        $db = \Config\Database::connect();
        $currentTime = date('Y-m-d H:i:s');
        
        try {
            // Find all scheduled posts (status='future') where:
            // (1) post_date is before today, OR
            // (2) post_date is today but scheduled time has already passed
            //  UPDATED: Also fetching post_name (slug) to create URL for IndexNow
            $query = $db->query(
                "SELECT ID, post_title, post_date, post_name 
                 FROM wp_posts 
                 WHERE post_status = 'future'
                   AND (
                        DATE(post_date) < CURDATE() 
                        OR (DATE(post_date) = CURDATE() AND TIME(post_date) <= CURTIME())
                       )
                   AND post_type = 'post'
                 ORDER BY post_date ASC"
            );
            
            $posts = $query->getResult();
            $publishedCount = 0;
            $publishedPosts = [];
            $urlsToSubmit = []; //  Array to collect URLs for IndexNow
            
            // Publish each scheduled post
            foreach ($posts as $post) {
                $updateQuery = $db->query(
                    "UPDATE wp_posts 
                     SET post_status = 'publish',
                         post_modified = NOW(),
                         post_modified_gmt = UTC_TIMESTAMP()
                     WHERE ID = ?",
                    [$post->ID]
                );
                
                if ($updateQuery) {
                    $publishedCount++;
                    $publishedPosts[] = [
                        'id' => $post->ID,
                        'title' => $post->post_title,
                        'scheduled_for' => $post->post_date
                    ];
                    
                    //  Build the URL for the newly published post
                    $post_slug = !empty($post->post_name) ? $post->post_name : sanitize_title($post->post_title);
                    $post_url = "https://flyppedhindi.com/" . $post_slug; // Check if your URL structure needs /news/ or similar
                    $urlsToSubmit[] = $post_url;
                    
                    // Log the published post
                    log_message('info', "Auto-published: {$post->post_title} (ID: {$post->ID}) - Scheduled for: {$post->post_date}");
                }
            }
            
            // =======================================================
            //  SUBMIT NEWLY PUBLISHED POSTS TO INDEXNOW BATCH
            // =======================================================
            if (!empty($urlsToSubmit)) {
                $this->submitToIndexNow($urlsToSubmit);
                log_message('info', 'IndexNow Auto-Triggered for ' . count($urlsToSubmit) . ' scheduled posts.');
            }
            
            // Return response based on how it was called
            if (is_cli()) {
                // Called from command line (cron job)
                if ($publishedCount > 0) {
                    echo "✓ Published {$publishedCount} scheduled post(s) at " . date('Y-m-d H:i:s') . "\n";
                    foreach ($publishedPosts as $post) {
                        echo "  - [{$post['id']}] {$post['title']} (scheduled for {$post['scheduled_for']})\n";
                    }
                    echo "✓ Submitted to IndexNow.\n";
                } else {
                    echo "✓ No scheduled posts to publish at " . date('Y-m-d H:i:s') . "\n";
                }
            } else {
                // Called from web browser/URL
                return $this->response->setJSON([
                    'success' => true,
                    'published_count' => $publishedCount,
                    'posts' => $publishedPosts,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => "Published {$publishedCount} scheduled post(s) and submitted to IndexNow."
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Scheduled Post Publisher Error: ' . $e->getMessage());
            
            if (is_cli()) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    // ==============================================================
    //  INDEXNOW FUNCTION FOR SCHEDULER
    // ==============================================================
    private function submitToIndexNow($urlList) 
    {
        $host = 'hin.flypped.com'; 
        $key = '55fba596ea4442cbb71744abc4737fac'; 
        $endpoint = 'https://api.indexnow.org/indexnow';
        
        $data = [
            'host' => $host,
            'key' => $key,
            'keyLocation' => "https://{$host}/{$key}.txt",
            'urlList' => $urlList
        ];
        
        $payload = json_encode($data);
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($payload)
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}