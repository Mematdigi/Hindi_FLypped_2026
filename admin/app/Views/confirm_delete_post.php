<!-- confirm_delete_post.php -->

<!-- Header Section -->
<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header BlogHeader text-white">
                        <h4>Delete Post</h4>
                    </div>
                    <div class="card-body">
                        <p>Are you sure you want to delete the post: <strong><?= $post->post_title; ?></strong>?</p>
                        <form action="<?= base_url('delete_post/' . $post->ID); ?>" method="post">
                            <input type="hidden" name="post_id" value="<?= $post->ID; ?>">
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            <a href="<?= base_url('all-posts'); ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

</div>

<!-- Footer Section -->
<?php include(APPPATH . 'Views/footer.php'); ?>
