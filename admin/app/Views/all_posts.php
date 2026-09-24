<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <!-- Filter Section -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header BlogHeader text-white">
                        <h4 class="mb-0">
                            Filter Posts
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= base_url('all-posts'); ?>" id="filterForm">
                            <div class="row align-items-end">
                                <!-- Post Title Search with Autocomplete -->
                                <div class="col-md-4 mb-3">
                                    <label for="title_search" class="form-label fw-bold">
                                        Post Title
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control form-control-lg shadow-sm" 
                                               id="title_search" 
                                               name="title_search" 
                                               placeholder="Start typing to search posts..." 
                                               value="<?= isset($_GET['title_search']) ? esc($_GET['title_search']) : ''; ?>"
                                               autocomplete="off">
                                        <div id="suggestions" class="suggestions-dropdown"></div>
                                        <div class="input-group-append position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                            <span class="input-group-text bg-transparent border-0">
                                                <i class="fa fa-search text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Author Filter -->
                                <div class="col-md-3">
                                    <label for="author_filter" class="form-label fw-bold">
                                        Author
                                    </label>
                                    <select class="form-control form-control-lg shadow-sm" id="author_filter" name="author_filter">
                                        <option value="">All Authors</option>
                                        <?php if (!empty($authors)): ?>
                                            <?php foreach ($authors as $author): ?>
                                                <option value="<?= $author->ID; ?>" 
                                                        <?= (isset($_GET['author_filter']) && $_GET['author_filter'] == $author->ID) ? 'selected' : ''; ?>>
                                                    <?= esc($author->display_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Post Status Filter -->
                        <div class="col-md-3" style="margin-top: 20px">
                            <label for="post_status" class="form-label fw-bold">
                                        Post Status
                                        </label>
                                <select class="form-control form-control-lg shadow-sm" id="post_status" name="post_status">
                    <option value="">All</option>
                    <option value="publish" <?= (isset($_GET['post_status']) && $_GET['post_status'] == 'publish') ? 'selected' : ''; ?>>Published</option>
                    <option value="future" <?= (isset($_GET['post_status']) && $_GET['post_status'] == 'future') ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="draft" <?= (isset($_GET['post_status']) && $_GET['post_status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    </select>
                    </div>


                                <!-- Single Date Filter -->
                                <div class="col-md-3">
                                    <label for="post_date" class="form-label fw-bold">
                                       Post Date
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-lg shadow-sm" 
                                           id="post_date" 
                                           name="post_date" 
                                           value="<?= isset($_GET['post_date']) ? esc($_GET['post_date']) : ''; ?>">
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-3">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                            <i class="fa fa-search mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <button type="button" class="btn btn-secondary shadow-sm" onclick="clearFilters()">
                                             Clear 
                                        </button>
                                       
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Status and Clear Button -->
                            <div class="row mt-2">
                                <div class="col-6">

                                    <?php if (!empty($_GET) && (isset($_GET['title_search']) || isset($_GET['author_filter']) || isset($_GET['post_date']))): ?>
                                                <div class="alert alert-info mb-0 py-2 px-3 shadow-sm">
                                                    <i class="fa fa-info-circle mr-1"></i> 
                                                    <strong>Showing filtered results:</strong> <?= count($posts); ?> post<?= count($posts) != 1 ? 's' : ''; ?> found
                                                </div>
                                            <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Posts Table -->
                <div class="card">
    <div class="card-header BlogHeader text-white">
        <h4 class="mb-0">All Posts</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 900px; overflow-y: auto;">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 35%;">Title</th>
                        <th style="width: 15%;">Author</th>
                        <th style="width: 20%;">Categories</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 15%;">Post Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <?php
                                $db = \Config\Database::connect();

                                // Fetch author name
                                $author_query = $db->query("SELECT display_name FROM wp_users WHERE ID = {$post->post_author}");
                                $author = $author_query->getRow()->display_name ?? 'Unknown';

                                // Fetch categories
                                $categories_query = $db->query("
                                    SELECT t.name 
                                    FROM wp_terms t
                                    JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                                    JOIN wp_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                    WHERE tt.taxonomy = 'category' AND tr.object_id = {$post->ID}
                                ");
                                $categories = array_map(fn($cat) => $cat->name, $categories_query->getResult());
                                $categories_str = implode(', ', $categories);

                                // Fetch post status and slug
                                $post_status_query = $db->query("SELECT post_status, post_name FROM wp_posts WHERE ID = {$post->ID}");
                                $post_row   = $post_status_query->getRow();
                                $post_status = $post_row->post_status ?? '';
                                $post_name   = $post_row->post_name ?? '';

                                //  Hindi site blog URL
                                $view_url = 'https://flyppedhindi.com/' . $post_name;
                            ?>
                            <tr>
                                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;" title="<?= esc($post->post_title); ?>">
                                    <?= esc($post->post_title); ?>
                                </td>
                                <td><?= esc($author); ?></td>
                                <td><?= esc($categories_str); ?></td>
                                <td><?= date('M j, Y', strtotime($post->post_date)); ?></td>
                                <td>
                                    <?php if ($post_status == 'publish'): ?>
                                        <span class="badge badge-success">Published</span>
                                    <?php elseif ($post_status == 'draft'): ?>
                                        <span class="badge badge-secondary">Draft</span>
                                    <?php elseif ($post_status == 'future'): ?>
                                        <span class="badge badge-info">Scheduled</span>
                                    <?php else: ?>
                                        <span class="badge badge-light"><?= esc(ucfirst($post_status)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Post actions">
                                        <a href="<?= esc($view_url); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-info" title="View on Hindi Site">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('edit_post/' . $post->ID); ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('confirm_delete_post/' . $post->ID); ?>" class="btn btn-sm btn-danger" title="Trash">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <?php if (!empty($_GET) && (isset($_GET['title_search']) || isset($_GET['author_filter']) || isset($_GET['post_date']))): ?>
                                    <div class="text-muted">
                                        <i class="fa fa-search fa-2x mb-2"></i>
                                        <h5>No posts found matching your filter criteria</h5>
                                        <p><a href="<?= base_url('all-posts'); ?>" class="btn btn-primary btn-sm">Clear filters</a> to see all posts.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">
                                        <i class="fa fa-file-text fa-2x mb-2"></i>
                                        <h5>No posts found</h5>
                                        <p>There are no posts available at the moment.</p>
                                    </div>
                                <?php endif; ?>
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
    </div>
<!-- CSS Styles -->
<style>
/* Custom CSS for Filter Section */
.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.suggestion-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
    font-size: 14px;
}

.suggestion-item:hover,
.suggestion-item.active {
    background-color: #f8f9fa;
    color: #495057;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.form-control-lg {
    border-radius: 8px;
    border: 1px solid #e0e6ed;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    transform: translateY(-1px);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #b6d4da;
    color: #0c5460;
    border-radius: 8px;
}

.fw-bold {
    font-weight: 600;
}

.form-label {
    margin-bottom: 8px;
    color: #495057;
}

/* Table improvements */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    font-size: 14px;
}

.btn-group .btn {
    margin-right: 0;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .col-md-4, .col-md-3, .col-md-2 {
        margin-bottom: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}
</style>

<script>
// Autocomplete functionality for post titles
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title_search');
    const suggestionsDiv = document.getElementById('suggestions');
    let debounceTimer;

    titleInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 2) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!titleInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    function fetchSuggestions(query) {
        fetch(`<?= base_url('get-post-suggestions'); ?>?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
            });
    }

    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        const html = suggestions.map(post => 
            `<div class="suggestion-item" onclick="selectSuggestion('${post.post_title.replace(/'/g, "\\'")}')">
                <strong>${post.post_title}</strong>
                <small class="text-muted d-block">by ${post.author_name} • ${post.post_date}</small>
            </div>`
        ).join('');

        suggestionsDiv.innerHTML = html;
        suggestionsDiv.style.display = 'block';
    }

    // Keyboard navigation for suggestions
    titleInput.addEventListener('keydown', function(e) {
        const items = suggestionsDiv.querySelectorAll('.suggestion-item');
        let activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (activeIndex < items.length - 1) {
                if (activeIndex >= 0) items[activeIndex].classList.remove('active');
                items[activeIndex + 1].classList.add('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeIndex > 0) {
                items[activeIndex].classList.remove('active');
                items[activeIndex - 1].classList.add('active');
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0) {
                items[activeIndex].click();
            }
        } else if (e.key === 'Escape') {
            suggestionsDiv.style.display = 'none';
        }
    });
});

function selectSuggestion(title) {
    document.getElementById('title_search').value = title;
    document.getElementById('suggestions').style.display = 'none';
}

function clearFilters() {
    // Clear all form inputs - FIXED: using correct field names
    document.getElementById('title_search').value = '';
    document.getElementById('author_filter').value = '';
    document.getElementById('post_date').value = ''; // Fixed: was date_from/date_to
    document.getElementById('post_status').value = ''; //  new line added
    
    // Hide suggestions
    document.getElementById('suggestions').style.display = 'none';
    
    // Redirect to clean URL
    window.location.href = '<?= base_url('all-posts'); ?>';
}
</script>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle mr-2"></i>
        <?= session()->getFlashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle mr-2"></i>
        <?= session()->getFlashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>