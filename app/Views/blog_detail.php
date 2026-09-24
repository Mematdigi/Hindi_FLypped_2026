<?php include(APPPATH . 'Views/components/header.php'); ?>

<section class="breadcrumb-section mt-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <p class="mb-0 breadcrumb-text text-start">
                        <a href="<?php echo base_url(); ?>" class="text-dark text-decoration-none text-uppercase fw-semibold">Home</a>
                        
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <span class="mx-2">|</span>
                                <a href="<?php echo base_url($category['slug']); ?>" class="text-dark text-decoration-none text-uppercase fw-semibold">
                                    <?php echo html_entity_decode($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <span class="mx-2">|</span>
                        <span class="text-muted">
                            <?php 
                            $title = htmlspecialchars($post['post_title']);
                            echo (strlen($title) > 80) ? substr($title, 0, 80) . '...' : $title;
                            ?>
                        </span>
                    </p>
                </nav>
            </div>
        </div>
    </div>
</section>

<main id="main-content">
<section class="blog-details py-5" itemscope itemtype="https://schema.org/NewsArticle">
  <div class="container">
    <div class="row">
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
      <ins class="adsbygoogle"
           style="display:block"
           data-ad-client="ca-pub-7949838781204630"
           data-ad-slot="1739499306"
           data-ad-format="auto"
           data-full-width-responsive="true"></ins>
      <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

      <div class="col-lg-8 col-md-12">
        <article class="blog-content-container" itemprop="articleBody">
          <header class="blog-header">
            <?php if (!empty($post)): ?>
              <h1 itemprop="headline"><?= htmlspecialchars($post['post_title'] ?? 'Untitled'); ?></h1>
              <!-- The "By Author | Date" meta section has been removed from here -->
            <?php else: ?>
              <p>Blog post not found.</p>
            <?php endif; ?>
          </header>

          <?php if (!empty($post['featured_image'])): ?>
            <?php
              $imgAlt = !empty($post['post_title'])
                        ? htmlspecialchars($post['post_title'])
                        : 'Featured image for article';
            ?>
            <figure class="blog-thumbnail my-4" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
              <img src="<?= $post['featured_image']; ?>" alt="<?= $imgAlt; ?>" class="img-fluid">
              <meta itemprop="url" content="<?= $post['featured_image']; ?>">
            </figure>
          <?php endif; ?>

            <div class="blog-content justified" id="blogContent">
    <?php if (!empty($post['post_content'])): ?>
        <?php
            $content = $post['post_content'];
            
            // 1) Decode HTML entities first
            $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // 2) Remove inline styles from images
            $content = preg_replace('/<img(.*?)style="[^"]*"(.*?)>/i', '<img$1$2>', $content);
            $content = preg_replace('/<img(.*?)width="[^"]*"(.*?)>/i',  '<img$1$2>', $content);
            $content = preg_replace('/<img(.*?)height="[^"]*"(.*?)>/i', '<img$1$2>', $content);

            // 3) Remove text-align styles
            $content = preg_replace('/style="[^"]*(text-align:[^;"]+;?)[^"]*"/i', '', $content);

            // 4) Auto-embed Tweets
            if (function_exists('embedTweet')) {
                $content = embedTweet($content);
            }

            // 5) Output the processed content
            echo $content;
        ?>
    <?php else: ?>
        <p>No content available for this post.</p>
    <?php endif; ?>
</div>

<?php 
// 🚀 FIXED TAGS LOGIC: Handle both string and array formats
$displayTags = [];
if (!empty($post['tags'])) {
    if (is_array($post['tags'])) {
        $displayTags = $post['tags'];
    } elseif (is_string($post['tags'])) {
        $displayTags = explode(',', $post['tags']); // Comma-separated tags ko array me convert kiya
    }
}

if (!empty($displayTags)): 
?>
<div class="blog-tags mt-4 mb-4" id="tagsSection" style="background: transparent; padding: 0;">
    <div class="d-flex align-items-center flex-wrap gap-2">
    <span class="text-muted fw-bold me-3" style="font-size: 16px; color: #6c757d;">Discover more:</span>
    <?php foreach ($displayTags as $tag): 
        $tagName = trim((string)$tag);
        if (empty($tagName)) continue; // Empty tags skip karein
        $tagSlug = strtolower(preg_replace('~[^a-z0-9]+~i', '-', $tagName));
    ?>
        <a class="btn btn-outline-primary btn-sm rounded-pill d-inline-flex align-items-center" style="font-size: 13.5px; padding: 5px 14px; border-color: #0d6efd; color: #0d6efd; text-decoration: none; transition: 0.3s;" href="<?= base_url('search?tag=' . urlencode($tagSlug)) ?>" title="View posts tagged '<?= htmlspecialchars($tagName) ?>'">
            <i class="fas fa-hashtag me-1" style="margin-right: 4px;"></i> <?= htmlspecialchars($tagName) ?>
        </a>
    <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php 
$latest_faqs = [];
try {
    $db = \Config\Database::connect();
    
    // Get Post ID dynamically
    $postId = $post['ID'] ?? $post['id'] ?? 0;
    if (!$postId && !empty($post['post_title'])) {
        $postRow = $db->query("SELECT ID FROM wp_posts WHERE post_title = ? LIMIT 1", [$post['post_title']])->getRow();
        if ($postRow) $postId = $postRow->ID;
    }

    if ($postId) {
        // Fetch new FAQs format first
        $faq_query = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key = '_flypped_faqs' ORDER BY meta_id DESC LIMIT 1", [$postId]);
        $faq_row = $faq_query->getRow();
        
        if ($faq_row && !empty($faq_row->meta_value)) {
            $latest_faqs = json_decode($faq_row->meta_value, true);
        } else {
            // Fallback to old format
            $alt_query = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id = ? AND meta_key IN ('faqs', 'faq_schema', '_faq_schema') LIMIT 1", [$postId]);
            $alt_row = $alt_query->getRow();
            if ($alt_row) {
                $latest_faqs = @json_decode($alt_row->meta_value, true) ?: @unserialize($alt_row->meta_value);
            }
        }
    }
} catch (\Exception $e) { } 

$faqList = (!empty($latest_faqs) && is_array($latest_faqs)) ? $latest_faqs : [];

// Purana fallback jo Hindi me tha usko bhi check lenge
if (empty($faqList) && !empty($postId)) {
    $allFaqMeta = $db->query(
        "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = ? AND (meta_key LIKE '_faq_question_%' OR meta_key LIKE '_faq_answer_%') ORDER BY meta_key ASC",
        [$postId]
    )->getResultArray();
    $old_faqs = [];
    foreach ($allFaqMeta as $row) {
        if (preg_match('/_faq_(question|answer)_(\d+)$/', $row['meta_key'], $m)) {
            $old_faqs[(int)$m[2]][$m[1]] = $row['meta_value'];
        }
    }
    ksort($old_faqs);
    if (!empty($old_faqs)) $faqList = $old_faqs;
}

// Validate & Normalize FAQ Array before checking HTML
$valid_faqs = [];
if (!empty($faqList) && is_array($faqList)) {
    // Agar data kisi aur array key me nested hai (e.g., 'faqs' wrapper ya Yoast/RankMath schema format)
    if (isset($faqList['faqs']) && is_array($faqList['faqs'])) {
        $faqList = $faqList['faqs'];
    } elseif (isset($faqList['mainEntity']) && is_array($faqList['mainEntity'])) {
        $faqList = $faqList['mainEntity'];
    }

    foreach ($faqList as $faq) {
        if (is_array($faq)) {
            // Har possible key structure ko check karein
            $q = $faq['question'] ?? $faq['q'] ?? $faq['faq_question'] ?? $faq['name'] ?? '';
            $a = $faq['answer'] ?? $faq['a'] ?? $faq['faq_answer'] ?? $faq['text'] ?? '';
            
            // Schema structure data check
            if (empty($a) && isset($faq['acceptedAnswer']['text'])) {
                $a = $faq['acceptedAnswer']['text'];
            }

            if (!empty($q) && !empty($a)) {
                $valid_faqs[] = [
                    'question' => trim($q),
                    'answer'   => trim($a)
                ];
            }
        }
    }
}

// Ensure hum wrapper tabhi dikhaye jab valid FAQs ho
if (!empty($valid_faqs)): 
?>

<div class="mt-5 mb-4">
    <div class="d-flex align-items-center gap-2 border-bottom border-primary pb-2 mb-4">
        <i class="fas fa-question-circle text-primary fs-4"></i>
        <h2 class="h4 fw-bold mb-0 text-dark">अक्सर पूछे जाने वाले सवाल (FAQ)</h2>
    </div>

    <div class="accordion" id="faqAccordion">
        <?php foreach ($valid_faqs as $i => $faq): 
            $q = $faq['question'];
            $a = $faq['answer'];
            $headingId  = 'faqHeading'  . $i;
            $collapseId = 'faqCollapse' . $i;
        ?>
        <div class="accordion-item border rounded-3 mb-3 shadow-sm">
            <h3 class="accordion-header" id="<?= $headingId ?>">
                <button class="accordion-button fw-semibold rounded-3"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#<?= $collapseId ?>"
                        aria-expanded="true"
                        aria-controls="<?= $collapseId ?>">
                    <span class="badge bg-primary rounded-pill me-3 flex-shrink-0">
                        Q<?= $i + 1 ?>
                    </span>
                    <?= esc($q) ?>
                </button>
            </h3>
            <div id="<?= $collapseId ?>"
                 class="accordion-collapse collapse show"
                 aria-labelledby="<?= $headingId ?>">
                <div class="accordion-body text-secondary lh-lg">
                    <i class="fas fa-circle-check text-success me-2"></i>
                    <?= nl2br(esc($a)) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        <?php 
        $schema_items = [];
        foreach ($valid_faqs as $faq) {
            $q = $faq['question'];
            $a = $faq['answer'];
            $schema_items[] = '{
                "@type": "Question",
                "name": "' . addslashes(esc($q)) . '",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "' . addslashes(esc($a)) . '"
                }
            }';
        }
        echo implode(',', $schema_items); 
        ?>
      ]
    }
    </script>
</div>
<?php endif; ?>

        </article>
        </div>
      
      <div class="col-lg-4 col-md-12">
        <div class="sidebar">
          <div class="portfolio-details mb-4">
            <h2>Blog Details</h2>
            <ul class="details-list" style="list-style: none; padding-left: 0;">
              <li class="mb-3 text-dark"><strong class="fw-bold">Author Name:</strong>
                <span class="text-uppercase ms-1 fw-normal" style="font-weight: 400 !important;">
                  <?php if (!empty($post['author_profile_url']) && $post['author_profile_url'] !== '#'): ?>
                    <a href="<?= $post['author_profile_url'] ?>" class="author-profile-link"
                       title="View <?= htmlspecialchars($post['author_name'] ?? 'Author') ?>'s Profile"
                       style="text-decoration: none; color: #495057; font-weight: 400 !important;">
                      <?= htmlspecialchars($post['author_name'] ?? 'Unknown Author') ?>
                      <i class="fas fa-external-link-alt ms-1" style="font-size: 12px;"></i>
                    </a>
                  <?php else: ?>
                    <span style="color: #495057;"><?= htmlspecialchars($post['author_name'] ?? 'Unknown Author') ?></span>
                  <?php endif; ?>
                </span>
              </li>
              <li class="mb-3 text-dark"><strong class="fw-bold">Category:</strong>
                <span class="text-uppercase ms-1 fw-normal" style="font-weight: 400 !important;">
                  <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $category): ?>
                      <a href="<?= base_url(htmlspecialchars($category['slug'] ?? 'uncategorized')) ?>"
                         class="category-link" style="text-decoration: none; color: #0d6efd; font-weight: 400 !important;">
                        <?= htmlspecialchars($category['name'] ?? '') ?>
                      </a><?php if ($index < count($categories) - 1): ?>, <?php endif; ?>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <span style="color: #495057;">No Categories</span>
                  <?php endif; ?>
                </span>
              </li>
              <li class="text-dark"><strong class="fw-bold">Updated:</strong>
                <!-- Sidebar Custom Date Display -->
                <span class="ms-1 fw-normal" style="color: #495057; font-weight: 400 !important;">
                  <?= !empty($post['post_date']) ? date('D, d M Y h:i A', strtotime($post['post_date'])) . ' (IST)' : 'Unknown Date' ?>
                </span>
              </li>
             
            </ul>
          </div>

          <div class="related-blogs latest-news mb-4">
            <h2 class="related-title">Related Blogs</h2>
            <?php if (!empty($related_blogs)): ?>
              <?php foreach ($related_blogs as $related_blog): ?>
                <div class="news-item mb-3">
                  <img src="<?= $related_blog['thumbnail_url'] ?? ''; ?>" class="news-thumb img-fluid"
                       alt="<?= htmlspecialchars($related_blog['post_title'] ?? 'Related Blog'); ?>">
                  <div class="news-details">
                    <h3><a href="<?= $related_blog['blog_detail_url'] ?? '#'; ?>">
                      <?= htmlspecialchars($related_blog['post_title'] ?? 'Untitled'); ?></a></h3>
                    <p class="author-date">
                      <?= !empty($related_blog['post_date']) ? date('F d, Y', strtotime($related_blog['post_date'])) : '' ?>
                    </p>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No related blogs available.</p>
            <?php endif; ?>
          </div>

          <div class="latest-news mb-4">
            <h2 class="related-title">Exclusive Blog</h2>
            <?php if (!empty($exclusive_news_posts)): ?>
              <?php foreach ($exclusive_news_posts as $news): ?>
                <div class="news-item mb-3">
                  <img src="<?= $news['thumbnail_url'] ?? 'default-thumbnail.jpg'; ?>" class="news-thumb img-fluid"
                       alt="<?= htmlspecialchars($news['post_title'] ?? 'Untitled'); ?>">
                  <div class="news-details">
                    <h3><a href="<?= $news['blog_detail_url'] ?? '#'; ?>">
                      <?= htmlspecialchars($news['post_title'] ?? 'Untitled'); ?></a></h3>
                    <p class="author-date">
                      <?= !empty($news['post_date']) ? date('F d, Y', strtotime($news['post_date'])) : 'Unknown Date'; ?>
                    </p>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No blogs available.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <?php
    $canonical = current_url();
    $categoryName = !empty($categories[0]['name']) ? html_entity_decode($categories[0]['name'], ENT_QUOTES, 'UTF-8') : '';
    $imgUrl   = !empty($post['featured_image']) ? $post['featured_image'] : '';
    
    // Schema SEO custom Date rendering
    $pubIso   = !empty($post['post_date']) ? date('c', strtotime($post['post_date'])) : '';
    $modIso   = !empty($post['updated_at']) ? date('c', strtotime($post['updated_at'])) : $pubIso;
    
    $headline = $post['post_title'] ?? '';
    $authorNm = $post['author_name'] ?? 'Unknown Author';
  ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "mainEntityOfPage": {"@type":"WebPage","@id":"<?= $canonical ?>"},
    "headline": <?= json_encode($headline) ?>,
    "image": <?= json_encode($imgUrl ? [$imgUrl] : []) ?>,
    "datePublished": "<?= $pubIso ?>",
    "dateModified": "<?= $modIso ?>",
    "author": {"@type":"Person","name": <?= json_encode($authorNm) ?>},
    "publisher": {
      "@type":"Organization",
      "name": "Flypped Hindi",
      "logo": {"@type":"ImageObject","url":"<?= base_url('public/assest/images/logo.webp'); ?>"}
    },
    "articleSection": <?= json_encode($categoryName) ?>
  }
  </script>

  <?php if (!empty($categories)): ?>
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[
      {"@type":"ListItem","position":1,"name":"Home","item":"<?= base_url(); ?>"}<?php
        $pos = 2;
        foreach($categories as $cat){
          $name = html_entity_decode($cat['name'], ENT_QUOTES, 'UTF-8');
          $url  = base_url($cat['slug']);
          echo ',{"@type":"ListItem","position":'.$pos++.',"name":'.json_encode($name).',"item":"'.$url.'"}';
        }
      ?>,
      {"@type":"ListItem","position":<?= (isset($pos)?$pos:2) ?>,"name":<?= json_encode($headline) ?>,"item":"<?= $canonical ?>"}
    ]
  }
  </script>
  <?php endif; ?>
</section>
</main>

<?php include(APPPATH . 'Views/components/footer.php'); ?>
<style>
    /* ===== CKEditor Table Styles for Frontend ===== */
    .content-area table,
    .post-content table,
    .blog-content table,
    article table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 20px 0;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 2px solid #333 !important;
    }

    .content-area table thead,
    .post-content table thead,
    .blog-content table thead,
    article table thead {
        background-color: #2c3e50;
        color: #fff;
    }

    .content-area table th,
    .post-content table th,
    .blog-content table th,
    article table th {
        padding: 12px 15px !important;
        text-align: left;
        font-weight: 600;
        border: 1px solid #333 !important;
        border-right: 1px solid #333 !important;
        border-left: 1px solid #333 !important;
        font-size: 14px;
    }

    .content-area table td,
    .post-content table td,
    .blog-content table td,
    article table td {
        padding: 12px 15px !important;
        border: 1px solid #333 !important;
        border-right: 1px solid #333 !important;
        border-left: 1px solid #333 !important;
        font-size: 14px;
        color: #333;
    }

    .content-area table tbody tr:hover,
    .post-content table tbody tr:hover,
    .blog-content table tbody tr:hover,
    article table tbody tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.3s ease;
    }

    /* Responsive Table */
    @media screen and (max-width: 768px) {
        .content-area table,
        .post-content table,
        .blog-content table,
        article table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* Alternating row colors */
    .content-area table tbody tr:nth-child(even),
    .post-content table tbody tr:nth-child(even),
    .blog-content table tbody tr:nth-child(even),
    article table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .blog-details .related-blogs{
        padding: 8px;
    }
    .latest-news{
        background-color: #f5f5f5;
        padding: 8px;
    }

    /* Remove any footer or injected links below embeds */
    .tweet-embed + a,
    .tweet-embed a[href*="x.com"][role="link"],
    .tweet-embed a[href*="twitter.com"][role="link"],
    blockquote.twitter-tweet + a[href*="x.com"],
    blockquote.twitter-tweet + a[href*="twitter.com"] {
      display: none !important;
    }

    /* Make embeds responsive */
    .tweet-embed { width:100%; max-width:100%; }
    .tweet-embed blockquote.twitter-tweet{ margin:1rem auto !important; width:100% !important; max-width:100% !important; }

    /* If X injects a trailing link after the iframe, hide it */
    .tweet-embed .twitter-tweet-rendered + a,
    .tweet-embed blockquote.twitter-tweet + a,
    .tweet-embed > a { display:none !important; }

    /* Extra safety: hide any link that X appends inside the wrapper */
    .tweet-embed .twitter-tweet-rendered ~ a[href*="x.com"],
    .tweet-embed .twitter-tweet-rendered ~ a[href*="twitter.com"]{
      display:none !important;
    }

    /* ===== Video Embedded Class ===== */
    .video-responsive-wrapper {
      position: relative;
      padding-bottom: 56.25%; /* 16:9 ratio */
      height: 0;
      overflow: hidden;
      margin: 30px 0;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      background: #000;
    }
    .video-responsive-wrapper iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    /* ===== Breadcrumb ===== */
    .breadcrumb-section { border-bottom: 1px solid #dee2e6; }
    .breadcrumb-text { font-size: 14px; line-height: 1.6; }
    .breadcrumb-text a:hover { color: #007bff !important; }

    /* ===== Blog Container ===== */
    .blog-content-container{
      position: relative;
      background:#fff;
      border-radius:12px;
      padding:30px;
      box-shadow:0 2px 15px rgba(0,0,0,.08);
      margin-bottom:30px;
    }

    /* ========================================================= */
    /* ===== CRITICAL FIX: FORCE BLOG CONTENT TO BE VISIBLE ==== */
    /* ========================================================= */
    .blog-content{
      position: relative;
      font-size: 17px;
      line-height: 1.8;
      color: #2c3e50;
      padding-bottom: 20px;
      word-wrap: break-word;
      overflow-wrap: anywhere;
      /* Overrides any external/cached height limits */
      height: auto !important; 
      max-height: none !important; 
      overflow: visible !important;
      display: block !important;
    }

    /* Force override any lingering 'hidden-content' classes */
    .blog-content .hidden-content,
    .blog-content [style*="display: none"],
    .blog-content.truncated {
        display: block !important;
        max-height: none !important;
        overflow: visible !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    /* ========================================================= */

    /* Paragraphs / images */
    .blog-content.justified p,
    .blog-content.justified div{
      text-justify:inter-word;
      line-height:1.8;
      margin-bottom:1.2rem;
      color:#2c3e50;
    }
    .blog-content.justified strong{ font-weight:600; color:#000; }
    .blog-content.justified img{
      display:block;
      margin:1.5rem auto;
      border-radius:8px;
      max-width:100%;
      height:auto;
    }

    /* ===== Responsive Twitter/X embeds ===== */
    .blog-content blockquote.twitter-tweet{
      margin:1rem auto !important;
      max-width:100% !important;
      width:100% !important;
      min-width:0 !important;
      box-sizing:border-box;
    }
    .blog-content blockquote.twitter-tweet > a{ display:block !important; }
    .blog-content .twitter-tweet-rendered{ max-width:100% !important; width:100% !important; }
    .blog-content blockquote.twitter-tweet iframe{
      max-width:100% !important; width:100% !important; min-width:0 !important;
    }

    /* ===== Header / Meta ===== */
    .blog-header{ margin-bottom:30px; padding-bottom:20px; border-bottom:3px solid #167ac6; }
    .blog-header h1{ font-size:32px; font-weight:700; color:#2c3e50; margin-bottom:15px; line-height:1.3; }
    .blog-meta{ color:#6c757d; font-size:15px; }

    /* ===== Featured Image ===== */
    .blog-thumbnail{ border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1); margin:30px 0; }
    .blog-thumbnail img{ width:100%; height:auto; display:block; }

    /* ===== Tags ===== */
    .blog-tags{
      padding:25px; background:#f8f9fa; border-radius:10px; margin-top:30px;
    }
    .blog-tags h5{ color:#2c3e50; font-weight:600; margin-bottom:15px; }
    .blog-tags .badge{
      background:#167ac6; color:#fff; padding:8px 16px; border-radius:20px; margin:5px; font-size:14px; font-weight:500;
    }

    /* ===== Sidebar ===== */
    .sidebar{
      position:sticky; top:100px;
      background:#fff; border-radius:12px; padding:25px;
      box-shadow:0 2px 15px rgba(0,0,0,.08); border:1px solid #e9ecef;
    }

    /* Sidebar cards */
    .sidebar .latest-news .news-item{
      display:block; padding:10px 12px; border-radius:12px; background:#fff; border:1px solid #eef0f4;
      transition:box-shadow .15s ease, transform .15s ease;
    }
    .sidebar .latest-news .news-item:hover{ transform:translateY(-1px); box-shadow:0 6px 18px rgba(16,24,40,.08); }
    .sidebar .latest-news .news-item .news-thumb{
      width:100%; height:140px; object-fit:cover; border-radius:10px; display:block; margin-bottom:8px;
    }
    .sidebar .latest-news .news-item h3,
    .sidebar .related-blogs .news-item h3{ font-size:15px; line-height:1.35; margin:6px 0 4px; font-weight:700; }
    .sidebar .latest-news .news-item h3 a,
    .sidebar .related-blogs .news-item h3 a{ color:#111827; text-decoration:none; }
    .sidebar .latest-news .news-item h3 a:hover,
    .sidebar .related-blogs .news-item h3 a:hover{ color:#0f5a94; text-decoration:none; }
    .sidebar .latest-news .news-item .author-date{ font-size:12px; color:#6b7280; margin:2px 0 0; }

    /* Generic card list */
    .news-item{ display:flex; gap:15px; padding:15px; background:#f8f9fa; border-radius:10px; transition:all .3s ease; margin-bottom:15px; }
    .news-item:hover{ background:#e9ecef; transform:translateX(5px); }
    .news-item .news-thumb{ width:80px; height:80px; object-fit:cover; border-radius:8px; flex-shrink:0; }
    .news-item h6{ font-size:14px; margin-bottom:8px; }
    .news-item h6 a{ color:#2c3e50; text-decoration:none; font-weight:600; transition:color .3s ease; }
    .news-item h6 a:hover{ color:#167ac6; }
    .news-item .author-date{ font-size:12px; color:#6c757d; margin:0; }

    /* ===== Responsive ===== */
    @media (min-width: 992px){
      .blog-details .col-lg-8{ flex:0 0 auto; width:66.6667%; }
      .blog-details .col-lg-4{ flex:0 0 auto; width:33.3333%; }
    }
    @media (max-width: 992px){ .sidebar{ position:relative; top:0; margin-top:0px; } }
    @media (max-width: 768px){
      .blog-content{ font-size:16px; }
      .blog-header h1{ font-size:24px; }
      .blog-content-container{ padding:20px; }
      .blog-content-container .blog-header h1{
          font-size: 30px;
        line-height: 45px;
      }
      .blog-details .col-lg-8 {
            margin-bottom: 0px;
        }
        .sidebar{
            padding: 12px;
        }
    }
</style>

<?php
if (!function_exists('embedTweet')) {
    function embedTweet(string $html): string
    {
        // 1) Pattern for any X/Twitter status URL
        $tweetUrl = '(https?:\/\/(?:www\.)?(?:twitter\.com|x\.com|mobile\.twitter\.com)\/[A-Za-z0-9_]+\/status\/\d+(?:\?[^"\'\s<]*)?)';

        $makeToken = function (string $url): string {
            return '__TWEET__' . base64_encode($url) . '__END__';
        };

        // a) Existing blockquotes -> token
        $blockquote = '~<blockquote[^>]*class="[^"]*\btwitter-tweet\b[^"]*"[^>]*>.*?<a[^>]+href="' . $tweetUrl . '"[^>]*>.*?<\/a>.*?<\/blockquote>~isu';
        $html = preg_replace_callback($blockquote, function ($m) use ($makeToken) {
            return $makeToken($m[1]);
        }, $html);

        // b) <a href="...status/...">...</a> -> token
        $anchor = '~<a[^>]+href="' . $tweetUrl . '"[^>]*>.*?<\/a>~isu';
        $html = preg_replace_callback($anchor, function ($m) use ($makeToken) {
            return $makeToken($m[1]);
        }, $html);

        // c) Bare URLs -> token (not inside quotes/tags)
        $bare = '~(?<![\'">])' . $tweetUrl . '(?![\'"<])~iu';
        $html = preg_replace_callback($bare, function ($m) use ($makeToken) {
            return $makeToken($m[1]);
        }, $html);

        $seen = [];
        $html = preg_replace_callback('~__TWEET__([A-Za-z0-9+/=]+)__END__~', function ($m) use (&$seen) {
            $raw = base64_decode($m[1], true);
            if ($raw === false) return '';
            $u = htmlspecialchars(trim($raw), ENT_QUOTES, 'UTF-8');
            if (isset($seen[$u])) return ''; // dedupe
            $seen[$u] = true;

            return '<div class="tweet-embed">'
                 .   '<blockquote class="twitter-tweet" data-dnt="true" data-conversation="none"'
                 .   ' data-chrome="nofooter noheader noborders transparent">'
                 .     '<a href="'.$u.'"></a>'
                 .   '</blockquote>'
                 . '</div>';
        }, $html);

        return $html;
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const blogContent = document.getElementById('blogContent');

  // =================================================================
  // SAFETY NET: Strip any hidden classes or inline styles dynamically
  // =================================================================
  if (blogContent) {
      blogContent.classList.remove('truncated');
      
      const hiddenElements = blogContent.querySelectorAll('.hidden-content, [style*="display: none"]');
      hiddenElements.forEach(el => {
          el.classList.remove('hidden-content');
          // If inline style is display:none, remove it
          if (el.style.display === 'none') {
              el.style.display = '';
          }
      });
  }

  function ensureTwitter(cb) {
    if (window.twttr && window.twttr.widgets) return cb && cb();
    const id = 'tw-widgets-js';
    if (document.getElementById(id)) {
      const wait = setInterval(() => {
        if (window.twttr && window.twttr.widgets) { clearInterval(wait); cb && cb(); }
      }, 200);
      return;
    }
    const s = document.createElement('script');
    s.id = id; s.async = true;
    s.src = 'https://platform.twitter.com/widgets.js';
    s.onload = () => cb && cb();
    document.head.appendChild(s);
  }

  function renderTweetsNow() {
    if (window.twttr && window.twttr.widgets) {
      window.twttr.widgets.load(blogContent);
    }
  }

  // Directly load and render embedded tweets on page load
  ensureTwitter(renderTweetsNow);
});
</script>