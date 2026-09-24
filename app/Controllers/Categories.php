<?php

namespace App\Controllers;

class Categories extends BaseController
{
    private $cache;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    public function index($category_slug)
    {
        //  Force lowercase slug — redirect if uppercase detected
        $lowercase_slug = strtolower($category_slug);
        if ($category_slug !== $lowercase_slug) {
            return redirect()->to(base_url($lowercase_slug), 301);
        }

        try {
            // Check if it's a static page first
            if ($this->isStaticPage($category_slug)) {
                return $this->handleStaticPage($category_slug);
            }

            //  Try cache first (5 minutes)
            $page = $this->request->getVar('page') ? (int)$this->request->getVar('page') : 1;
            $cacheKey = 'category_' . $category_slug . '_page_' . $page;
            $cachedView = $this->cache->get($cacheKey);

            if ($cachedView !== null) {
                return $cachedView;
            }

            $db = \Config\Database::connect();

            //  Check database connection
            if (!$db->connID) {
                throw new \Exception('Database connection failed');
            }

            $perPage = 12;

            // ==== Get Category Info with SEO in one query ====
            $categoryQuery = $db->query("
                SELECT 
                    t.term_id, 
                    t.name, 
                    t.slug, 
                    tt.description,
                    MAX(CASE WHEN tm.meta_key = '_seo_title' THEN tm.meta_value END) as seo_title,
                    MAX(CASE WHEN tm.meta_key = '_seo_description' THEN tm.meta_value END) as seo_description,
                    MAX(CASE WHEN tm.meta_key = '_seo_keywords' THEN tm.meta_value END) as seo_keywords
                FROM wp_terms t
                INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                LEFT JOIN wp_termmeta tm ON t.term_id = tm.term_id 
                    AND tm.meta_key IN ('_seo_title', '_seo_description', '_seo_keywords')
                WHERE t.slug = ? AND tt.taxonomy = 'category'
                GROUP BY t.term_id, t.name, t.slug, tt.description
                LIMIT 1
            ", [$category_slug]);

            $category = $categoryQuery ? $categoryQuery->getRow() : null;

            // If category doesn't exist, throw 404
            if (!$category) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            //  SEO Meta
            $seoTitle       = $category->seo_title       ?? $category->name . ' - Flypped Hindi';
            $seoDescription = $category->seo_description ?? ($category->description ?? 'Latest ' . $category->name . ' news on Flypped Hindi');
            $seoKeywords    = $category->seo_keywords    ?? '';

            //  Count total posts
            //  ipl2026 → fetch from 'sports' slug, last 10 days (matches English behavior)
            if ($category_slug === 'ipl2026') {
                $totalQuery = $db->query("
                    SELECT COUNT(DISTINCT p.ID) as total
                    FROM wp_posts p
                    INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                    INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN wp_terms t ON tt.term_id = t.term_id
                    WHERE t.slug = 'sports'
                      AND p.post_status = 'publish'
                      AND p.post_type = 'post'
                      AND tt.taxonomy = 'category'
                      AND p.post_date >= ?
                ", [date('Y-m-d', strtotime('-10 days'))]);
            } else {
                $totalQuery = $db->query("
                    SELECT COUNT(DISTINCT p.ID) as total
                    FROM wp_posts p
                    INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                    INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN wp_terms t ON tt.term_id = t.term_id
                    WHERE t.slug = ?
                      AND p.post_status = 'publish'
                      AND p.post_type = 'post'
                      AND tt.taxonomy = 'category'
                ", [$category_slug]);
            }

            $totalResult = $totalQuery ? $totalQuery->getRow() : null;
            $totalPosts  = $totalResult ? $totalResult->total : 0;
            $totalPages  = ceil($totalPosts / $perPage);

            //  If no posts, throw 404 (matches English behavior)
            if ($totalPosts == 0) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            //  Fetch posts with thumbnails in ONE query (no N+1)
            if ($category_slug === 'ipl2026') {
                $query = $db->query("
                    SELECT DISTINCT 
                        p.ID, 
                        p.post_title, 
                        p.post_name, 
                        p.post_date,
                        thumb.guid AS thumbnail_url
                    FROM wp_posts p
                    INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                    INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN wp_terms t ON tt.term_id = t.term_id
                    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                    LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
                    WHERE t.slug = 'sports'
                      AND p.post_status = 'publish'
                      AND p.post_type = 'post'
                      AND tt.taxonomy = 'category'
                      AND p.post_date >= ?
                    ORDER BY p.post_date DESC
                    LIMIT ? OFFSET ?
                ", [date('Y-m-d', strtotime('-10 days')), $perPage, ($page - 1) * $perPage]);
            } else {
                $query = $db->query("
                    SELECT DISTINCT 
                        p.ID, 
                        p.post_title, 
                        p.post_name, 
                        p.post_date,
                        thumb.guid AS thumbnail_url
                    FROM wp_posts p
                    INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
                    INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN wp_terms t ON tt.term_id = t.term_id
                    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
                    LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
                    WHERE t.slug = ?
                      AND p.post_status = 'publish'
                      AND p.post_type = 'post'
                      AND tt.taxonomy = 'category'
                    ORDER BY p.post_date DESC
                    LIMIT ? OFFSET ?
                ", [$category_slug, $perPage, ($page - 1) * $perPage]);
            }

            $posts = $query ? $query->getResultArray() : [];

            //  Add default thumbnail and correct URL for each post
            foreach ($posts as &$post) {
                if (empty($post['thumbnail_url'])) {
                    $post['thumbnail_url'] = base_url('public/assest/images/default-thumbnail.jpg');
                }
                //  ipl2026 posts link to /sports/post-name
                $post['blog_detail_url'] = base_url(
                    ($category_slug === 'ipl2026' ? 'sports' : $category_slug) . '/' . $post['post_name']
                );
            }
            unset($post);

            //  Pagination
            $pager      = \Config\Services::pager();
            $pagerLinks = $pager->makeLinks($page, $perPage, $totalPosts, 'default_full');

            //  Footer data (cached separately)
            $footer_data = $this->getFooterData($db);

            //  Prepare view data
            $viewData = [
                'posts'                      => $posts,
                'category_slug'              => $category_slug,
                'pagerLinks'                 => $pagerLinks,
                'currentPage'                => $page,
                'totalPages'                 => $totalPages,
                'category_name'              => $category->name ?? ucfirst(str_replace('-', ' ', $category_slug)),
                'seoTitle'                   => $seoTitle,
                'seoDescription'             => $seoDescription,
                'seoKeywords'                => $seoKeywords,
                'latest_footer_blogs'        => $footer_data['latest_footer_blogs']        ?? [],
                'categories_with_post_count' => $footer_data['categories_with_post_count'] ?? [],
                'categories_column_1'        => $footer_data['categories_column_1']        ?? [],
                'categories_column_2'        => $footer_data['categories_column_2']        ?? [],
                'is_static_page'             => false,
            ];

            //  Render and cache view (5 minutes)
            $renderedView = view('categories', $viewData);
            $this->cache->save($cacheKey, $renderedView, 300);

            return $renderedView;

        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            log_message('error', 'Categories::index() Exception: ' . $e->getMessage() . ' | Line: ' . $e->getLine());

            if (ENVIRONMENT === 'production') {
                return view('errors/html/error_500', [
                    'heading' => 'Service Temporarily Unavailable',
                    'message' => 'Please try again in a moment.',
                ]);
            }

            return 'Error: ' . $e->getMessage() . '<br>Line: ' . $e->getLine();
        }
    }

    // =========================================================
    //  Optimized footer data with caching
    // =========================================================
    private function getFooterData($db)
    {
        $cacheKey = 'footer_data_hindi_v2';
        $cached   = $this->cache->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        //  Latest 3 blogs with thumbnails in ONE query
        $latest_footer_blogs_query = $db->query("
            SELECT DISTINCT 
                p.ID, 
                p.post_title, 
                p.post_name, 
                p.post_date,
                t.slug AS category_slug,
                thumb.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts thumb ON pm.meta_value = thumb.ID
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
              AND tt.taxonomy = 'category'
              AND t.slug NOT IN ('stories', 'week-post', 'weekly-post', 'story', 'uncategorized')
            ORDER BY p.post_date DESC
            LIMIT 3
        ");

        $latest_footer_blogs = $latest_footer_blogs_query
            ? $latest_footer_blogs_query->getResultArray()
            : [];

        foreach ($latest_footer_blogs as &$blog) {
            if (empty($blog['thumbnail_url'])) {
                $blog['thumbnail_url'] = base_url('public/assest/images/default-thumbnail.jpg');
            }
            $blog['blog_detail_url'] = base_url($blog['category_slug'] . '/' . $blog['post_name']);
            $blog['formatted_date']  = date('F d, Y', strtotime($blog['post_date']));
        }
        unset($blog);

        //  Categories with post counts
        $categories_query = $db->query("
            SELECT t.name, t.slug, COUNT(DISTINCT p.ID) as post_count
            FROM wp_terms t
            INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = 'category'
            INNER JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN wp_posts p ON tr.object_id = p.ID 
                AND p.post_status = 'publish' 
                AND p.post_type = 'post'
            WHERE t.slug NOT IN ('stories', 'week-post', 'weekly-post', 'story', 'latest', 'other', 'uncategorized')
            GROUP BY t.term_id, t.name, t.slug
            HAVING post_count > 0
            ORDER BY post_count DESC
            LIMIT 20
        ");

        $categories_with_post_count = $categories_query
            ? $categories_query->getResultArray()
            : [];

        //  Normalize slugs
        foreach ($categories_with_post_count as &$cat) {
            $cat['slug'] = strtolower($cat['slug']);
            if (
                stripos($cat['name'], 'Tech') !== false &&
                stripos($cat['name'], 'Gadgets') !== false
            ) {
                $cat['name'] = 'Tech & Gadgets';
                $cat['slug'] = 'tech-gadgets';
            }
        }
        unset($cat);

        //  Split into 2 columns for footer
        $total_categories   = count($categories_with_post_count);
        $half               = ceil($total_categories / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        $data = [
            'latest_footer_blogs'        => $latest_footer_blogs,
            'categories_with_post_count' => $categories_with_post_count,
            'categories_column_1'        => $categories_column_1,
            'categories_column_2'        => $categories_column_2,
        ];

        //  Cache footer for 5 minutes
        $this->cache->save($cacheKey, $data, 300);

        return $data;
    }

    // =========================================================
    //  Static page check
    // =========================================================
    private function isStaticPage($slug)
    {
        //  ipl2026 is NOT a static page
        return in_array($slug, [
            'disclaimer',
            'privacy-policy',
            'about-us',
            'terms-of-service',
            'contact',
            'write-for-us',
            'authors',
        ]);
    }

    // =========================================================
    //  Handle static pages
    // =========================================================
    private function handleStaticPage($page_slug)
    {
        //  Try cache first (1 hour for static pages)
        $cacheKey   = 'static_page_hindi_' . $page_slug;
        $cachedView = $this->cache->get($cacheKey);

        if ($cachedView !== null) {
            return $cachedView;
        }

        $db          = \Config\Database::connect();
        $footer_data = $this->getFooterData($db);

        $data = [
            'latest_footer_blogs'        => $footer_data['latest_footer_blogs']        ?? [],
            'categories_with_post_count' => $footer_data['categories_with_post_count'] ?? [],
            'categories_column_1'        => $footer_data['categories_column_1']        ?? [],
            'categories_column_2'        => $footer_data['categories_column_2']        ?? [],
            'is_static_page'             => true,
        ];

        switch ($page_slug) {
            case 'disclaimer':
                $data['category_slug']   = 'disclaimer';
                $data['page_title']      = 'Disclaimer';
                $data['seoTitle']        = 'Disclaimer - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi के Disclaimer को पढ़ें। वेबसाइट की सामग्री की सटीकता और उपयोग की शर्तों के बारे में जानें।';
                $data['seoKeywords']     = 'Flypped Hindi disclaimer, अस्वीकरण';
                $data['static_content']  = $this->getDisclaimerContent();
                break;

            case 'privacy-policy':
                $data['category_slug']   = 'privacy-policy';
                $data['page_title']      = 'Privacy Policy';
                $data['seoTitle']        = 'Privacy Policy - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi की Privacy Policy पढ़ें। जानें हम आपकी जानकारी कैसे एकत्र, उपयोग और सुरक्षित करते हैं।';
                $data['seoKeywords']     = 'Flypped Hindi privacy policy, गोपनीयता नीति';
                $data['static_content']  = $this->getPrivacyPolicyContent();
                break;

            case 'about-us':
                $data['category_slug']   = 'about-us';
                $data['page_title']      = 'हमारे बारे में';
                $data['seoTitle']        = 'हमारे बारे में - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi के बारे में जानें। हम हिंदी पाठकों के लिए विश्वसनीय समाचार और जानकारी प्रदान करते हैं।';
                $data['seoKeywords']     = 'Flypped Hindi के बारे में, about us';
                $data['static_content']  = $this->getAboutUsContent();
                break;

            case 'contact':
                $data['category_slug']   = 'contact';
                $data['page_title']      = 'संपर्क करें';
                $data['seoTitle']        = 'संपर्क करें - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi से संपर्क करें। किसी भी सुझाव या सहायता के लिए हमें लिखें।';
                $data['seoKeywords']     = 'Flypped Hindi contact, संपर्क';
                $data['static_content']  = $this->getContactContent();
                break;

            case 'write-for-us':
                $data['category_slug']   = 'write-for-us';
                $data['page_title']      = 'हमारे लिए लिखें';
                $data['seoTitle']        = 'हमारे लिए लिखें - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi के लिए लिखें और अपने विचार लाखों पाठकों तक पहुँचाएं।';
                $data['seoKeywords']     = 'write for us, Flypped Hindi contributor';
                $data['static_content']  = $this->getWriteForUsContent();
                break;

            case 'authors':
                $data['category_slug']   = 'authors';
                $data['page_title']      = 'हमारे लेखक';
                $data['seoTitle']        = 'हमारे लेखक - Flypped Hindi';
                $data['seoDescription']  = 'Flypped Hindi के अनुभवी लेखकों से मिलें जो आपके लिए विश्वसनीय सामग्री तैयार करते हैं।';
                $data['seoKeywords']     = 'Flypped Hindi authors, लेखक';
                $data['static_content']  = $this->getAuthorsContent();
                break;

            default:
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $renderedView = view('categories', $data);
        $this->cache->save($cacheKey, $renderedView, 3600);

        return $renderedView;
    }

    // =========================================================
    //  Static page content methods
    // =========================================================
    private function getDisclaimerContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>Disclaimer / अस्वीकरण</h4>
                <div class='content-text'>
                    <p>इस वेबसाइट पर सभी सामग्री इंटरनेट से शोध करके प्रस्तुत की गई है। Flypped Hindi किसी भी समाचार की सत्यता की गारंटी नहीं देता।</p>
                    <p>किसी भी विवाद या समस्या के मामले में कृपया हमें <strong>support@flyppedhindi.com</strong> पर ईमेल करें।</p>
                    <p>इस वेबसाइट पर दी गई जानकारी केवल सामान्य सूचना उद्देश्यों के लिए है। किसी भी जानकारी पर निर्भरता पूरी तरह आपके अपने जोखिम पर है।</p>
                    <p>Flypped Hindi किसी भी प्रकार की हानि या क्षति के लिए उत्तरदायी नहीं होगा।</p>
                    <p>इस वेबसाइट के माध्यम से आप अन्य वेबसाइटों से जुड़ सकते हैं जो हमारे नियंत्रण में नहीं हैं।</p>
                </div>
            </div>
        ";
    }

    private function getPrivacyPolicyContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>Privacy Policy / गोपनीयता नीति</h4>
                <div class='content-text'>
                    <p>Flypped Hindi आपकी गोपनीयता का सम्मान करता है। यह नीति बताती है कि हम आपकी जानकारी कैसे एकत्र और उपयोग करते हैं।</p>
                    <p>हम निम्नलिखित जानकारी एकत्र कर सकते हैं: नाम, ईमेल, डिवाइस जानकारी और उपयोग व्यवहार।</p>
                    <p>हम आपका डेटा तीसरे पक्ष को नहीं बेचते। हम केवल सेवा सुधार के लिए डेटा का उपयोग करते हैं।</p>
                    <p>किसी भी प्रश्न के लिए: <strong>support@flyppedhindi.com</strong></p>
                </div>
            </div>
        ";
    }

    private function getAboutUsContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>हमारे बारे में</h4>
                <div class='content-text'>
                    <p>Flypped Hindi एक विश्वसनीय डिजिटल हिंदी न्यूज़ पोर्टल है जो पाठकों को सटीक, भरोसेमंद और सरल भाषा में जानकारी प्रदान करता है।</p>
                    <p>हमारा उद्देश्य हिंदी पाठकों तक हर प्रकार की ताज़ा खबरें — समाचार, मनोरंजन, स्वास्थ्य, टेक्नोलॉजी, खेल और लाइफस्टाइल — एक ही जगह पहुँचाना है।</p>
                    <p>संपर्क: <strong>support@flyppedhindi.com</strong></p>
                </div>
            </div>
        ";
    }

    private function getContactContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>संपर्क करें</h4>
                <div class='content-text'>
                    <p>किसी भी सुझाव, शिकायत या सहयोग के लिए हमसे संपर्क करें:</p>
                    <p><strong>ईमेल:</strong> support@flyppedhindi.com</p>
                    <p><strong>संपादकीय:</strong> editors@flyppedhindi.com</p>
                    <p><strong>विज्ञापन:</strong> ads@flyppedhindi.com</p>
                    <p><strong>वेबसाइट:</strong> https://flyppedhindi.com</p>
                </div>
            </div>
        ";
    }

    private function getWriteForUsContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>हमारे लिए लिखें</h4>
                <div class='content-text'>
                    <p>क्या आप हिंदी में लिखना पसंद करते हैं? Flypped Hindi के लिए लिखें और अपने विचार लाखों पाठकों तक पहुँचाएं।</p>
                    <p><strong>हम किस तरह का कंटेंट चाहते हैं:</strong></p>
                    <p>समाचार, स्वास्थ्य, टेक्नोलॉजी, लाइफस्टाइल, शिक्षा और खेल से जुड़े मौलिक हिंदी लेख।</p>
                    <p><strong>संपर्क करें:</strong> editors@flyppedhindi.com</p>
                </div>
            </div>
        ";
    }

    private function getAuthorsContent()
    {
        return "
            <div class='static-content'>
                <h4 class='mb-4'>हमारे लेखक</h4>
                <div class='content-text'>
                    <p>Flypped Hindi की टीम अनुभवी और समर्पित लेखकों से बनी है जो आपके लिए सर्वोत्तम सामग्री तैयार करते हैं।</p>
                    <p>हमारे सभी लेखक अपने-अपने क्षेत्र के विशेषज्ञ हैं और पाठकों को सटीक व विश्वसनीय जानकारी देने के लिए प्रतिबद्ध हैं।</p>
                    <p>लेखक बनने के लिए: <strong>editors@flyppedhindi.com</strong></p>
                </div>
            </div>
        ";
    }
}