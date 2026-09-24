<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>


<style>
/* Preview Container Styles */
.preview-wrapper {
    background: #f5f5f5;
    min-height: 100vh;
    padding: 20px 0;
}

.preview-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.preview-banner h4 {
    margin: 0;
    font-size: 18px;
}

.preview-banner .btn {
    margin: 0 5px;
}

.preview-container {
    max-width: 1200px;
    margin: 30px auto;
    background: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

/* Blog Detail Styles - Matching Frontend */
.blog-details {
    padding: 40px;
}

.breadcrumb-section {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}

.breadcrumb-text {
    margin: 0;
    font-size: 14px;
}

.blog-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #167ac6;
}

.blog-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
    line-height: 1.3;
}

.blog-meta {
    color: #6c757d;
    font-size: 15px;
}

.blog-thumbnail {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
    margin: 30px 0;
}

.blog-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
}

.blog-content {
    font-size: 17px;
    line-height: 1.8;
    color: #2c3e50;
    padding-bottom: 20px;
}

.blog-content.justified p,
.blog-content.justified div {
    text-align: justify;
    text-justify: inter-word;
    line-height: 1.8;
    margin-bottom: 1.2rem;
    color: #2c3e50;
}

.blog-content.justified strong {
    font-weight: 600;
    color: #000;
}

.blog-content.justified img {
    display: block;
    margin: 1.5rem auto;
    border-radius: 8px;
    max-width: 100%;
    height: auto;
}

.blog-content h2 {
    font-size: 24px;
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 15px;
    color: #2c3e50;
}

.blog-content h3 {
    font-size: 20px;
    font-weight: 600;
    margin-top: 25px;
    margin-bottom: 12px;
    color: #2c3e50;
}

.blog-tags {
    padding: 25px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-top: 30px;
}

.blog-tags h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
}

.tag-chip {
    display: inline-block;
    padding: .35rem .7rem;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    background: #f8fafc;
    font-size: .85rem;
    color: #111827;
    text-decoration: none;
    margin: 5px;
}

.tag-chip:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #1f3bb3;
}

/* Sidebar Styles */
.sidebar_preview {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,.08);
    border: 1px solid #e9ecef;
}

.portfolio-details h2,
.related-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
}

.details-list {
    list-style: none;
    padding: 0;
}

.details-list li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.details-list li:last-child {
    border-bottom: none;
}

/* Debug Alert */
.debug-alert {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.debug-alert img {
    max-width: 200px;
    margin-top: 10px;
    border: 2px solid #ffc107;
}

/* Responsive */
@media (max-width: 768px) {
    /*.blog-details {*/
    /*    padding: 20px;*/
    /*}*/
    
    .blog-header h1 {
        font-size: 24px;
    }
    
    .blog-content {
        font-size: 16px;
    }
}

/* Twitter Embed Styles */
.tweet-embed {
    width: 100%;
    max-width: 100%;
    margin: 20px 0;
}

.tweet-embed blockquote.twitter-tweet {
    margin: 1rem auto !important;
    width: 100% !important;
    max-width: 100% !important;
}
</style>

<div class="preview-wrapper">
    <!-- Preview Banner -->
    <!--<div class="preview-banner">-->
    <!--    <div class="container-fluid">-->
    <!--        <div class="d-flex justify-content-between align-items-center flex-wrap">-->
    <!--            <div>-->
    <!--                <h4><i class="fas fa-eye"></i> PREVIEW MODE - This is how your post will look on the live site</h4>-->
    <!--            </div>-->
    <!--            <div>-->
    <!--                <button onclick="window.history.back()" class="btn btn-light btn-sm">-->
    <!--                    <i class="fas fa-arrow-left"></i> Back to Editor-->
    <!--                </button>-->
    <!--                <button onclick="window.print()" class="btn btn-info btn-sm">-->
    <!--                    <i class="fas fa-print"></i> Print-->
    <!--                </button>-->
    <!--                <button onclick="if(confirm('Close preview?')) window.close();" class="btn btn-secondary btn-sm">-->
    <!--                    <i class="fas fa-times"></i> Close-->
    <!--                </button>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

   

    <!-- Preview Content -->
    <div class="preview-container">
        <!-- Breadcrumb -->
        <section class="breadcrumb-section">
            <div class="container">
                <p class="breadcrumb-text">
                    <a href="<?= base_url(); ?>" class="text-dark text-decoration-none text-uppercase fw-semibold">Home</a>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <span class="mx-2">|</span>
                            <span class="text-dark text-uppercase fw-semibold">
                                <?= htmlspecialchars($category['name']); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <span class="mx-2">|</span>
                    <span class="text-muted">
                        <?php 
                        $title = htmlspecialchars($post['post_title'] ?? 'Untitled');
                        echo (strlen($title) > 80) ? substr($title, 0, 80) . '...' : $title;
                        ?>
                    </span>
                </p>
            </div>
        </section>

        <!-- Blog Content -->
        <section class="blog-details">
            <div class="container">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <article>
                            <!-- Blog Header -->
                            <header class="blog-header">
                                <h1><?= htmlspecialchars($post['post_title'] ?? 'Untitled'); ?></h1>
                                <div class="blog-meta">
                                    <span>
                                        By <strong><?= htmlspecialchars($post['author_name'] ?? 'Unknown'); ?></strong>
                                        | 
                                        <time><?= date('F d, Y', strtotime($post['post_date'] ?? 'now')); ?></time>
                                    </span>
                                </div>
                            </header>

                            <!-- Featured Image - SIMPLIFIED -->
                            <!-- Featured Image (strict) -->
                            <?php if (!empty($post['featured_image'])): ?>
                              <div class="blog-thumbnail">
                                <img
                                  src="<?= htmlspecialchars($post['featured_image']); ?>"
                                  alt="<?= htmlspecialchars($post['post_title'] ?? 'Featured image'); ?>"
                                  loading="eager"
                                  decoding="async">
                              </div>
                            <?php endif; ?>


                            <!-- Blog Content -->
                            <div class="blog-content justified">
                                <?php if (!empty($post['post_content'])): ?>
                                    <?php
                                    // Clean up content
                                    $content = preg_replace('/<img(.*?)style="[^"]*"(.*?)>/i', '<img$1$2>', $post['post_content']);
                                    $content = preg_replace('/<img(.*?)width="[^"]*"(.*?)>/i', '<img$1$2>', $content);
                                    $content = preg_replace('/<img(.*?)height="[^"]*"(.*?)>/i', '<img$1$2>', $content);
                                    $content = preg_replace('/style="[^"]*(text-align:[^;"]+;?)[^"]*"/i', '', $content);
                                    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    
                                    echo $content;
                                    ?>
                                <?php else: ?>
                                    <p>No content available.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Tags -->
                            <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
                                <div class="blog-tags">
                                    <h5>Tags:</h5>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($post['tags'] as $tag): ?>
                                            <span class="tag-chip"><?= htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </article>
                        
                        
                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <div class="sidebar_preview">
                            <div class="portfolio-details ">
                                <h2>Post Details</h2>
                                <ul class="details-list">
                                    <li>
                                        <strong>Author:</strong> 
                                        <?= htmlspecialchars($post['author_name'] ?? 'Unknown'); ?>
                                    </li>
                                    <li>
                                        <strong>Category:</strong>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $index => $category): ?>
                                                <?= htmlspecialchars($category['name']); ?><?php if ($index < count($categories) - 1): ?>, <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            No Categories
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <strong>Date:</strong> 
                                        <?= date('F d, Y', strtotime($post['post_date'] ?? 'now')); ?>
                                    </li>
                                    <li>
                                        <strong>Status:</strong> 
                                        <span class="badge bg-warning text-dark">Preview / Draft</span>
                                    </li>
                                    <!--<li>-->
                                    <!--    <strong>Featured Image:</strong> -->
                                    <!--    <?php if (!empty($post['featured_image'])): ?>-->
                                    <!--        <span class="badge bg-success"> Yes</span>-->
                                    <!--    <?php else: ?>-->
                                    <!--        <span class="badge bg-danger">❌ No</span>-->
                                    <!--    <?php endif; ?>-->
                                    <!--</li>-->
                                </ul>
                            </div>

                        </div>
                    </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>

<!-- Twitter Widgets Script -->
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<script>
// Load Twitter widgets after page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.twttr && window.twttr.widgets) {
        window.twttr.widgets.load();
    }
});

// Print styles
window.addEventListener('beforeprint', function() {
    const banner = document.querySelector('.preview-banner');
    if (banner) banner.style.display = 'none';
});

window.addEventListener('afterprint', function() {
    const banner = document.querySelector('.preview-banner');
    if (banner) banner.style.display = 'block';
});
</script>

<?php $viewContext = 'preview'; ?>

<style>


/* Remove any footer or injected links below embeds */
.tweet-embed + a,
.tweet-embed a[href*="x.com"][role="link"],
.tweet-embed a[href*="twitter.com"][role="link"],
blockquote.twitter-tweet + a[href*="x.com"],
blockquote.twitter-tweet + a[href*="twitter.com"] {
  display: none !important;
}

/* Make embeds responsive (you already have similar rules) */
.tweet-embed { width:100%; max-width:100%; }
.tweet-embed blockquote.twitter-tweet{ margin:1rem auto !important; width:100% !important; max-width:100% !important; }

/* If X injects a trailing link after the iframe, hide it */
.tweet-embed .twitter-tweet-rendered + a,
.tweet-embed blockquote.twitter-tweet + a,
.tweet-embed > a { display:none !important; }

/* Extra safety: hide any link that X appends inside the wrapper */
.tweet-embed a[href*="twitter.com"],
.tweet-embed a[href*="x.com"]{
  /* don’t hide the anchor inside the blockquote pre-render */
}
.tweet-embed .twitter-tweet-rendered ~ a[href*="x.com"],
.tweet-embed .twitter-tweet-rendered ~ a[href*="twitter.com"]{
  display:none !important;
}


</style>

<?php
function embedTweet(string $html): string
{
    // 1) Pattern for any X/Twitter status URL
    $tweetUrl = '(https?:\/\/(?:www\.)?(?:twitter\.com|x\.com|mobile\.twitter\.com)\/[A-Za-z0-9_]+\/status\/\d+(?:\?[^"\'\s<]*)?)';

    // Use a safe token that will never show up in real content
    $makeToken = function (string $url): string {
        return '__TWEET__' . base64_encode($url) . '__END__';
    };

    // --- Convert existing embeds/links/urls into tokens ---

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

    // --- Replace tokens with our controlled embed (no footer/header/borders) ---
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

    return $html; // no bracket cleanup needed anymore
}
?>



<script>
document.addEventListener('DOMContentLoaded', function () {
  const blogContent = document.getElementById('blogContent');
  const readMoreBtn = document.getElementById('readMoreBtn');
  const readMoreContainer = document.getElementById('readMoreContainer');
  const contentFade = document.getElementById('contentFade');

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

function setupReadMore() {
    const paras = blogContent.querySelectorAll('p');

    // If the article is short (2 or fewer paragraphs), show all content and render tweets
    if (paras.length <= 2) {
        blogContent.classList.remove('truncated');
        contentFade.style.display = 'none';
        readMoreContainer.style.display = 'none';
        ensureTwitter(renderTweetsNow);
        return;
    }

    // Hide everything after the 2nd paragraph EXCEPT video embeds (iframe or video containers)
    let hide = false;
    Array.from(blogContent.children).forEach(el => {
        // Don't hide video containers or iframe elements
        const isVideoContainer = el.style.position === 'relative' && el.style.paddingBottom === '56.25%';
        const hasIframe = el.querySelector('iframe');
        
        // If it's after the 2nd paragraph and not a video embed, hide it
        if (hide && !isVideoContainer && !hasIframe) {
            el.classList.add('hidden-content');
        }
        
        // Set flag to start hiding content after the 2nd paragraph
        if (el === paras[1]) hide = true;
    });

    // Apply the truncated class and ensure the "Read More" button appears
    blogContent.classList.add('truncated');
    contentFade.style.display = 'block';
    readMoreContainer.style.display = 'block';
}


  if (readMoreBtn) {
    readMoreBtn.addEventListener('click', () => {
      blogContent.querySelectorAll('.hidden-content').forEach(el => el.classList.remove('hidden-content'));
      blogContent.classList.remove('truncated');
      contentFade.style.display = 'none';
      readMoreContainer.style.display = 'none';

      // Only now, after expanding, render tweets
      ensureTwitter(renderTweetsNow);

      window.scrollTo({ top: blogContent.offsetTop - 100, behavior: 'smooth' });
    });
  }

  setupReadMore();
});
</script>
