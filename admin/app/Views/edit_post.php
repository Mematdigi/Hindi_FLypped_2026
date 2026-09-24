<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

  <!-- ===== Add the form with the appropriate method and action ===== -->
  <form method="post" action="<?= base_url('update_post/' . $post->ID) ?>" enctype="multipart/form-data" id="editPostForm">
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">
          <!-- Blog Content Section (col-8) -->
          <div class="col-lg-8" style="height: 100vh; overflow-y: auto;">
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header BlogHeader text-white">
                <h4 class="mb-0">Edit Post</h4>
              </div>
              <div class="card-body">
                <!-- Blog Title -->
                <div class="form-group mb-4">
                  <label for="postTitle">Blog Title:</label>
                  <input type="text" class="form-control" id="postTitle" name="post_title" value="<?php echo esc($post->post_title); ?>" required>
                  <small class="text-muted">The main title of your post</small>
                </div>

                <!-- Blog Content (CKEditor) -->
                <div class="form-group mb-4">
                  <label for="postContent">Blog Content:</label>
                  <textarea name="post_content" id="editor"><?php echo $post->post_content; ?></textarea>
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
                <input type="text" class="form-control" id="metaTitle" name="seo_title" 
                        placeholder="SEO optimized title for search engines" maxlength="60" value="<?php echo !empty($seo_data['title']) ? esc($seo_data['title']) : esc($post->post_title); ?>">
                <div class="char-counter" id="titleCounter">0/60 characters</div>
                <small class="form-text text-muted">Auto-generated from blog title. Recommended: 50-60 characters</small>
                </div>

                <!-- Focus Keywords -->
                <div class="form-group mb-3">
                <label for="focusKeywords">Focus Keywords (1-5 keywords)</label>
                <input type="text" class="form-control" id="focusKeywords" name="focus_keywords" 
                        placeholder="Enter 1-5 focus keywords, separated by commas" value="<?php echo !empty($seo_data['keywords']) ? esc($seo_data['keywords']) : ''; ?>">
                <small class="form-text text-muted">These keywords will be analyzed throughout your content</small>
                </div>

                <!-- Meta Description -->
                <div class="form-group mb-3">
                <label for="metaDescription">Meta Description</label>
                <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" 
                            placeholder="Enter meta description (120-160 characters recommended)" maxlength="160"><?php 
                            if (!empty($seo_data['description'])) {
                                echo esc($seo_data['description']);
                            } else {
                                $content = strip_tags($post->post_content);
                                echo esc(mb_substr($content, 0, 150));
                            }
                        ?></textarea>
                <div class="char-counter" id="metaCounter">0/160 characters</div>
                </div>

                <!-- URL Slug -->
                <div class="form-group mb-3">
                <label for="urlSlug">URL Slug</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text">https://flyppedhindi.com/</span>
                    </div>
                    <input type="text" class="form-control" id="urlSlug" name="post_slug" placeholder="auto-generated-from-title" value="<?php echo !empty($seo_data['slug']) ? esc($seo_data['slug']) : esc($post->post_name); ?>">
                </div>
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
                <div class="seo-checklist" id="seoChecklist">
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
                    <div class="preview-url" id="previewUrl">https://flyppedhindi.com/<?php echo !empty($seo_data['slug']) ? esc($seo_data['slug']) : esc($post->post_name); ?></div>
                    <div class="preview-description" id="previewDescription">Your meta description will appear here...</div>
                    </div>
                </div>
                </div>

            </div>
            </div>

            <!-- Edit FAQ Schema Section -->
            <div class="card shadow-sm mb-4 mt-4" style="border-radius: 10px;">
                <div class="card-header BlogHeader text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa fa-question-circle"></i> Edit FAQ Schema</h4>
                    <button type="button" class="btn btn-sm btn-light text-primary" id="addFaqBtn">
                        <i class="fa fa-plus"></i> Add Question
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Update existing FAQs or add new ones for this post.</p>
                    
                    <div id="faqItemsContainer">
                        <?php if (!empty($existing_faqs)): ?>
                            <?php foreach ($existing_faqs as $faq): ?>
                                <div class="faq-item mb-3 p-3 border rounded bg-light position-relative">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px;" onclick="this.parentElement.remove()">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Question</label>
                                        <input type="text" class="form-control" name="faq_questions[]" value="<?php echo esc(trim($faq['question'])); ?>" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Answer</label>
                                        <textarea class="form-control" name="faq_answers[]" rows="2" required><?php echo esc(trim($faq['answer'])); ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="faq-item mb-3 p-3 border rounded bg-light position-relative">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px;" onclick="this.parentElement.remove()">
                                    <i class="fa fa-times"></i>
                                </button>
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Question</label>
                                    <input type="text" class="form-control" name="faq_questions[]" placeholder="e.g., What is SEO?" required>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold">Answer</label>
                                    <textarea class="form-control" name="faq_answers[]" rows="2" placeholder="Enter the answer..." required></textarea>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

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
    return container ? container.querySelectorAll('.faq-item-card').length : 0;
  }

  // ── Update badge + limit message ───────────────────────────
  function updateUI() {
    if (!badge) return;
    const count = faqCount();
    badge.textContent = count + (count === 1 ? ' FAQ' : ' FAQs');
    if(addBtn) addBtn.disabled   = count >= MAX_FAQS;
    if(limitMsg) limitMsg.textContent = count >= MAX_FAQS
      ? '✓ Maximum 10 FAQs reached'
      : (count > 0 ? (MAX_FAQS - count) + ' more allowed' : '');
    updateSchemaPreview();
  }

  // ── Schema Preview ─────────────────────────────────────────
  function updateSchemaPreview() {
    if (!container || !previewBox) return;
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
    if(previewPre) previewPre.textContent = JSON.stringify({
      "@context"   : "https://schema.org",
      "@type"      : "FAQPage",
      "mainEntity" : entities
    }, null, 2);
  }

  if(toggleBtn && previewWrap) {
      toggleBtn.addEventListener('click', function () {
        const hidden = previewWrap.style.display === 'none';
        previewWrap.style.display  = hidden ? 'block' : 'none';
        toggleBtn.textContent      = hidden ? 'Hide' : 'Show';
      });
  }

  updateUI();

})();
</script>

          </div>

          <!-- Post Settings Section (col-4) -->
          <div class="col-lg-4">

            <!-- Edit Post Date & Time Section (Added for IST Local Time) -->
            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header text-white" style="background-color: #9258d4; border-radius: 10px 10px 0 0;">
                <h4 class="mb-0" style="font-size: 16px; font-weight: 600;">Edit Post Date & Time</h4>
              </div>
              <div class="card-body">
                <div class="form-group mb-0">
                    <label for="post_date_edit" style="font-weight: 500; font-size: 15px; margin-bottom: 10px;">Publish Date & Time (Edit Here):</label>
                    <input type="datetime-local" class="form-control" name="post_date" id="post_date_edit" form="editPostForm" value="<?php 
                        // Using IST timezone as requested
                        $timezone = new DateTimeZone('Asia/Kolkata');
                        
                        if (!empty($post->post_date) && $post->post_date !== '0000-00-00 00:00:00') {
                            // Load existing DB time and format it
                            $dt = new DateTime($post->post_date);
                            $dt->setTimezone($timezone);
                            echo $dt->format('Y-m-d\TH:i');
                        } else {
                            // If empty, initialize to the current IST local time
                            $dt = new DateTime('now', $timezone);
                            echo $dt->format('Y-m-d\TH:i');
                        }
                    ?>" style="padding: 10px; border-radius: 5px; border: 1px solid #ced4da; box-shadow: none;">
                </div>
              </div>
            </div>

            <div class="card shadow-sm mb-4" style="border-radius: 10px;">
              <div class="card-header BlogHeader text-white" style="background-color: #9258d4; border-radius: 10px 10px 0 0;">
                <h4 class="mb-0" style="font-size: 16px; font-weight: 600;">Post Settings</h4>
              </div>
             <div class="card-body">
    
                <!-- Visibility Settings -->
                <div class="form-group mb-3">
                    <label for="post_status" style="font-weight: 600; color: #495057; font-size: 15px;">Visibility:</label>
                    <select class="form-control" name="post_status" id="post_status" form="editPostForm">
                        <option value="publish" <?= ($post->post_status == 'publish') ? 'selected' : ''; ?>>
                            Publish - Visible to everyone immediately
                        </option>
                        <option value="draft" <?= ($post->post_status == 'draft') ? 'selected' : ''; ?>>
                            Draft - Save as draft (not visible to public)
                        </option>
                        <option value="future" <?= ($post->post_status == 'future') ? 'selected' : ''; ?>>
                            Scheduled - Publish at a future date
                        </option>
                    </select>
                </div>

                <div id="scheduledDateTime" style="display: <?= ($post->post_status == 'future') ? 'block' : 'none'; ?>;">
                    <div class="form-group">
                        <label for="schedule_time">Schedule Date & Time:</label>
                        <input type="datetime-local"
                                class="form-control"
                                name="schedule_time"
                                id="schedule_time"
                                value="<?php echo date('Y-m-d\TH:i', strtotime($post->post_date)); ?>"
                                form="editPostForm">
                        <small class="text-muted">
                            Post will be automatically published at this time
                        </small>
                    </div>
                </div>

                <!-- Author Dropdown -->
                <div class="form-group mb-3 mt-3">
                    <label for="post_author" style="font-weight: 600; color: #495057; font-size: 15px;">Author:</label>
                    <select class="form-control" name="post_author" id="post_author" form="editPostForm">
                        <?php foreach ($authors as $author): ?>
                            <option value="<?php echo $author->ID; ?>"<?php echo($author->ID == $post->post_author) ? 'selected' : ''; ?>>
                                <?php echo esc($author->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
    
                <!-- Categories Checkboxes -->
                <div class="form-group mb-3 mt-3">
                  <label style="font-weight: 600; color: #495057; font-size: 15px;">Categories</label>
                  <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <?php foreach ($categories as $category): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category->term_id; ?>" id="category_<?php echo $category->term_id; ?>" <?php echo in_array($category->term_id, $selected_categories) ? 'checked' : ''; ?> form="editPostForm">
                            <label class="form-check-label" for="category_<?php echo $category->term_id; ?>">
                                <?php echo esc($category->name); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Tags Input -->
               <div class="form-group mb-3 mt-3">
                    <label for="post_tags" style="font-weight: 600; color: #495057; font-size: 15px;">Tags</label>
                    <input type="text" 
                          class="form-control" 
                          id="post_tags" 
                          name="tags" 
                          value="<?php echo esc(implode(', ', array_column($tags, 'name'))); ?>"
                          placeholder="Add tags, separated by commas"
                          form="editPostForm">
                </div>
                <div id="tagPreview" class="mt-2"></div>


                <!-- Feature Image Upload -->
                <div class="form-group mb-3 mt-3">
                  <label for="featured_image" style="font-weight: 600; color: #495057; font-size: 15px;">Featured Image</label>
                  <input type="file" class="form-control-file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" form="editPostForm">
                </div>

                <?php if (!empty($post->featured_image_url)): ?>
                    <div class="form-group" id="currentFeaturedImageWrap">
                        <label>Current Featured Image:</label><br>
                        <div style="position: relative; display: inline-block;">
                            <img src="<?php echo esc($post->featured_image_url); ?>" 
                                 alt="Featured Image" 
                                 id="currentFeaturedImage"
                                 style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #ddd; display: block;">
                            <button type="button"
                                    onclick="deleteFeaturedImage(<?php echo (int)$post->ID; ?>)"
                                    style="position:absolute;top:-10px;right:-10px;width:26px;height:26px;border-radius:50%;background:#dc3545;border:none;color:white;font-size:16px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);"
                                    title="Remove featured image">&times;</button>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="imagePreview" style="display: none;" class="form-group">
                    <label>New Image Preview:</label><br>
                    <img id="previewImg" src="" alt="Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #28a745;">
                </div>

                <!-- Submit Button -->
                <div class="form-group mt-3">
                  <button type="submit" class="btn btn-success btn-block" id="publishPostBtn" form="editPostForm">Update Post</button>
                  <a href="<?php echo base_url('all-posts'); ?>" class="btn btn-secondary btn-block mt-2">Cancel</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </form>

<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>

<!-- Twitter Widgets Script -->
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<!--  CKEditor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize CKEditor - REMOVED SourceEditing and GeneralHtmlSupport
    const {
        ClassicEditor, Essentials, Bold, Italic, Heading, Paragraph, List, Font, Image, ImageToolbar, ImageUpload, ImageCaption, ImageResize, SimpleUploadAdapter, Link, MediaEmbed, Table, TableToolbar, TableProperties, TableCellProperties
    } = CKEDITOR;
    
    let editorInstance;
    
    ClassicEditor.create(document.querySelector('#editor'), {
        plugins: [
            Essentials, Bold, Italic, Heading, Paragraph, List, Font,
            Image, ImageToolbar, ImageUpload, ImageCaption, ImageResize, SimpleUploadAdapter,
            Link, MediaEmbed,
            Table, TableToolbar, TableProperties, TableCellProperties
        ],
        
        toolbar: {
            items: [
                'undo', 'redo', '|', 'heading', '|', 'bold', 'italic', 'strikethrough', 'underline',
                '|', 'bulletedList', 'numberedList', 'todoList', '|', 'alignment',
                '|', 'link', 'blockQuote', 'insertTable', 'mediaEmbed',
                '|', 'imageUpload', '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor'
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn', 'tableRow', 'mergeTableCells',
                'tableProperties', 'tableCellProperties'
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
        image: {
            toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side'],
            styles: ['full', 'side']
        },
        mediaEmbed: {
            previewsInData: true,
            providers: [
                {
                    name: 'youtube',
                    url: [
                        /^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)/,
                        /^(?:m\.)?youtube\.com\/v\/([\w-]+)/,
                        /^youtube\.com\/embed\/([\w-]+)/,
                        /^youtu\.be\/([\w-]+)/,
                        /^(?:m\.)?youtube\.com\/shorts\/([\w-]+)/
                    ],
                    html: match => {
                        const id = match[1];
                        return (
                            '<div style="position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;">' +
                                `<iframe src="https://www.youtube.com/embed/${id}" ` +
                                    'style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;" ' +
                                    'frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>' +
                                '</iframe>' +
                            '</div>'
                        );
                    }
                }
            ]
        },
        simpleUpload: {
            // 🔥 FIXED: Directing to the CodeIgniter route that saves as attachment!
            uploadUrl: '<?= base_url('upload-editor-image') ?>',
            headers: {}
        }
    })
    .then(editor => {
        editorInstance = editor;
        window.editorInstance = editor;
        
        editor.model.document.on('change:data', () => {
            if (typeof updateSEOAnalysis === 'function') {
                updateSEOAnalysis();
            }
        });
        
        const editForm = document.getElementById('editPostForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                document.querySelector('#editor').value = editor.getData();
            });
        }
        
        console.log('CKEditor initialized successfully');
    })
    .catch(error => console.error('Editor initialization error:', error));
});

// Image preview functionality
document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            this.value = '';
            previewDiv.style.display = 'none';
            return;
        }
        
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, GIF, or WebP)');
            this.value = '';
            previewDiv.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewDiv.style.display = 'none';
    }
});

// Post Status / Schedule functionality
document.getElementById('post_status')?.addEventListener('change', function() {
    const scheduledDiv = document.getElementById('scheduledDateTime');
    const scheduleTimeInput = document.getElementById('schedule_time');
    
    if (this.value === 'future') {
        scheduledDiv.style.display = 'block';
        scheduleTimeInput.required = true;
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        scheduleTimeInput.min = now.toISOString().slice(0, 16);
    } else {
        scheduledDiv.style.display = 'none';
        scheduleTimeInput.required = false;
        scheduleTimeInput.value = '';
        scheduleTimeInput.removeAttribute('min');
    }
});

// Initial load state for post status
window.addEventListener('DOMContentLoaded', function() {
    const postStatusEl = document.getElementById('post_status');
    const scheduledDiv = document.getElementById('scheduledDateTime');
    const scheduleTimeInput = document.getElementById('schedule_time');
    
    if(postStatusEl) {
        if (postStatusEl.value === 'future') {
            if(scheduledDiv) scheduledDiv.style.display = 'block';
            if(scheduleTimeInput) scheduleTimeInput.required = true;
        } else {
            if(scheduledDiv) scheduledDiv.style.display = 'none';
            if(scheduleTimeInput) scheduleTimeInput.required = false;
        }
    }
});

// Delete featured image via × button
function deleteFeaturedImage(postId) {
    if (!confirm('Remove the featured image from this post?')) return;
    fetch('<?= base_url('delete-featured-image'); ?>/' + postId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('currentFeaturedImageWrap').innerHTML =
                '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No featured image set for this post</div>';
            document.getElementById('imagePreview').style.display = 'none';
        } else {
            alert('Failed to remove image. Please try again.');
        }
    })
    .catch(() => alert('Network error. Please try again.'));
}
</script>

<script>
// ==========================================
// SEO OPTIMIZATION JAVASCRIPT
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const seoTitle = document.getElementById('metaTitle');
    const metaDescription = document.getElementById('metaDescription');
    const focusKeywords = document.getElementById('focusKeywords');
    const postSlug = document.getElementById('urlSlug');
    const postTitle = document.getElementById('postTitle');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewUrl = document.getElementById('previewUrl');
    const previewDescription = document.getElementById('previewDescription');
    const slugPreview = document.getElementById('slugPreview');
    
    const titleCounter = document.getElementById('titleCounter');
    const titleProgress = document.getElementById('titleProgress');
    const descriptionCounter = document.getElementById('metaCounter');
    const descriptionProgress = document.getElementById('descriptionProgress');
    
    function updateTitleCounter() {
        if (!seoTitle || !titleCounter) return;
        const length = seoTitle.value.length;
        titleCounter.textContent = length + '/60 characters';
        updatePreview();
    }
    
    function updateDescriptionCounter() {
        if (!metaDescription || !descriptionCounter) return;
        const length = metaDescription.value.length;
        descriptionCounter.textContent = length + '/160 characters';
        updatePreview();
    }
    
    function updateSlugPreview() {
        if (!postSlug || !previewUrl) return;
        const baseUrl = 'https://flyppedhindi.com/';
        const slugValue = postSlug.value || 'auto-generated';
        previewUrl.textContent = baseUrl + slugValue;
        updatePreview();
    }
    
    function generateSlug(text) {
        return text.toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
    }
    
    function updatePreview() {
        if (previewTitle && seoTitle && postTitle) {
            previewTitle.textContent = seoTitle.value || postTitle.value || 'Your SEO title will appear here';
        }
        if (previewDescription && metaDescription) {
            previewDescription.textContent = metaDescription.value || 'Your meta description will appear here...';
        }
    }
    
    function calculateSEOScore() {
        let score = 0;
        const checklist = [];
        
        if(seoTitle && seoTitle.value.length >= 50 && seoTitle.value.length <= 60) score += 20;
        if(metaDescription && metaDescription.value.length >= 120 && metaDescription.value.length <= 160) score += 20;
        if(focusKeywords && focusKeywords.value.trim().length > 0) score += 20;
        if(postSlug && postSlug.value.length > 0) score += 20;
        
        if (window.editorInstance) {
            const content = window.editorInstance.getData();
            const wordCount = content.split(/\s+/).length;
            if (wordCount >= 300) score += 20;
        }
        
        const scoreElement = document.getElementById('seoScoreCircle');
        if(scoreElement) scoreElement.textContent = score;
    }
    
    if (seoTitle) seoTitle.addEventListener('input', () => { updateTitleCounter(); calculateSEOScore(); });
    if (metaDescription) metaDescription.addEventListener('input', () => { updateDescriptionCounter(); calculateSEOScore(); });
    if (focusKeywords) focusKeywords.addEventListener('input', calculateSEOScore);
    if (postSlug) postSlug.addEventListener('input', () => { updateSlugPreview(); calculateSEOScore(); });
    
    if (postTitle) {
        postTitle.addEventListener('blur', function() {
            if (postSlug && !postSlug.value) {
                postSlug.value = generateSlug(this.value);
                updateSlugPreview();
            }
            if (seoTitle && !seoTitle.value) {
                seoTitle.value = this.value.substring(0, 60);
                updateTitleCounter();
            }
            calculateSEOScore();
        });
    }
    
    updateTitleCounter();
    updateDescriptionCounter();
    updateSlugPreview();
    calculateSEOScore();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('faqItemsContainer');
    const addBtn = document.getElementById('addFaqBtn');

    if(addBtn && container) {
        addBtn.addEventListener('click', function() {
            const itemHtml = `
                <div class="faq-item mb-3 p-3 border rounded bg-light position-relative">
                    <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px;" onclick="this.parentElement.remove()">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Question</label>
                        <input type="text" class="form-control" name="faq_questions[]" placeholder="e.g., What is SEO?" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Answer</label>
                        <textarea class="form-control" name="faq_answers[]" rows="2" placeholder="Enter the answer..." required></textarea>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }
});
</script>