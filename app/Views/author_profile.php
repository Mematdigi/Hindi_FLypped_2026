<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>

<!-- Include Font Awesome if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- ===== Author Profile Section ===== -->
<section class="author-profile-section py-5">
    <div class="container">
        <!-- Author Header Card -->
        <div class="author-header-card">
            <div class="row align-items-center">
                
                <!-- Author Info -->
                <div class="col-lg-8 col-md-12">
                    <div class="author-info">
                        <h1 class="author-name"><?= esc(isset($author->display_name) ? $author->display_name : 'Unknown Author') ?></h1>
                        <p class="author-title"><?= esc(isset($role) ? $role : 'Author') ?> & Content Creator</p>
                        
                        <?php if (isset($meta_data['mobile']) && !empty($meta_data['mobile'])): ?>
                        <p class="author-location">
                            <i class="fas fa-phone"></i>
                            <?= esc($meta_data['mobile']) ?>
                        </p>
                        <?php endif; ?>

                        <!-- Author Stats -->
                        <!--<div class="author-stats">-->
                        <!--    <div class="stat-item">-->
                        <!--        <span class="stat-number"><?= isset($stats['followers']) ? number_format($stats['followers']) : '0' ?></span>-->
                        <!--        <span class="stat-label">Followers</span>-->
                        <!--    </div>-->
                        <!--    <div class="stat-item">-->
                        <!--        <span class="stat-number"><?= isset($total_posts) ? $total_posts : '0' ?></span>-->
                        <!--        <span class="stat-label">Posts</span>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                </div>

                <!-- Follow Button & Social Links -->
                <div class="col-lg-4 col-md-12 text-start mt-4 mt-lg-0">
                    <button class="btn btn-follow" id="followBtn">
                        <i class="fas fa-plus me-2"></i>Follow
                    </button>
                    
                    <!-- Social Links -->
                    <div class="social-links mt-3">
                        <a href="https://www.facebook.com/flyppedhindi/" class="social-link" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/hindiflypped/" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@flyppedhindinews" class="social-link" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="row mt-5">
            <!-- Left Column - About & Skills -->
            <div class="col-lg-4">
                <!-- About Section -->
                <div class="profile-card mb-4">
                    <h3 class="card-title">About</h3>
                    <div class="about-content">
                        <?php if (isset($meta_data['description']) && !empty($meta_data['description'])): ?>
                            <p><?= nl2br(esc($meta_data['description'])) ?></p>
                        <?php else: ?>
                            <p>Passionate content creator and digital marketing strategist with over 8 years of experience helping brands tell their stories. 
                            I specialize in creating engaging content that drives results and builds meaningful connections with audiences.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Total Posts Written -->
                <div class="profile-card">
                    <div class="posts-written-card">
                        <div class="posts-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="posts-info">
                            <div class="posts-number"><?= isset($total_posts) ? $total_posts : '0' ?></div>
                            <div class="posts-label">Total Posts Written</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Recent Posts -->
            <div class="col-lg-8">
                <div class="profile-card">
                    <h3 class="card-title">Recent Posts</h3>
                    
                    <?php if (isset($posts) && !empty($posts)): ?>
                        <div class="recent-posts">
                            <?php foreach ($posts as $post): ?>
                                <div class="post-item">
                                    <div class="post-thumbnail">
                                        <a href="<?= isset($post['blog_detail_url']) ? $post['blog_detail_url'] : '#' ?>">
                                            <img src="<?= isset($post['thumbnail_url']) ? $post['thumbnail_url'] : base_url('public/assets/images/default-thumbnail.jpg') ?>" 
                                                 alt="<?= esc(isset($post['post_title']) ? $post['post_title'] : 'Blog Post') ?>">
                                        </a>
                                    </div>
                                    <div class="post-content">
                                        <h4 class="post-title">
                                            <a href="<?= isset($post['blog_detail_url']) ? $post['blog_detail_url'] : '#' ?>">
                                                <?= esc(isset($post['post_title']) ? $post['post_title'] : 'Untitled Post') ?>
                                            </a>
                                        </h4>
                                        <div class="post-meta">
                                            <span class="post-date">
                                                <i class="far fa-calendar"></i>
                                                <?php 
                                                $postDate = isset($post['post_date']) ? $post['post_date'] : 'now';
                                                echo date('M d, Y', strtotime($postDate));
                                                ?>
                                            </span>
                                            <?php if (isset($post['categories']) && !empty($post['categories'])): ?>
                                                <span class="post-category">
                                                    <i class="fas fa-tag"></i>
                                                    <?= esc($post['categories']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (isset($post['post_excerpt']) && !empty($post['post_excerpt'])): ?>
                                            <p class="post-excerpt"><?= esc(substr($post['post_excerpt'], 0, 120)) ?>...</p>
                                        <?php endif; ?>
                                        
                                        <!-- Read More Button -->
                                        <div class="post-actions mt-3">
                                             <a href="<?= base_url($post['post_name']); ?>" class="readMoreBtn"> Read More </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Simple Pagination -->
                        <?php if (isset($total_pages) && $total_pages > 1): ?>
                            <div class="pagination-wrapper mt-4">
                                <nav aria-label="Posts pagination">
                                    <ul class="pagination justify-content-start">
                                        <!-- Previous Button -->
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= base_url('author/' . urlencode($author->user_login) . '?page=' . ($current_page - 1)) ?>">
                                                    Previous
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Page Numbers -->
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <?php if ($i <= 10): // Show maximum 10 pages ?>
                                                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="<?= base_url('author/' . urlencode($author->user_login) . '?page=' . $i) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        
                                        <!-- Next Button -->
                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= base_url('author/' . urlencode($author->user_login) . '?page=' . ($current_page + 1)) ?>">
                                                    Next
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                
                                <!-- Debug Info - Remove this after testing -->
                                <div class="text-start mt-2">
                                    <small class="text-muted">
                                        Page <?= $current_page ?> of <?= $total_pages ?> 
                                        (Total posts: <?= $total_posts ?>, Posts per page: <?= $per_page ?>)
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="no-posts">
                            <div class="no-posts-icon">
                                <i class="fas fa-pen-alt"></i>
                            </div>
                            <h4>No Posts Yet</h4>
                            <p>This author hasn't published any posts yet. Check back later for amazing content!</p>
                            <a href="<?= base_url('authors') ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-users me-2"></i>Browse Other Authors
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Follow Button JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followBtn = document.getElementById('followBtn');
    if (followBtn) {
        let isFollowing = false;

        followBtn.addEventListener('click', function() {
            if (!isFollowing) {
                this.innerHTML = '<i class="fas fa-check me-2"></i>Following';
                this.classList.add('following');
                isFollowing = true;
            } else {
                this.innerHTML = '<i class="fas fa-plus me-2"></i>Follow';
                this.classList.remove('following');
                isFollowing = false;
            }
        });
    }
});
</script>

<style>
/* ===== Author Profile CSS ===== */
.author-profile-section {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  padding: 3rem 0;
}
.author-profile-section .container {
  max-width: 1200px;
}

/* ===== Author Header Card ===== */
.author-header-card {
  background: #ffffff;
  border-radius: 1.25rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
  padding: 2rem;
  margin-bottom: 2rem;
  border: 1px solid #f0f0f0;
  position: relative;
  text-align: left;
}
.author-header-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #007bff, #17a2b8, #28a745);
  border-top-left-radius: 1.25rem;
  border-top-right-radius: 1.25rem;
}
.author-header-card .author-info .author-name {
  font-size: 1.6rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  color: #343a40;
}
.author-header-card .author-info .author-title {
  font-size: 1rem;
  font-weight: 600;
  color: #007bff;
  margin-bottom: 0.8rem;
}
.author-header-card .author-info .author-location {
  font-size: 0.95rem;
  color: #6c757d;
  margin-bottom: 0.4rem;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}
.author-header-card .author-info .author-location i {
  margin-right: 6px;
  color: #007bff;
}
.author-header-card .author-info .author-location a {
  color: #6c757d;
  text-decoration: none;
  font-weight: 500;
}
.author-header-card .author-info .author-location a:hover {
  color: #007bff;
}
.author-header-card .author-info .author-stats {
  display: flex;
  justify-content: flex-start;
  gap: 2.5rem;
  margin-top: 1rem;
}
.author-header-card .author-info .author-stats .stat-item {
  text-align: left;
}
.author-header-card .author-info .author-stats .stat-item .stat-number {
  font-size: 1.3rem;
  font-weight: 700;
  color: #007bff;
  display: block;
}
.author-header-card .author-info .author-stats .stat-item .stat-label {
  font-size: 0.8rem;
  text-transform: uppercase;
  color: #6c757d;
  letter-spacing: 0.5px;
}
.author-header-card .btn-follow {
  background: #007bff;
  color: #ffffff;
  border-radius: 8px;
  padding: 0.5rem 1.2rem;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  display: inline-block;
}
.author-header-card .btn-follow:hover {
  background: #0069d9;
  color: #ffff;
}
.author-header-card .btn-follow.following {
  background: #28a745;
}
.author-header-card .social-links {
  display: flex;
  justify-content: flex-start;
  margin-top: 1rem;
  gap: 0.8rem;
}
.author-header-card .social-links .social-link {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  color: #6c757d;
  transition: all 0.3s ease;
}
.author-header-card .social-links .social-link:hover {
  background: #007bff;
  color: #ffffff;
}

/* ===== Author Info ===== */
.author-info {
  text-align: left;
}
.author-info .author-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: #343a40;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}
@media (max-width: 768px) {
  .author-info .author-name {
    font-size: 2rem;
  }
}
@media (max-width: 576px) {
  .author-info .author-name {
    font-size: 1.75rem;
  }
}
.author-info .author-title {
  font-size: 1.25rem;
  color: #007bff;
  font-weight: 500;
  margin-bottom: 1rem;
}
@media (max-width: 768px) {
  .author-info .author-title {
    font-size: 1.1rem;
  }
}
.author-info .author-location {
  font-size: 1rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 0.5rem;
}
.author-info .author-location i {
  width: 20px;
  text-align: left;
  color: #17a2b8;
}
.author-info .author-location a {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s ease;
}
.author-info .author-location a:hover {
  color: #007bff;
}

/* ===== Author Stats ===== */
.author-stats {
  display: flex;
  justify-content: flex-start;
  gap: 2rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(204, 38, 38, 0.1);
}
@media (max-width: 768px) {
  .author-stats {
    gap: 3rem;
  }
}
@media (max-width: 576px) {
  .author-stats {
    gap: 1.5rem;
  }
}
.author-stats .stat-item {
  text-align: left;
}
.author-stats .stat-item .stat-number {
  display: block;
  font-size: 1.75rem;
  font-weight: 700;
  color: #007bff;
  line-height: 1.2;
}
@media (max-width: 576px) {
  .author-stats .stat-item .stat-number {
    font-size: 1.5rem;
  }
}
.author-stats .stat-item .stat-label {
  font-size: 0.9rem;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
}

/* ===== Follow Button ===== */
.btn-follow {
  background: linear-gradient(135deg, #007bff, #17a2b8);
  color: #ffffff;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 1rem;
  font-weight: 500;
  font-size: 1rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: inline-block;
}
.btn-follow::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}
.btn-follow:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.btn-follow:hover::before {
  left: 100%;
}
.btn-follow.following {
  background: linear-gradient(135deg, #28a745, #20c997);
}
@media (max-width: 768px) {
  .btn-follow {
    width: 100%;
    margin-bottom: 1rem;
    text-align: left;
  }
}

/* ===== Social Links ===== */
.social-links {
  display: flex;
  gap: 1rem;
  justify-content: flex-start;
}
.social-links .social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 45px;
  height: 45px;
  background: #f8f9fa;
  border-radius: 50%;
  color: #6c757d;
  text-decoration: none;
  transition: all 0.3s ease;
}
.social-links .social-link:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.social-links .social-link:hover[title="Twitter"] {
  background: #1da1f2;
  color: #ffffff;
}
.social-links .social-link:hover[title="LinkedIn"] {
  background: #0077b5;
  color: #ffffff;
}
.social-links .social-link:hover[title="Facebook"] {
  background: #1877f2;
  color: #ffffff;
}
.social-links .social-link:hover[title="Instagram"] {
  background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
  color: #ffffff;
}
.social-links .social-link i {
  font-size: 1.1rem;
}

/* ===== Profile Cards ===== */
.profile-card {
  background: #ffffff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  text-align: left;
}
.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}
@media (max-width: 768px) {
  .profile-card {
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }
}
.profile-card .card-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #343a40;
  margin-bottom: 1.5rem;
  position: relative;
  padding-bottom: 0.5rem;
}
.profile-card .card-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(90deg, #007bff, #17a2b8);
  border-radius: 2px;
}
@media (max-width: 768px) {
  .profile-card .card-title {
    font-size: 1.3rem;
  }
}

/* ===== Skills Grid ===== */
.skills-grid {
  text-align: left;
}
.skills-grid .skill-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
.skills-grid .skill-item:last-child {
  border-bottom: none;
}
.skills-grid .skill-item .skill-name {
  font-weight: 500;
  color: #343a40;
  font-size: 1rem;
}
.skills-grid .skill-item .skill-rating {
  display: flex;
  gap: 0.25rem;
}
.skills-grid .skill-item .skill-rating i {
  font-size: 1rem;
}
.skills-grid .skill-item .skill-rating i.fas {
  color: #ffc107;
}
.skills-grid .skill-item .skill-rating i.far {
  color: #e9ecef;
}

/* ===== About Content ===== */
.about-content {
  text-align: left;
}
.about-content p {
  color: #6c757d;
  line-height: 1.6;
  font-size: 1rem;
  margin-bottom: 0;
}

/* ===== Posts Written Card ===== */
.posts-written-card {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 1.5rem;
  padding: 1rem;
  background: linear-gradient(135deg, #007bff, #17a2b8);
  border-radius: 0.5rem;
  color: #ffffff;
  text-align: left;
}
.posts-written-card .posts-icon {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.posts-written-card .posts-icon i {
  font-size: 1.5rem;
}
.posts-written-card .posts-info .posts-number {
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 0.25rem;
}
@media (max-width: 576px) {
  .posts-written-card .posts-info .posts-number {
    font-size: 1.75rem;
  }
}
.posts-written-card .posts-info .posts-label {
  font-size: 0.9rem;
  opacity: 0.9;
  font-weight: 500;
}

/* ===== Recent Posts ===== */
.recent-posts {
  text-align: left;
}
.recent-posts .post-item {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  transition: background-color 0.3s ease;
  border-radius: 0.5rem;
}
.recent-posts .post-item:hover {
  background: rgba(0, 123, 255, 0.02);
  padding-left: 1rem;
  padding-right: 1rem;
}
.recent-posts .post-item:last-child {
  border-bottom: none;
}
@media (max-width: 768px) {
  .recent-posts .post-item {
    flex-direction: column;
    gap: 1rem;
  }
}
.recent-posts .post-item .post-thumbnail {
  flex-shrink: 0;
  width: 120px;
  height: 90px;
  border-radius: 0.5rem;
  overflow: hidden;
}
@media (max-width: 768px) {
  .recent-posts .post-item .post-thumbnail {
    width: 100%;
    height: 200px;
  }
}
.recent-posts .post-item .post-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.recent-posts .post-item .post-thumbnail img:hover {
  transform: scale(1.05);
}
.recent-posts .post-item .post-content .post-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  line-height: 1.3;
}
@media (max-width: 768px) {
  .recent-posts .post-item .post-content .post-title {
    font-size: 1.1rem;
  }
}
.recent-posts .post-item .post-content .post-title a {
  color: #343a40;
  text-decoration: none;
  transition: color 0.3s ease;
}
.recent-posts .post-item .post-content .post-title a:hover {
  color: #007bff;
}
.recent-posts .post-item .post-content .post-meta {
  display: flex;
  justify-content: flex-start;
  gap: 1.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: #6c757d;
}
@media (max-width: 576px) {
  .recent-posts .post-item .post-content .post-meta {
    flex-direction: column;
    gap: 0.5rem;
  }
}
.recent-posts .post-item .post-content .post-meta span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.recent-posts .post-item .post-content .post-meta span i {
  color: #17a2b8;
}
.recent-posts .post-item .post-content .post-excerpt {
  color: #6c757d;
  line-height: 1.5;
  font-size: 0.95rem;
  margin-bottom: 0;
}

/* ===== No Posts Message ===== */
.no-posts {
  text-align: left;
  padding: 3rem 2rem;
  color: #6c757d;
}
.no-posts i {
  font-size: 3rem;
  color: #e9ecef;
  margin-bottom: 1rem;
}
.no-posts h4 {
  font-weight: 700;
  color: #343a40;
  margin-bottom: 0.5rem;
}
.no-posts p {
  font-size: 1rem;
  margin-bottom: 0;
}

/* ===== Pagination ===== */
.pagination-wrapper {
  display: flex;
  justify-content: flex-start;
}
.pagination-wrapper .pagination .page-item {
  margin: 0 0.25rem;
}
.pagination-wrapper .pagination .page-item .page-link {
  border: 2px solid transparent;
  background: transparent;
  color: #6c757d;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
}
.pagination-wrapper .pagination .page-item .page-link:hover {
  background: #007bff;
  color: #ffffff;
  border-color: #007bff;
  transform: translateY(-2px);
}
.pagination-wrapper .pagination .page-item.active .page-link {
  background: #007bff;
  color: #ffffff;
  border-color: #007bff;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* ===== Responsive Utilities ===== */
@media (max-width: 992px) {
  .author-profile-section {
    padding: 2rem 0;
  }
  .author-header-card .row > div {
    margin-bottom: 1.5rem;
  }
  .author-header-card .row > div:last-child {
    margin-bottom: 0;
  }
}
@media (max-width: 768px) {
  .author-info {
    text-align: left;
    margin-bottom: 2rem;
  }
  .social-links {
    margin-top: 1rem !important;
  }
}
@media (max-width: 576px) {
  .author-profile-section {
    padding: 1.5rem 0;
  }
  .profile-card {
    padding: 1.25rem;
  }
  .author-stats {
    gap: 1rem;
  }
}

/* ===== Animation Classes ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-in-up {
  animation: fadeInUp 0.6s ease forwards;
}

/* ===== Loading States ===== */
.loading {
  position: relative;
  pointer-events: none;
}
.loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 30px;
  height: 30px;
  margin: -15px 0 0 -15px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* ===== Print Styles ===== */
@media print {
  .author-profile-section {
    background: none !important;
    padding: 0 !important;
  }
  .btn-follow,
  .social-links,
  .pagination-wrapper {
    display: none !important;
  }
  .profile-card {
    box-shadow: none !important;
    border: 1px solid #ddd !important;
    break-inside: avoid;
  }
}
</style>

<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/components/footer.php'); ?>