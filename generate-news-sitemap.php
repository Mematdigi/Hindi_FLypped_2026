<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== 'flypped_123') {
        die("<h2>🚨 ERROR: Access Denied.</h2>");
    }
}

$content = "\xEF\xBB\xBF";
$content .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

try {
    $conn = new mysqli('localhost', 'hindi_user', 'HindiFlypped@2025', 'hindi_flypped_production');
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    
    $query = "
        SELECT p.post_name, p.post_title, DATE_FORMAT(p.post_date, '%Y-%m-%dT%H:%i:%s+05:30') AS formatted_date 
        FROM wp_posts p
        INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
        INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN wp_terms t ON tt.term_id = t.term_id
        WHERE p.post_date >= NOW() - INTERVAL 48 HOUR 
        AND p.post_status = 'publish' 
        AND p.post_type = 'post' 
        AND tt.taxonomy = 'category'
        AND t.slug = 'news' 
        ORDER BY p.post_date DESC
    ";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $post_url = 'https://flyppedhindi.com/' . htmlspecialchars($row["post_name"] ?? '');
            $title = $row["post_title"] ?? 'No Title';
            $pub_date = $row["formatted_date"];
            
            $content .= "    <url>\n";
            $content .= "        <loc>" . $post_url . "</loc>\n";
            $content .= "        <news:news>\n";
            $content .= "            <news:publication>\n";
            $content .= "                <news:name>Flypped Hindi News</news:name>\n";
            $content .= "                <news:language>hi</news:language>\n";
            $content .= "            </news:publication>\n";
            $content .= "            <news:publication_date>" . $pub_date . "</news:publication_date>\n";
            $content .= "            <news:title>" . htmlspecialchars(trim($title)) . "</news:title>\n";
            $content .= "        </news:news>\n";
            $content .= "    </url>\n";
        }
    }
    $conn->close();
} catch (Throwable $e) {
    die("Script Error: " . $e->getMessage());
}

$content .= '</urlset>';

$file_path = __DIR__ . '/google-news-sitemap.xml';
$write_status = file_put_contents($file_path, $content);

if ($write_status !== false) {
    echo "SUCCESS: Sitemap generated at ROOT folder: " . $file_path . "\n";
} else {
    echo "ERROR: Could not write file in root. Please check permissions.\n";
}
?>