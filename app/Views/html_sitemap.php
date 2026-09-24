<?php include(APPPATH . 'Views/components/header.php'); ?>

<section class="breadcrumb-section py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <p class="mb-0 breadcrumb-text text-start">
                        <a href="<?= base_url() ?>"
                           class="text-dark text-decoration-none text-uppercase fw-semibold">Home</a>
                        <span class="mx-2">|</span>
                        <span class="text-muted text-uppercase fw-semibold">Sitemap</span>
                    </p>
                </nav>
            </div>
        </div>
    </div>
</section>

<main id="main-content" class="py-5 bg-white">
    <div class="container sitemap-container">

        <!-- ── Page Heading ── -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bold mb-2">Flypped Hindi Sitemap</h1>
                <p class="text-muted">
                    Browse all our latest Hindi blogs, news, and categories in one place.
                </p>
                <!-- Total counts -->
                <?php
                    $totalPosts = 0;
                    foreach ($groupedPosts as $catPosts) {
                        $totalPosts += count($catPosts);
                    }
                    $totalCats = 0;
                    foreach ($categories as $cat) {
                        if (!empty($groupedPosts[$cat->slug])) $totalCats++;
                    }
                ?>
                <div class="d-flex justify-content-center gap-4 mt-3">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <?= $totalCats ?> Categories
                    </span>
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <?= $totalPosts ?> Total Posts
                    </span>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- ── Left Column: Static Pages + Categories Index ── -->
            <div class="col-lg-3 mb-5">

                <!-- Static Pages -->
                <div class="sitemap-box mb-4">
                    <h3 class="sitemap-title">Main Pages</h3>
                    <ul class="sitemap-list">
                        <?php foreach ($staticPages as $page): ?>
                            <li>
                                <a href="<?= base_url($page['slug']) ?>">
                                    <?= htmlspecialchars($page['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Categories Quick Index -->
                <div class="sitemap-box">
                    <h3 class="sitemap-title">Categories</h3>
                    <ul class="sitemap-list">
                        <?php foreach ($categories as $cat): ?>
                            <?php
                                $posts = $groupedPosts[$cat->slug] ?? [];
                                if (empty($posts)) continue;
                            ?>
                            <li>
                                <a href="#cat-<?= esc($cat->slug) ?>">
                                    <?= htmlspecialchars($cat->name) ?>
                                    <span class="sitemap-count">
                                        (<?= count($posts) ?>)
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>

            <!-- ── Right Column: All Posts Grouped by Category ── -->
            <div class="col-lg-9">
                <div class="sitemap-box">
                    <h3 class="sitemap-title mb-4">All Articles</h3>

                    <?php
                        $anyCategoryFound = false;
                        foreach ($categories as $cat):
                            $catPosts = $groupedPosts[$cat->slug] ?? [];
                            if (empty($catPosts)) continue;
                            $anyCategoryFound = true;
                    ?>
                        <!-- Category Section -->
                        <div class="category-group mb-5" id="cat-<?= esc($cat->slug) ?>">

                            <!-- Category Heading -->
                            <div class="d-flex align-items-center justify-content-between
                                        border-bottom border-2 pb-2 mb-3"
                                 style="border-color: #167ac6 !important;">
                                <h4 class="category-heading mb-0">
                                    <a href="<?= base_url($cat->slug) ?>"
                                       class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($cat->name) ?>
                                    </a>
                                </h4>
                                <span class="badge bg-primary rounded-pill">
                                    <?= count($catPosts) ?> posts
                                </span>
                            </div>

                            <!-- Posts Grid -->
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <?php foreach ($catPosts as $post): ?>
                                    <div class="col">
                                        <div class="sitemap-post-item">
                                            <i class="fas fa-angle-right sitemap-arrow"></i>
                                            <a href="<?= base_url($post->post_name) ?>"
                                               title="<?= htmlspecialchars($post->post_title) ?>">
                                                <?= htmlspecialchars($post->post_title) ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Back to top link -->
                            <div class="text-end mt-2">
                                <a href="#main-content"
                                   class="text-muted small text-decoration-none back-to-top">
                                    ↑ Back to top
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>

                    <?php if (!$anyCategoryFound): ?>
                        <div class="alert alert-info">
                            No posts found. Please check back later.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</main>

<style>
/* ── Sitemap Container ── */
.sitemap-container { max-width: 1200px; }

/* ── Sitemap Box ── */
.sitemap-box {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

/* ── Section Title ── */
.sitemap-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #167ac6;
    border-bottom: 2px solid #167ac6;
    padding-bottom: 6px;
    margin-bottom: 20px;
    display: inline-block;
}

/* ── Category Heading ── */
.category-heading {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.category-heading a:hover { color: #167ac6 !important; }

/* ── Static + Category Index List ── */
.sitemap-list {
    list-style: none;
    padding-left: 0;
    margin: 0;
}
.sitemap-list li {
    margin-bottom: 10px;
    padding-left: 18px;
    position: relative;
}
.sitemap-list li::before {
    content: "•";
    color: #167ac6;
    font-size: 1.1rem;
    position: absolute;
    left: 0;
    top: -1px;
}
.sitemap-list a {
    color: #495057;
    text-decoration: none;
    font-size: 0.95rem;
    transition: color 0.2s;
}
.sitemap-list a:hover { color: #167ac6; text-decoration: underline; }

.sitemap-count {
    color: #aaa;
    font-size: 0.85rem;
}

/* ── Individual Post Item ── */
.sitemap-post-item {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    padding: 6px 8px;
    border-radius: 6px;
    transition: background 0.15s;
    margin-bottom: 2px;
}
.sitemap-post-item:hover { background: #f0f7ff; }
.sitemap-arrow {
    color: #167ac6;
    font-size: 12px;
    margin-top: 4px;
    flex-shrink: 0;
}
.sitemap-post-item a {
    color: #333;
    text-decoration: none;
    font-size: 0.9rem;
    line-height: 1.45;
    transition: color 0.2s;
}
.sitemap-post-item a:hover { color: #167ac6; text-decoration: underline; }

/* ── Back to top ── */
.back-to-top { font-size: 0.8rem; }
.back-to-top:hover { color: #167ac6 !important; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .sitemap-box { padding: 16px; }
    .sitemap-title { font-size: 1.1rem; }
}
@media (max-width: 767px) {
    .category-heading { font-size: 1rem; }
    .sitemap-post-item a { font-size: 0.88rem; }
}
</style>

<?php include(APPPATH . 'Views/components/footer.php'); ?>