<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between">
                    <h4>Media Library</h4>

                    <!-- Add Image Button -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">Add Image</button>

                </div>
            </div>
            <div class="row">
                <?php foreach ($images as $image): ?>
                    <div class="col-md-2 mb-3">
                        <div class="card ImgCard">
                            <img src="<?= $image['guid']; ?>" class="card-img-top img-fluid" alt="Media Image">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>



            
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $currentPage - 1; ?>">Previous</a>
                    </li>
                    <?php 
                    $start = max($currentPage - 2, 1);
                    $end = min($currentPage + 2, $totalPages);
                    
                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                        if ($start > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor;
                    
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $currentPage + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>


    <!-- Add Image Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel">Add New Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addImageForm" action="<?= base_url('media-library/upload-image'); ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="imageFile">Select Image</label>
                            <input type="file" class="form-control" id="imageFile" name="imageFile" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload Image</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>
