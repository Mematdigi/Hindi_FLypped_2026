<?php include(APPPATH . 'Views/components/header.php'); ?>

<!-- Include Authors List CSS if you have one -->
<link rel="stylesheet" href="<?= base_url('public/assets/css/authors.css') ?>">

<!-- ===== Authors List Section ===== -->
<section class="authors-section py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="page-title">Meet Our Authors</h1>
                <p class="page-subtitle">Discover the talented writers behind our content</p>
            </div>
        </div>

        <!-- Authors Grid -->
        <?php if (!empty($authors)): ?>
            <div class="row">
                <?php foreach ($authors as $author): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="author-card h-100">
                            <div class="author-card-body">
                                <!-- Author Avatar -->
                                

                                <!-- Author Info -->
                                <div class="author-info text-center">
                                    <h4 class="author-name">
                                        <a href="<?= base_url('author/' . rawurlencode($author->user_login)) ?>">
                                            <?= esc($author->display_name) ?>
                                        </a>
                                    </h4>
                                    <p class="author-role"><?= esc($author->role_display ?? 'Author') ?></p>
                                    <p class="author-email">
                                        <i class="far fa-envelope"></i>
                                        <a href="mailto:<?= esc($author->user_email) ?>"><?= esc($author->user_email) ?></a>
                                    </p>
                                </div>

                                <!-- Author Stats -->
                                <div class="author-stats d-flex justify-content-center mt-3">
                                    <div class="stat-item text-center mx-3">
                                        <span class="stat-number"><?= number_format($author->post_count) ?></span>
                                        <span class="stat-label d-block">Posts</span>
                                    </div>
                                    <div class="stat-item text-center mx-3">
                                        <span class="stat-number"><?= number_format(rand(1000, 50000)) ?></span>
                                        <span class="stat-label d-block">Readers</span>
                                    </div>
                                </div>

                                <!-- View Profile Button -->
                                <div class="text-center mt-4">
                                    <a href="<?= base_url('author/' . rawurlencode($author->user_login)) ?>" 
                                       class="btn btn-primary btn-sm">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- No Authors Found -->
            <div class="row">
                <div class="col-12 text-center">
                    <div class="no-authors-message">
                        <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                        <h3>No Authors Found</h3>
                        <p>There are currently no authors with published posts.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Basic styling for authors list - you can move this to a separate CSS file */
.authors-section {
    background-color: #f8f9fa;
    min-height: 80vh;
}

.page-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
}

.author-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.author-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.author-card-body {
    padding: 2rem 1.5rem;
}

.author-avatar {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border: 3px solid #e9ecef;
}

.author-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.author-name a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.author-name a:hover {
    color: #007bff;
}

.author-role {
    color: #007bff;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.author-email {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.author-email a {
    color: inherit;
    text-decoration: none;
}

.author-stats {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
    margin-top: 1rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    display: block;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.no-authors-message {
    padding: 4rem 2rem;
    color: #6c757d;
}
</style>

<?php include(APPPATH . 'Views/components/footer.php'); ?>