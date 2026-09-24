<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <!-- Categories Table (col-8) -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header BlogHeader text-white">
                        <h4>All Categories</h4>
                    </div>
                    <div class="card-body ">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Count</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?= $category->name; ?></td>
                                                <td><?= $category->slug; ?></td>
                                                <td><?= $category->count; ?></td>
                                                <td>
                                                   <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('categories/view/' . $category->term_id); ?>'">View</button>
                                                    <button class="btn btn-sm btn-warning" onclick="editCategory(<?= $category->term_id; ?>)">Edit</button>
                                                    <a href="<?= base_url('categories/delete/' . $category->term_id) ?>" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No categories found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Category Form (col-4) -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header BlogHeader text-white">
                        <h4>Add New Category</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('categories/add') ?>" method="post">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name (e.g., अध्यात्म)" required>
                            </div>
                            <div class="form-group">
                                <label for="slug">Slug (English)</label>
                                <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter slug in English (e.g., adhyatm)" required>
                                <small class="form-text text-muted">Use lowercase letters, numbers, and hyphens only</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCategoryForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editCategoryId">
                        <div class="form-group">
                            <label for="editCategoryName">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" required>
                        </div>
                        <div class="form-group">
                            <label for="editCategorySlug">Slug (English)</label>
                            <input type="text" class="form-control" id="editCategorySlug" required>
                            <small class="form-text text-muted">Use lowercase letters, numbers, and hyphens only</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveCategory()">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>

<script>
function editCategory(id) {
    // Fetch category details using AJAX
    $.get('<?= base_url('categories/edit') ?>/' + id, function(data) {
        $('#editCategoryId').val(data.term_id);
        $('#editCategoryName').val(data.name);
        $('#editCategorySlug').val(data.slug);
        $('#editCategoryModal').modal('show');
    });
}

function saveCategory() {
    var id = $('#editCategoryId').val();
    var name = $('#editCategoryName').val();
    var slug = $('#editCategorySlug').val();

    $.post('<?= base_url('categories/update') ?>/' + id, {name: name, slug: slug}, function() {
        $('#editCategoryModal').modal('hide');
        location.reload();
    });
}
</script>