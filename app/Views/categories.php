<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>

<!-- ==== Breadcrumb Section ==== -->
<section class="breadcrumb-section mb-5" style="background-image: url('<?php echo base_url("public/assest/images/{$category_slug}.webp"); ?>'); background-size: cover; background-position: center; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center text-white">
                    <?php 
                    if (isset($is_static_page) && $is_static_page) {
                        echo $page_title;
                    } else {
                        echo ucfirst(str_replace('-', ' ', $category_slug)) . ' Trending Updates';
                    }
                    ?>
                </h1>
                <div class="text-center">
                    <nav aria-label="breadcrumb" style="display: inline-block;">
                        <p class="text-white mb-0">
                            <a href="<?php echo base_url('/'); ?>" class="text-white text-decoration-none">Home</a>
                            <span class="mx-2">|</span>
                            <span>
                                <?php 
                                if (isset($is_static_page) && $is_static_page) {
                                    echo $page_title;
                                } else {
                                    echo ucfirst(str_replace('-', ' ', $category_slug));
                                }
                                ?>
                            </span>
                        </p>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Check if it's a static page or category page -->
<?php if (isset($is_static_page) && $is_static_page): ?>
    <!-- ===== Static Page Content Section ===== -->
    <section class="static-page-content py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <?= $static_content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    


<?php else: ?>
<!-- ===== Cricket Section (Only for Sports Category) ===== -->
<?php if (isset($category_slug) && $category_slug === 'ipl2026'): ?>

<section class="cricket-category-section py-4">
    <div class="container" style="max-width: 100%; padding: 0 15px;">
        <div class="row">
            <div class="col-12">
                
                <div class="cricket-header-category mb-4 d-flex justify-content-between align-items-center">
                    
                    <h3 class="section-title-category mb-0">
                    Indian Premier League 2026 - Live Scores & Match Details
                    </h3>
                    <div class="slider-controls">
                        <button class="slider-btn slider-prev-hindi" onclick="slideMatchesHindi('prev')">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="slider-btn slider-next-hindi" onclick="slideMatchesHindi('next')">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Loading Spinner -->
                <div id="cricket-loading-category-hindi" class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">लोड हो रहा है...</span>
                    </div>
                    <p class="mt-2 text-muted">आज के मैच लोड हो रहे हैं...</p>
                </div>
                
                <!-- Cricket Matches Slider Container -->
                <div class="cricket-slider-wrapper-hindi" style="display: none;">
                    <div id="cricket-matches-container-category-hindi" class="cricket-slider-hindi">
                        <!-- Matches will be loaded here -->
                    </div>
                </div>
                
                <!-- Error Message -->
                <div id="cricket-error-category-hindi" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    क्रिकेट मैच लोड करने में असमर्थ।
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scorecard Modal for Hindi Category -->
<div class="modal fade" id="scorecardModalCategoryHindi" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scorecardModalCategoryHindiTitle">
                    <img src="https://flyppedhindi.com/public/assest/images/Flypped-hindi-logo.webp" alt="Flypped Hindi" style="height: 42px; margin-right: 14px; vertical-align: middle; background:white; border-radius:6px;">
                    <span>मैच विवरण</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scorecardModalCategoryHindiBody">
                <p>लोड हो रहा है...</p>
            </div>
        </div>
    </div>
</div>

<!-- Player Info Modal for Hindi Category -->
<div class="modal fade" id="playerInfoModalCategoryHindi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="playerInfoModalCategoryHindiTitle">खिलाड़ी जानकारी</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="playerInfoModalCategoryHindiBody"></div>
        </div>
    </div>
</div>

<?php endif; ?>

    <!-- ===== Categories Section ===== -->

    <section class="category-page">
        <div class="container">
<div class="row">
    <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <?php if (strtotime($post['post_date']) < strtotime('2026-03-28')) continue; ?>
                <div class="col-md-4 mb-4">
                    <a href="<?= base_url($post['post_name']); ?>" class="text-decoration-none">
                        <div class="card mb-4">
                            <img src="<?= $post['thumbnail_url']; ?>" 
                                class="card-img-top" 
                                alt="<?= htmlspecialchars($post['post_title']); ?>">
                            <div class="card-body mb-3">
                                <h2 class="card-title" style="text-transform:capitalize;font-size:21px;line-height:31px;">
                                    <?= htmlspecialchars(strtolower($post['post_title'])); ?>
                                </h2>
                                <p class="card-text">
                                    <?= strtoupper(date('F d, Y', strtotime($post['post_date']))); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
    <?php else: ?>
         <div class="col-12">
        <div class="text-center py-5">
            <div style="font-size:56px; margin-bottom:16px;">
                <?= (isset($category_slug) && $category_slug === 'ipl2026') ? '🏏' : '📰' ?>
            </div>
            <h4 class="fw-bold">
                <?= (isset($category_slug) && $category_slug === 'ipl2026')
                    ? 'IPL 2026 खबरें जल्द आएंगी!'
                    : 'No Posts Found' ?>
            </h4>
            <p class="text-muted">
                <?= (isset($category_slug) && $category_slug === 'ipl2026')
                    ? 'IPL 2026 से जुड़े लेटेस्ट अपडेट यहाँ जल्द उपलब्ध होंगे।'
                    : 'No posts found in this category.' ?>
            </p>
            <a href="<?= base_url('/') ?>" class="btn btn-primary mt-2">होम पर जाएं</a>
        </div>
    </div>
    <?php endif; ?>
</div>

            <!-- Pagination Section (only for categories, not static pages) -->
            <?php if (!empty($posts)): ?>
                <div class="row mb-5 mt-3">
                    <div class="col-12 d-flex justify-content-between">
                        <nav aria-label="Page navigation">
                            <?php if (!empty($pagerLinks)): ?>
                                <ul class="pagination">
                                    <?= $pagerLinks; ?>
                                </ul>
                            <?php endif; ?>
                        </nav>

                        <!-- Showing current page number -->
                        <div class="pagination-info">
                            <span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<!-- responsive link ad -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-7949838781204630"
     data-ad-slot="1739499306"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
        </div>
    </section>
<?php endif; ?>
<!-- Custom CSS for Static Pages  and cricket modal -->
<style>
/* Body overflow */
body {
    overflow-x: hidden;
}
/* ========================================
   CRICKET CATEGORY SECTION — HINDI
======================================== */

.section-title-category {
    color: #333;
    font-size: 24px;
    font-weight: 700;
    border-bottom: 3px solid #667eea;
    padding-bottom: 10px;
    display: inline-block;
    margin-left: 5%;
}

/* Cricket Slider for Hindi */
.cricket-slider-wrapper-hindi {
    position: relative;
    overflow: hidden;
    padding: 0 60px;
    margin-bottom: 20px;
}

.cricket-slider-hindi {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 10px 0;
}

.cricket-slider-hindi::-webkit-scrollbar {
    display: none;
}

.slider-controls {
    display: flex;
    gap: 10px;
}

.slider-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 2px solid #667eea;
    background: white;
    color: #667eea;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.slider-btn:hover {
    background: #667eea;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102,126,234,0.4);
}

.slider-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

/* Cricket Match Cards - Hindi Category */
.cricket-match-card-category-hindi {
    cursor: pointer;
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
    transition: all 0.2s;
    min-width: 350px;
    flex-shrink: 0;
}

.cricket-match-card-category-hindi:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    border-color: #667eea;
}

.live-badge-category {
    background: linear-gradient(135deg, #ff0000, #ff4444);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    animation: pulse-live 2s infinite;
}

@keyframes pulse-live {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

.match-header-category {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.match-type-badge-category {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
}

.badge-t20 { background: #4CAF50; color: white; }
.badge-odi { background: #2196F3; color: white; }
.badge-test { background: #FF9800; color: white; }

.teams-container-category { margin: 16px 0; }

.team-row-category {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.team-row-category:last-child { border-bottom: none; }

.team-info-category {
    display: flex;
    align-items: center;
    gap: 10px;
}

.team-flag-category {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
}

.team-name-category {
    font-weight: 600;
    font-size: 15px;
    color: #333;
}

.team-score-category {
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.score-runs-category {
    font-weight: bold;
    font-size: 18px;
    color: #333;
}

.score-overs-category {
    font-size: 13px;
    color: #666;
}

.score-placeholder-category { color: #999; }

.match-footer-category {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f0f0f0;
}

.match-status-category {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.status-indicator-category { font-size: 8px; }

.status-live .status-indicator-category {
    color: #ff0000;
    animation: blink-live 1s infinite;
}

@keyframes blink-live {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.status-upcoming .status-indicator-category { color: #FFC107; }
.status-ended .status-indicator-category { color: #9E9E9E; }

.status-text-category {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.match-venue-category {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #999;
}

/* ========================================
   SCORECARD MODAL — HINDI CATEGORY
======================================== */

#scorecardModalCategoryHindi .modal-content {
    background-color: #ffffff !important;
    border-radius: 12px;
    overflow: hidden;
}

#scorecardModalCategoryHindi .modal-body {
    background-color: #ffffff !important;
    padding: 20px;
}

#scorecardModalCategoryHindi .modal-header {
    padding: 1.2rem 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-bottom: none;
}

#scorecardModalCategoryHindi .modal-title {
    font-size: 17px;
    font-weight: 600;
    display: flex;
    align-items: center;
    color: white;
}

#scorecardModalCategoryHindi .btn-close {
    width: 32px !important;
    height: 32px !important;
    padding: 0 !important;
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") center/1.2em auto no-repeat !important;
    opacity: 1 !important;
    border: 2px solid rgba(255,255,255,0.6);
    border-radius: 50%;
    transition: all 0.3s ease;
}

#scorecardModalCategoryHindi .btn-close:hover {
    background-color: rgba(255,255,255,0.3) !important;
    border-color: rgba(255,255,255,1);
    transform: rotate(90deg);
}

/* ========================================
   SCORECARD INNER STYLES (Hindi Category)
======================================== */

/* Use same classes as main hindi with -hc suffix */
.match-result-header-hc {
    background:#fff; padding:20px 24px;
    border-bottom:1px solid #e0e0e0;
    margin:-20px -20px 0 -20px;
}
.result-text-hc { font-size:18px; font-weight:600; color:#202124; margin-bottom:6px; }
.live-indicator-hc { display:flex; align-items:center; color:#d93025; }
.live-dot-hc { width:8px; height:8px; background:#d93025; border-radius:50%; margin-right:8px; animation:pulse-dot-hc 1.5s infinite; }
.match-meta-hc { font-size:14px; color:#5f6368; }

@keyframes pulse-dot-hc { 0%,100%{opacity:1} 50%{opacity:0.5} }

.teams-score-container-hc {
    background:#fff; padding:16px 24px;
    margin:0 -20px; border-bottom:1px solid #e0e0e0;
}

.team-score-row-hc {
    display:flex; justify-content:space-between;
    align-items:center; padding:12px 0;
    border-bottom:1px solid #f1f3f4;
}
.team-score-row-hc:last-child { border-bottom:none; }
.team-score-row-hc.winner-hc {
    background:#e8f5e9; margin:0 -24px;
    padding-left:24px; padding-right:24px;
    border-left:4px solid #34a853;
}
.team-flag-name-hc { display:flex; align-items:center; gap:12px; }
.team-flag-lg-hc { width:32px; height:32px; border-radius:50%; object-fit:cover; }
.team-name-lg-hc { font-size:16px; font-weight:600; color:#202124; }
.team-total-score-hc { display:flex; align-items:baseline; gap:8px; }
.total-runs-hc { font-size:24px; font-weight:700; color:#202124; }
.total-overs-hc { font-size:16px; color:#5f6368; }

.scorecard-info-card-compact-hc {
    background:#f8f9fa; padding:16px 20px;
    margin:20px 0; border-radius:8px;
}
.info-row-hc { display:flex; justify-content:space-between; padding:6px 0; font-size:14px; }
.info-label-hc { color:#5f6368; font-weight:500; }
.info-value-hc { color:#202124; font-weight:600; }

.scorecard-tabs-hc {
    border-bottom:2px solid #e0e0e0;
    margin:0 -20px; padding:0 20px;
}
.scorecard-tabs-hc .nav-link {
    color:#5f6368; font-size:15px; font-weight:600;
    border:none; border-bottom:3px solid transparent;
    padding:14px 20px; margin-bottom:-2px;
    background:transparent; transition:all 0.2s;
}
.scorecard-tabs-hc .nav-link:hover { color:#1a73e8; border-bottom-color:#dfe1e5; }
.scorecard-tabs-hc .nav-link.active { color:#1a73e8; border-bottom-color:#1a73e8; background:transparent; }

.scorecard-tab-content-hc { padding:0; }

/* Overview styles */
.overview-container-hc { padding:0; }

.innings-overview-hc {
    background:#fff; border:1px solid #e0e0e0;
    border-radius:8px; padding:20px; margin-bottom:16px;
}
.innings-header-hc {
    display:flex; justify-content:space-between;
    align-items:center; margin-bottom:16px;
    padding-bottom:12px; border-bottom:2px solid #1a73e8;
}
.innings-title-hc { font-size:18px; font-weight:700; color:#202124; margin:0; }
.innings-score-hc { font-size:22px; font-weight:700; color:#1a73e8; }

.overview-stats-grid-hc {
    display:grid; grid-template-columns:repeat(4,1fr);
    gap:12px; margin-bottom:20px;
}
.stat-box-hc {
    background:#f8f9fa; padding:16px; border-radius:8px;
    text-align:center; border:1px solid #e0e0e0;
}
.stat-label-hc { font-size:13px; color:#5f6368; margin-bottom:6px; font-weight:500; }
.stat-value-hc { font-size:24px; font-weight:700; color:#202124; }

.top-performer-hc {
    background:#e8f5e9; padding:12px 16px;
    border-radius:6px; margin-bottom:8px;
    border-left:4px solid #34a853;
}
.performer-label-hc {
    font-size:12px; color:#137333; font-weight:600;
    margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;
}
.performer-details-hc {
    display:flex; justify-content:space-between; align-items:center;
}
.performer-name-hc { font-size:15px; font-weight:600; color:#202124; }
.performer-score-hc { font-size:16px; font-weight:700; color:#137333; }

.yet-to-bat-hc {
    background:#fff3cd; padding:12px 16px;
    border-radius:6px; border-left:4px solid #ffc107;
}
.ytb-label-hc { font-size:14px; color:#664d03; font-weight:600; }
.ytb-count-hc { font-weight:700; color:#533f03; }

/* Graphs */
.match-graphs-hc {
    background:#fff; border-radius:8px;
    padding:16px; border:1px solid #e0e0e0; margin-top:20px;
}
.graph-section-hc { margin-bottom:24px; }
.graph-section-hc:last-child { margin-bottom:0; }
.graph-title-hc {
    font-size:15px; font-weight:600; color:#202124;
    margin-bottom:12px; padding-bottom:8px;
    border-bottom:1px solid #e0e0e0;
}
.live-dot-small-hc {
    display:inline-block; width:8px; height:8px;
    background:#d93025; border-radius:50%;
    margin-right:6px; animation:pulse-dot-hc 1.5s infinite;
    vertical-align:middle;
}
.worm-graph-hc {
    background:#f8f9fa; border-radius:8px;
    padding:16px; position:relative;
}
.worm-graph-hc canvas { width:100%; height:150px; display:block; }
.ball-by-ball-hc { background:#f8f9fa; padding:16px; border-radius:8px; }
.balls-container-hc { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; }
.ball-hc {
    width:40px; height:40px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:700;
    box-shadow:0 2px 4px rgba(0,0,0,0.1);
    transition:transform 0.2s;
}
.ball-hc:hover { transform:scale(1.1); }
.ball-dot-hc { background:#9aa0a6; color:#fff; }
.ball-run-hc { background:#4285f4; color:#fff; }
.ball-four-hc { background:#fbbc04; color:#202124; }
.ball-six-hc { background:#ea4335; color:#fff; }
.ball-wicket-hc { background:#202124; color:#fff; border:2px solid #ea4335; }

/* Match analysis */
.match-analysis-card-hc {
    background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
    border-radius:12px; padding:20px; color:white;
    box-shadow:0 4px 12px rgba(102,126,234,0.3);
}
.analysis-title-hc {
    font-size:18px; font-weight:700; margin-bottom:16px;
    color:white; display:flex; align-items:center; gap:8px;
}
.analysis-section-hc { margin-bottom:16px; }
.analysis-item-hc {
    display:flex; align-items:center; gap:16px;
    background:rgba(255,255,255,0.15);
    padding:16px; border-radius:8px;
    backdrop-filter:blur(10px);
}
.winner-analysis-hc { border-left:4px solid #ffd700; }
.analysis-icon-hc { font-size:32px; flex-shrink:0; }
.analysis-content-hc { flex:1; }
.analysis-label-hc { font-size:13px; color:rgba(255,255,255,0.9); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px; }
.analysis-value-hc { font-size:16px; font-weight:600; color:white; }

.comparison-grid-hc { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
.comparison-item-hc { background:rgba(255,255,255,0.15); padding:12px; border-radius:8px; text-align:center; }
.comp-label-hc { font-size:12px; color:rgba(255,255,255,0.9); margin-bottom:8px; font-weight:500; text-transform:uppercase; }
.comp-values-hc { display:flex; flex-direction:column; gap:4px; }
.comp-team1-hc,.comp-team2-hc { font-size:14px; font-weight:600; color:white; }

/* Tables and other sections - copy all remaining styles from your hindi index */
.extras-total-section-hc { background:#fff; border-radius:8px; padding:0; }
.extras-card-hc { background:#f8f9fa; border:1px solid #e0e0e0; border-radius:8px; padding:16px; }
.extras-title-hc {
    font-size:16px; font-weight:600; color:#202124;
    margin-bottom:12px; padding-bottom:8px;
    border-bottom:2px solid #1a73e8;
}
.extras-breakdown-hc { display:flex; flex-direction:column; gap:8px; }
.extras-row-hc {
    display:flex; justify-content:space-between; align-items:center;
    padding:8px 12px; background:white; border-radius:6px;
}
.extras-row-hc.total-row-hc { background:#e8f5e9; border:2px solid #34a853; }
.extras-detail-hc {
    padding:4px 12px; font-size:13px; color:#5f6368;
    background:white; border-radius:4px; border-left:3px solid #fbbc04;
}

/* Fall of wickets */
.fall-of-wickets-section-hc { background:#fff; border-radius:8px; padding:0; }
.fow-title-hc {
    font-size:16px; font-weight:600; color:#202124;
    margin-bottom:12px; padding-bottom:8px;
    border-bottom:2px solid #ea4335;
    display:flex; align-items:center; gap:6px;
}
.fow-container-hc { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; }
.fow-item-hc {
    display:flex; align-items:center; gap:12px;
    background:#fff5f5; border:1px solid #ffcdd2;
    border-radius:8px; padding:12px; transition:all 0.2s;
}
.fow-item-hc:hover { background:#ffebee; transform:translateY(-2px); box-shadow:0 2px 8px rgba(234,67,53,0.2); }
.fow-wicket-hc {
    width:36px; height:36px; background:#ea4335; color:white;
    border-radius:50%; display:flex; align-items:center;
    justify-content:center; font-size:16px; font-weight:700; flex-shrink:0;
}
.fow-details-hc { flex:1; }
.fow-player-hc { font-size:14px; font-weight:600; color:#202124; margin-bottom:2px; }
.fow-stats-hc { display:flex; gap:12px; margin-top:4px; margin-bottom:4px; }
.fow-player-score-hc { font-size:13px; font-weight:600; color:#1a73e8; background:#e8f0fe; padding:2px 8px; border-radius:4px; }
.fow-team-score-hc { font-size:12px; color:#5f6368; font-weight:500; }
.fow-dismissal-hc { font-size:11px; color:#ea4335; font-style:italic; margin-top:2px; }

/* Yet to bat with names */
.yet-to-bat-section-hc { margin-bottom:20px; }
.ytb-card-hc {
    background:#ffffff; border:1px solid #e0e0e0;
    border-left:4px solid #1a73e8; border-radius:8px; padding:16px;
}
.ytb-title-hc {
    font-size:16px; font-weight:600; color:#202124;
    margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;
}
.ytb-players-grid-hc { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; margin-top:12px; }
.ytb-player-card-hc {
    background:#f8f9fa; border:1px solid #e0e0e0;
    border-radius:6px; padding:12px;
    transition:all 0.2s ease; cursor:pointer;
    user-select:none; position:relative;
}
.ytb-player-card-hc:hover {
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(26,115,232,0.3);
    border-color:#1a73e8; background:#e8f0fe;
}
.ytb-player-name-hc { font-size:14px; font-weight:600; color:#202124; margin-bottom:4px; line-height:1.3; }
.ytb-player-role-hc { font-size:12px; color:#5f6368; font-weight:500; display:inline-block; background:#e8f0fe; padding:2px 8px; border-radius:3px; margin-top:4px; }
.ytb-player-style-hc { font-size:11px; color:#666; margin-top:4px; padding:2px 6px; background:rgba(0,166,81,0.1); border-radius:3px; display:inline-block; }
.ytb-content-hc { display:flex; align-items:center; justify-content:center; }
.ytb-count-badge-hc {
    display:flex; align-items:center; gap:10px;
    background:#f8f9fa; padding:12px 20px; border-radius:6px;
    border:1px solid #e0e0e0; font-size:15px; font-weight:600; color:#202124;
}
.ytb-count-badge-hc i { font-size:18px; color:#1a73e8; }

/* Scorecard tables */
.table-section-hc { background:#fff; margin-bottom:24px; }
.table-section-title-hc {
    font-size:16px; font-weight:600; color:#202124;
    margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;
}
.scorecard-table-hc {
    width:100%; margin-bottom:0;
    font-size:14px !important; background-color:#ffffff; border-collapse:collapse;
}
.scorecard-table-hc thead th {
    background:#f8f9fa; padding:12px 8px !important;
    font-size:13px !important; font-weight:600; text-align:center;
    border-bottom:2px solid #e0e0e0; color:#5f6368;
    text-transform:uppercase; letter-spacing:0.5px;
}
.scorecard-table-hc thead th:first-child { text-align:left; padding-left:16px !important; }
.scorecard-table-hc tbody td {
    padding:14px 8px !important; font-size:15px !important;
    border-bottom:1px solid #f1f3f4; vertical-align:top;
    text-align:center; color:#202124;
}
.scorecard-table-hc tbody td:first-child { text-align:left; padding-left:16px !important; padding-right:16px !important; }
.scorecard-table-hc tbody tr:hover { background-color:#f8f9fa; }
.batsman-cell-hc { display:flex; flex-direction:column; gap:4px; }
.batsman-name-hc { font-weight:600; font-size:15px !important; color:#202124; }
.dismissal-text-hc { font-size:13px !important; color:#5f6368; font-weight:400; }
.bowler-name-hc { font-weight:600; font-size:15px !important; color:#202124; }
.batting-now-hc { background-color:#e8f5e9 !important; }
.batting-now-hc:hover { background-color:#d4edda !important; }
.player-clickable-hc { cursor:pointer !important; transition:all 0.2s ease; }
.player-clickable-hc:hover { background-color:#e8f0fe !important; transform:translateX(2px); }
.player-clickable-hc .batsman-name-hc,
.player-clickable-hc .bowler-name-hc { color:#1a73e8 !important; }

/* Player info modal */
#playerInfoModalCategoryHindi .modal-content { background-color:#ffffff; border-radius:12px; overflow:hidden; }
#playerInfoModalCategoryHindi .modal-header { background:linear-gradient(135deg,#00a651 0%,#00c853 100%); color:white; padding:1.2rem 1.5rem; border-bottom:none; }
#playerInfoModalCategoryHindi .modal-title { font-size:18px; font-weight:700; color:white; }
#playerInfoModalCategoryHindi .btn-close {
    background:transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") center/1.2em auto no-repeat;
    opacity:1; border:2px solid rgba(255,255,255,0.6); border-radius:50%; padding:8px;
}
#playerInfoModalCategoryHindi .btn-close:hover { background-color:rgba(255,255,255,0.3); border-color:rgba(255,255,255,1); transform:rotate(90deg); }

.player-info-container-hc { padding:20px; }
.player-image-section-hc { text-align:center; margin-bottom:24px; }
.player-modal-img-hc {
    width:120px; height:120px; border-radius:50%; object-fit:cover;
    border:4px solid #00a651; box-shadow:0 4px 12px rgba(0,166,81,0.3);
}
.player-details-grid-hc {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:16px; margin-bottom:24px;
}
.player-detail-item-hc {
    background:#f8f9fa; padding:16px; border-radius:8px;
    border-left:4px solid #00a651; transition:all 0.2s ease;
}
.player-detail-item-hc:hover { background:#e8f5e9; transform:translateX(4px); }
.detail-label-hc { font-size:13px; color:#5f6368; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
.detail-value-hc { font-size:16px; color:#202124; font-weight:700; }
.player-role-badge-large-hc {
    background:linear-gradient(135deg,#00a651 0%,#00c853 100%);
    color:white; padding:16px 24px; border-radius:12px;
    text-align:center; display:flex; align-items:center;
    justify-content:center; gap:12px; font-size:18px; font-weight:700;
    box-shadow:0 4px 12px rgba(0,166,81,0.3);
}
.role-icon-hc { font-size:28px; }
.role-text-hc { font-size:16px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }

/* Responsive */
@media (max-width: 768px) {
    .cricket-slider-wrapper-hindi { padding: 0 10px; }
    .cricket-match-card-category-hindi { min-width: 280px; }
    .slider-controls { display: none; }
    .overview-stats-grid-hc { grid-template-columns: repeat(2,1fr); }
    .comparison-grid-hc { grid-template-columns: 1fr; }
    .fow-container-hc { grid-template-columns: 1fr; }
    .performer-details-hc { flex-direction: column; align-items: flex-start; gap: 4px; }
    .analysis-item-hc { flex-direction: column; text-align: center; }
    .ytb-players-grid-hc { grid-template-columns: repeat(auto-fill,minmax(140px,1fr)); }
    .player-details-grid-hc { grid-template-columns: 1fr; }
    .total-runs-hc { font-size: 20px; }
    .innings-score-hc { font-size: 18px; }
    .stat-value-hc { font-size: 20px; }
    .scorecard-tabs-hc .nav-link { padding: 12px 12px; font-size: 14px; }
}

@media (max-width: 576px) {
    .overview-stats-grid-hc { grid-template-columns: repeat(2,1fr); gap: 8px; }
    .stat-box-hc { padding: 12px 8px; }
    .stat-value-hc { font-size: 18px; }
    .ball-hc { width: 35px; height: 35px; font-size: 14px; }
    .fow-item-hc { padding: 10px; }
    .ytb-players-grid-hc { grid-template-columns: 1fr; }
    .player-info-container-hc { padding: 12px; }
    .player-modal-img-hc { width: 100px; height: 100px; }
}

/* Static Page Styles */
.static-content {
    line-height: 1.7;
}
.breadcrumb-item{
    color:#fff;
}
.static-content .content-text p {
    margin-bottom: 1.2rem;
    text-align: justify;
    color: #333;
}

.static-content .content-text h5 {
    color: #2c3e50;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.static-content h4 {
    color: #2c3e50;
    font-weight: 700;
    border-bottom: 3px solid #3498db;
    padding-bottom: 0.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #fff;
}

@media (max-width: 768px) {
    .breadcrumb-section {
        padding: 40px 0;
    }
    
    .card-body {
        padding: 2rem 1.5rem;
    }
    
    .static-content .content-text {
        font-size: 0.95rem;
    }
}

.static-page-content .card {
    border: none;
    border-radius: 10px;
}

.static-page-content .card-body {
    background: #f8f9fa;
    border-radius: 10px;
}</style>

<?php if (isset($category_slug) && $category_slug === 'ipl2026'): ?>

<script data-cfasync="false">let currentSlidePositionHindi = 0;

function slideMatchesHindi(direction) {
    const slider = document.getElementById('cricket-matches-container-category-hindi');
    if (!slider) return;
    
    const cardWidth = 370;
    
    if (direction === 'next') {
        currentSlidePositionHindi += cardWidth;
    } else {
        currentSlidePositionHindi -= cardWidth;
    }
    
    const maxScroll = slider.scrollWidth - slider.clientWidth;
    if (currentSlidePositionHindi < 0) currentSlidePositionHindi = 0;
    if (currentSlidePositionHindi > maxScroll) currentSlidePositionHindi = maxScroll;
    
    slider.scrollTo({
        left: currentSlidePositionHindi,
        behavior: 'smooth'
    });
}

document.addEventListener('DOMContentLoaded', function() {
    
       // TEMP DEBUG - remove after fix
    console.log('Script loaded');
    fetch('https://flyppedhindi.com/api/cricket/todaymatches')
        .then(r => r.json())
        .then(data => {
            console.log('Response:', data);
            console.log('MatchList:', data.data.matchList);
            console.log('Count:', data.data.matchList.length);
        })
        .catch(err => console.error('ERROR:', err));
    // END DEBUG
    /* ===================================================
       HELPER FUNCTIONS
    =================================================== */
    
    function convertGMTtoIST(gmtDateTimeString) {
        if (!gmtDateTimeString) return null;
        
        try {
            const gmtDate = new Date(gmtDateTimeString);
            const istDate = new Date(gmtDate.getTime() + (5.5 * 60 * 60 * 1000));
            
            let hours = istDate.getHours();
            let minutes = istDate.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            
            const timeIST = hours + ':' + minutes + ' ' + ampm;
            const months = ['जन', 'फर', 'मार', 'अप्र', 'मई', 'जून', 'जुल', 'अग', 'सित', 'अक्टू', 'नव', 'दिस'];
            const monthName = months[istDate.getMonth()];
            const day = istDate.getDate();
            const year = istDate.getFullYear();
            
            return {
                time: timeIST,
                date: `${monthName} ${day}`,
                fullDate: `${monthName} ${day}, ${year}`,
                dateObject: istDate
            };
        } catch (e) {
            console.error('Date conversion error:', e);
            return null;
        }
    }
    
    function capitalizeFirstLetter(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }

    function countBoundaries(batting, type) {
        if (!batting) return 0;
        return batting.reduce((sum, bat) => sum + (bat[type] || 0), 0);
    }

    function getRoleIcon(role) {
        if (!role) return '👤';
        const r = role.toLowerCase();
        if (r.includes('batsman') || r.includes('batter')) return '🏏';
        if (r.includes('bowler')) return '⚡';
        if (r.includes('allrounder') || r.includes('all-rounder')) return '⭐';
        if (r.includes('wk') || r.includes('wicket')) return '🧤';
        return '👤';
    }

    function getRoleHindi(role) {
        if (!role) return role || '';
        const r = role.toLowerCase();
        if (r.includes('batsman') || r.includes('batter')) return 'बल्लेबाज';
        if (r.includes('bowler')) return 'गेंदबाज';
        if (r.includes('allrounder') || r.includes('all-rounder')) return 'ऑलराउंडर';
        if (r.includes('wk') || r.includes('wicket')) return 'विकेट-कीपर';
        return role;
    }

    /* ===================================================
       TEAM NAME RESOLVER
    =================================================== */

    const TEAM_MAP = {
        'sl':'Sri Lanka', 'nz':'New Zealand',
        'sa':'South Africa', 'wi':'West Indies',
        'usa':'United States', 'uae':'United Arab Emirates',
        'png':'Papua New Guinea','ind':'India',
        'pak':'Pakistan', 'aus':'Australia',
        'eng':'England', 'ban':'Bangladesh',
        'zim':'Zimbabwe', 'afg':'Afghanistan',
        'ire':'Ireland', 'ned':'Netherlands',
        'sco':'Scotland', 'nam':'Namibia',
        'can':'Canada', 'uga':'Uganda',
        'oma':'Oman', 'nep':'Nepal',
        'ken':'Kenya', 'bah':'Bahrain',
        'sri':'Sri Lanka', 'new':'New Zealand',
        'south':'South Africa', 'west':'West Indies',
        'papua':'Papua New Guinea','united':'United States',
        'india':'India', 'pakistan':'Pakistan',
        'australia':'Australia', 'england':'England',
        'bangladesh':'Bangladesh', 'zimbabwe':'Zimbabwe',
        'afghanistan':'Afghanistan', 'ireland':'Ireland',
        'netherlands':'Netherlands', 'scotland':'Scotland',
        'namibia':'Namibia', 'canada':'Canada',
        'uganda':'Uganda', 'oman':'Oman',
        'nepal':'Nepal', 'kenya':'Kenya',
        'sri lanka':'Sri Lanka', 'new zealand':'New Zealand',
        'south africa':'South Africa', 'west indies':'West Indies',
        'papua new guinea':'Papua New Guinea',
        'united arab emirates':'United Arab Emirates',
        'united states':'United States',
        'united states of america':'United States',
    };

    function getFullTeamName(inningStr, matchInfo, index) {
        if (!inningStr) {
            return (matchInfo && matchInfo.teamInfo && matchInfo.teamInfo[index])
                ? matchInfo.teamInfo[index].name : `टीम ${(index || 0) + 1}`;
        }
        
        // Handle comma-corrupted inning strings
        if (inningStr.includes(',')) {
            if (matchInfo && matchInfo.teamInfo && matchInfo.teamInfo[index]) {
                const team = matchInfo.teamInfo[index];
                
                const parts = inningStr.split(',').map(p => p.trim());
                
                for (let part of parts) {
                    const cleanPart = part
                        .replace(/\s*(1st|2nd|3rd|4th)\s*(inning|innings)\s*$/gi, '')
                        .replace(/\s*(inning|innings)\s*\d*\s*$/gi, '')
                        .trim()
                        .toLowerCase();
                    
                    const teamNameLower = team.name.toLowerCase();
                    const teamShortLower = team.shortname.toLowerCase();
                    
                    if (cleanPart === teamNameLower || 
                        cleanPart === teamShortLower ||
                        teamNameLower.includes(cleanPart) ||
                        teamShortLower.includes(cleanPart) ||
                        cleanPart.includes(teamNameLower) ||
                        cleanPart.includes(teamShortLower)) {
                        return team.name;
                    }
                }
                
                return team.name;
            }
            
            inningStr = inningStr.split(',')[0].trim();
        }
        
        const cleaned = inningStr
            .replace(/\s*(1st|2nd|3rd|4th)\s*(inning|innings)\s*$/gi, '')
            .replace(/\s*(inning|innings)\s*\d*\s*$/gi, '')
            .trim();
        
        if (matchInfo && matchInfo.teamInfo) {
            const exactName = matchInfo.teamInfo.find(t => cleaned.toLowerCase() === t.name.toLowerCase());
            if (exactName) return exactName.name;
            
            const exactShort = matchInfo.teamInfo.find(t => cleaned.toLowerCase() === t.shortname.toLowerCase());
            if (exactShort) return exactShort.name;
            
            const startsWith = matchInfo.teamInfo.find(t => t.name.toLowerCase().startsWith(cleaned.toLowerCase()));
            if (startsWith) return startsWith.name;
            
            const shortIn = matchInfo.teamInfo.find(t => cleaned.toLowerCase().startsWith(t.shortname.toLowerCase()));
            if (shortIn) return shortIn.name;
            
            const contains = matchInfo.teamInfo.find(t => {
                const c = cleaned.toLowerCase();
                const tn = t.name.toLowerCase();
                const ts = t.shortname.toLowerCase();
                return tn.includes(c) || c.includes(ts) || c.includes(tn) || ts.includes(c);
            });
            if (contains) return contains.name;
        }
        
        const key = cleaned.toLowerCase();
        if (TEAM_MAP[key]) return TEAM_MAP[key];
        
        return cleaned.replace(/\b\w/g, c => c.toUpperCase());
    }

    /* ===================================================
       SMART SCORE MATCHING - FIXED
    =================================================== */

    function smartScoreMatch(matchInfo, team, index) {
        if (!matchInfo.score || matchInfo.score.length === 0) return null;
        
        function norm(str) {
            return (str || '').toLowerCase()
                .replace('united states of america', 'usa')
                .replace('united arab emirates', 'uae')
                .replace('papua new guinea', 'png')
                .replace('west indies', 'wi')
                .replace('south africa', 'sa')
                .replace('new zealand', 'nz')
                .replace('sri lanka', 'sl');
        }
        
        const teamName = norm(team.name);
        const teamShort = norm(team.shortname);
        
        const found = matchInfo.score.find(s => {
            if (!s.inning) return false;
            
            let inningStr = s.inning;
            
            // Handle comma-corrupted inning strings
            if (inningStr.includes(',')) {
                const parts = inningStr.split(',').map(p => p.trim());
                
                for (let part of parts) {
                    const partNorm = norm(part);
                    
                    const teamPart = partNorm
                        .replace(/\s*(1st|2nd|3rd|4th)?\s*(inning|innings)\s*\d*\s*$/gi, '')
                        .trim();
                    
                    if (teamPart === teamName || 
                        teamPart === teamShort ||
                        teamName.includes(teamPart) ||
                        teamShort.includes(teamPart) ||
                        teamPart.includes(teamName) ||
                        teamPart.includes(teamShort)) {
                        return true;
                    }
                }
            }
            
            // Normal matching
            const ing = norm(inningStr);
            
            const cleanInning = ing
                .replace(/\s*(1st|2nd|3rd|4th)?\s*(inning|innings)\s*\d*\s*$/gi, '')
                .trim();
            
            return cleanInning === teamName || 
                   cleanInning === teamShort ||
                   cleanInning.includes(teamName) || 
                   cleanInning.includes(teamShort) || 
                   teamName.includes(cleanInning) || 
                   teamShort.includes(cleanInning) ||
                   teamName === cleanInning.split(' ')[0] || 
                   teamShort === cleanInning.split(' ')[0];
        });
        
        return found || null;
    }

    /* ===================================================
       API FUNCTIONS
    =================================================== */

    function loadMatchInfoCategoryHindi(matchId) {
        return fetch(`https://api.cricapi.com/v1/match_info?apikey=9bafa7bc-e94e-45de-818e-2cf8c7404f5e&id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    function loadMatchScorecardCategoryHindi(matchId) {
        return fetch(`https://flyppedhindi.com/api/cricket/scorecard?id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    function loadMatchSquadCategoryHindi(matchId) {
        return fetch(`https://api.cricapi.com/v1/match_squad?apikey=9bafa7bc-e94e-45de-818e-2cf8c7404f5e&id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    /* ===================================================
       LOAD MATCHES
    =================================================== */

    function loadCricketMatchesCategoryHindi() {
        const container = document.getElementById('cricket-matches-container-category-hindi');
        const sliderWrapper = document.querySelector('.cricket-slider-wrapper-hindi');
        const loading = document.getElementById('cricket-loading-category-hindi');
        const error = document.getElementById('cricket-error-category-hindi');
        
        if (!container || !loading || !error) return;
        
        loading.style.display = 'block';
        if (sliderWrapper) sliderWrapper.style.display = 'none';
        error.style.display = 'none';
        
        fetch('https://flyppedhindi.com/api/cricket/todaymatches')
            .then(r => r.json())
            .then(data => {

const matchList = Array.isArray(data.data) ? data.data : (data.data.matchList || data.data.data || []);

if (data.status === 'success' && matchList && matchList.length > 0) {
                  const matches = data.data.matchList.slice(0, 10);
                    const promises = matches.map(m => 
                        loadMatchInfoCategoryHindi(m.id)
                            .then(info => info || m)
                            .catch(() => m)
                    );
                    
                    Promise.all(promises).then(detailedMatches => {
                        loading.style.display = 'none';
                        container.innerHTML = generateMatchCardsCategoryHindi(detailedMatches);
                        if (sliderWrapper) sliderWrapper.style.display = 'block';
                        attachMatchCardClickEventsCategoryHindi();
                    });
                } else {
                    loading.style.display = 'none';
                    error.style.display = 'block';
                    error.innerHTML = '<i class="fas fa-info-circle"></i> आज कोई मैच नहीं।';
                }
            })
            .catch(() => {
                loading.style.display = 'none';
                error.style.display = 'block';
            });
    }

    /* ===================================================
       GENERATE MATCH CARDS - FIXED
    =================================================== */

    function generateMatchCardsCategoryHindi(matches) {
        let html = '';
        matches.forEach(match => {
            if (!match) return;
            
            const badge = (match.matchType || 't20').toUpperCase();
            let statusClass = 'status-upcoming';
            let statusBadge = '';
            let displayStatus = match.status || 'स्थिति उपलब्ध नहीं';
            
            if (match.dateTimeGMT && !match.matchStarted) {
                const ist = convertGMTtoIST(match.dateTimeGMT);
                if (ist) displayStatus = `मैच ${ist.date}, ${ist.time} IST पर शुरू होगा`;
            }
            
            if (match.matchEnded) {
                statusClass = 'status-ended';
            } else if (match.matchStarted && !match.matchEnded) {
                statusClass = 'status-live';
                statusBadge = '<span class="live-badge-category">🔴 लाइव</span>';
            }
            
            html += `
                <div class="cricket-match-card-category-hindi" data-match-id="${match.id}">
                    <div class="match-header-category">
                        <span class="match-type-badge-category badge-t20">${badge}</span>
                        ${statusBadge}
                    </div>
                    <div class="teams-container-category">`;
            
            if (match.teamInfo && match.teamInfo.length > 0) {
                match.teamInfo.forEach((team, i) => {
                    const score = smartScoreMatch(match, team, i);
                    
                    html += `
                        <div class="team-row-category">
                            <div class="team-info-category">
                                <img src="${team.img}" class="team-flag-category"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22%3E%3Crect fill=%22%23ddd%22 width=%2224%22 height=%2224%22/%3E%3C/svg%3E';">
                                <span class="team-name-category">${team.shortname}</span>
                            </div>
                            <div class="team-score-category">
                                ${score && score.r !== undefined
                                    ? `<span class="score-runs-category">${score.r}/${score.w}</span>
                                       <span class="score-overs-category">(${parseFloat(score.o).toFixed(1)})</span>`
                                    : `<span class="score-placeholder-category">-</span>`}
                            </div>
                        </div>`;
                });
            }
            
            html += `
                    </div>
                    <div class="match-footer-category">
                        <div class="match-status-category ${statusClass}">
                            <i class="fas fa-circle status-indicator-category"></i>
                            <span class="status-text-category">${displayStatus}</span>
                        </div>
                        ${match.venue ? `<div class="match-venue-category"><i class="fas fa-map-marker-alt"></i><span>${match.venue}</span></div>` : ''}
                    </div>
                </div>`;
        });
        return html;
    }

    function attachMatchCardClickEventsCategoryHindi() {
        document.querySelectorAll('.cricket-match-card-category-hindi').forEach(card => {
            card.addEventListener('click', function() {
                openModalCategoryHindi(this.getAttribute('data-match-id'));
            });
        });
    }

    /* ===================================================
       OPEN MODAL
    =================================================== */

    function openModalCategoryHindi(matchId) {
        const modal = new bootstrap.Modal(document.getElementById('scorecardModalCategoryHindi'));
        const modalBody = document.getElementById('scorecardModalCategoryHindiBody');
        const modalTitle = document.getElementById('scorecardModalCategoryHindiTitle');
        
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">मैच विवरण लोड हो रहा है...</p></div>';
        modal.show();
        
        Promise.all([
            loadMatchScorecardCategoryHindi(matchId),
            loadMatchSquadCategoryHindi(matchId)
        ]).then(([scorecardData, squadData]) => {
            if (scorecardData) {
                if (squadData) scorecardData.squadData = squadData;
                modalTitle.innerHTML = `
                    <img src="https://flyppedhindi.com/public/assest/images/Flypped-hindi-logo.webp"
                         alt="Flypped Hindi"
                         style="height:52px;margin-right:12px;vertical-align:middle;background:white;border-radius:6px;">
                    <span style="vertical-align:middle;">${scorecardData.name || 'मैच विवरण'}</span>`;
                modalBody.innerHTML = generateScorecardCategoryHindi(scorecardData);
            } else {
                throw new Error('no scorecard');
            }
        }).catch(() => {
            Promise.all([
                loadMatchInfoCategoryHindi(matchId),
                loadMatchSquadCategoryHindi(matchId)
            ]).then(([matchInfoData, squadData]) => {
                if (matchInfoData) {
                    if (squadData) matchInfoData.squadData = squadData;
                    modalTitle.innerHTML = `
                        <img src="https://flyppedhindi.com/public/assest/images/Flypped-hindi-logo.webp"
                             alt="Flypped Hindi"
                             style="height:44px;margin-right:12px;vertical-align:middle;background:white;border-radius:6px;">
                        <span style="vertical-align:middle;">${matchInfoData.name || 'मैच विवरण'}</span>`;
                    modalBody.innerHTML = generateScorecardCategoryHindi(matchInfoData);
                } else {
                    throw new Error('no data');
                }
            }).catch(() => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>मैच विवरण लोड नहीं हो सका</strong>
                        <p class="mb-0 mt-2">यह मैच अभी शुरू नहीं हुआ है या डेटा अस्थायी रूप से उपलब्ध नहीं है।</p>
                    </div>`;
            });
        });
    }

    /* ===================================================
       GENERATE SCORECARD HTML
    =================================================== */

    function generateScorecardCategoryHindi(matchInfo) {
        let html = '';
        let matchTimeIST = '';
        
        if (matchInfo.dateTimeGMT) {
            const ist = convertGMTtoIST(matchInfo.dateTimeGMT);
            if (ist) matchTimeIST = `${ist.fullDate} बजे ${ist.time} IST`;
        }

        // Result header
        html += `<div class="match-result-header-hc">`;
        if (matchInfo.matchWinner) {
            const st = matchInfo.status || '';
            const numMatch = st.match(/\d+/);
            const margin = numMatch
                ? (st.toLowerCase().includes('run') ? numMatch[0] + ' रन से' : numMatch[0] + ' विकेट से')
                : st;
            html += `<div class="result-text-hc">${matchInfo.matchWinner} ने ${margin} जीता</div>`;
        } else if (matchInfo.matchStarted && !matchInfo.matchEnded) {
            html += `<div class="result-text-hc live-indicator-hc"><span class="live-dot-hc"></span> लाइव</div>`;
        } else {
            html += `<div class="result-text-hc">${matchInfo.status || ''}</div>`;
        }
        html += `<div class="match-meta-hc">${matchInfo.matchType ? matchInfo.matchType.toUpperCase() + ' • ' : ''}${matchInfo.venue || ''}</div></div>`;

        // Teams score
        if (matchInfo.teamInfo && matchInfo.teamInfo.length >= 2) {
            html += `<div class="teams-score-container-hc">`;
            matchInfo.teamInfo.forEach((team, index) => {
                const score = smartScoreMatch(matchInfo, team, index);
                let isWinner = false;
                if (matchInfo.matchWinner) {
                    const wl = matchInfo.matchWinner.toLowerCase();
                    const tl = team.name.toLowerCase();
                    const ts = team.shortname.toLowerCase();
                    isWinner = wl.includes(tl) || wl.includes(ts) || tl.includes(wl) || ts.includes(wl);
                }
                
                html += `
                    <div class="team-score-row-hc ${isWinner ? 'winner-hc' : ''}">
                        <div class="team-flag-name-hc">
                            <img src="${team.img}" class="team-flag-lg-hc"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22%3E%3Crect fill=%22%23ddd%22 width=%2224%22 height=%2224%22/%3E%3C/svg%3E';">
                            <span class="team-name-lg-hc">${team.shortname}</span>
                        </div>
                        <div class="team-total-score-hc">
                            ${score
                                ? `<span class="total-runs-hc">${score.r}/${score.w}</span><span class="total-overs-hc">(${parseFloat(score.o).toFixed(1)})</span>`
                                : '<span class="total-runs-hc">-</span>'}
                        </div>
                    </div>`;
            });
            html += `</div>`;
        }

        // Toss/Time info
        const tossChoice = matchInfo.tossChoice === 'bat' ? 'बल्लेबाजी' : matchInfo.tossChoice === 'field' ? 'गेंदबाजी' : (matchInfo.tossChoice || '');
        html += `
            <div class="scorecard-info-card-compact-hc">
                <div class="info-row-hc">
                    <span class="info-label-hc">टॉस:</span>
                    <span class="info-value-hc">${matchInfo.tossWinner ? capitalizeFirstLetter(matchInfo.tossWinner) + ' जीता, ' + tossChoice + ' चुना' : 'N/A'}</span>
                </div>
                <div class="info-row-hc">
                    <span class="info-label-hc">समय:</span>
                    <span class="info-value-hc">${matchTimeIST || 'N/A'}</span>
                </div>
            </div>`;

        if (!matchInfo.matchStarted) {
            html += `
                <div class="alert alert-info mt-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-3" style="font-size:28px;"></i>
                        <div>
                            <strong style="font-size:16px;">आगामी मैच</strong>
                            <p class="mb-0 mt-1" style="font-size:14px;">मैच शुरू होने के बाद स्कोरकार्ड उपलब्ध होगा।</p>
                        </div>
                    </div>
                </div>`;
        } else if (matchInfo.scorecard && matchInfo.scorecard.length > 0) {
            // Tabs
            html += `
                <ul class="nav nav-tabs scorecard-tabs-hc mt-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hc-overview-tab" type="button" role="tab">अवलोकन</button>
                    </li>`;

            matchInfo.scorecard.forEach((inning, index) => {
                const teamName = getFullTeamName(inning.inning, matchInfo, index);
                const suffix = index === 0 ? 'पहली' : index === 1 ? 'दूसरी' : index === 2 ? 'तीसरी' : 'चौथी';
                html += `
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hc-inning-${index}" type="button" role="tab">
                            ${teamName} <small style="font-size:11px;opacity:.7;">${suffix}</small>
                        </button>
                    </li>`;
            });

            html += `</ul><div class="tab-content scorecard-tab-content-hc">
                <div class="tab-pane fade show active" id="hc-overview-tab" role="tabpanel">
                    ${generateOverviewTabCategoryHindi(matchInfo)}
                </div>`;
            
            matchInfo.scorecard.forEach((inning, index) => {
                html += `<div class="tab-pane fade" id="hc-inning-${index}" role="tabpanel">${generateInningTabCategoryHindi(inning, matchInfo.matchStarted && !matchInfo.matchEnded, matchInfo)}</div>`;
            });
            
            html += `</div>`;
        } else {
            html += '<div class="alert alert-info mt-4"><i class="fas fa-info-circle"></i> विस्तृत स्कोरकार्ड उपलब्ध नहीं है।</div>';
        }
        
        return html;
    }

    /* ===================================================
       OVERVIEW TAB - FIXED
=================================================== */

function generateOverviewTabCategoryHindi(matchInfo) {
    let html = '<div class="overview-container-hc mt-3">';

    const isLive = matchInfo.matchStarted && !matchInfo.matchEnded;
    const isCompleted = matchInfo.matchEnded;

    if (isCompleted && matchInfo.matchWinner) {
        html += generateMatchAnalysisCategoryHindi(matchInfo);
    }

    // IMPORTANT: Only iterate through teams that have actually batted
    // by checking if they have a corresponding score entry
    if (matchInfo.teamInfo && matchInfo.teamInfo.length > 0) {
        matchInfo.teamInfo.forEach((team, teamIndex) => {
            
            // Find if this team has a score
            const score = smartScoreMatch(matchInfo, team, teamIndex);
            
            // Skip if team hasn't batted
            if (!score || score.r === undefined) {
                console.log(`Skipping ${team.name} - no score`);
                return;
            }
            
            // Find corresponding inning in scorecard
            let inning = null;
            if (matchInfo.scorecard && matchInfo.scorecard.length > 0) {
                inning = matchInfo.scorecard.find(inn => {
                    if (!inn.inning) return false;
                    const inningLower = inn.inning.toLowerCase();
                    const teamNameLower = team.name.toLowerCase();
                    const teamShortLower = team.shortname.toLowerCase();
                    return inningLower.includes(teamNameLower) || 
                           inningLower.includes(teamShortLower);
                });
            }
            
            // Skip if no inning data or no batting data
            if (!inning || !inning.batting || inning.batting.length === 0) {
                console.log(`Skipping ${team.name} - no batting data`);
                return;
            }

            const teamName = team.name;
            let totalBattingRuns = 0;
            let extras = 0;
            let yetToBatPlayers = [];

            if (inning.batting) {
                inning.batting.forEach(bat => {
                    totalBattingRuns += bat.r || 0;
                });
                extras = (score.r || 0) - totalBattingRuns;

                const totalPlayers = 11;
                const battedCount = inning.batting.length;
                yetToBatPlayers = Array(totalPlayers - battedCount).fill('खिलाड़ी');
            }

            const runRate = score.o > 0 ? (score.r / score.o).toFixed(2) : '0.00';

            html += `
                <div class="innings-overview-hc mb-4">
                    <div class="innings-header-hc">
                        <h6 class="innings-title-hc">${teamName}</h6>
                        <div class="innings-score-hc">${score.r}/${score.w} (${parseFloat(score.o).toFixed(1)})</div>
                    </div>
                    
                    <div class="overview-stats-grid-hc">
                        <div class="stat-box-hc">
                            <div class="stat-label-hc">रन रेट</div>
                            <div class="stat-value-hc">${runRate}</div>
                        </div>
                        <div class="stat-box-hc">
                            <div class="stat-label-hc">एक्स्ट्रा</div>
                            <div class="stat-value-hc">${extras < 0 ? 0 : extras}</div>
                        </div>
                        <div class="stat-box-hc">
                            <div class="stat-label-hc">चौके</div>
                            <div class="stat-value-hc">${countBoundaries(inning.batting, '4s')}</div>
                        </div>
                        <div class="stat-box-hc">
                            <div class="stat-label-hc">छक्के</div>
                            <div class="stat-value-hc">${countBoundaries(inning.batting, '6s')}</div>
                        </div>
                    </div>
            `;

            html += generateMatchGraphsCategoryHindi(inning, score, isLive);

            if (inning.batting && inning.batting.length > 0) {
                const topBatsman = [...inning.batting].sort((a, b) => b.r - a.r)[0];
                const topBatsmanSR = topBatsman.sr.toFixed(1);
                html += `
                    <div class="top-performer-hc mt-3">
                        <div class="performer-label-hc">🏏 शीर्ष स्कोरर</div>
                        <div class="performer-details-hc">
                            <span class="performer-name-hc">${topBatsman.batsman.name}</span>
                            <span class="performer-score-hc">${topBatsman.r}(${topBatsman.b}) • एसआर: ${topBatsmanSR}</span>
                        </div>
                    </div>
                `;
            }

            if (inning.bowling && inning.bowling.length > 0) {
                const topBowler = [...inning.bowling].sort((a, b) => b.w - a.w)[0];
                if (topBowler.w > 0) {
                    html += `
                        <div class="top-performer-hc">
                            <div class="performer-label-hc">⚡ शीर्ष गेंदबाज</div>
                            <div class="performer-details-hc">
                                <span class="performer-name-hc">${topBowler.bowler.name}</span>
                                <span class="performer-score-hc">${topBowler.w}/${topBowler.r} (${topBowler.o} ओवर) • इकॉ: ${topBowler.eco.toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                }
            }

            if (yetToBatPlayers.length > 0) {
                const ytbLabel = isCompleted ? 'बल्लेबाजी नहीं की' : 'बल्लेबाजी बाकी';
                html += `
                    <div class="yet-to-bat-hc mt-3">
                        <div class="ytb-label-hc">${ytbLabel}: <span class="ytb-count-hc">${yetToBatPlayers.length} खिलाड़ी</span></div>
                    </div>
                `;
            }

            html += `</div>`;
        });
    }

    html += '</div>';
    return html;
}
    function generateMatchAnalysisCategoryHindi(matchInfo) {
        let html = '<div class="match-analysis-card-hc mb-4">';
        html += `<h6 class="analysis-title-hc">📊 मैच विश्लेषण</h6>
            <div class="analysis-section-hc">
                <div class="analysis-item-hc winner-analysis-hc">
                    <div class="analysis-icon-hc">🏆</div>
                    <div class="analysis-content-hc">
                        <div class="analysis-label-hc">मैच परिणाम</div>
                        <div class="analysis-value-hc">${matchInfo.status || ''}</div>
                    </div>
                </div>
            </div>`;

        if (matchInfo.scorecard && matchInfo.scorecard.length >= 2) {
            const t1n = getFullTeamName(matchInfo.scorecard[0].inning, matchInfo, 0);
            const t2n = getFullTeamName(matchInfo.scorecard[1].inning, matchInfo, 1);
            const t1 = matchInfo.teamInfo && matchInfo.teamInfo[0];
            const t2 = matchInfo.teamInfo && matchInfo.teamInfo[1];
            const t1s = t1 ? smartScoreMatch(matchInfo, t1, 0) : matchInfo.score && matchInfo.score[0];
            const t2s = t2 ? smartScoreMatch(matchInfo, t2, 1) : matchInfo.score && matchInfo.score[1];

            if (t1s && t2s) {
                html += `
                    <div class="comparison-grid-hc mt-3">
                        <div class="comparison-item-hc">
                            <div class="comp-label-hc">कुल रन</div>
                            <div class="comp-values-hc">
                                <span class="comp-team1-hc">${t1n}: ${t1s.r}</span>
                                <span class="comp-team2-hc">${t2n}: ${t2s.r}</span>
                            </div>
                        </div>
                        <div class="comparison-item-hc">
                            <div class="comp-label-hc">रन रेट</div>
                            <div class="comp-values-hc">
                                <span class="comp-team1-hc">${t1s.o > 0 ? (t1s.r / t1s.o).toFixed(2) : '0.00'}</span>
                                <span class="comp-team2-hc">${t2s.o > 0 ? (t2s.r / t2s.o).toFixed(2) : '0.00'}</span>
                            </div>
                        </div>
                    </div>`;
            }
        }
        html += '</div>';
        return html;
    }

    /* ===================================================
       INNING TAB
    =================================================== */

    function generateInningTabCategoryHindi(inning, isLive, matchInfo) {
        let html = '';
        let totalBattingRuns = 0;
        let extras = 0;
        let totalRuns = 0;
        let extrasBreakdown = { wides: 0, noBalls: 0, byes: 0, legByes: 0 };

        // Batting table
        html += `
            <div class="table-section-hc mt-4">
                <h6 class="table-section-title-hc">बल्लेबाजी</h6>
                <div class="table-responsive">
                    <table class="table scorecard-table-hc">
                        <thead><tr>
                            <th style="text-align:left;">बल्लेबाज</th>
                            <th>R</th><th>B</th><th>4s</th><th>6s</th><th>S/R</th>
                        </tr></thead>
                        <tbody>`;

        if (inning.batting && inning.batting.length > 0) {
            inning.batting.forEach(bat => {
                const isBatting = bat['dismissal-text'] === 'batting';
                const dismissal = !isBatting
                    ? `<div class="dismissal-text-hc">${bat['dismissal-text'] === 'batting' ? 'बल्लेबाजी' : capitalizeFirstLetter(bat['dismissal-text'])}</div>`
                    : '';
                
                let playerData = null;
                if (matchInfo && matchInfo.squadData) {
                    matchInfo.squadData.forEach(squad => {
                        if (squad.players && !playerData) {
                            const f = squad.players.find(p => p.id === bat.batsman.id || p.name === bat.batsman.name);
                            if (f) playerData = { 
                                id: f.id, 
                                name: f.name, 
                                role: f.role || 'Batsman', 
                                battingStyle: f.battingStyle || null, 
                                bowlingStyle: f.bowlingStyle || null, 
                                country: f.country || null, 
                                playerImg: f.playerImg || null 
                            };
                        }
                    });
                }
                
                const clickAttr = playerData 
                    ? `onclick='showPlayerInfoCategoryHindi(${JSON.stringify(playerData)})' style='cursor:pointer;'` 
                    : '';
                
                html += `
                    <tr class="${isBatting ? 'batting-now-hc' : ''} ${playerData ? 'player-clickable-hc' : ''}" ${clickAttr}>
                        <td style="text-align:left;">
                            <div class="batsman-cell-hc">
                                <div class="batsman-name-hc">${bat.batsman.name}</div>
                                ${dismissal}
                            </div>
                        </td>
                        <td><strong>${bat.r}</strong></td>
                        <td>${bat.b}</td>
                        <td>${bat['4s']}</td>
                        <td>${bat['6s']}</td>
                        <td>${bat.sr.toFixed(1)}</td>
                    </tr>`;
            });
        }
        html += `</tbody></table></div></div>`;

        if (inning.batting) inning.batting.forEach(bat => totalBattingRuns += bat.r || 0);

        if (inning.inning && matchInfo && matchInfo.score) {
            const hasCorrupt = matchInfo.score.some(s => s.inning && s.inning.includes(','));
            let inningScore = null;
            if (hasCorrupt) {
                const idx = matchInfo.scorecard ? matchInfo.scorecard.indexOf(inning) : -1;
                inningScore = idx >= 0 ? matchInfo.score[idx] : null;
            } else {
                inningScore = matchInfo.score.find(s => s.inning && s.inning.toLowerCase() === inning.inning.toLowerCase());
            }
            if (inningScore) {
                totalRuns = inningScore.r || 0;
                extras = Math.max(0, totalRuns - totalBattingRuns);
            }
        }

        if (inning.bowling) inning.bowling.forEach(bowl => {
            extrasBreakdown.wides += bowl.wd || 0;
            extrasBreakdown.noBalls += bowl.nb || 0;
        });
        
        if (totalRuns === 0) {
            extras = extrasBreakdown.wides + extrasBreakdown.noBalls;
            totalRuns = totalBattingRuns + extras;
        }

        // Extras card
        html += `
            <div class="extras-total-section-hc mt-3">
                <div class="extras-card-hc">
                    <h6 class="extras-title-hc">अतिरिक्त और कुल</h6>
                    <div class="extras-breakdown-hc">
                        ${extrasBreakdown.wides > 0 ? `<div class="extras-detail-hc"><span>वाइड: ${extrasBreakdown.wides}</span></div>` : ''}
                        ${extrasBreakdown.noBalls > 0 ? `<div class="extras-detail-hc"><span>नो बॉल: ${extrasBreakdown.noBalls}</span></div>` : ''}
                        ${extrasBreakdown.byes > 0 ? `<div class="extras-detail-hc"><span>बाई: ${extrasBreakdown.byes}</span></div>` : ''}
                        ${extrasBreakdown.legByes > 0 ? `<div class="extras-detail-hc"><span>लेग बाई: ${extrasBreakdown.legByes}</span></div>` : ''}
                        <div class="extras-row-hc total-row-hc mt-2">
                            <span class="info-label-hc"><strong>कुल रन:</strong></span>
                            <span class="info-value-hc"><strong>${totalRuns}</strong></span>
                        </div>
                    </div>
                </div>
            </div>`;

        // Fall of wickets
        if (inning.batting) {
            const fow = [];
            let cumRuns = 0;
            inning.batting.forEach(bat => {
                cumRuns += bat.r || 0;
                if (bat['dismissal-text'] !== 'batting' && bat['dismissal-text'] !== 'not out') {
                    fow.push({
                        wicket: fow.length + 1,
                        player: bat.batsman.name,
                        runs: bat.r || 0,
                        balls: bat.b || 0,
                        teamScore: cumRuns,
                        dismissal: bat['dismissal-text']
                    });
                }
            });
            
            if (fow.length > 0) {
                html += `<div class="fall-of-wickets-section-hc mt-3"><h6 class="fow-title-hc">📉 विकेट पतन</h6><div class="fow-container-hc">`;
                fow.forEach(f => {
                    html += `
                        <div class="fow-item-hc">
                            <div class="fow-wicket-hc">${f.wicket}</div>
                            <div class="fow-details-hc">
                                <div class="fow-player-hc">${f.player}</div>
                                <div class="fow-stats-hc">
                                    <span class="fow-player-score-hc">${f.runs}(${f.balls})</span>
                                    <span class="fow-team-score-hc">टीम: ${f.teamScore}</span>
                                </div>
                                <div class="fow-dismissal-hc">${capitalizeFirstLetter(f.dismissal)}</div>
                            </div>
                        </div>`;
                });
                html += `</div></div>`;
            }
        }

        // Yet to bat
        if (inning.batting) {
            const battedIds = new Set(inning.batting.map(b => b.batsman.id));
            const ytbPlayers = [];
            
            if (matchInfo && matchInfo.squadData && matchInfo.squadData.length > 0) {
                const rawTeam = inning.inning ? inning.inning.replace(/,.*/, '') : '';
                const inningTeam = rawTeam.split(' ')[0].toLowerCase();
                const squad = matchInfo.squadData.find(sq => {
                    const tn = (sq.teamName || '').toLowerCase();
                    const sn = (sq.shortname || '').toLowerCase();
                    return inningTeam.includes(tn) || inningTeam.includes(sn) || tn.includes(inningTeam) || sn.includes(inningTeam);
                });
                
                if (squad && squad.players) {
                    squad.players.forEach(p => {
                        if (!battedIds.has(p.id)) {
                            ytbPlayers.push({
                                id: p.id,
                                name: p.name,
                                role: p.role || 'Player',
                                battingStyle: p.battingStyle || null,
                                bowlingStyle: p.bowlingStyle || null,
                                country: p.country || null,
                                playerImg: p.playerImg || null
                            });
                        }
                    });
                }
            }
            
            const ytbCount = ytbPlayers.length > 0 ? ytbPlayers.length : Math.max(0, 11 - inning.batting.length);
            if (ytbCount > 0) {
                const ytbLabel = isLive ? 'बल्लेबाजी बाकी' : 'नहीं खेले';
                html += `<div class="yet-to-bat-section-hc mt-3"><div class="ytb-card-hc"><h6 class="ytb-title-hc">${ytbLabel}</h6>`;
                
                if (ytbPlayers.length > 0) {
                    html += `<div class="ytb-players-grid-hc">`;
                    ytbPlayers.forEach(p => {
                        html += `
                            <div class="ytb-player-card-hc" onclick='showPlayerInfoCategoryHindi(${JSON.stringify(p)})'>
                                <div class="ytb-player-name-hc">${p.name}</div>
                                <div class="ytb-player-role-hc">${getRoleHindi(p.role)}</div>
                                ${p.battingStyle ? `<div class="ytb-player-style-hc">⚾ ${p.battingStyle}</div>` : ''}
                                ${p.bowlingStyle ? `<div class="ytb-player-style-hc">🎯 ${p.bowlingStyle}</div>` : ''}
                            </div>`;
                    });
                    html += `</div>`;
                } else {
                    html += `<div class="ytb-content-hc"><div class="ytb-count-badge-hc"><i class="fas fa-users"></i><span>${ytbCount} खिलाड़ी ${isLive ? 'बल्लेबाजी बाकी' : 'नहीं खेले'}</span></div></div>`;
                }
                html += `</div></div>`;
            }
        }

        // Bowling table
        html += `
            <div class="table-section-hc mt-4">
                <h6 class="table-section-title-hc">गेंदबाजी</h6>
                <div class="table-responsive">
                    <table class="table scorecard-table-hc">
                        <thead><tr>
                            <th style="text-align:left;">गेंदबाज</th>
                            <th>O</th><th>M</th><th>R</th><th>W</th><th>ECO</th>
                        </tr></thead>
                        <tbody>`;

        if (inning.bowling && inning.bowling.length > 0) {
            inning.bowling.forEach(bowl => {
                let playerData = null;
                if (matchInfo && matchInfo.squadData) {
                    matchInfo.squadData.forEach(squad => {
                        if (squad.players && !playerData) {
                            const f = squad.players.find(p => p.id === bowl.bowler.id || p.name === bowl.bowler.name);
                            if (f) playerData = {
                                id: f.id,
                                name: f.name,
                                role: f.role || 'Bowler',
                                battingStyle: f.battingStyle || null,
                                bowlingStyle: f.bowlingStyle || null,
                                country: f.country || null,
                                playerImg: f.playerImg || null
                            };
                        }
                    });
                }
                
                const clickAttr = playerData
                    ? `onclick='showPlayerInfoCategoryHindi(${JSON.stringify(playerData)})' style='cursor:pointer;'`
                    : '';
                
                html += `
                    <tr class="${playerData ? 'player-clickable-hc' : ''}" ${clickAttr}>
                        <td style="text-align:left;"><div class="bowler-name-hc">${bowl.bowler.name}</div></td>
                        <td>${bowl.o}</td>
                        <td>${bowl.m}</td>
                        <td>${bowl.r}</td>
                        <td><strong>${bowl.w}</strong></td>
                        <td>${bowl.eco.toFixed(2)}</td>
                    </tr>`;
            });
        }
        html += `</tbody></table></div></div>`;
        
        return html;
    }

    /* ===================================================
       GRAPHS
    =================================================== */

function generateMatchGraphsCategoryHindi(inning, score, isLive) {
    let html = '<div class="match-graphs-hc mt-4">';
    
    // IMPORTANT: Create unique ID for each graph
    const uid = `${score.inning ? score.inning.replace(/\s+/g, '-') : 'team'}-${Math.random().toString(36).substr(2, 5)}`;
    
    html += `
        <div class="graph-section-hc mb-4">
            <h6 class="graph-title-hc">${isLive ? '<span class="live-dot-small-hc"></span>' : ''} ओवर दर ओवर रन रेट</h6>
            <div class="worm-graph-hc">
                <canvas id="hc-rrg-${uid}" class="hc-run-rate-canvas" height="150"></canvas>
            </div>
        </div>`;
    
    if (inning.batting && inning.batting.length > 0) {
        html += `
            <div class="graph-section-hc">
                <h6 class="graph-title-hc">${isLive ? 'हालिया गेंदें (पिछले 2 ओवर)' : 'पिछले 2 ओवर'}</h6>
                <div class="ball-by-ball-hc">${generateBallByBallCategoryHindi(inning)}</div>
            </div>`;
    }
    html += '</div>';

    setTimeout(() => {
        const canvas = document.getElementById(`hc-rrg-${uid}`);
        if (canvas && !canvas.dataset.init) {
            initRunRateGraphCategoryHindi(canvas, score, inning);
            canvas.dataset.init = '1';
        }
    }, 150);

    return html;
}

function generateBallByBallCategoryHindi(inning) {
    let balls = [];
    
    // Try to get actual ball-by-ball data if available
    if (inning.batting && inning.batting.length > 0) {
        // Get last 2 overs worth of balls (12 balls)
        // This is simulated based on actual batsman scores
        const recentBatsmen = inning.batting.slice(-2); // Last 2 batsmen
        
        recentBatsmen.forEach(bat => {
            const runs = bat.r || 0;
            const ballsFaced = bat.b || 0;
            const fours = bat['4s'] || 0;
            const sixes = bat['6s'] || 0;
            
            // Add sixes
            for (let i = 0; i < sixes; i++) {
                balls.push(6);
            }
            
            // Add fours
            for (let i = 0; i < fours; i++) {
                balls.push(4);
            }
            
            // Calculate remaining runs from 1s, 2s, 3s
            const boundaryRuns = (fours * 4) + (sixes * 6);
            const otherRuns = runs - boundaryRuns;
            
            // Distribute other runs
            for (let i = 0; i < otherRuns; i++) {
                const rand = Math.random();
                if (rand < 0.5) balls.push(1);
                else if (rand < 0.8) balls.push(2);
                else balls.push(3);
            }
            
            // Fill remaining balls with dots
            const totalBalls = balls.length;
            if (totalBalls < ballsFaced) {
                for (let i = 0; i < (ballsFaced - totalBalls); i++) {
                    balls.push(0);
                }
            }
        });
    }
    
    // If we have more than 12 balls, take last 12
    if (balls.length > 12) {
        balls = balls.slice(-12);
    }
    
    // If we have less than 12, simulate remaining
    while (balls.length < 12) {
        const r = Math.random();
        if (r < 0.45) balls.push(0);
        else if (r < 0.65) balls.push(1);
        else if (r < 0.80) balls.push(2);
        else if (r < 0.90) balls.push(4);
        else balls.push(6);
    }
    
    // Render balls
    let html = '<div class="balls-container-hc">';
    balls.forEach(b => {
        let cls = 'ball-dot-hc';
        let txt = '•';
        if (b === 'W') {
            cls = 'ball-wicket-hc';
            txt = 'W';
        } else if (b === 6) {
            cls = 'ball-six-hc';
            txt = '6';
        } else if (b === 4) {
            cls = 'ball-four-hc';
            txt = '4';
        } else if (b > 0) {
            cls = 'ball-run-hc';
            txt = b;
        }
        html += `<div class="ball-hc ${cls}">${txt}</div>`;
    });
    return html + '</div>';
}

    function simulateBallsCategoryHindi(score) {
        const balls = [];
        for (let i = 0; i < 12; i++) {
            const r = Math.random();
            if (r < 0.10) balls.push('W');
            else if (r < 0.15) balls.push(6);
            else if (r < 0.25) balls.push(4);
            else if (r < 0.45) balls.push(0);
            else if (r < 0.65) balls.push(1);
            else if (r < 0.80) balls.push(2);
            else balls.push(3);
        }
        return balls;
    }

function initRunRateGraphCategoryHindi(canvas, score, inning) {
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width = canvas.offsetWidth || 300;
    const height = canvas.height = 150;
    const totalOvers = Math.ceil(score.o || 0);
    const totalRuns = score.r || 0;
    
    // Build actual over-by-over data from batting
    const data = [];
    let cumulativeRuns = 0;
    
    if (inning && inning.batting && inning.batting.length > 0) {
        // Create approximate over progression based on batsmen
        inning.batting.forEach((bat, idx) => {
            const batsmanRuns = bat.r || 0;
            const batsmanBalls = bat.b || 0;
            const oversForBatsman = batsmanBalls / 6;
            
            // Add data points for this batsman's contribution
            if (oversForBatsman > 0) {
                const startOver = data.length > 0 ? data[data.length - 1].over : 0;
                const endOver = Math.min(startOver + oversForBatsman, totalOvers);
                const startRuns = cumulativeRuns;
                cumulativeRuns += batsmanRuns;
                
                // Add intermediate point
                if (endOver - startOver > 1) {
                    const midOver = startOver + (endOver - startOver) / 2;
                    const midRuns = startRuns + (batsmanRuns / 2);
                    data.push({ over: midOver, runs: midRuns });
                }
                
                data.push({ over: endOver, runs: cumulativeRuns });
            }
        });
    }
    
    // If no data or incomplete, create simulated progression
    if (data.length === 0) {
        const avgRunRate = totalRuns / (totalOvers || 1);
        for (let i = 1; i <= totalOvers; i++) {
            const variance = (Math.random() - 0.5) * 15;
            const runs = Math.max(0, Math.min(avgRunRate * i + variance, totalRuns));
            data.push({ over: i, runs: runs });
        }
    }
    
    // Ensure last point is exact total
    if (data.length > 0) {
        data[data.length - 1].runs = totalRuns;
        data[data.length - 1].over = totalOvers;
    }

    // Draw graph (same as before)
    ctx.clearRect(0, 0, width, height);
    const pad = 35;
    const gw = width - 2 * pad;
    const gh = height - 2 * pad;
    const maxR = Math.max(...data.map(d => d.runs), totalRuns || 1);
    const xS = gw / (totalOvers || 1);
    const yS = maxR > 0 ? gh / maxR : 1;

    // Grid lines
    ctx.strokeStyle = '#f0f0f0';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = pad + (gh / 5) * i;
        ctx.beginPath();
        ctx.moveTo(pad, y);
        ctx.lineTo(width - pad, y);
        ctx.stroke();
    }

    // Axes
    ctx.strokeStyle = '#5f6368';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(pad, pad);
    ctx.lineTo(pad, height - pad);
    ctx.lineTo(width - pad, height - pad);
    ctx.stroke();

    // Gradient fill
    const grad = ctx.createLinearGradient(0, pad, 0, height - pad);
    grad.addColorStop(0, 'rgba(26,115,232,0.3)');
    grad.addColorStop(1, 'rgba(26,115,232,0.05)');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.moveTo(pad, height - pad);
    data.forEach(p => ctx.lineTo(pad + p.over * xS, height - pad - p.runs * yS));
    ctx.lineTo(width - pad, height - pad);
    ctx.closePath();
    ctx.fill();

    // Line
    ctx.strokeStyle = '#1a73e8';
    ctx.lineWidth = 3;
    ctx.beginPath();
    data.forEach((p, i) => {
        const x = pad + p.over * xS;
        const y = height - pad - p.runs * yS;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    ctx.stroke();

    // Points
    data.forEach((p, i) => {
        const x = pad + p.over * xS;
        const y = height - pad - p.runs * yS;
        ctx.fillStyle = '#1a73e8';
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
        if (i === data.length - 1) {
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, 2 * Math.PI);
            ctx.stroke();
        }
    });

    // Labels
    ctx.fillStyle = '#5f6368';
    ctx.font = '11px Arial';
    ctx.textAlign = 'center';
    for (let i = 0; i <= totalOvers; i += Math.ceil(totalOvers / 5)) {
        ctx.fillText(i, pad + i * xS, height - pad + 15);
    }
    
    ctx.textAlign = 'right';
    for (let i = 0; i <= 5; i++) {
        ctx.fillText(Math.round((maxR / 5) * (5 - i)), pad - 8, pad + (gh / 5) * i + 4);
    }
    
    ctx.fillStyle = '#202124';
    ctx.font = 'bold 12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('ओवर', width / 2, height - 5);
    ctx.save();
    ctx.translate(12, height / 2);
    ctx.rotate(-Math.PI / 2);
    ctx.fillText('रन', 0, 0);
    ctx.restore();
}
    /* ===================================================
       PLAYER INFO MODAL
    =================================================== */

    window.showPlayerInfoCategoryHindi = function(player) {
        const modal = new bootstrap.Modal(document.getElementById('playerInfoModalCategoryHindi'));
        const modalBody = document.getElementById('playerInfoModalCategoryHindiBody');
        const modalTitle = document.getElementById('playerInfoModalCategoryHindiTitle');
        
        if (modalTitle) modalTitle.textContent = player.name;

        modalBody.innerHTML = `
            <div class="player-info-container-hc">
                ${player.playerImg && player.playerImg !== 'https://h.cricapi.com/img/icon512.png' 
                    ? `<div class="player-image-section-hc">
                        <img src="${player.playerImg}" alt="${player.name}" class="player-modal-img-hc">
                    </div>` 
                    : ''}
                <div class="player-details-grid-hc">
                    <div class="player-detail-item-hc">
                        <div class="detail-label-hc">🏏 भूमिका</div>
                        <div class="detail-value-hc">${getRoleHindi(player.role) || 'N/A'}</div>
                    </div>
                    ${player.battingStyle 
                        ? `<div class="player-detail-item-hc">
                            <div class="detail-label-hc">⚾ बल्लेबाजी शैली</div>
                            <div class="detail-value-hc">${player.battingStyle}</div>
                        </div>` 
                        : ''}
                    ${player.bowlingStyle 
                        ? `<div class="player-detail-item-hc">
                            <div class="detail-label-hc">🎯 गेंदबाजी शैली</div>
                            <div class="detail-value-hc">${player.bowlingStyle}</div>
                        </div>` 
                        : ''}
                    <div class="player-detail-item-hc">
                        <div class="detail-label-hc">🌍 देश</div>
                        <div class="detail-value-hc">${player.country || 'N/A'}</div>
                    </div>
                </div>
                ${player.role 
                    ? `<div class="player-role-badge-large-hc">
                        <span class="role-icon-hc">${getRoleIcon(player.role)}</span>
                        <span class="role-text-hc">${getRoleHindi(player.role)}</span>
                    </div>` 
                    : ''}
            </div>`;
        modal.show();
    };

    /* ===================================================
       INITIALIZE
    =================================================== */

    loadCricketMatchesCategoryHindi();
    setInterval(loadCricketMatchesCategoryHindi, 600000);
});</script>

<?php endif; ?>

<!-- ==== DYNAMIC CATEGORY CONTENT ==== -->
<?= $this->include('components/categorycontent') ?>

<!-- ==== Footer Section ==== -->

<?php include(APPPATH . 'Views/components/footer.php'); ?>