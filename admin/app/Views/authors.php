<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

<div class="main-panel">
  <div class="content-wrapper">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row">
      <!-- Author List -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white d-flex justify-content-between">
            <h5 class="mb-0">All Users</h5>
          </div>
          <div class="card-body table-responsive">
            <table class="table table-hover table-striped">
              <thead class="thead-dark">
                <tr>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Posts</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($authors)): ?>
                  <?php foreach($authors as $author): ?>
                    <?php 
                      $roleArr = @unserialize($author->role);
                      $role = (is_array($roleArr)) ? ucfirst(key($roleArr)) : 'N/A';
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($author->display_name); ?></td>
                      <td><?= htmlspecialchars($role); ?></td>
                      <td><?= $author->post_count; ?></td>
                      <td>
                        <!-- View -->
                        <a href="<?= site_url('admin/author/view-posts/'.$author->ID) ?>" 
                           class="text-primary mr-2" title="View Posts">
                          <i class="fa fa-eye"></i>
                        </a>
                        <!-- Edit -->
                        <a href="javascript:void(0)" 
                           class="text-warning mr-2 editUser" 
                           data-id="<?= $author->ID; ?>" 
                           data-name="<?= htmlspecialchars($author->display_name); ?>"
                           data-email="<?= htmlspecialchars($author->user_email); ?>"
                           data-role="<?= strtolower($role); ?>"
                           title="Edit User">
                          <i class="fa fa-pencil"></i>
                        </a>
                        <!-- Delete -->
                        <a href="<?= site_url('admin/author/delete/'.$author->ID) ?>" 
                           class="text-danger" 
                           onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" 
                           title="Delete User">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center">No users found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Add Author -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0">Add New User</h5>
          </div>
          <div class="card-body">
            <form action="<?= site_url('admin/author/create') ?>" method="post" enctype="multipart/form-data" id="createUserForm">

              <!-- Profile Image Upload -->
              <div class="text-center mb-4">
                <div class="position-relative d-inline-block">
                  <img id="profilePreview" 
                       src="<?= base_url('public/assets/images/faces/User_img.png'); ?>" 
                       class="rounded-circle" 
                       width="140" height="140" 
                       alt="Profile Image"
                       style="object-fit: cover; border: 3px solid #ddd;">

                  <input type="file" name="profile_image" id="profileInput" class="d-none" accept="image/*" onchange="previewImage(event)">

                  <label for="profileInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="cursor:pointer;">
                    <i class="fa fa-camera"></i>
                  </label>
                </div>
              </div>

              <!-- Row 1 -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label>First Name *</label>
                  <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label>Last Name</label>
                  <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>">
                </div>
              </div>

              <!-- Row 2 -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label>Email *</label>
                  <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label>Mobile Number</label>
                  <input type="text" name="mobile" class="form-control" value="<?= old('mobile') ?>" placeholder="+91 9876543210">
                </div>
              </div>

              <!-- Bio -->
              <div class="form-group mb-3">
                <label>Bio</label>
                <textarea name="bio" class="form-control" rows="3"><?= old('bio') ?></textarea>
              </div>

              <!-- Role -->
              <div class="form-group mb-3">
                <label>Role *</label>
                <select name="role" class="form-control" required>
                  <option value="">Select Role</option>
                  <option value="subscriber" <?= old('role') === 'subscriber' ? 'selected' : '' ?>>Subscriber</option>
                  <option value="contributor" <?= old('role') === 'contributor' ? 'selected' : '' ?>>Contributor</option>
                  <option value="author" <?= old('role') === 'author' ? 'selected' : '' ?>>Author</option>
                  <option value="editor" <?= old('role') === 'editor' ? 'selected' : '' ?>>Editor</option>
                  <option value="administrator" <?= old('role') === 'administrator' ? 'selected' : '' ?>>Administrator</option>
                  <option value="seo_editor" <?= old('role') === 'seo_editor' ? 'selected' : '' ?>>SEO Editor</option>
                  <option value="seo_manager" <?= old('role') === 'seo_manager' ? 'selected' : '' ?>>SEO Manager</option>
                </select>
              </div>

              <!-- Password -->
              <div class="form-group mb-3">
                <label>Password *</label>
                <div class="input-group">
                  <input type="password" name="password" id="newPassword" class="form-control" required minlength="6">
                  <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                    <i class="fa fa-eye"></i>
                  </button>
                </div>
                <small class="form-text text-muted">Minimum 6 characters</small>
              </div>

              <button type="submit" class="btn btn-success btn-block" id="submitBtn">
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                Save Changes
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Edit Modal ===== -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="editUserForm" method="post">
          <div class="modal-header bg-warning text-white">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="editUserId">

            <div class="form-group mb-3">
              <label>Name *</label>
              <input type="text" name="display_name" id="editName" class="form-control" required>
            </div>

            <div class="form-group mb-3">
              <label>Email *</label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>

            <div class="form-group mb-3">
              <label>Password</label>
              <div class="input-group">
                <input type="password" name="password" id="editPassword" class="form-control" placeholder="Leave blank to keep current password" minlength="6">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
              <small class="form-text text-muted">Leave blank to keep current password</small>
            </div>

            <div class="form-group mb-3">
              <label>Role *</label>
              <select name="role" id="editRole" class="form-control" required>
                <option value="subscriber">Subscriber</option>
                <option value="contributor">Contributor</option>
                <option value="author">Author</option>
                <option value="editor">Editor</option>
                <option value="administrator">Administrator</option>
                <option value="seo_editor">SEO Editor</option>
                <option value="seo_manager">SEO Manager</option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-warning" id="updateBtn">
              <span class="spinner-border spinner-border-sm d-none" role="status"></span>
              Save Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===== Footer Section ===== -->
  <?php include(APPPATH . 'Views/footer.php'); ?>

  <script>
  $(document).ready(function(){
    
    // Toggle password visibility for new user
    $("#toggleNewPassword").click(function(){
      let input = $("#newPassword");
      let type = input.attr("type") === "password" ? "text" : "password";
      input.attr("type", type);
      $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });

    // Toggle password visibility for edit user
    $("#togglePassword").click(function(){
      let input = $("#editPassword");
      let type = input.attr("type") === "password" ? "text" : "password";
      input.attr("type", type);
      $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });

    // Open edit modal with user data
    $(".editUser").click(function(){
      $("#editUserId").val($(this).data("id"));
      $("#editName").val($(this).data("name"));
      $("#editEmail").val($(this).data("email"));
      $("#editRole").val($(this).data("role"));
      $("#editPassword").val(""); // Reset password field
      $("#editUserModal").modal("show");
    });

    // Handle edit form submission
    $("#editUserForm").submit(function(e){
      e.preventDefault();
      
      let submitBtn = $("#updateBtn");
      let spinner = submitBtn.find(".spinner-border");
      
      // Show loading state
      submitBtn.prop("disabled", true);
      spinner.removeClass("d-none");
      
      $.ajax({
        url: "<?= site_url('admin/author/update') ?>/" + $("#editUserId").val(),
        type: "POST",
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response){
          if (response.status === 'success') {
            $("#editUserModal").modal("hide");
            location.reload();
          } else {
            alert("Error: " + (response.message || "Unknown error occurred"));
          }
        },
        error: function(xhr, status, error){
          console.error("AJAX Error:", xhr.responseText);
          alert("Error updating user. Please check the console for details.");
        },
        complete: function(){
          // Hide loading state
          submitBtn.prop("disabled", false);
          spinner.addClass("d-none");
        }
      });
    });

    // Handle create form submission
    $("#createUserForm").submit(function(e){
      let submitBtn = $("#submitBtn");
      let spinner = submitBtn.find(".spinner-border");
      
      // Show loading state
      submitBtn.prop("disabled", true);
      spinner.removeClass("d-none");
      submitBtn.text("Creating...");
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function(){
      $(".alert").fadeOut();
    }, 5000);
  });

  // Preview image function
  function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('profilePreview').src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  }
  </script>