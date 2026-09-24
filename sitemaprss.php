<?php
// --- 0. SECURITY & PROTECTION ---
header("X-Robots-Tag: noindex, nofollow", true);
$secret_key = 'flypped_secure_123';

if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    header("HTTP/1.1 404 Not Found");
    exit(); 
}

error_reporting(0);
ini_set('display_errors', 0);

// --- 1. XML CONTENT PREPARE ---
//  CRITICAL FIX: Adding UTF-8 BOM (\xEF\xBB\xBF) to force Browser & RSS Readers to read Hindi correctly
$content = "\xEF\xBB\xBF";
$content .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$content .= '<rss version="2.0">' . "\n";
$content .= '<channel>' . "\n";
$content .= '<title>Flypped Hindi - Latest Updates</title>' . "\n";
$content .= '<link>https://flyppedhindi.com/</link>' . "\n";
$content .= '<description>Latest content updates from Flypped Hindi</description>' . "\n";
$content .= '<language>hi-IN</language>' . "\n";

try {
    // --- 2. FETCH LATEST 20 POSTS OVERALL ---
    $conn = new mysqli('localhost', 'hindi_user', 'HindiFlypped@2025', 'hindi_flypped_production');
    
    //  CONNECTION: Strict utf8 (As it worked for LLMS)
    $conn->set_charset("utf8");
    $conn->query("SET NAMES 'utf8'");
    $conn->query("SET CHARACTER SET 'utf8'");

    if (!$conn->connect_error) {
        $query = "SELECT post_title, post_name, post_excerpt, post_content, 
                         DATE_FORMAT(post_date_gmt, '%a, %d %b %Y %T +0000') AS rss_date 
                  FROM wp_posts 
                  WHERE post_status = 'publish' 
                  AND post_type = 'post' 
                  ORDER BY post_date DESC 
                  LIMIT 20";

        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $post_url = 'https://flyppedhindi.com/' . htmlspecialchars($row["post_name"] ?? '');
                
                $title = $row["post_title"] ?? 'No Title';
                $excerpt = $row["post_excerpt"] ?? '';
                $content_text = $row["post_content"] ?? '';
                
                if (!empty(trim($excerpt))) {
                    $description = $excerpt;
                } else {
                    $clean_content = strip_tags($content_text);
                    // Ensure cutting text doesn't break Hindi characters
                    $description = mb_substr($clean_content, 0, 160, 'UTF-8') . '...';
                }
                
                $pub_date = !empty($row["rss_date"]) ? $row["rss_date"] : 'Thu, 01 Jan 1970 00:00:00 +0000';
                
                $content .= '<item>' . "\n";
                $content .= '<title><![CDATA[' . trim($title) . ']]></title>' . "\n";
                $content .= '<link>' . $post_url . '</link>' . "\n";
                $content .= '<guid isPermaLink="true">' . $post_url . '</guid>' . "\n";
                $content .= '<description><![CDATA[' . trim($description) . ']]></description>' . "\n";
                $content .= '<pubDate>' . $pub_date . '</pubDate>' . "\n";
                $content .= '</item>' . "\n";
            }
        }
        $conn->close();
    }
} catch (Throwable $e) {
    // Fail silently in background
}

$content .= '</channel>' . "\n";
$content .= '</rss>';

// --- 3. WRITE TO FILE SILENTLY ---
file_put_contents(__DIR__ . '/sitemap.rss', $content);

// --- 4. FAKE 404 ---
header("HTTP/1.1 404 Not Found");
exit();
?>