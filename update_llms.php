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

// --- 1. SETTINGS & CONTENT PREPARE ---
$content = "\xEF\xBB\xBF"; 
$content .= "# Flypped Hindi: Latest Breaking News, Trends, Lifestyle & Knowledge\n\n";
$content .= "Website: https://flyppedhindi.com/\n\n";

$categories = [
    'news' => 'News',
    'entertainment' => 'Entertainment',
    'sports' => 'Sports',
    'tech' => 'Tech',
    'business' => 'Business',
    'health-fitness' => 'Health-Fitness',
    'lifestyle' => 'Lifestyle',
    'travel' => 'Travel',
    'education' => 'Education',
    'relationship' => 'Relationship',
    'spiritual' => 'Spiritual',
    'crazy-facts' => 'Crazy-Facts'
];

// --- 2. FETCH LATEST 25 POSTS PER CATEGORY ---
$conn = new mysqli('localhost', 'hindi_user', 'HindiFlypped@2025', 'hindi_flypped_production');

//  CRITICAL: SET NAMES 'utf8' (As suggested)
$conn->set_charset("utf8");
$conn->query("SET NAMES 'utf8'");
$conn->query("SET CHARACTER SET 'utf8'");

if (!$conn->connect_error) {
    foreach ($categories as $slug => $name) {
        $content .= "## {$name}\n\n";
        
        $query = "SELECT p.post_title, p.post_name 
                  FROM wp_posts p 
                  JOIN wp_term_relationships tr ON p.ID = tr.object_id 
                  JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                  JOIN wp_terms t ON tt.term_id = t.term_id 
                  WHERE p.post_status = 'publish' 
                  AND p.post_type = 'post' 
                  AND t.slug = '" . $conn->real_escape_string($slug) . "' 
                  ORDER BY p.post_date DESC 
                  LIMIT 25";
                  
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $title = trim(strip_tags($row['post_title']));
                $title = str_replace(['[', ']'], '', $title);
                $content .= "- [{$title}](https://flyppedhindi.com/{$slug}/" . $row['post_name'] . ")\n";
            }
        } else {
            $content .= "- New updates coming soon.\n";
        }
        $content .= "\n";
    }
    $conn->close();
}

// --- 3. WRITE TO FILE SILENTLY ---
file_put_contents(__DIR__ . '/llms.txt', $content);

// --- 4. FAKE 404 ---
header("HTTP/1.1 404 Not Found");
exit();
?>