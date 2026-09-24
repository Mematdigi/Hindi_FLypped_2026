<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle fa-lg mr-2"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="close ml-auto" data-dismiss="alert">&times;</button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle fa-lg mr-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="close ml-auto" data-dismiss="alert">&times;</button>
                </div>
            </div>
        <?php endif; ?>


        <!-- CARD 1: Update Post URL & Create Redirect (BLUE) -->

        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5><i class="fa fa-exchange"></i> Update Post URL & Create 301 Redirect</h5>
                <small>Changes the post slug in WordPress AND creates a redirect. Hindi URLs have NO category prefix.</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/add') ?>" method="POST" id="addRedirectForm">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Current URL (Old) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="old_url" id="old_url" required
                                    placeholder="old-post-slug OR category/old-post-slug">
                                <small class="text-muted">Can include category prefix if needed (e.g. <code>tech/old-slug</code>)</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>New URL (Destination) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="new_url" id="new_url" required
                                    placeholder="new-post-slug">
                                <small class="text-muted">Just the slug — <strong>NO category</strong> (e.g. <code>new-post-slug</code>)</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg" name="redirect_type" id="redirect_type" required>
                                    <option value="301" selected>301 - Permanent</option>
                                    <option value="302">302 - Temporary</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-exclamation-triangle"></i> What this does:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Updates the WordPress post slug to new slug</li>
                            <li>Creates redirect: old URL → new-post-slug</li>
                            <li>Visitors to old URL will redirect to <code>hi.flypped.com/new-post-slug</code></li>
                        </ol>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Update Post & Create Redirect
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- CARD 2: Change Category (YELLOW) -->

        <!-- <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5><i class="fa fa-folder"></i> Change Post Category in DB</h5>
                <small>Changes the post's category in WordPress DB. Source redirect includes old category, destination is just slug.</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/add-category') ?>" method="POST" id="addCategoryRedirectForm">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Post Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="post_slug" id="post_slug" required
                                    placeholder="my-post-slug">
                                <small class="text-muted">Just the post slug (no category prefix)</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>New Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="category" id="category" required
                                    placeholder="technology">
                                <small class="text-muted">Category slug (e.g. technology, sports, entertainment)</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg" name="redirect_type" required>
                                    <option value="301" selected>301 - Permanent</option>
                                    <option value="302">302 - Temporary</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-light border">
                                <strong>Preview:</strong><br>
                                <span class="text-danger">From:</span> <code id="preview_from">old-category/post-slug</code><br>
                                <span class="text-success">To:</span> <code id="preview_to">post-slug</code>
                                <small class="text-muted d-block mt-1">Hindi URLs have NO category — destination is always just the slug</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-warning btn-lg text-dark">
                            <i class="fa fa-folder"></i> Change Category & Create Redirect
                        </button>
                    </div>
                </form>
            </div>
        </div> -->


        <!-- CARD 3: URL Mapping (TEAL) -->

        <!-- <div class="card mb-4 border-info">
            <div class="card-header bg-info text-white">
                <h5><i class="fa fa-map-marker"></i> Map URL → URL (Destination Must Exist in DB)</h5>
                <small>Redirect any source URL to a destination post — verified in DB first</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/add-mapping') ?>" method="POST" id="addMappingForm">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>From URL (Source) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="from_url" id="mapping_from_url" required
                                    placeholder="old-article-slug OR category/old-slug">
                                <small class="text-muted">Can include category prefix</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>To URL (Destination) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="to_url" id="mapping_to_url" required
                                    placeholder="new-article-slug">
                                <small class="text-muted">Just the slug — must exist in DB</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg" name="redirect_type" required>
                                    <option value="301" selected>301 - Permanent</option>
                                    <option value="302">302 - Temporary</option>
                                    <option value="410">410 - Gone (Deindex)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border">
                        <strong>Preview:</strong><br>
                        <span class="text-danger">From: </span><code>https://hi.flypped.com/<span id="mapping_preview_from">source-url</span></code><br>
                        <span class="text-success">To: </span><code>https://hi.flypped.com/<span id="mapping_preview_to">destination-slug</span></code>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-info btn-lg text-white">
                            <i class="fa fa-map-marker"></i> Create URL Mapping
                        </button>
                    </div>
                </form>
            </div>
        </div> -->

        <!-- ============================================================ -->
        <!-- CARD 4: Bulk Excel Upload (GREY) -->
        <!-- ============================================================ -->
        <!-- <div class="card mb-4 border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5><i class="fa fa-file-excel-o"></i> Bulk Redirect via Excel Upload</h5>
                <small>Upload an Excel file with URLs — all will redirect to your chosen destination slug</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/bulk-upload') ?>" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Excel File (.xlsx) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
                                <small class="text-muted">Column A = Full URLs (e.g. https://hi.flypped.com/article-slug)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Redirect To <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" name="to_url" id="bulk_to_url"
                                        value="/" placeholder="/ for homepage or post-slug">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="document.getElementById('bulk_to_url').value='__410__'"
                                            title="Mark URL as Gone - Deindex from Google">
                                            410
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Use <code>/</code> for homepage, any slug, or click <strong>410</strong> to deindex</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg" name="redirect_type" id="bulk_redirect_type" required>
                                    <option value="301" selected>301 - Permanent</option>
                                    <option value="302">302 - Temporary</option>
                                    <option value="410">410 - Gone (Deindex)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-secondary btn-block">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <strong><i class="fa fa-info-circle"></i> Excel Format:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Column A = Full URLs (e.g. <code>https://hi.flypped.com/article-slug</code>)</li>
                            <li>First row = header (skipped automatically)</li>
                            <li>Duplicate URLs already in DB will be <strong>updated</strong></li>
                        </ul>
                    </div>
                </form>
            </div>
        </div> -->

        <!-- ============================================================ -->
        <!-- CARD 5: Single Post Delete (RED) -->
        <!-- ============================================================ -->
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5><i class="fa fa-trash"></i> Delete Single Post by URL</h5>
                <small>Enter a URL — post matching the slug will be permanently deleted</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/delete-single-post') ?>" method="POST" id="singleDeleteForm">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Post URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="delete_url" id="delete_url" required
                                    placeholder="https://hi.flypped.com/article-slug OR article-slug">
                                <small class="text-muted">Full URL or just slug — post will be found by slug</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-danger btn-lg btn-block">
                                    <i class="fa fa-trash"></i> Delete Post
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Deletes:</strong> Post data + all meta + featured image file + associated redirects.
                        <strong>Cannot be undone!</strong>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- CARD 6: Bulk Delete Posts (DARK RED) -->
        <!-- ============================================================ -->
        <!-- <div class="card mb-4" style="border-color: #8b0000;">
            <div class="card-header text-white" style="background-color: #8b0000;">
                <h5><i class="fa fa-trash"></i> Bulk Delete Posts via Excel</h5>
                <small>Upload Excel with URLs — posts matching slug will be permanently deleted</small>
            </div>
            <div class="card-body">
                <form action="<?= site_url('redirect/bulk-delete') ?>" method="POST" enctype="multipart/form-data" id="bulkDeleteForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Excel File (.xlsx) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="delete_excel_file" id="delete_excel_file" accept=".xlsx,.xls" required>
                                <small class="text-muted">Column A = Full URLs — First row header is skipped automatically</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-lg btn-block text-white" style="background-color: #8b0000;">
                                    <i class="fa fa-trash"></i> Bulk Delete Posts
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger mb-0">
                        <strong><i class="fa fa-exclamation-triangle"></i> Warning — PERMANENT:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Post found by slug — no category match required</li>
                            <li>Deletes post, all meta, featured image file, and redirects</li>
                            <li><strong>Cannot be undone — backup DB first!</strong></li>
                        </ul>
                    </div>
                </form>
            </div>
        </div> -->

        <!-- Single Delete Warning Modal -->
        <!-- <div class="modal fade" id="deleteWarningModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Permanent Delete Warning</h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <h6><strong>The following will be PERMANENTLY deleted:</strong></h6>
                            <ul class="mb-0">
                                <li>✅ Post content and data</li>
                                <li>✅ All post meta (SEO, custom fields)</li>
                                <li>✅ Featured image file from server</li>
                                <li>✅ Category/tag relationships</li>
                                <li>✅ Associated redirects in DB</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <strong>This action CANNOT be undone.</strong> Please backup your database before proceeding.
                        </div>
                        <div class="mt-2">
                            <label class="font-weight-bold">URL to be deleted:</label>
                            <code id="modalDeleteUrl" class="d-block mt-1 text-danger p-2 bg-light rounded"></code>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel — Keep Post
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteBtn">
                            <i class="fa fa-trash"></i> Yes, Delete Permanently
                        </button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Bulk Delete Warning Modal -->
        <!-- <div class="modal fade" id="bulkDeleteWarningModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Bulk Permanent Delete Warning</h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <h6><strong>For EVERY URL in the Excel file:</strong></h6>
                            <ul class="mb-0">
                                <li>✅ Post content and data deleted</li>
                                <li>✅ All post meta deleted</li>
                                <li>✅ Featured image file deleted</li>
                                <li>✅ Category/tag relationships deleted</li>
                                <li>✅ Associated redirects deleted</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Posts are found by slug only — no category match required for Hindi.</strong>
                        </div>
                        <div class="alert alert-danger mb-0">
                            <strong>This action CANNOT be undone.</strong> Please backup your database before proceeding.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel — Keep Posts
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" id="confirmBulkDeleteBtn">
                            <i class="fa fa-trash"></i> Yes, Bulk Delete Permanently
                        </button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- ============================================================ -->
        <!-- Active Redirects Table -->
        <!-- ============================================================ -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5><i class="fa fa-list"></i> Active Redirects (<?= count($redirects) ?>) </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="redirectsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">#</th>
                                <th width="40%">From (Old URL)</th>
                                <th width="40%">To (New URL)</th>
                                <th width="5%">Type</th>
                                <th width="5%">Hits</th>
                                <th width="5%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($redirects)): ?>
                                <?php foreach ($redirects as $index => $redirect): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><code class="text-danger" style="font-size:13px;"><?= esc($redirect->old_slug) ?></code></td>
                                        <td><code class="text-success" style="font-size:13px;"><?= esc($redirect->new_slug) ?></code></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $redirect->status_code == '301' ? 'success' : ($redirect->status_code == '410' ? 'dark' : 'warning') ?>">
                                                <?= $redirect->status_code ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><span class="badge badge-info"><?= $redirect->hits ?></span></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('redirect/delete/' . $redirect->id) ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this redirect?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                        <!-- <td>Code </br>Commented</td> -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fa fa-info-circle fa-3x mb-3"></i>
                                        <p class="mb-0">No redirects yet. Add your first redirect above.</p>
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

<script>
$(document).ready(function() {

    // ===== Card 1: Update Post URL Validate =====
    $('#addRedirectForm').on('submit', function(e) {
        var oldUrl = $('#old_url').val().trim();
        var newUrl = $('#new_url').val().trim();
        if (!oldUrl || !newUrl) {
            e.preventDefault();
            alert('Both URLs are required');
            return false;
        }
        if (newUrl.includes('/')) {
            e.preventDefault();
            alert('❌ New URL (destination) should NOT contain "/" — Hindi URLs have no category.\n\nJust enter the post slug e.g: my-article-title');
            $('#new_url').focus();
            return false;
        }
        if (!confirm('This will:\n1. Update the post slug in WordPress\n2. Create a redirect: ' + oldUrl + ' → ' + newUrl + '\n\nContinue?')) {
            e.preventDefault();
            return false;
        }
    });

    // ===== Card 2: Category Redirect Live Preview =====
    function updateCategoryPreview() {
        var slug     = $('#post_slug').val().trim() || 'post-slug';
        var category = $('#category').val().trim() || 'old-category';
        slug     = slug.replace(/\//g, '');
        category = category.replace(/\//g, '');
        $('#preview_from').text(category + '/' + slug);
        $('#preview_to').text(slug); // ✅ Hindi: no category in destination
    }
    $('#post_slug, #category').on('keyup change paste', updateCategoryPreview);

    // ===== Card 2: Category Redirect Validate =====
    $('#addCategoryRedirectForm').on('submit', function(e) {
        var postSlug = $('#post_slug').val().trim();
        var category = $('#category').val().trim();
        if (!postSlug || !category) {
            e.preventDefault();
            alert('Both fields are required');
            return false;
        }
        if (!confirm('Change category to "' + category + '" for post "' + postSlug + '"?\n\nRedirect: ' + category + '/' + postSlug + ' → ' + postSlug + '\n\nContinue?')) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    // ===== Card 3: URL Mapping Live Preview =====
    $('#mapping_from_url, #mapping_to_url').on('input', function() {
        $('#mapping_preview_from').text($('#mapping_from_url').val() || 'source-url');
        $('#mapping_preview_to').text($('#mapping_to_url').val() || 'destination-slug');
    });

    // ===== Card 3: URL Mapping Validate =====
    $('#addMappingForm').on('submit', function(e) {
        var from = $('#mapping_from_url').val().trim();
        var to   = $('#mapping_to_url').val().trim();
        if (!from || !to) {
            e.preventDefault();
            alert('Both URLs are required');
            return false;
        }
        if (!confirm('Create URL mapping?\n\nFrom: ' + from + '\nTo: ' + to + '\n\nDestination post will be verified in DB.\n\nContinue?')) {
            e.preventDefault();
            return false;
        }
    });

    // ===== Card 4: Bulk Upload Validate =====
    $('#bulkUploadForm').on('submit', function(e) {
        var file         = $('#excel_file').val();
        var toUrl        = $('#bulk_to_url').val().trim();
        var redirectType = $('#bulk_redirect_type').val();

        if (!file) {
            e.preventDefault();
            alert('Please select an Excel file');
            return false;
        }

        if (redirectType !== '410' && !toUrl) {
            e.preventDefault();
            alert('Please enter a destination URL or slug');
            return false;
        }

        var confirmMsg = redirectType === '410'
            ? 'Upload and mark these URLs as GONE (410)?\n\nGoogle will deindex them permanently.\n\nDuplicates will be updated.\n\nContinue?'
            : 'Upload and create bulk redirects?\n\nAll URLs will redirect to: ' + toUrl + '\n\nDuplicates will be updated.\n\nContinue?';

        if (!confirm(confirmMsg)) {
            e.preventDefault();
            return false;
        }
    });

    // ===== Card 4: Auto set __410__ when 410 type selected =====
    $('#bulk_redirect_type').on('change', function() {
        if ($(this).val() === '410') {
            $('#bulk_to_url').val('__410__');
            $('#bulk_to_url').prop('readonly', true);
        } else {
            if ($('#bulk_to_url').val() === '__410__') {
                $('#bulk_to_url').val('/');
            }
            $('#bulk_to_url').prop('readonly', false);
        }
    });

    // ===== Card 5: Single Delete — Show Warning Modal =====
    $('#singleDeleteForm').on('submit', function(e) {
        e.preventDefault();
        var url = $('#delete_url').val().trim();
        if (!url) { alert('Please enter a URL'); return false; }
        $('#modalDeleteUrl').text(url);
        $('#confirmDeleteBtn').data('form', this);
        $('#deleteWarningModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        $('#deleteWarningModal').modal('hide');
        var form = $(this).data('form');
        $(form).off('submit').submit();
    });

    // ===== Card 6: Bulk Delete — Show Warning Modal =====
    $('#bulkDeleteForm').on('submit', function(e) {
        e.preventDefault();
        var file = $('#delete_excel_file').val();
        if (!file) { alert('Please select an Excel file'); return false; }
        $('#confirmBulkDeleteBtn').data('form', this);
        $('#bulkDeleteWarningModal').modal('show');
    });

    $('#confirmBulkDeleteBtn').on('click', function() {
        $('#bulkDeleteWarningModal').modal('hide');
        var form = $(this).data('form');
        $(form).off('submit').submit();
    });

});
</script>

<?php include(APPPATH . 'Views/footer.php'); ?>