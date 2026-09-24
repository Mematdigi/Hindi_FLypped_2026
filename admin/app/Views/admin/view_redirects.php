<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fa fa-exchange-alt"></i> Active 301 Redirects</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">These URLs automatically redirect to their new locations with 301 status.</p>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Post Title</th>
                                <th>Current URL</th>
                                <th>Old URLs (Redirecting From)</th>
                                <th>Redirect Hits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($redirects)): ?>
                                <?php foreach ($redirects as $redirect): ?>
                                    <tr>
                                        <td><strong><?= esc($redirect->post_title) ?></strong></td>
                                        <td><code><?= base_url(esc($redirect->post_name)) ?></code></td>
                                        <td>
                                            <?php 
                                            $old_urls = explode('|||', $redirect->old_urls); 
                                            foreach ($old_urls as $old_url): 
                                            ?>
                                                <div class="mb-1">
                                                    <code><?= base_url(esc($old_url)) ?></code>
                                                    <form action="<?= base_url('admin/redirect/delete/' . $redirect->ID) ?>" method="POST" style="display: inline;">
                                                        <input type="hidden" name="old_slug" value="<?= esc($old_url) ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Remove this redirect?')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><span class="badge badge-info"><?= $redirect->hits ?? 0 ?></span></td>
                                        <td>
                                            <a href="<?= base_url('edit_post/' . $redirect->ID) ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Edit Post
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fa fa-info-circle"></i> No redirects found. Redirects are automatically created when you change a post's URL.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(APPPATH . 'Views/footer.php'); ?>