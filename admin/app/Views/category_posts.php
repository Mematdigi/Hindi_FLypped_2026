<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

<section class="category-section py-5">
    <div class="container">
        <h2><?= htmlspecialchars($category->name); ?> Category</h2>
        <div class="row mt-5">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if (!empty($post->thumbnail_url)): ?>
                                <img src="<?= $post->thumbnail_url; ?>" class="card-img-top" alt="<?= htmlspecialchars($post->post_title); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($post->post_title); ?></h5>
                                <p class="card-text"><?= date('F d, Y', strtotime($post->post_date)); ?></p>
                                <!-- Update Read More Button to point to flypped.com -->
                                <a href="https://hi.flypped.com/blog_detail/<?= $post->ID; ?>" target="_blank" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found under this category.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination Section -->
        <div class="row">
            <div class="col-md-12 d-flex justify-content-center category-section">
                <?= $pagination; ?>
            </div>
        </div>
    </div>
            </div>
</section>

<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>
