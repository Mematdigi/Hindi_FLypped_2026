<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

  <!-- ===== Add the form with the appropriate method and action ===== -->
  <form id="postForm" method="post" action="<?= base_url('/save_post') ?>" enctype="multipart/form-data">
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">
          <!-- Blog Content Section (col-8) -->
          <div class="col-lg-8" style="height: 100vh; overflow-y: auto;">
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header BlogHeader text-white">
                <h4 class="mb-0">Add New Post</h4>
              </div>
              <div class="card-body">
                <!-- Blog Title -->
                <div class="form-group mb-4">
                  <label for="postTitle">Blog Title</label>
                  <input type="text" class="form-control" id="postTitle" name="postTitle" placeholder="Enter blog title" required>
                </div>

                <!-- Blog Content (CKEditor) -->
                <div class="form-group mb-4">
                  <label for="postContent">Blog Content</label>
                  <textarea name="postContent" id="editor"></textarea>
                </div>
              </div>
            </div>
            
             <!-- ===== FAQ Section ===== -->
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header text-white d-flex justify-content-between align-items-center"
                   style="background: linear-gradient(135deg, #6f42c1, #8e5fd5);">
                <h4 class="mb-0">
                  <i class="fas fa-question-circle mr-2"></i> FAQ Section
                  <small class="ml-2" style="font-size:13px; opacity:0.85;">(Optional — adds FAQ Schema for SEO)</small>
                </h4>
                <span class="badge badge-light text-purple" id="faqCountBadge">0 FAQs</span>
              </div>
              <div class="card-body">

                <p class="text-muted small mb-3">
                  <i class="fas fa-info-circle text-primary"></i>
                  FAQs are saved directly in the WordPress database and generate
                  <strong>FAQ structured schema</strong> for better Google rich results.
                </p>

                <!-- ── FAQ Items Container ── -->
                <div id="faqContainer"></div>

                <!-- ── Action Buttons ── -->
                <div class="d-flex align-items-center mt-2 gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addFaqBtn">
                    <i class="fas fa-plus-circle mr-1"></i> Add FAQ
                  </button>
                  <span class="text-muted small ml-2" id="faqLimitMsg"></span>
                </div>

                <!-- ── Live Schema Preview ── -->
                <div id="faqSchemaPreviewBox" class="mt-4" style="display:none;">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mb-0 font-weight-bold small">
                      <i class="fas fa-code text-secondary mr-1"></i> Live FAQ Schema Preview
                    </label>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                            id="toggleSchemaBtn" style="font-size:11px;">Hide</button>
                  </div>
                  <div id="schemaPreviewWrapper"
                       style="background:#1e1e1e; border-radius:6px; padding:12px; max-height:220px; overflow-y:auto;">
                    <pre id="schemaPreviewContent"
                         style="color:#d4d4d4; font-size:11px; margin:0; white-space:pre-wrap;"></pre>
                  </div>
                </div>

              </div>
            </div>

            <!-- SEO Plugin Panel -->
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
            <div class="card-header BlogHeader text-white">
                <h4 class="mb-0"><i class="fas fa-search-plus"></i> SEO Plugin (Optional)</h4>
            </div>
            <div class="card-body">
                
                <!-- Meta Title -->
                <div class="form-group mb-3">
                <label for="metaTitle">Meta Title</label>
                <input type="text" class="form-control" id="metaTitle" name="metaTitle" 
                        placeholder="SEO optimized title for search engines" maxlength="65">
                <div class="char-counter" id="titleCounter">0/65 characters</div>
                <small class="form-text text-muted">Auto-generated from blog title. Recommended: 50-65 characters</small>
                </div>

                <!-- Focus Keywords -->
                <div class="form-group mb-3">
                <label for="focusKeywords">Focus Keywords (1-5 keywords)</label>
                <input type="text" class="form-control" id="focusKeywords" name="focusKeywords" 
                        placeholder="Enter 1-5 focus keywords, separated by commas">
                <small class="form-text text-muted">These keywords will be analyzed throughout your content</small>
                </div>

                <!-- Meta Description -->
                <div class="form-group mb-3">
                <label for="metaDescription">Meta Description</label>
                <textarea class="form-control" id="metaDescription" name="metaDescription" rows="3" 
                            placeholder="Enter meta description (120-160 characters recommended)" maxlength="200"></textarea>
                <div class="char-counter" id="metaCounter">0/200 characters</div>
                </div>

                <!-- URL Slug -->
                <div class="form-group mb-3">
                <label for="urlSlug">URL Slug</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><?= base_url() ?>/</span>
                    </div>
                    <input type="text" class="form-control" id="urlSlug" name="urlSlug" placeholder="auto-generated-from-title">
                </div>
                <small class="form-text text-muted">Auto-generated if left empty</small>
                </div>

                <!-- SEO Analysis Dashboard -->
                <div class="seo-dashboard mt-4">
                <h5><i class="fas fa-chart-line"></i> SEO Analysis</h5>
                
                <!-- SEO Score Circle -->
                <div class="seo-score-container mb-3">
                    <div class="seo-score-circle" id="seoScoreCircle">0</div>
                    <div class="seo-score-text">
                    <strong>SEO Score</strong>
                    <div id="seoScoreMessage">Start writing to see analysis</div>
                    </div>
                </div>

                <!-- SEO Checklist -->
                <div class="seo-checklist">
                    <div class="checklist-item" id="titleKeyword">
                    <i class="fas fa-times text-danger"></i>
                    <span>Title contains focus keyword</span>
                    </div>
                    <div class="checklist-item" id="metaKeyword">
                    <i class="fas fa-times text-danger"></i>
                    <span>Meta description contains focus keyword</span>
                    </div>
                    <div class="checklist-item" id="contentLength">
                    <i class="fas fa-times text-danger"></i>
                    <span>Content is 300+ words (<span id="wordCount">0</span> words)</span>
                    </div>
                    <div class="checklist-item" id="firstParagraph">
                    <i class="fas fa-times text-danger"></i>
                    <span>First paragraph contains focus keyword</span>
                    </div>
                    <div class="checklist-item" id="headingStructure">
                    <i class="fas fa-times text-danger"></i>
                    <span>Proper heading structure (H2-H3)</span>
                    </div>
                    <div class="checklist-item" id="imageAlt">
                    <i class="fas fa-times text-danger"></i>
                    <span>Images have ALT text with keywords</span>
                    </div>
                </div>

                <!-- Keyword Density -->
                <div class="keyword-density mt-3">
                    <label><strong>Keyword Density</strong></label>
                    <div id="densityAnalysis">Add focus keywords to see analysis</div>
                </div>

                <!-- Google Preview -->
                <div class="google-preview mt-3">
                    <label><strong>Google Search Preview</strong></label>
                    <div class="preview-container">
                    <div class="preview-title" id="previewTitle">Your Blog Title Will Appear Here</div>
                    <div class="preview-url" id="previewUrl"><?= base_url() ?>/your-slug</div>
                    <div class="preview-description" id="previewDescription">Your meta description will appear here...</div>
                    </div>
                </div>
                </div>

            </div>
            </div>
            </div>

          <!-- Post Settings Section (col-4) -->
          <div class="col-lg-4">
          
            <!-- Add New Post Date & Time Section (IST Local Time) -->
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header text-white" style="background-color: #9258d4; border-radius: 10px 10px 0 0;">
                <h4 class="mb-0" style="font-size: 16px; font-weight: 600;">Post Date & Time</h4>
              </div>
              <div class="card-body">
                <div class="form-group mb-0">
                    <label for="publishTime" style="font-weight: 500; font-size: 15px; margin-bottom: 10px;">Publish Date & Time:</label>
                    <input type="datetime-local" class="form-control" name="publishTime" id="publishTime" value="<?php 
                        // Using IST timezone to display current local time
                        $timezone = new DateTimeZone('Asia/Kolkata');
                        $dt = new DateTime('now', $timezone);
                        echo $dt->format('Y-m-d\TH:i');
                    ?>" style="padding: 10px; border-radius: 5px; border: 1px solid #ced4da; box-shadow: none;">
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> Leave as current time to publish immediately, set a future date to schedule, or set a past date to backdate the post.
                    </small>
                </div>
              </div>
            </div>

            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header BlogHeader text-white" style="background-color: #9258d4; border-radius: 10px 10px 0 0;">
                <h4 class="mb-0">Post Settings</h4>
              </div>
             <div class="card-body">
    
    <!-- Visibility Settings -->
    <div class="form-group mb-3">
        <label for="visibility">Visibility</label>
        <select class="form-control" id="visibility" name="visibility">
            <option value="public">Public</option>
            <option value="private">Private</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
        </select>
    </div>

    <!-- Author Dropdown -->
    <div class="form-group mb-3">
        <label for="author">Author</label>
        <select class="form-control" id="author" name="author">
            <?php
            $db = \Config\Database::connect();
            $query = $db->query("SELECT ID, display_name FROM wp_users");
            $authors = $query->getResult();
            foreach ($authors as $author) {
                echo "<option value='{$author->ID}'>{$author->display_name}</option>";
            }
            ?>
        </select>
    </div>
    
                <!-- Categories Checkboxes -->
                <div class="form-group mb-3">
                  <label>Categories</label>
                  <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 0px 38px; border-radius: 5px;">
                    <?php
                    $query = $db->query("SELECT t.term_id, t.name FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'category'");
                    $categories = $query->getResult();
                    foreach ($categories as $category) {
                        echo "<div class='form-check'>
                            <input class='form-check-input' type='checkbox' id='category{$category->term_id}' name='categories[]' value='{$category->term_id}'>
                            <label class='form-check-label' for='category{$category->term_id}'>{$category->name}</label>
                        </div>";
                    }
                    ?>
                  </div>
                </div>

                <!-- Tags Input -->
               <div class="form-group mb-3">
                    <label for="tags">Tags</label>
                    <input type="text" 
                          class="form-control" 
                          id="tags" 
                          name="tags" 
                          placeholder="Add tags, separated by commas">
                </div>
                <div id="tagPreview" class="mt-2"></div>


                <!-- Feature Image Upload -->
                <div class="form-group mb-3">
                  <label for="featureImage">Featured Image</label>
                  <input type="file" class="form-control-file" id="featureImage" name="featureImage">
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group mt-3">
              <button type="submit" class="btn btn-success btn-block" id="publishPostBtn">Publish Post</button>
                   <button type="button" class="btn btn-info btn-block" id="previewPostBtn">
                    Preview 
               </button>
            </div>
          </div>
        </div>
      </div>
  </form>



<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>

<!-- Include SweetAlert2 for popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Twitter Widgets Script -->
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<script>
// Prevent double clicking & handle server responses via SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    const postForm = document.getElementById('postForm');
    const publishBtn = document.getElementById('publishPostBtn');

    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            if (publishBtn) {
                // Immediately disable button to prevent double click
                publishBtn.disabled = true;
                publishBtn.innerHTML = 'Publishing... <span class="spinner-border spinner-border-sm"></span>';
                publishBtn.style.opacity = '0.7';
                publishBtn.style.cursor = 'not-allowed';
            }
        });
    }

    // Success popup message
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
            icon: 'success',
            confirmButtonText: 'Great!',
            timer: 3000
        });
    <?php endif; ?>

    // Error popup message
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
    <?php endif; ?>
});
</script>

<script>
//  FIXED VERSION - Publish button always enabled, no validation
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize CKEditor
    const { ClassicEditor, Essentials, Bold, Italic, Heading, Paragraph, List, Font, Image, ImageToolbar, ImageUpload, ImageCaption, ImageResize, SimpleUploadAdapter, Link, MediaEmbed, Table, TableToolbar, TableProperties, TableCellProperties } = CKEDITOR;
    
    let editorInstance;

    ClassicEditor.create(document.querySelector('#editor'), {
        plugins: [
            Essentials, Bold, Italic, Heading, Paragraph, List, Font,
            Image, ImageToolbar, ImageUpload, ImageCaption, ImageResize, SimpleUploadAdapter,
            Link, MediaEmbed, Table, TableToolbar, TableProperties, TableCellProperties
        ],
        toolbar: {
            items: [
                'undo', 'redo', '|', 'heading', '|', 'bold', 'italic', 'strikethrough', 'underline',
                '|', 'bulletedList', 'numberedList', 'todoList', '|', 'alignment',
                '|', 'link', 'blockQuote', 'insertTable', 'mediaEmbed',
                '|', 'imageUpload', '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor'
            ]
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        simpleUpload: {
            uploadUrl: '<?= base_url('/upload-editor-image') ?>'
        }
    })
    .then(editor => {
        editorInstance = editor;
        window.editorInstance = editor; // Make global for other scripts
        console.log('CKEditor initialized successfully');
    })
    .catch(error => console.error('Editor initialization error:', error));

    // ============================================================
    // ★ THE FIX: SYNC DATA ON FORM SUBMIT
    // ============================================================
    const form = document.getElementById('postForm');
    
    form.addEventListener('submit', function(e) {
        // Check if editor is loaded
        if (window.editorInstance) {
            // Get data from CKEditor
            const data = window.editorInstance.getData();
            
            // Put it into the textarea so PHP can read it
            document.querySelector('#editor').value = data;
            
            // Optional debugging: check console to see if data exists
            console.log("Submitting content:", data); 
            
            if (!data) {
                // Prevent submit if truly empty (optional safety)
                alert("Blog content cannot be empty.");
                e.preventDefault();
                
                // If we prevent submit, we need to re-enable the button
                const publishBtn = document.getElementById('publishPostBtn');
                if (publishBtn) {
                    publishBtn.disabled = false;
                    publishBtn.innerHTML = 'Publish Post';
                    publishBtn.style.opacity = '1';
                    publishBtn.style.cursor = 'pointer';
                }
            }
        }
    });
});
</script>


<script>
  document.getElementById('tags').addEventListener('input', function () {
      const input = this.value.trim(); // Get the trimmed input value
      const tagsArray = input.split(',').map(tag => tag.trim()); // Split tags by commas and trim spaces

      // Remove any occurrences of 'value' prefix
      const cleanTagsArray = tagsArray.map(tag => tag.replace(/^value/i, '').trim());

      // Update the input value without the 'value' prefix
      this.value = cleanTagsArray.join(', ');

      // Display the tags preview
      const tagPreview = document.getElementById('tagPreview');
      tagPreview.innerHTML = ''; // Clear the preview
      cleanTagsArray.forEach(tag => {
          if (tag) {
              const tagElement = document.createElement('span');
              tagElement.className = 'badge badge-primary mr-1';
              tagElement.textContent = tag; // Display tag without 'value' prefix
              tagPreview.appendChild(tagElement);
          }
      });
  });

</script>

<!-- Twitter Embed Function -->
<?php
function embedTweet($content) {
    // Detect any Twitter links
    $pattern = '/https?:\/\/(www\.)?twitter\.com\/[A-Za-z0-9_]+\/status\/[0-9]+/';
    
    // Replace Twitter links with embedded tweet HTML
    return preg_replace_callback($pattern, function ($matches) {
        $tweet_url = $matches[0];
        return '<blockquote class="twitter-tweet"><a href="' . $tweet_url . '"></a></blockquote>';
    }, $content);
}
?>
<script>
// Preview Post on Live Site - FIXED URL CONSTRUCTION
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('previewPostBtn');
    
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            // Get form data
            const postTitle = document.getElementById('postTitle').value;
            const author = document.getElementById('author').value;
            const visibility = document.getElementById('visibility').value;
            const tags = document.getElementById('tags').value;
            const publishTime = document.getElementById('publishTime').value;
            
            // Get selected categories
            const categories = [];
            document.querySelectorAll('input[name="categories[]"]:checked').forEach(function(checkbox) {
                categories.push(checkbox.value);
            });
            
            // Get featured image
            const featureImageInput = document.getElementById('featureImage');
            let featuredImageFile = null;
            if (featureImageInput.files && featureImageInput.files[0]) {
                featuredImageFile = featureImageInput.files[0];
            }
            
            // Get blog content from CKEditor
            let postContent = '';
            if (window.editorInstance) {
                postContent = window.editorInstance.getData();
            }
            
            // Get SEO data
            const metaTitle = document.getElementById('metaTitle').value;
            const metaDescription = document.getElementById('metaDescription').value;
            const focusKeywords = document.getElementById('focusKeywords').value;
            const urlSlug = document.getElementById('urlSlug').value;
            
            // Validation
            if (!postTitle || !postContent) {
                alert('Please add a title and content before previewing');
                return;
            }
            
            if (categories.length === 0) {
                alert('Please select at least one category');
                return;
            }
            
            // Show loading state
            previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Preview...';
            previewBtn.disabled = true;
            
            // Create FormData
            const formData = new FormData();
            formData.append('postTitle', postTitle);
            formData.append('postContent', postContent);
            formData.append('author', author);
            formData.append('visibility', visibility);
            formData.append('tags', tags);
            formData.append('publishTime', publishTime);
            formData.append('metaTitle', metaTitle);
            formData.append('metaDescription', metaDescription);
            formData.append('focusKeywords', focusKeywords);
            formData.append('urlSlug', urlSlug);
            
            // Add categories
            categories.forEach(function(cat) {
                formData.append('categories[]', cat);
            });
            
            // Add featured image if exists
            if (featuredImageFile) {
                formData.append('featureImage', featuredImageFile);
            }
            
            //  FIXED: Proper URL construction without duplication
            // Get the origin (https://hi.flypped.com)
            const origin = window.location.origin;
            
            // Get the pathname (/admin/add_post)
            const pathname = window.location.pathname;
            
            // Extract admin base path
            // If pathname is /admin/add_post, adminBasePath should be /admin
            let adminBasePath = '/admin';
            
            // Check if current path contains /admin/
            if (pathname.includes('/admin/')) {
                const adminIndex = pathname.indexOf('/admin/');
                adminBasePath = pathname.substring(0, adminIndex + 6); // +6 to include '/admin'
            }
            
            // Remove trailing slash if exists
            adminBasePath = adminBasePath.replace(/\/$/, '');
            
            // Construct the preview API URL
            const previewApiUrl = origin + adminBasePath + '/create-preview';
            
            console.log('Origin:', origin);
            console.log('Pathname:', pathname);
            console.log('Admin Base Path:', adminBasePath);
            console.log('Preview API URL:', previewApiUrl);
            
            // Send AJAX request to create preview
            fetch(previewApiUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                
                return response.json();
            })
            .then(data => {
                // Reset button
                previewBtn.innerHTML = 'Preview';
                previewBtn.disabled = false;
                
                console.log('Response data:', data);
                
                if (data.success) {
                    console.log('Opening preview URL:', data.preview_url);
                    
                    //  Open preview in new tab
                    const previewWindow = window.open(data.preview_url, '_blank', 'noopener,noreferrer');
                    
                    if (!previewWindow) {
                        alert('Preview created! Please allow popups.\n\nPreview URL:\n' + data.preview_url);
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to create preview'));
                }
            })
            .catch(error => {
                console.error('Preview Error:', error);
                previewBtn.innerHTML = 'Preview';
                previewBtn.disabled = false;
                alert('Failed to generate preview.\n\nError: ' + error.message);
            });
        });
    }
});
</script>

<style>
    .faq-item-card {
  border: 1px solid #dee2e6;
  border-left: 4px solid #6f42c1;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 12px;
  background: #fafafa;
  transition: border-left-color 0.2s, box-shadow 0.2s;
  position: relative;
}
.faq-item-card:hover {
  border-left-color: #4e2d8a;
  box-shadow: 0 2px 8px rgba(111,66,193,0.1);
}
.faq-item-card .faq-label {
  font-size: 12px;
  font-weight: 600;
  color: #6f42c1;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 10px;
}
.faq-item-card .remove-faq-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  color: #dc3545;
  font-size: 16px;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
  transition: transform 0.15s;
}
.faq-item-card .remove-faq-btn:hover { transform: scale(1.2); }
.faq-ans-counter {
  font-size: 11px;
  color: #aaa;
  text-align: right;
  margin-top: 3px;
}
.faq-ans-counter.warn  { color: #e6a817; }
.faq-ans-counter.limit { color: #dc3545; }
.faq-empty-state {
  text-align: center;
  padding: 24px 16px;
  color: #aaa;
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  margin-bottom: 12px;
}
.faq-empty-state i { font-size: 28px; margin-bottom: 8px; display: block; }

/* SEO Plugin Styles */
.seo-dashboard {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 15px;
}

.seo-score-container {
  display: flex;
  align-items: center;
  gap: 15px;
}

.seo-score-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #dc3545;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 18px;
  color: white;
  transition: background-color 0.3s;
}

.seo-score-circle.good { background: #28a745; }
.seo-score-circle.fair { background: #ffc107; color: #000; }
.seo-score-circle.poor { background: #dc3545; }

.checklist-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 0;
  font-size: 14px;
}

.checklist-item i.fa-check { color: #28a745; }
.checklist-item i.fa-times { color: #dc3545; }

.keyword-density .density-bar {
  height: 6px;
  background: #e9ecef;
  border-radius: 3px;
  margin: 5px 0;
  overflow: hidden;
}

.keyword-density .density-fill {
  height: 100%;
  transition: width 0.3s;
}

.density-optimal { background: #28a745; }
.density-warning { background: #ffc107; }
.density-high { background: #dc3545; }

.google-preview .preview-container {
  border: 1px solid #e1e4e8;
  border-radius: 4px;
  padding: 12px;
  background: white;
}

.preview-title {
  color: #1a0dab;
  font-size: 16px;
  margin-bottom: 4px;
  cursor: pointer;
}

.preview-url {
  color: #006621;
  font-size: 13px;
  margin-bottom: 6px;
}

.preview-description {
  color: #4d5156;
  font-size: 13px;
  line-height: 1.4;
}

.char-counter {
  font-size: 12px;
  margin-top: 5px;
  color: #6c757d;
}

.char-counter.warning { color: #ffc107; }
.char-counter.danger { color: #dc3545; }
.char-counter.success { color: #28a745; }
</style>

<!-- ── FAQ JavaScript ── -->
<script>
(function () {
  const MAX_FAQS    = 10;
  const container   = document.getElementById('faqContainer');
  const addBtn      = document.getElementById('addFaqBtn');
  const badge       = document.getElementById('faqCountBadge');
  const limitMsg    = document.getElementById('faqLimitMsg');
  const previewBox  = document.getElementById('faqSchemaPreviewBox');
  const previewPre  = document.getElementById('schemaPreviewContent');
  const toggleBtn   = document.getElementById('toggleSchemaBtn');
  const previewWrap = document.getElementById('schemaPreviewWrapper');

  

  // ── Count current FAQ cards ────────────────────────────────
  function faqCount() {
    return container.querySelectorAll('.faq-item-card').length;
  }

  // ── Update badge + limit message ───────────────────────────
  function updateUI() {
    const count = faqCount();
    badge.textContent = count + (count === 1 ? ' FAQ' : ' FAQs');
    addBtn.disabled   = count >= MAX_FAQS;
    limitMsg.textContent = count >= MAX_FAQS
      ? '✓ Maximum 10 FAQs reached'
      : (count > 0 ? (MAX_FAQS - count) + ' more allowed' : '');
    updateSchemaPreview();
  }

  // ── Build one FAQ card ─────────────────────────────────────
  function buildCard(index) {
    const card = document.createElement('div');
    card.className   = 'faq-item-card';
    card.dataset.idx = index;
    card.innerHTML   = `
      <div class="faq-label">FAQ #<span class="faq-num">${index + 1}</span></div>
      <button type="button" class="remove-faq-btn" title="Remove this FAQ">
        <i class="fas fa-times-circle"></i>
      </button>

      <div class="form-group mb-2">
        <label class="small font-weight-semibold mb-1">
          Question <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control form-control-sm faq-q"
               name="faq_questions[]"
               placeholder="e.g. What is IPL 2026?"
               maxlength="300"
               autocomplete="off">
      </div>

      <div class="form-group mb-0">
        <label class="small font-weight-semibold mb-1">
          Answer <span class="text-danger">*</span>
        </label>
        <textarea class="form-control form-control-sm faq-a"
                  name="faq_answers[]"
                  rows="3"
                  placeholder="Write a clear, helpful answer..."
                  maxlength="2000"></textarea>
        <div class="faq-ans-counter">0 / 2000</div>
      </div>`;

    // Remove button
    card.querySelector('.remove-faq-btn').addEventListener('click', function () {
      card.style.transition = 'opacity 0.2s';
      card.style.opacity    = '0';
      setTimeout(function () {
        card.remove();
        renumberCards();
        if (faqCount() === 0) renderEmptyState();
        updateUI();
      }, 200);
    });

    // Answer char counter
    const textarea = card.querySelector('.faq-a');
    const counter  = card.querySelector('.faq-ans-counter');
    textarea.addEventListener('input', function () {
      const len = this.value.length;
      counter.textContent  = len + ' / 2000';
      counter.className    = 'faq-ans-counter' +
        (len > 1800 ? ' limit' : len > 1400 ? ' warn' : '');
      updateSchemaPreview();
    });

    // Question live preview update
    card.querySelector('.faq-q').addEventListener('input', updateSchemaPreview);

    return card;
  }

  // ── Renumber all cards after remove ───────────────────────
  function renumberCards() {
    container.querySelectorAll('.faq-item-card').forEach(function (card, i) {
      card.dataset.idx = i;
      card.querySelector('.faq-num').textContent = i + 1;
    });
  }

  // ── Add FAQ button ─────────────────────────────────────────
  addBtn.addEventListener('click', function () {
    if (faqCount() >= MAX_FAQS) return;

    // Remove empty state if present
    const empty = document.getElementById('faqEmptyState');
    if (empty) empty.remove();

    const card = buildCard(faqCount());
    container.appendChild(card);

    // Smooth scroll + focus
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    setTimeout(function () { card.querySelector('.faq-q').focus(); }, 100);

    updateUI();
  });

  // ── Schema Preview ─────────────────────────────────────────
  function updateSchemaPreview() {
    const qInputs = container.querySelectorAll('.faq-q');
    const aInputs = container.querySelectorAll('.faq-a');
    const entities = [];

    qInputs.forEach(function (q, i) {
      const qVal = (q.value || '').trim();
      const aVal = (aInputs[i] ? aInputs[i].value : '').trim();
      if (qVal && aVal) {
        entities.push({
          "@type": "Question",
          "name": qVal,
          "acceptedAnswer": { "@type": "Answer", "text": aVal }
        });
      }
    });

    if (entities.length === 0) {
      previewBox.style.display = 'none';
      return;
    }

    previewBox.style.display = 'block';
    previewPre.textContent = JSON.stringify({
      "@context"   : "https://schema.org",
      "@type"      : "FAQPage",
      "mainEntity" : entities
    }, null, 2);
  }

  // ── Toggle schema preview visibility ──────────────────────
  toggleBtn.addEventListener('click', function () {
    const hidden = previewWrap.style.display === 'none';
    previewWrap.style.display  = hidden ? 'block' : 'none';
    toggleBtn.textContent      = hidden ? 'Hide' : 'Show';
  });

  // ── Init ───────────────────────────────────────────────────
  // Note: renderEmptyState removed for brevity if missing, handled safely
  updateUI();

})();
</script>

<script>
// Enhanced SEO Plugin JavaScript (Non-blocking)
document.addEventListener('DOMContentLoaded', function() {
    const seoElements = {
        focusKeywords: document.getElementById('focusKeywords'),
        metaDescription: document.getElementById('metaDescription'),
        metaTitle: document.getElementById('metaTitle'),
        urlSlug: document.getElementById('urlSlug'),
        postTitle: document.getElementById('postTitle'),
        metaCounter: document.getElementById('metaCounter'),
        titleCounter: document.getElementById('titleCounter'),
        seoScoreCircle: document.getElementById('seoScoreCircle'),
        seoScoreMessage: document.getElementById('seoScoreMessage'),
        wordCount: document.getElementById('wordCount')
    };

    let seoData = {
        keywords: [],
        title: '',
        metaTitle: '',
        content: '',
        meta: '',
        slug: ''
    };

    let editorInstance = null;

    // Wait for CKEditor to be ready (more patient waiting)
    let editorCheckAttempts = 0;
    const maxEditorChecks = 20;
    
    function checkForEditor() {
        if (typeof window.editorInstance !== 'undefined') {
            editorInstance = window.editorInstance;
            editorInstance.model.document.on('change:data', updateSEOAnalysis);
            console.log('SEO Plugin: CKEditor connected successfully');
            return;
        }
        
        editorCheckAttempts++;
        if (editorCheckAttempts < maxEditorChecks) {
            setTimeout(checkForEditor, 1000);
        } else {
            console.log('SEO Plugin: CKEditor not found, continuing without editor integration');
        }
    }
    
    setTimeout(checkForEditor, 2000);

    // Auto-generate meta title from blog title
    seoElements.postTitle.addEventListener('input', function() {
        const title = this.value;
        
        // Auto-generate slug if empty
        if (!seoElements.urlSlug.value) {
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .trim();
            seoElements.urlSlug.value = slug;
        }
        
        // Auto-generate meta title if empty
        if (!seoElements.metaTitle.value) {
            seoElements.metaTitle.value = title;
            updateTitleCounter();
        }
        
        updateSEOAnalysis();
    });

    // Meta title counter
    seoElements.metaTitle.addEventListener('input', function() {
        updateTitleCounter();
        updateSEOAnalysis();
    });

    function updateTitleCounter() {
        const length = seoElements.metaTitle.value.length;
        const counter = seoElements.titleCounter;
        counter.textContent = `${length}/65 characters`;
        
        if (length < 30) {
            counter.className = 'char-counter warning';
        } else if (length > 65) {
            counter.className = 'char-counter danger';
        } else {
            counter.className = 'char-counter success';
        }
    }

    // Meta description counter
    seoElements.metaDescription.addEventListener('input', function() {
        const length = this.value.length;
        const counter = seoElements.metaCounter;
        counter.textContent = `${length}/200 characters`;
        
        if (length < 120) {
            counter.className = 'char-counter warning';
        } else if (length > 200) {
            counter.className = 'char-counter danger';
        } else {
            counter.className = 'char-counter success';
        }
        updateSEOAnalysis();
    });

    // Focus keywords analysis
    seoElements.focusKeywords.addEventListener('input', function() {
        seoData.keywords = this.value.split(',').map(k => k.trim()).filter(k => k);
        updateSEOAnalysis();
    });

    // URL slug update
    seoElements.urlSlug.addEventListener('input', updateSEOAnalysis);

    function updateSEOAnalysis() {
        seoData.title = seoElements.postTitle.value;
        seoData.metaTitle = seoElements.metaTitle.value;
        seoData.content = getEditorContent();
        seoData.meta = seoElements.metaDescription.value;
        seoData.slug = seoElements.urlSlug.value;

        // Update word count
        const wordCount = countWords(seoData.content);
        seoElements.wordCount.textContent = wordCount;

        // Update Google preview
        updateGooglePreview();
        
        // Update SEO checks
        updateSEOChecks();
        
        // Update keyword density
        updateKeywordDensity();
        
        // Calculate and update SEO score
        updateSEOScore();
    }

    function getEditorContent() {
        if (editorInstance) {
            return editorInstance.getData();
        }
        return '';
    }

    function countWords(content) {
        const plainText = content.replace(/<[^>]*>/g, '').trim();
        if (!plainText) return 0;
        return plainText.split(/\s+/).filter(word => word.length > 0).length;
    }

    function updateGooglePreview() {
        document.getElementById('previewTitle').textContent = seoData.metaTitle || seoData.title || 'Your Blog Title Will Appear Here';
        document.getElementById('previewUrl').textContent = `<?= base_url() ?>/${seoData.slug || 'your-slug'}`;
        document.getElementById('previewDescription').textContent = seoData.meta || 'Your meta description will appear here...';
    }

    function updateSEOChecks() {
        const checks = {
            titleKeyword: checkKeywordInText(seoData.title, seoData.keywords),
            metaKeyword: checkKeywordInText(seoData.meta, seoData.keywords),
            contentLength: checkContentLength(seoData.content),
            firstParagraph: checkFirstParagraph(seoData.content, seoData.keywords),
            headingStructure: checkHeadingStructure(seoData.content),
            imageAlt: checkImageAlt(seoData.content, seoData.keywords)
        };

        Object.keys(checks).forEach(checkId => {
            const element = document.getElementById(checkId);
            if (element) {
                const icon = element.querySelector('i');
                if (checks[checkId]) {
                    icon.className = 'fas fa-check text-success';
                } else {
                    icon.className = 'fas fa-times text-danger';
                }
            }
        });
    }

    function checkKeywordInText(text, keywords) {
        if (!keywords.length || !text) return false;
        return keywords.some(keyword => 
            text.toLowerCase().includes(keyword.toLowerCase())
        );
    }

    function checkContentLength(content) {
        const wordCount = countWords(content);
        return wordCount >= 300;
    }

    function checkFirstParagraph(content, keywords) {
        if (!keywords.length || !content) return false;
        
        let firstParagraph = '';
        const pMatch = content.match(/<p[^>]*>(.*?)<\/p>/i);
        if (pMatch) {
            firstParagraph = pMatch[1];
        } else {
            const plainText = content.replace(/<[^>]*>/g, '').trim();
            firstParagraph = plainText.substring(0, 200);
        }
        
        firstParagraph = firstParagraph.replace(/<[^>]*>/g, '');
        return checkKeywordInText(firstParagraph, keywords);
    }

    function checkHeadingStructure(content) {
        if (!content) return false;
        const headingPattern = /<h[2-6][^>]*>.*?<\/h[2-6]>/i;
        return headingPattern.test(content);
    }

    function checkImageAlt(content, keywords) {
        if (!content) return true;
        const images = content.match(/<img[^>]*>/gi) || [];
        if (!images.length) return true;
        
        return images.some(img => {
            const altMatch = img.match(/alt\s*=\s*["']([^"']*)["']/i);
            if (!altMatch) return false;
            return checkKeywordInText(altMatch[1], keywords);
        });
    }

    function updateKeywordDensity() {
        const container = document.getElementById('densityAnalysis');
        if (!seoData.keywords.length || !seoData.content) {
            container.innerHTML = 'Add focus keywords to see analysis';
            return;
        }

        const plainText = seoData.content.replace(/<[^>]*>/g, '');
        const wordCount = countWords(plainText);
        
        if (wordCount === 0) {
            container.innerHTML = 'Add content to see keyword density';
            return;
        }
        
        let densityHTML = '';
        seoData.keywords.forEach(keyword => {
            const regex = new RegExp(keyword.trim(), 'gi');
            const matches = (plainText.match(regex) || []).length;
            const density = ((matches / wordCount) * 100).toFixed(1);
            
            let status = 'density-high';
            let statusText = 'Too high';
            if (density >= 1 && density <= 3) {
                status = 'density-optimal';
                statusText = 'Optimal';
            } else if (density > 0.5 && density < 1) {
                status = 'density-warning';
                statusText = 'Low';
            } else if (density > 3) {
                statusText = 'Too high';
            } else {
                statusText = 'Very low';
            }
            
            densityHTML += `
                <div style="margin: 8px 0;">
                    <small><strong>${keyword}:</strong> ${density}% (${matches} times) - ${statusText}</small>
                    <div class="density-bar">
                        <div class="density-fill ${status}" style="width: ${Math.min(density * 20, 100)}%"></div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = densityHTML;
    }

    function updateSEOScore() {
        let score = 0;
        const maxScore = 6;
        
        const checks = [
            checkKeywordInText(seoData.title, seoData.keywords),
            checkKeywordInText(seoData.meta, seoData.keywords),
            checkContentLength(seoData.content),
            checkFirstParagraph(seoData.content, seoData.keywords),
            checkHeadingStructure(seoData.content),
            checkImageAlt(seoData.content, seoData.keywords)
        ];
        
        score = checks.filter(Boolean).length;
        const percentage = Math.round((score / maxScore) * 100);
        
        const circle = seoElements.seoScoreCircle;
        const message = seoElements.seoScoreMessage;
        
        circle.textContent = percentage;
        
        if (percentage >= 80) {
            circle.className = 'seo-score-circle good';
            message.textContent = 'Excellent SEO optimization!';
        } else if (percentage >= 60) {
            circle.className = 'seo-score-circle fair';
            message.textContent = 'Good, but can be improved';
        } else {
            circle.className = 'seo-score-circle poor';
            message.textContent = 'Needs SEO improvement';
        }
    }

    // Initialize analysis
    updateSEOAnalysis();
    
    // Set up periodic content checking for CKEditor
    setInterval(() => {
        if (editorInstance) {
            updateSEOAnalysis();
        }
    }, 3000);
});
</script>