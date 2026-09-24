<!-- ===== Header Section ===== -->
<?php include('./app/Views/header.php'); ?>

<!-- ===== Review Table ===== -->
<div class="main-panel">
    <div class="content-wrapper">
        <h3>Reviews</h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Comment ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Content</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($reviews) && !empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?= esc($review['comment_ID']); ?></td>
                                <td><?= esc($review['comment_author']); ?></td>
                                <td><?= esc($review['comment_author_email']); ?></td>
                                <td><?= esc($review['comment_content']); ?></td>
                                <td>
                                    <!-- Delete Button -->
                                    <form action="<?= base_url('/deleteReview') ?>" method="post" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="comment_id" value="<?= esc($review['comment_ID']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this review?');">
                                            Trash
                                        </button>
                                    </form>

                                    <!-- Publish Button -->
                                    <form action="<?= base_url('/publishReview') ?>" method="post" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="comment_id" value="<?= esc($review['comment_ID']); ?>">
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to publish this review?');">
                                            Publish
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No reviews found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            
        </div>
        <!-- Pagination -->
<div class="pagination mb-5">
    <div class="pagination_table">

        <?php if ($current_page > 1): ?>
            <a href="<?= base_url('/reviews?page=1'); ?>">FIRST</a>
            <a href="<?= base_url('/reviews?page=' . ($current_page - 1)); ?>">PREV</a>
        <?php endif; ?>
    
        <?php for ($i = max(1, $current_page - 1); $i <= min($total_pages, $current_page + 1); $i++): ?>
            <a href="<?= base_url('/reviews?page=' . $i); ?>" class="<?= $i == $current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= base_url('/reviews?page=' . ($current_page + 1)); ?>">NEXT</a>
            <a href="<?= base_url('/reviews?page=' . $total_pages); ?>">LAST</a>
        <?php endif; ?>
    </div>

    <span>Page <?= $current_page ?> of <?= $total_pages ?></span>
</div>


    <!-- Flash Messages for Success or Error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
</div>

<!-- ===== Footer Section ===== -->
<?php include('./app/Views/footer.php'); ?>


<style>
.pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
}

.pagination a, .pagination span {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #ddd;
    color: #007bff;
    text-decoration: none;
    background: aliceblue;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
    border-color: #007bff;
}

.pagination a:hover:not(.active) {
    background-color: #f0f0f0;
}

.pagination span {
    color: #333;
    font-weight: bold;
}
</style>
