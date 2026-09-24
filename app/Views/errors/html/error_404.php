<!-- ==== Header Section ==== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>

<style>
    /* ==== Page Container ==== */
    .error-container {
        text-align: center;
        padding: 28px 20px;
        background-color: #fafafa;
        color: #333;
        min-height: 100vh;
    }

    .error-image {
        max-width: 45%;
        margin: 0 auto 25px;
        display: block;
        filter: drop-shadow(0px 0px 6px rgba(0,0,0,0.05));
    }

    .error-title {
        font-size: 2.4rem;
        font-weight: 700;
        color: #068add;
        margin-bottom: 12px;
    }

    .error-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 35px;
        line-height: 1.6;
    }

    .home-btn {
        display: inline-block;
        background: #068add;
        color: #ffffffff;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 50px;
        transition: all 0.3s ease;
        box-shadow: 0px 4px 6px rgba(0,0,0,0.08);
    }
    .home-btn:hover {
        background: #e6b800;
        transform: scale(1.05);
    }

    /* ==== Categories Section ==== */
    .categories-section {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
        margin-bottom: 50px;
    }

    .categories-title {
        font-size: 1.6rem;
        color: #222;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        justify-items: center;
    }

    .category-tag {
        display: inline-block;
        padding: 10px 18px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 25px;
        color: #333;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0px 2px 4px rgba(0,0,0,0.05);
    }

    .category-tag:hover {
        background-color: #ffcc00;
        color: #000;
        border-color: #ffcc00;
        transform: translateY(-2px);
    }

    /* ==== Latest Blogs Section ==== */
    .latest-section {
        margin-top: 50px;
        padding: 0 20px;
        text-align: center;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .latest-title {
        font-size: 1.8rem;
        color: #222;
        font-weight: 600;
        margin-bottom: 30px;
    }

    .latest-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        justify-items: center;
    }

    .blog-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        text-align: left;
        width: 100%;
        max-width: 350px;
        transition: all 0.3s ease;
    }

    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.12);
    }

    .blog-thumb {
        width: 100%;
        /*height: 200px;*/
        object-fit: cover;
    }

    .blog-content {
        padding: 18px;
    }

    .blog-title {
        font-size: 1.1rem;
        color: #222;
        font-weight: 600;
        margin-bottom: 10px;
        text-decoration: none;
        display: block;
        line-height: 1.5;
    }

    .blog-title:hover {
        color: #ffcc00;
    }

    .blog-date {
        font-size: 0.85rem;
        color: #888;
    }

    @media (max-width: 768px) {
        .error-title { font-size: 2rem; }
        .error-subtitle { font-size: 1rem; }
        .latest-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="error-container">
    <img src="<?= base_url('public/assest/images/404-error.jpg') ?>" 
         alt="404 Error" class="error-image">
    
    <h1 class="error-title">ओह! पेज नहीं मिला</h1>
    <p class="error-subtitle">
        क्षमा करें, आप जो पेज खोज रहे हैं वह मौजूद नहीं है या हटा दिया गया है।<br>
        कृपया होम पर वापस जाएं या नीचे दिए गए विकल्पों में से चुनें।
    </p>
    
    <a href="<?= base_url('/') ?>" class="home-btn">
        <i class="fas fa-home"></i> होम पर जाएं
    </a>

    <!-- Latest 5 Blogs -->
    <div class="latest-section">
        <h2 class="latest-title">नवीनतम ब्लॉग्स</h2>
        <div class="latest-grid">
            <?php if (!empty($latestBlogs)): ?>
                <?php foreach ($latestBlogs as $blog): ?>
                    <div class="blog-card">
                        <a href="<?= esc($blog['blog_url']) ?>">
                            <img src="<?= esc($blog['thumbnail_url']) ?>" 
                                 alt="<?= esc($blog['title']) ?>" 
                                 class="blog-thumb">
                        </a>
                        <div class="blog-content">
                            <a href="<?= esc($blog['blog_url']) ?>" 
                               class="blog-title">
                                <?= esc($blog['title']) ?>
                            </a>
                            <p class="blog-date">
                                <?= esc($blog['formatted_date']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>कोई ब्लॉग उपलब्ध नहीं है।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ==== Footer ==== -->
<?php include(APPPATH . 'Views/components/footer.php'); ?>
