<!-- ==== Header Section ==== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>

<?php if (!empty($latest_posts[0]['thumbnail_url'])): ?>
<link rel="preload"
      as="image"
      href="<?= htmlspecialchars($latest_posts[0]['thumbnail_url'], ENT_QUOTES, 'UTF-8') ?>"
      fetchpriority="high">
<?php endif; ?>

    <!-- ==== SLIDER SECTION ==== -->
<div id="sliderSection" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
    <?php $is_first = true; ?>
    <?php foreach ($latest_posts as $post): ?>
        <?php
        $detailUrl = !empty($post['blog_detail_url'])
            ? $post['blog_detail_url']
            : base_url(rawurlencode($post['post_name'] ?? ''));

        $imgSrc    = htmlspecialchars($post['thumbnail_url'] ?? '', ENT_QUOTES, 'UTF-8');
        $imgAlt    = htmlspecialchars($post['post_title']    ?? 'Post image', ENT_QUOTES, 'UTF-8');
        $postTitle = htmlspecialchars($post['post_title']    ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <div class="carousel-item <?= $is_first ? 'active' : '' ?>">

            <?php if ($is_first): ?>
                <!-- First slide: load immediately for LCP -->
                <img
                    src="<?= $imgSrc ?>"
                    class="d-block w-100 carousel-image"
                    alt="<?= $imgAlt ?>"
                    width="1200"
                    height="630"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            <?php else: ?>
                <!-- Other slides: load normally but mark as lazy -->
                <img
                    src="<?= $imgSrc ?>"
                    class="d-block w-100 carousel-image"
                    alt="<?= $imgAlt ?>"
                    width="1200"
                    height="630"
                    loading="lazy"
                    decoding="async"
                >
            <?php endif; ?>

            <!-- Desktop Caption -->
            <div class="carousel-caption d-none d-md-block">
                <h3>
                    <a href="<?= $detailUrl ?>" class="text-white Blogtitle">
                        <?= $postTitle ?>
                    </a>
                </h3>
                <p>Latest Post</p>
            </div>

            <!-- Mobile Caption -->
            <div class="carousel-caption-mobile d-md-none" style="background:#0f0f0fc7; text-align:center;">
                <a href="<?= $detailUrl ?>" class="text-white small-caption-link" style="text-transform:capitalize; line-height:24px;">
                    <h3><?= $postTitle ?></h3>
                </a>
            </div>

        </div>
        <?php $is_first = false; ?>
    <?php endforeach; ?>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#sliderSection" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#sliderSection" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

    <!-- ===== समाचार (NEWS) SECTION ===== -->
    <section class="entertainment-section grey-box hindi-news-section ">

        <div class="container">
             <h1 class="text-center text-light">Flypped हिंदी न्यूज़ पोर्टल: ताज़ा समाचार, स्वास्थ्य, खेल, मनोरंजन, शिक्षा और टेक्नोलॉजी अपडेट्स</h1> 
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #eee;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        मुख्य हिंदी समाचार और वर्तमान घटनाओं की अपडेट्स
                        <span class="position-absolute start-0 bg-warning" style="bottom: -10px; width: 100%; height: 3px;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('news'); ?>" class="btn View-all-btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>

            <div class="row">
                <?php 
                // Check if there are posts
                if (!empty($politics_tab_posts)): 
                    // Separate the 1st post for the main view
                    $main_post = $politics_tab_posts[0];
                    // Take the next 4 posts for the sidebar
                    $sidebar_posts = array_slice($politics_tab_posts, 1, 4);
                ?>

                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="overflow-hidden position-relative">
                            <a href="<?= $main_post['blog_detail_url']; ?>">
                                <img loading="lazy" src="<?= $main_post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover"  alt="<?= htmlspecialchars($main_post['post_title']); ?>">
                            </a>
                        </div>
                        <div class="card-body ">
                            <div class="mb-2 text-muted small text-uppercase fw-bold">
                                <span class="text-warning me-2"><i class="fas fa-folder"></i> News</span>
                                <span><i class="far fa-clock text-warning"></i> <?= date('M d, Y', strtotime($main_post['post_date'])); ?></span>
                            </div>
                            <h3 class="card-title fw-bold mb-3" >
                                <a href="<?= $main_post['blog_detail_url']; ?>" class="text-dark text-decoration-none" style="font-size: 23px; font-weight: 500; line-height: 32px;">
                                    <?= htmlspecialchars($main_post['post_title']); ?>
                                </a>
                            </h3>
                            <!-- <p class="card-text text-muted mb-4">
                                <?= substr(strip_tags($main_post['post_content'] ?? $main_post['post_title']), 0, 150) . '...'; ?>
                            </p> -->
                            <!-- <a href="<?= $main_post['blog_detail_url']; ?>" class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fw-bold" style="font-size: 12px;">Read More</a> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-list">
                        <?php foreach ($sidebar_posts as $post): ?>
                            <div class="card border-0 mb-3 shadow-sm">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 overflow-hidden">
                                        <a href="<?= $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?= $post['thumbnail_url']; ?>" class="img-fluid h-100 w-100 object-fit-cover" style="height: 90px; min-height: 90px;" alt="thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body py-2 px-3">
                                            <h3 class="card-title mb-1 fw-bold" style="font-size: 15px; line-height: 1.4;">
                                                <a href="<?= $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= htmlspecialchars($post['post_title']); ?>
                                                </a>
                                            </h3>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="far fa-calendar-alt text-warning me-1"></i> <?= date('M d, Y', strtotime($post['post_date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>

    </section>
    
    <!-- ===== मनोरंजन (ENTERTAINMENT) SECTION ===== -->
    <section class="entertainment-section bg-white grey-box hindi-entertainment-section">
        <div class="container">
            <div class="d-flex section-title-wrapper flypped-headings justify-content-between align-items-center" style="width: 100%;">
                <h2 class="text-dark">मनोरंजन और मीडिया से जुड़े विषय</h2>
                <a href="<?= base_url('entertainment'); ?>" class="View-all-btn d-lg-flex">और भी</a>
            </div>

            <div class="row">
                <?php if (!empty($entertainment_tab_posts) && is_array($entertainment_tab_posts)): ?>
                    <?php foreach ($entertainment_tab_posts as $post): ?>
                        <div class="col-md-4">
                            <a href="<?= $post['blog_detail_url']; ?>" class="text-decoration-none">
                                <div class="card mb-4 mt-4">
                                    <img loading="lazy" src="<?= $post['thumbnail_url']; ?>"
                                        class="card-img-top"
                                        alt="<?= htmlspecialchars($post['post_title']); ?>">
                                    <div class="card-body">
                                        <p class="card-text text-dark">
                                            <?= htmlspecialchars($post['post_title']); ?>
                                        </p>
                                        <p class="author-date">
                                            <?= date('F d, Y', strtotime($post['post_date'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== खेल (SPORTS) SECTION ===== -->
    <section class="entertainment-section grey-box hindi-sports-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        खेल समाचार, क्रिकेट न्यूज़, और खिलाड़ी जानकारी
                        <span class="position-absolute start-0 bg-primary" style="bottom: -10px; width: 100%; height: 3px;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('sports'); ?>" class="btn View-all-btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>
            
            <div class="row">
                <!-- Left Column - Sports Posts (col-md-8) -->
                <div class="col-md-12">
                    <div class="row g-3">
                        <?php 
                        // Limit to exactly 6 posts
                        $grid_posts = array_slice($sports_tab_posts, 0, 6);
                        
                        foreach ($grid_posts as $post): ?>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="card h-100 border-0 shadow-sm sports-card-small">
                                    <div class="overflow-hidden position-relative rounded-top">
                                        <a href="<?php echo $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?php echo $post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 170px;" alt="<?php echo htmlspecialchars($post['post_title']); ?>">
                                        </a>
                                        <span class="position-absolute bottom-0 start-0 bg-primary text-white px-2 py-1 m-2 rounded" style="font-size: 10px; font-weight: 600;">
                                            खेल
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="mb-2 text-muted" style="font-size: 11px;">
                                            <i class="far fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                                        </div>
                                        <h3 class="card-title fw-bold mb-0" style="font-size: 16px; line-height: 1.4;">
                                            <a href="<?php echo $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-blue">
                                                <?php echo htmlspecialchars($post['post_title']); ?>
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== स्वास्थ्य और फिटनेस (HEALTH & FITNESS) SECTION ===== -->
    <section class="entertainment-section grey-box hindi-health-fitness-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #eee;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                       स्वास्थ्य और शरीर से जुड़ी उपयोगी जानकारी 
                        <span class="position-absolute start-0 bg-warning" style="bottom: -10px; width: 100%; height: 3px;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('health-fitness'); ?>" class="btn View-all-btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>

            <div class="row">
                <?php 
                // Check if there are posts
                if (!empty($health_fitness_tab_posts) && is_array($health_fitness_tab_posts)): 
                    // Separate the 1st post for the main view
                    $main_post = $health_fitness_tab_posts[0];
                    // Take the next 4 posts for the sidebar
                    $sidebar_posts = array_slice($health_fitness_tab_posts, 1, 4);
                ?>

                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="overflow-hidden position-relative">
                            <a href="<?= $main_post['blog_detail_url']; ?>">
                                <img loading="lazy" src="<?= $main_post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" alt="<?= htmlspecialchars($main_post['post_title']); ?>">
                            </a>
                        </div>
                        <div class="card-body" style="padding: 15px;">
                            <div class="mb-2 text-muted small text-uppercase fw-bold">
                                <span class="text-warning me-2"><i class="fas fa-heartbeat"></i> Health</span>
                                <span><i class="far fa-clock text-warning"></i> <?= date('M d, Y', strtotime($main_post['post_date'])); ?></span>
                            </div>
                            <h3 class="card-title fw-bold mb-0">
                                <a href="<?= $main_post['blog_detail_url']; ?>" class="text-dark text-decoration-none" style="font-size: 20px; font-weight: 600; line-height: 28px;">
                                    <?= htmlspecialchars($main_post['post_title']); ?>
                                </a>
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-list" style="display: flex; flex-direction: column; gap: 12px;">
                        <?php foreach ($sidebar_posts as $post): ?>
                            <div class="card border-0 shadow-sm" style="margin-bottom: 0;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 overflow-hidden">
                                        <a href="<?= $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?= $post['thumbnail_url']; ?>" class="img-fluid h-100 w-100 object-fit-cover" style="height: 85px; min-height: 85px;" alt="thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body" style="padding: 10px 12px;">
                                            <h3 class="card-title mb-1 fw-bold" style="font-size: 14px; line-height: 1.3;">
                                                <a href="<?= $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= htmlspecialchars($post['post_title']); ?>
                                                </a>
                                            </h3>
                                            <small class="text-muted d-flex align-items-center" style="font-size: 11px; margin-top: 5px;">
                                                <i class="far fa-calendar-alt text-warning me-1"></i> <?= date('M d, Y', strtotime($post['post_date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== रिलेशनशिप (RELATIONSHIP) SECTION ===== -->
    <section class="entertainment-section hindi-relationship-section">
        <div class="container">
            <div class="d-flex flypped-headings justify-content-between align-items-center mb-4">
                <h2>रिश्तों की सलाह, प्यार से जुड़े सवाल और भावनात्मक समझ</h2>
                <a href="<?= base_url('relationship'); ?>" class="View-all-btn d-lg-flex">और भी</a>
            </div>

            <div class="row">
                <?php if (!empty($relationship_tab_posts) && is_array($relationship_tab_posts)): ?>
                    <?php 
                    // Limit the loop to 6 items for a clean 2-row grid
                    $display_posts = array_slice($relationship_tab_posts, 0, 6);
                    foreach ($display_posts as $post): 
                    ?>
                        <div class="col-md-4">
                            <a href="<?= $post['blog_detail_url']; ?>" class="text-decoration-none">
                                <div class="card mb-4 mt-4">
                                    <img loading="lazy" src="<?= $post['thumbnail_url']; ?>"
                                        class="card-img-top"
                                        alt="<?= htmlspecialchars($post['post_title']); ?>">
                                    <div class="card-body">
                                        <p class="card-text text-dark">
                                            <?= htmlspecialchars($post['post_title']); ?>
                                        </p>
                                        <p class="author-date">
                                            <?= strtoupper(date('F d, Y', strtotime($post['post_date']))); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== लाइफस्टाइल (LIFESTYLE) SECTION ===== -->
    <section class="entertainment-section bg-white grey-box hindi-lifestyle-section">
     <div class="container">
        <!-- Heading and और भी Button -->
        <div class="d-flex section-title-wrapper flypped-headings justify-content-between align-items-center" style="width: 100%;">
            <h2 class="text-dark">लाइफ़स्टाइल अपडेट्स: रोज़मर्रा की ज़िंदगी और फ़ैशन ट्रेंड्स हिंदी में</h2>
            <a href="<?= base_url('lifestyle'); ?>" class="View-all-btn d-lg-flex">और भी</a>
        </div>

        <div class="row">
            <?php if (!empty($lifestyle_tab_posts) && is_array($lifestyle_tab_posts)): ?>
                <?php foreach ($lifestyle_tab_posts as $post): ?>
                    <div class="col-md-4">
                        <a href="<?= $post['blog_detail_url']; ?>" class="text-decoration-none">
                            <div class="card mb-4 mt-4">
                                <img loading="lazy" src="<?= $post['thumbnail_url']; ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($post['post_title']); ?>">
                                <div class="card-body">
                                    <p class="card-text text-dark">
                                        <?= htmlspecialchars($post['post_title']); ?>
                                    </p>
                                    <p class="author-date">
                                        <?= date('F d, Y', strtotime($post['post_date'])); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </div>
    </section>

    <!-- ===== टेक्नोलॉजी (TECHNOLOGY) SECTION ===== -->
    <section class="entertainment-section grey-box hindi-technology-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                       टेक्नोलॉजी, गैजेट्स और डिजिटल ट्रेंड्स लेटेस्ट अपडेट
                        <span class="position-absolute start-0 bg-info" style="bottom: -10px; width: 100%; height: 3px;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('technology'); ?>" class="btn btn-outline-dark View-all-btn btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>

            <div class="row">
                <?php 
                if (!empty($tech_tab_posts) && is_array($tech_tab_posts)): 
                    // Separate 1st post for main, next 4 for sidebar
                    $main_tech_post = $tech_tab_posts[0];
                    $sidebar_tech_posts = array_slice($tech_tab_posts, 1, 4);
                ?>

                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 tech-card">
                        <div class="overflow-hidden position-relative rounded-top">
                            <span class="position-absolute top-0 start-0 bg-info text-white px-3 py-1 fw-bold m-3 rounded" style="z-index:1; font-size:12px;">
                                LATEST TECH
                            </span>
                            <a href="<?= $main_tech_post['blog_detail_url']; ?>">
                                <img loading="lazy" src="<?= $main_tech_post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 450px; transition: 0.5s ease;" alt="<?= htmlspecialchars($main_tech_post['post_title']); ?>">
                            </a>
                        </div>
                        <div class="card-body p-4 position-relative">
                            <div class="mb-2 text-muted small text-uppercase fw-bold">
                                <span class="text-info me-2"><i class="fas fa-microchip"></i> Technology</span>
                                <span><i class="far fa-clock text-secondary"></i> <?= date('M d, Y', strtotime($main_tech_post['post_date'])); ?></span>
                            </div>
                            <h3 class="card-title fw-bold mb-3">
                                <a href="<?= $main_tech_post['blog_detail_url']; ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($main_tech_post['post_title']); ?>
                                </a>
                            </h3>
                            <!-- <p class="card-text text-muted mb-4">
                                <?= substr(strip_tags($main_tech_post['post_content'] ?? $main_tech_post['post_title']), 0, 160) . '...'; ?>
                            </p>
                            <a href="<?= $main_tech_post['blog_detail_url']; ?>" class="btn btn-info rounded-pill px-4 py-2 fw-bold text-white" style="font-size: 12px;">
                                READ FULL REVIEW <i class="fas fa-arrow-right ms-1"></i>
                            </a> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-list">
                        <?php foreach ($sidebar_tech_posts as $post): ?>
                            <div class="card border-0 mb-3 shadow-sm hover-card">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 overflow-hidden rounded-start">
                                        <a href="<?= $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?= $post['thumbnail_url']; ?>" class="img-fluid h-100 w-100 object-fit-cover" style="height: 100px; min-height: 100px;" alt="thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body py-2 px-3">
                                            <h3 class="card-title mb-1 fw-bold" style="font-size: 15px; line-height: 1.4;">
                                                <a href="<?= $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-info-text">
                                                    <?= htmlspecialchars($post['post_title']); ?>
                                                </a>
                                            </h3>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="far fa-calendar-alt me-1"></i> <?= date('M d, Y', strtotime($post['post_date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    
    </section>
    
    <!-- ===== बिज़नेस (BUSINESS) SECTION ===== -->
    <section class="entertainment-section bg-white hindi-business-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        बिज़नेस और आर्थिक सोच से जुड़े लेख
                        <span class="position-absolute start-0" style="bottom: -10px; width: 100%; height: 3px; background-color: #6610f2;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('business'); ?>" class="btn View-all-btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>

            <div class="row g-3"> 
                <?php 
                // Check if posts exist
                if (!empty($business_tab_posts)):
                    // Limit to exactly 6 posts
                    $grid_business_posts = array_slice($business_tab_posts, 0, 6);
                    
                    foreach ($grid_business_posts as $post): 
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm business-card-small">
                            <div class="overflow-hidden position-relative rounded-top">
                                <a href="<?php echo $post['blog_detail_url']; ?>">
                                    <img loading="lazy" src="<?php echo $post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 170px;" alt="<?php echo htmlspecialchars($post['post_title']); ?>">
                                </a>
                                <span class="position-absolute bottom-0 start-0 text-white px-2 py-1 m-2 rounded" style="font-size: 10px; font-weight: 600; background-color: #6610f2;">
                                    BUSINESS
                                </span>
                            </div>

                            <div class="card-body p-3">
                                <div class="mb-2 text-muted" style="font-size: 11px;">
                                    <i class="far fa-calendar-alt me-1" style="color: #6610f2;"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                                </div>

                                <h3 class="card-title fw-bold mb-0" style="font-size: 16px; line-height: 1.4;">
                                    <a href="<?php echo $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-business">
                                        <?php echo htmlspecialchars($post['post_title']); ?>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== शिक्षा (Education) SECTION ===== -->
    <section class="grey-box hindi-education-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        शिक्षा, सीखने के साधन और करियर से जुड़ी जानकारी
                        <span class="position-absolute start-0" style="bottom: -10px; width: 60px; height: 3px; background-color: #e74c3c;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('education'); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी<</a>
            </div>

            <div class="row">
            <?php 
                // Check if posts exist and is array
                if (!empty($education_tab_posts) && is_array($education_tab_posts)): 
                    // Separate the 1st post for the main view
                    $main_edu_post = $education_tab_posts[0];
                    // Take the next 4 posts for the sidebar list
                    $sidebar_edu_posts = array_slice($education_tab_posts, 1, 4);
                ?>

                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 education-main-card">
                        <div class="overflow-hidden position-relative rounded-top">
                            <a href="<?= $main_edu_post['blog_detail_url']; ?>">
                                <img loading="lazy" src="<?= $main_edu_post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 400px; transition: 0.5s ease;" alt="<?= htmlspecialchars($main_edu_post['post_title']); ?>">
                            </a>
                            <span class="position-absolute bottom-0 start-0 text-white px-3 py-1 m-3 rounded" style="font-size: 12px; font-weight: 600; background-color: #e74c3c;">
                                EDUCATION
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <h3 class="card-title fw-bold mb-3">
                                <a href="<?= $main_edu_post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-education" style=" font-size: 24px; ">
                                    <?= htmlspecialchars($main_edu_post['post_title']); ?>
                                </a>
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-list d-flex flex-column gap-3">
                        <?php foreach ($sidebar_edu_posts as $post): ?>
                            <div class="card border-0 shadow-sm hover-card-side">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 overflow-hidden rounded-start">
                                        <a href="<?= $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?= $post['thumbnail_url']; ?>" class="img-fluid h-100 w-100 object-fit-cover" style="height: 90px; min-height: 90px;" alt="thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body py-2 px-3">
                                            <h3 class="card-title mb-2 fw-bold" style="font-size: 15px; line-height: 1.4;">
                                                <a href="<?= $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-education" title="<?= htmlspecialchars($post['post_title']); ?>">
                                                    <?php 
                                                        //  TITLE TRIMMED HERE (Max 55 characters)
                                                        echo htmlspecialchars(mb_strimwidth($post['post_title'], 0, 55, "...")); 
                                                    ?>
                                                </a>
                                            </h3>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="far fa-calendar-alt me-1" style="color: #e74c3c;"></i> <?= date('M d, Y', strtotime($post['post_date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== यात्रा (Travel) SECTION ===== -->
    <section class="entertainment-section bg-white hindi-travel-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        यात्रा गाइड, घूमने की जगहें और ट्रैवल टिप्स हिंदी में
                        <span class="position-absolute start-0" style="bottom: -10px; width: 100%; height: 3px; background-color: #17a2b8;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('travel'); ?>" class="btn View-all-btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी</a>
            </div>

            <div class="row g-3"> 
                <?php 
                // Check if posts exist
                if (!empty($travel_tab_posts) && is_array($travel_tab_posts)):
                    // Limit to exactly 6 posts
                    $grid_travel_posts = array_slice($travel_tab_posts, 0, 6);
                    
                    foreach ($grid_travel_posts as $post): 
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm travel-card-small">
                            <div class="overflow-hidden position-relative rounded-top">
                                <a href="<?php echo $post['blog_detail_url']; ?>">
                                    <img loading="lazy" src="<?php echo $post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 170px;" alt="<?php echo htmlspecialchars($post['post_title']); ?>">
                                </a>
                                <span class="position-absolute bottom-0 start-0 text-white px-2 py-1 m-2 rounded" style="font-size: 10px; font-weight: 600; background-color: #17a2b8;">
                                    TRAVEL
                                </span>
                            </div>

                            <div class="card-body p-3">
                                <div class="mb-2 text-muted" style="font-size: 11px;">
                                    <i class="far fa-calendar-alt me-1" style="color: #17a2b8;"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                                </div>

                                <h3 class="card-title fw-bold mb-0" style="font-size: 16px; line-height: 1.4;">
                                    <a href="<?php echo $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-travel">
                                        <?php echo htmlspecialchars($post['post_title']); ?>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== आध्यात्मिक (Spiritual) SECTION ===== -->
    <section class="entertainment-section hindi-spiritual-section py-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: #d1d1d1;">
                <div class="section-title-wrapper">
                    <h2 class="fw-bold m-0 text-dark position-relative" style="font-size: 24px;">
                        धर्म, आंतरिक शांति और आध्यात्म पर आधारित लेख
                        <span class="position-absolute start-0" style="bottom: -10px; width: 60px; height: 3px; background-color: #ff9933;"></span>
                    </h2>
                </div>
                <a href="<?= base_url('spiritual'); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">और भी<</a>
            </div>

            <div class="row">
                <?php 
                // Check if posts exist and is array
                if (!empty($spiritual_tab_posts) && is_array($spiritual_tab_posts)): 
                    // Separate the 1st post for the main view
                    $main_spirit_post = $spiritual_tab_posts[0];
                    // Take the next 4 posts for the sidebar list
                    $sidebar_spirit_posts = array_slice($spiritual_tab_posts, 1, 4);
                ?>

                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 spiritual-main-card">
                        <div class="overflow-hidden position-relative rounded-top">
                            <a href="<?= $main_spirit_post['blog_detail_url']; ?>">
                                <img loading="lazy" src="<?= $main_spirit_post['thumbnail_url']; ?>" class="card-img-top w-100 object-fit-cover" style="height: 400px; transition: 0.5s ease;" alt="<?= htmlspecialchars($main_spirit_post['post_title']); ?>">
                            </a>
                            <span class="position-absolute bottom-0 start-0 text-white px-3 py-1 m-3 rounded" style="font-size: 12px; font-weight: 600; background-color: #ff9933;">
                                SPIRITUAL
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <h3 class="card-title fw-bold mb-3">
                                <a href="<?= $main_spirit_post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-spiritual">
                                    <?= htmlspecialchars($main_spirit_post['post_title']); ?>
                                </a>
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-list d-flex flex-column gap-3">
                        <?php foreach ($sidebar_spirit_posts as $post): ?>
                            <div class="card border-0 shadow-sm hover-card-side">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 overflow-hidden rounded-start">
                                        <a href="<?= $post['blog_detail_url']; ?>">
                                            <img loading="lazy" src="<?= $post['thumbnail_url']; ?>" class="img-fluid h-100 w-100 object-fit-cover" style="height: 90px; min-height: 90px;" alt="thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body py-2 px-3">
                                            <h3 class="card-title mb-2 fw-bold" style="font-size: 15px; line-height: 1.4;">
                                                <a href="<?= $post['blog_detail_url']; ?>" class="text-dark text-decoration-none hover-spiritual">
                                                    <?= htmlspecialchars($post['post_title']); ?>
                                                </a>
                                            </h3>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="far fa-calendar-alt me-1" style="color: #ff9933;"></i> <?= date('M d, Y', strtotime($post['post_date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== FLYPPED DESCRIPTION SECTION ===== -->
    <section class="flypped-description-section">
        <div class="container">
            <div class="flypped-description">
                <h2 style="font-size: 26px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 20px;">
                    Flypped Hindi E-Magazine परिचय

                </h2>

                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    Flypped Hindi E-Magazine एक तेजी से उभरता हुआ हिंदी न्यूज़ पोर्टल और डिजिटल मैगज़ीन है, 
                    जहाँ पाठकों को Latest Hindi News, Trending News, Technology News, Sports News, Health & Fitness Tips, Entertainment Updates,
                     Education News, Lifestyle Articles, Business News, Travel Guides, Relationship Tips और Spiritual Content एक ही प्लेटफ़ॉर्म पर पढ़ने को मिलता है।

                </p>

                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    हमारा उद्देश्य अपने पाठकों तक सटीक, भरोसेमंद और तथ्य-आधारित जानकारी पहुँचाना है। पिछले कई वर्षों से Flypped Hindi अपने पाठकों को गुणवत्तापूर्ण हिंदी कंटेंट उपलब्ध
                     कराने के लिए लगातार कार्य कर रहा है। प्रत्येक समाचार, लेख और जानकारी को प्रकाशित करने से पहले उसकी सत्यता और विश्वसनीयता की जाँच की जाती है ताकि पाठकों को सही जानकारी प्राप्त हो सके।

                </p>

                <h2 style="font-size: 24px; font-weight: 700; color: #2c3e50; margin-top: 40px; margin-bottom: 20px;">
                    Latest Hindi News और Trending News Updates

                </h2>

                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    Flypped Hindi पर आपको देश-दुनिया की ताज़ा खबरें, ब्रेकिंग न्यूज़ (</span><a href="https://flyppedhindi.com/news"><span >Breaking News in Hindi</span></a><span>), वायरल ट्रेंड्स, करंट अफेयर्स और महत्वपूर्ण घटनाओं से जुड़ी जानकारी सरल हिंदी भाषा में पढ़ने को मिलती है। 
                    हमारा लक्ष्य है कि पाठक हर महत्वपूर्ण अपडेट से जुड़े रहें और उन्हें समय पर सही जानकारी प्राप्त हो।</span>
                </p>

                

                <h3 style="font-size: 20px; font-weight: 700; color: #2c3e50; margin-top: 40px; margin-bottom: 20px;">
                    Technology News in Hindi और Digital Updates
                </h3>

                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    क्नोलॉजी की दुनिया में होने वाले नए बदलावों, स्मार्टफोन लॉन्च, AI, गैजेट्स, इंटरनेट सुरक्षा, साइबर सिक्योरिटी, डिजिटल इंडिया और ऑनलाइन सेवाओं से जुड़ी महत्वपूर्ण जानकारियाँ Flypped Hindi पर नियमित रूप से प्रकाशित की जाती हैं। यदि आप
                 </span><a href="https://flyppedhindi.com/technology"><span >Technology News in Hindi</span></a><span > पढ़ना पसंद करते हैं तो यह आपके लिए उपयोगी मंच है।</span>
                </p>
                
                

                <h3 style="font-size: 20px; font-weight: 700; color: #2c3e50; margin-top: 40px; margin-bottom: 20px;">
                    Sports News in Hindi, Cricket News और IPL Updates
                </h3>

                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    खेल प्रेमियों के लिए Flypped Hindi पर </span><a href="https://flyppedhindi.com/sports"><span >Sports News in Hindi</span></a><span >,
                     Cricket and IPL Updates, Indian Cricket Team News, Football News, Kabaddi Updates और अन्य खेलों की ताज़ा जानकारी उपलब्ध कराई जाती है। हमारा प्रयास है कि पाठकों तक खेल जगत की हर बड़ी खबर और विश्लेषण सरल भाषा में पहुँचाया जाए।</span>
                </p>

                <h2 style="font-size: 24px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Health & Fitness Tips in Hindi
                </h2>


                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >स्वस्थ जीवनशैली को बढ़ावा देने के लिए Flypped Hindi पर </span><a href="https://flyppedhindi.com/health-fitness"><span >Health &amp; Fitness Tips in Hindi</span></a><span >, Weight Loss Guide, Healthy Diet Plans, Home Workout Ideas, Nutrition Information और Wellness से जुड़ी उपयोगी जानकारी प्रकाशित की जाती है।
                     हमारा लक्ष्य पाठकों को स्वास्थ्य संबंधी जागरूक और जानकारीपूर्ण बनाना है।</span></p>


                <h4 style="font-size: 18px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Entertainment News, Bollywood Updates और OTT Releases
                </h4>

                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >मनोरंजन जगत से जुड़ी हर महत्वपूर्ण खबर, </span><a href="https://flyppedhindi.com/entertainment"><span >Bollywood News in Hindi</span></a><span >, Celebrity News, Movie Reviews, OTT Releases, Web Series Reviews और मनोरंजन उद्योग की नई अपडेट्स Flypped Hindi पर उपलब्ध हैं।
                     पाठकों को मनोरंजन की दुनिया से जुड़े ताज़ा घटनाक्रम एक ही स्थान पर मिलते हैं।</span></p>


                <h2 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Education News in Hindi और Career Guidance

                </h2>
                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >छात्रों और प्रतियोगी परीक्षाओं की तैयारी करने वाले युवाओं के लिए </span>
                <a href="https://flyppedhindi.com/education"><span >Education News in Hindi</span></a><span >, Career Guidance, Government Exam Updates, Scholarship Information, Study Tips, Online Learning Resources और करियर से जुड़ी महत्वपूर्ण जानकारियाँ प्रकाशित की जाती हैं।
                     हमारा उद्देश्य युवाओं को शिक्षा और करियर के क्षेत्र में बेहतर मार्गदर्शन प्रदान करना है।</span></p>


                
                
                
                <h3 style="font-size: 20px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Lifestyle, Relationship Tips और Personal Development

                </h3>
                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >Flypped Hindi पर </span><a href="https://flyppedhindi.com/lifestyle"><span >Lifestyle Tips in Hindi</span></a><span >, Self Improvement Articles, </span><a href="https://flyppedhindi.com/relationship"><span >Relationship Advice</span></a><span >, Family Life Guidance, Personality Development और दैनिक जीवन को बेहतर बनाने वाले विषयों पर उपयोगी कंटेंट प्रकाशित किया जाता है।
                     यह सामग्री पाठकों के व्यक्तिगत विकास और बेहतर जीवनशैली को ध्यान में रखकर तैयार की जाती है।</span>
                </p>


                     
                <h4 style="font-size: 18px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Spiritual Articles और Motivation
                </h4>
                <p class="text-justify" style="line-height: 1.8; color: #333;">
                    <span >आध्यात्मिकता और सकारात्मक सोच को बढ़ावा देने के लिए Flypped Hindi पर </span><a href="https://flyppedhindi.com/spiritual"><span >Spiritual Motivation in Hindi</span></a><span >
                       , Bhagavad Gita Teachings, Meditation Tips, Inspirational Stories, Life Lessons और भारतीय संस्कृति से जुड़े लेख प्रकाशित किए जाते हैं। हमारा उद्देश्य पाठकों को मानसिक और आध्यात्मिक रूप से सशक्त बनाना है।
                    </span>
                <p>
                <h3 style="font-size: 20px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Business News in Hindi और Travel Information

                </h3>


                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >Flypped Hindi पर </span><a href="https://flyppedhindi.com/business"><span >Business News in Hindi</span></a><span >, Startup Stories, Finance Updates, Market Trends और आर्थिक जगत की महत्वपूर्ण खबरें भी प्रकाशित की जाती हैं।
                     इसके साथ ही Travel Guides in Hindi, Tourist Destinations, Travel Tips और भारत के प्रमुख पर्यटन स्थलों से जुड़ी जानकारी भी उपलब्ध कराई जाती है।</span>
                </p>


                <h4 style="font-size: 18px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Flypped Hindi क्यों अलग है?

                </h4>
                <p class="text-justify" style="line-height: 1.8; color: #333;">Flypped Hindi केवल समाचार प्रकाशित करने वाला प्लेटफ़ॉर्म नहीं है, बल्कि यह एक ऐसा हिंदी नॉलेज पोर्टल है जहाँ पाठकों को जानकारी के साथ संदर्भ और विश्लेषण भी मिलता है।
                     हम वायरल कंटेंट की बजाय उपयोगी, शोध-आधारित और पाठक-केंद्रित कंटेंट पर अधिक ध्यान देते हैं। हमारा लक्ष्य है कि पाठकों को कम समय में अधिक और गुणवत्तापूर्ण जानकारी प्राप्त हो।

                </p>     

                <h5 style="font-size: 16px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    Flypped Hindi का विज़न

                </h5>
              <p class="text-justify" style="line-height: 1.8; color: #333;"><span >हमारा विज़न हिंदी इंटरनेट पर एक ऐसा डिजिटल प्लेटफ़ॉर्म बनाना है जहाँ Latest Hindi News, Technology Updates, Sports News, Health Tips, Education Content, </span><a href="https://flyppedhindi.com/travel"><span >Travel Tips

                </span></a><span >, Entertainment News, Business Updates, Lifestyle Articles और Spiritual Knowledge विश्वसनीय रूप से उपलब्ध हो। हम चाहते हैं कि हिंदी पाठकों को भी विश्वस्तरीय डिजिटल कंटेंट का अनुभव मिले।</span>
              </p>


                <h6 style="font-size: 14px; font-weight: 700; color: #2c3e50; margin-top: 30px; margin-bottom: 10px;">
                    हमारे पाठकों के लिए संदेश
                </h6>
                <p class="text-justify" style="line-height: 1.8; color: #333;"><span >हमारे पाठकों का विश्वास ही हमारी सबसे बड़ी ताकत है। हम भविष्य में भी सटीक, निष्पक्ष और उपयोगी जानकारी प्रदान करने के लिए प्रतिबद्ध रहेंगे। यदि आपके पास कोई सुझाव या प्रतिक्रिया है, तो आप हमसे संपर्क कर सकते हैं। आपके सहयोग से ही Flypped Hindi और बेहतर बन सकता है।</span></p>
                <br>
                <br>
                
            </div>
        </div>
    </section>

    <!-- ==== DYNAMIC CATEGORY CONTENT ==== -->

    <?php include(APPPATH . 'Views/components/categorycontent.php'); ?>
    <!-- ==== Footer Section ==== -->
    <?php include(APPPATH . 'Views/components/footer.php'); ?>


    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileBlogTitle = document.getElementById('mobileBlogTitle');
        const mobileBlogTitleLink = document.getElementById('mobileBlogTitleLink');
        const trendingSlider = document.getElementById('trendingSlider');
    
        function updateMobileBlogTitle() {
            const activeItem = trendingSlider.querySelector('.carousel-item.active');
            const title = activeItem.getAttribute('data-title');
            const url = activeItem.getAttribute('data-url');
            mobileBlogTitleLink.textContent = title;
            mobileBlogTitleLink.href = url;
        }
    
        // Initialize the title on page load
        updateMobileBlogTitle();
    
        // Update the title when the carousel slides
        trendingSlider.addEventListener('slid.bs.carousel', updateMobileBlogTitle);
    });
</script>

<script>
    function adjustImageHeight() {
        const images = document.querySelectorAll('.trending-card img');
        
        images.forEach(img => {
            if (window.innerWidth <= 768) {
                img.style.height = 'auto'; // For mobile and small devices
            } else {
                img.style.height = '500px'; // Fixed height for larger screens
            }
        });
    }

    // Adjust image height on page load
    adjustImageHeight();

    // Adjust image height on window resize
    window.addEventListener('resize', adjustImageHeight);
</script>

<script>
    function openReelModal(videoSrc) {
        const modalReelVideo = document.getElementById('modalReelVideo');
        modalReelVideo.src = videoSrc;

        // Pause all other videos in the carousel
        document.querySelectorAll('.reel-video-container video').forEach(video => video.pause());

        // Show the modal
        const reelModal = new bootstrap.Modal(document.getElementById('reelModal'));
        reelModal.show();

        // When the modal is hidden, stop the video
        document.getElementById('reelModal').addEventListener('hidden.bs.modal', () => {
            modalReelVideo.pause();
            modalReelVideo.src = ''; // Clear the source to reset the video
        });
    }
</script>
