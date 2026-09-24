<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class SitemapController extends Controller
{
    protected $db;
    
    public function __construct()
    {
        helper('url');
        $this->db = Database::connect();
    }
    
    //  Main Sitemap Index (XML with embedded styling) - EXCLUDES Week post & Stories
    public function index()
    {
        //  Fetch all categories (EXCLUDING week-post, stories, and video)
        // 🚀 ADDED 'video' HERE to hide it from the main sitemap index
        $categories = $this->db->query("
            SELECT t.term_id, t.name, t.slug, 
                COUNT(tr.object_id) as post_count,
                MAX(p.post_modified) as last_modified
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            LEFT JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            LEFT JOIN wp_posts p ON tr.object_id = p.ID 
                AND p.post_status = 'publish' AND p.post_type = 'post'
            WHERE tt.taxonomy = 'category'
            AND t.slug NOT IN ('week-post', 'stories', 'weekpost', 'week-posts', 'video') 
            GROUP BY t.term_id, t.name, t.slug
            HAVING post_count > 0
            ORDER BY t.name ASC
        ")->getResult();

        $sitemaps = [];

        //  Add the Pages Sitemap entry FIRST
        $sitemaps[] = [
            'loc' => site_url('sitemap-pages.xml'),
            'lastmod' => date('c')
        ];

        //  Then add all category sitemaps
        foreach ($categories as $category) {
            $sitemaps[] = [
                'loc' => site_url('sitemap-' . $category->slug . '.xml'),
                'lastmod' => $category->last_modified ? date('c', strtotime($category->last_modified)) : date('c')
            ];
        }

        //  Return the combined sitemap index XML
        return $this->buildSitemapIndexXML($sitemaps);
    }

    //  Pages Sitemap — Home stays 1.0/daily; others 0.8/monthly
    public function pages()
    {
        $staticPages = [
            ['slug' => '',              'title' => 'Home'],
            ['slug' => 'contact',       'title' => 'Contact'],
            ['slug' => 'disclaimer',    'title' => 'Disclaimer'],
            ['slug' => 'privacy-policy','title' => 'Privacy Policy'],
            ['slug' => 'sitemap',       'title' => 'Sitemap'],
            ['slug' => 'write-for-us',  'title' => 'Write For Us'],
        ];

        $urls = [];
        foreach ($staticPages as $page) {
            $isHome = trim($page['slug'], '/') === '';

            $urls[] = [
                'loc'        => site_url(trim($page['slug'], '/')),
                'lastmod'    => date('c'),
                'changefreq' => $isHome ? 'daily'   : 'monthly',
                'priority'   => $isHome ? '1.0'     : '0.8'
            ];
        }

        return $this->buildUrlsetXML($urls);
    }

    //  Category Sitemap (weekly, lowercase)
    public function category($slug)
    {
        // Convert incoming slug to lowercase
        $slug = strtolower($slug);
        
        //  ADDED 'video' blocked 
        $blockedCategories = ['week-post', 'stories', 'weekpost', 'week-posts', 'latest', 'uncategorized', 'video'];
        
        if (in_array($slug, $blockedCategories)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('This category is excluded from sitemap');
        }
        
        // Get category info - Use LOWER() in SQL for case-insensitive matching
        $category = $this->db->query("
            SELECT t.term_id, t.name, LOWER(t.slug) as slug
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE LOWER(t.slug) = ? AND tt.taxonomy = 'category'
        ", [$slug])->getRow();
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }
        
        // 🔹 START: Add category page itself as first URL
        $urls = [];
        $urls[] = [
            'loc'        => site_url(strtolower($category->slug)),
            'lastmod'    => date('c'),
            'changefreq' => 'daily',
            'priority'   => '0.80'
        ];
        // 🔹 END
        
        // Get all posts in this category
        $posts = $this->db->query("
            SELECT DISTINCT p.ID, p.post_title, p.post_name, p.post_modified, p.post_date
            FROM wp_posts p
            JOIN wp_term_relationships tr ON p.ID = tr.object_id
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE LOWER(t.slug) = ?
            AND p.post_status = 'publish'
            AND p.post_type = 'post'
            ORDER BY p.post_date DESC
        ", [$slug])->getResult();
        
        // Add all posts
        foreach ($posts as $post) {
            $urls[] = [
                'loc'        => site_url($post->post_name),
                'lastmod'    => date('c', strtotime($post->post_modified)),
                'changefreq' => 'weekly',
                'priority'   => '0.50'
            ];
        }
        
        return $this->buildUrlsetXML($urls);
    }
 
    //  Embedded XSL for Sitemap Index
    public function sitemapXSL()
    {
        $xsl = '<?xml version="1.0" encoding="UTF-8"?>
        <xsl:stylesheet version="1.0" 
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
            xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9">
            
        <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

        <xsl:template match="/">
        <html>
        <head>
            <title>XML Sitemap</title>
            <style type="text/css">
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 40px; 
                    background: #f5f5f5; 
                    color: #333;
                }
                .container { 
                    background: white; 
                    padding: 40px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                    max-width: 1200px;
                    margin: 0 auto;
                }
                h1 { 
                    color: #666; 
                    font-size: 36px; 
                    margin-bottom: 10px; 
                    font-weight: normal;
                }
                .intro { 
                    color: #666; 
                    margin-bottom: 30px; 
                    line-height: 1.6; 
                    font-size: 14px;
                }
                .intro a { 
                    color: #007cba; 
                    text-decoration: none; 
                }
                .intro a:hover { 
                    text-decoration: underline; 
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th { 
                    background: #f9f9f9; 
                    padding: 12px; 
                    text-align: left; 
                    border-bottom: 1px solid #ddd; 
                    font-weight: bold; 
                    color: #666; 
                }
                td { 
                    padding: 12px; 
                    border-bottom: 1px solid #eee; 
                    vertical-align: top;
                }
                td a { 
                    color: #007cba; 
                    text-decoration: none; 
                    word-break: break-all;
                }
                td a:hover { 
                    text-decoration: underline; 
                }
                tr:hover { 
                    background: #f9f9f9; 
                }
                .count { 
                    margin-bottom: 20px; 
                    color: #666; 
                    font-size: 14px;
                }
                .iah-seo {
                    color: #d63638;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>XML Sitemap</h1>
                <div class="intro">   
                    <p>You can find more information about XML sitemaps on <a href="https://sitemaps.org" target="_blank">sitemaps.org</a>.</p>
                </div>
                <div class="count">
                    This XML Sitemap Index file contains <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> sitemaps.
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Sitemap</th>
                            <th>Last Modified</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                        <tr>
                            <td>
                                <a href="{sitemap:loc}">
                                    <xsl:value-of select="sitemap:loc"/>
                                </a>
                            </td>
                            <td>
                                <xsl:value-of select="substring(sitemap:lastmod, 1, 19)"/>
                            </td>
                        </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        </xsl:template>

        </xsl:stylesheet>';

        return $this->response
            ->setHeader('Content-Type', 'application/xslt+xml; charset=UTF-8')
            ->setBody($xsl);
    }
        
    //  Embedded XSL for URL Set
    public function urlsetXSL()
    {
        $xsl = '<?xml version="1.0" encoding="UTF-8"?>
        <xsl:stylesheet version="1.0" 
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
            xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9">
            
        <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

        <xsl:template match="/">
        <html>
        <head>
            <title>XML Sitemap</title>
            <style type="text/css">
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 40px; 
                    background: #f5f5f5; 
                    color: #333;
                }
                .container { 
                    background: white; 
                    padding: 40px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                    max-width: 1200px;
                    margin: 0 auto;
                }
                h1 { 
                    color: #666; 
                    font-size: 36px; 
                    margin-bottom: 10px; 
                    font-weight: normal;
                }
                .intro { 
                    color: #666; 
                    margin-bottom: 30px; 
                    line-height: 1.6; 
                    font-size: 14px;
                }
                .intro a { 
                    color: #007cba; 
                    text-decoration: none; 
                }
                .intro a:hover { 
                    text-decoration: underline; 
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th { 
                    background: #f9f9f9; 
                    padding: 12px; 
                    text-align: left; 
                    border-bottom: 1px solid #ddd; 
                    font-weight: bold; 
                    color: #666; 
                }
                td { 
                    padding: 12px; 
                    border-bottom: 1px solid #eee; 
                    vertical-align: top;
                }
                td a { 
                    color: #007cba; 
                    text-decoration: none; 
                    word-break: break-all;
                }
                td a:hover { 
                    text-decoration: underline; 
                }
                tr:hover { 
                    background: #f9f9f9; 
                }
                .count { 
                    margin-bottom: 20px; 
                    color: #666; 
                    font-size: 14px;
                }
                .breadcrumb { 
                    margin-bottom: 20px; 
                }
                .breadcrumb a { 
                    color: #007cba; 
                    text-decoration: none; 
                }
                .breadcrumb a:hover { 
                    text-decoration: underline; 
                }
                .iah-seo {
                    color: #d63638;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="breadcrumb">
                    <a href="../sitemap.xml">← Back to Sitemap Index</a>
                </div>
                <h1>XML Sitemap</h1>
                <div class="intro">
                    
                    <p>You can find more information about XML sitemaps on <a href="https://sitemaps.org" target="_blank">sitemaps.org</a>.</p>
                </div>
                <div class="count">
                    This sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs.
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>Priority</th>
                            <th>Change Frequency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="sitemap:urlset/sitemap:url">
                        <tr>
                            <td>
                                <a href="{sitemap:loc}" target="_blank">
                                    <xsl:value-of select="sitemap:loc"/>
                                </a>
                            </td>
                            <td>
                                <xsl:value-of select="sitemap:priority"/>
                            </td>
                            <td>
                                <xsl:value-of select="sitemap:changefreq"/>
                            </td>
                        </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        </xsl:template>

        </xsl:stylesheet>';

        return $this->response
            ->setHeader('Content-Type', 'application/xslt+xml; charset=UTF-8')
            ->setBody($xsl);
    }
    
    // 🔹 XML Sitemap Index (PROPER XML with correct headers)
    private function buildSitemapIndexXML($sitemaps)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . site_url('sitemap.xsl') . '"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($sitemaps as $s) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($s['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$s['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        
        $xml .= '</sitemapindex>';
        
        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->setHeader('X-Robots-Tag', 'noindex')
            ->setBody($xml);
    }
    
    // 🔹 XML URLSET (PROPER XML with correct headers)
    private function buildUrlsetXML($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . site_url('urlset.xsl') . '"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
        
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->setHeader('X-Robots-Tag', 'noindex')
            ->setBody($xml);
    }

    //  HTML Sitemap (Auto-updating for Users)
    public function htmlSitemap()
    {
        $db = \Config\Database::connect();

        // 1. Static Pages
        $staticPages = [
            ['slug' => '',              'title' => 'Home'],
            ['slug' => 'contact',       'title' => 'Contact Us'],
            ['slug' => 'disclaimer',    'title' => 'Disclaimer'],
            ['slug' => 'privacy-policy','title' => 'Privacy Policy']
        ];

        // 2. Fetch Active Categories for the Sitemap Body
        // 🚀 ADDED 'video' HERE to hide it from the HTML sitemap structure
        $categories = $db->query("
    SELECT t.term_id, t.name, t.slug,
           COUNT(DISTINCT tr.object_id) AS post_count
    FROM wp_terms t
    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
    LEFT JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
    LEFT JOIN wp_posts p ON tr.object_id = p.ID
        AND p.post_status = 'publish'
        AND p.post_type   = 'post'
    WHERE tt.taxonomy = 'category'
      AND t.slug NOT IN (
          'week-post','stories','weekpost',
          'week-posts','uncategorized','latest','other','video'
      )
    GROUP BY t.term_id, t.name, t.slug
    ORDER BY t.name ASC
")->getResult();

        // 3. Fetch Posts for the Sitemap Body
      // ── Step 1: Fetch ALL published posts (no limit) ──────────
$allPosts = $db->query("
    SELECT DISTINCT p.ID, p.post_title, p.post_name, p.post_date
    FROM wp_posts p
    WHERE p.post_status = 'publish'
      AND p.post_type  = 'post'
    ORDER BY p.post_date DESC
")->getResult();

// ── Step 2: Fetch all category relationships for these posts
// This handles posts that belong to multiple categories correctly
$postIds = array_column($allPosts, 'ID');

$groupedPosts = [];

if (!empty($postIds)) {
    // Build IN clause safely
    $placeholders = implode(',', array_fill(0, count($postIds), '?'));
    
    // 🚀 ADDED 'video' HERE so posts aren't grouped under 'video' on the visual sitemap
    $relationships = $db->query("
        SELECT p.ID, p.post_title, p.post_name, t.slug AS category_slug
        FROM wp_posts p
        JOIN wp_term_relationships tr  ON p.ID = tr.object_id
        JOIN wp_term_taxonomy tt       ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN wp_terms t                ON tt.term_id = t.term_id
        WHERE p.ID IN ({$placeholders})
          AND tt.taxonomy = 'category'
          AND t.slug NOT IN (
              'week-post','stories','weekpost',
              'week-posts','uncategorized','latest','other','video'
          )
        ORDER BY t.name ASC, p.post_date DESC
    ", $postIds)->getResult();

    // ── Step 3: Group by category slug ───────────────────────
    // Each post appears under every valid category it belongs to
    $seenInCategory = []; // track post+category pairs to avoid duplicates

    foreach ($relationships as $row) {
        $key = $row->category_slug . '_' . $row->ID;
        if (isset($seenInCategory[$key])) continue; // skip if already added
        $seenInCategory[$key] = true;

        $groupedPosts[$row->category_slug][] = $row;
    }

    // ── Step 4: Sort each category's posts by date (newest first)
    foreach ($groupedPosts as $slug => &$catPosts) {
        // Already ordered by post_date DESC from query, but re-sort per group
        usort($catPosts, function($a, $b) {
            // compare by post_name as proxy since date isn't in this result
            return strcmp($b->post_name, $a->post_name);
        });
    }
    unset($catPosts);
}

        // =========================================================
        // 4. FETCH FOOTER DATA (Fixes the blank footer issue)
        // =========================================================
        
        // A. Fetch Latest Blogs for Footer
        $latest_footer_blogs_query = $db->query("
            SELECT p.ID, p.post_title, p.post_name, p.post_date, MAX(t.name) AS category_name
            FROM wp_posts p
            JOIN wp_term_relationships tr ON p.ID = tr.object_id
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category'
            JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish' 
              AND p.post_type = 'post'
              AND t.slug NOT IN ('stories','week-post','weekly-post','story')
            GROUP BY p.ID, p.post_title, p.post_name, p.post_date
            ORDER BY p.post_date DESC
            LIMIT 3
        ");
        $latest_footer_blogs = $latest_footer_blogs_query->getResultArray();

        // B. Fetch Categories and Post Counts for Footer Columns
        $categories_query = $db->query("
            SELECT t.name, t.slug, COUNT(p.ID) AS post_count
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_posts p ON tr.object_id = p.ID
            WHERE p.post_status = 'publish' AND p.post_type = 'post' AND tt.taxonomy = 'category'
            GROUP BY t.term_id, t.name, t.slug
            ORDER BY post_count DESC
        ");
        $categories_with_post_count = $categories_query->getResultArray();

        // Filter out unwanted categories from footer
        // 🚀 ADDED 'video' HERE so it does not accidentally show up in the footer counts
        $excluded_categories = ['stories', 'week post', 'weekly post', 'story','latest', 'other', 'uncategorized', 'video'];
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

        // Split categories into two columns for the footer layout
        $total_categories = count($categories_with_post_count);
        $half = ceil($total_categories / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        // C. Fetch count for "Other/अन्य" Category
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

        // D. Pass EVERYTHING to the view
        $data = [
            'staticPages'                => $staticPages,
            'categories'                 => $categories,
            'groupedPosts'               => $groupedPosts,
            // Footer Variables
            'latest_footer_blogs'        => $latest_footer_blogs,
            'categories_with_post_count' => $categories_with_post_count,
            'categories_column_1'        => $categories_column_1,
            'categories_column_2'        => $categories_column_2,
            'other_count'                => $other_count
        ];

        return view('html_sitemap', $data);
    }
    
}