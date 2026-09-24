<!DOCTYPE html>
<html lang="en">
    
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Flypped</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/flag-icon-css/css/flag-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/vertical-light-layout/style.css'); ?>">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="<?php echo base_url('public/assets'); ?>/images/favicon.png" />
    
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.1/ckeditor5.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">

    <!-- Custom CSS for sidebar-only layout -->
    <style>
      /* Remove top navbar spacing */
      .page-body-wrapper {
        margin-top: 0 !important;
        padding-top: 0 !important;
      }
      
      /* Sidebar brand wrapper styling */
      .sidebar-brand-wrapper {
        background: #2c2c54;
        padding: 15px 20px;
        border-bottom: 1px solid #373751;
        margin-bottom: 10px;
      }
      
      /* Logo styling in sidebar */
      .sidebar-brand img {
        max-height: 35px;
        width: auto;
      }
      
      /* Toggle button styling in sidebar */
      .sidebar .navbar-toggler {
        border: none;
        background: transparent;
        color: white;
        font-size: 1.2rem;
        padding: 5px;
      }
      
      .sidebar .navbar-toggler:hover {
        background: rgba(255,255,255,0.1);
        border-radius: 4px;
      }
      
      /* Main panel adjustment */
      .main-panel {
        margin-top: 0 !important;
        min-height: 100vh;
      }
      
      /* Content wrapper adjustment */
      .content-wrapper {
        padding-top: 20px;
      }
      
      /* Sidebar profile section spacing */
      .nav-profile {
        margin-top: 15px !important;
        margin-bottom: 10px;
      }
      
      /* Responsive adjustments */
      @media (max-width: 991px) {
        .sidebar-brand-wrapper {
          padding: 10px 15px;
        }
        
        .sidebar-brand img {
          max-height: 30px;
        }
      }
    </style>

  </head>
  <body>

  <?php
    // Get user role and name from session
    $userRole = session()->get('user_role') ?? 'subscriber';
    $userName = session()->get('user_name') ?? 'User';
    $isAdmin = ($userRole === 'administrator');
    $canManagePosts = in_array($userRole, ['administrator', 'editor', 'author', 'seo_editor', 'seo_manager']);
  ?>

  <div class="container-scroller">
      <!-- Remove top navbar completely -->
      
      <div class="container-fluid page-body-wrapper">
        <!-- Enhanced sidebar with logo and toggle -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          
         
          <ul class="nav">
            <li class="nav-item nav-profile mt-3 ">
              <a class="sidebar-brand brand-logo " href="<?php echo base_url('/dashboard'); ?>">
                <img src="<?php echo base_url('public/assets'); ?>/images/Logo.png" alt="logo" style="max-height: 40px;" />
              </a>
              <a href="#" class="nav-link mt-2">
                <div class="profile-image">
                  <img class="img-xs rounded-circle" src="<?php echo base_url('public/assets'); ?>/images/faces/User_img.png" alt="profile image">
                  <div class="dot-indicator bg-success"></div>
                </div>
                <div class="text-wrapper">
                  <p class="profile-name"><?= $userName ?></p>
                  <p class="designation"><?= ucfirst($userRole) ?></p>
                </div>
              </a>
            </li>
           
            
            <!-- Dashboard - Available to all users -->
            <li class="nav-item mt-2">
              <a class="nav-link" href="<?php echo base_url('/dashboard'); ?>">
                <span class="menu-title">Dashboard</span>
                <i class="icon-screen-desktop menu-icon"></i>
              </a>
            </li>

            <!-- BLOG POSTING - Available to users who can manage posts -->
            <!-- BLOG POSTING - Available to users who can manage posts -->
<?php if ($canManagePosts): ?>
<li class="nav-item nav-category"><span class="nav-link">BLOG POSTING</span></li>
<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
    <span class="menu-title">Post</span>
    <i class="icon-layers menu-icon"></i>
  </a>
  <div class="collapse" id="ui-basic">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('all-posts'); ?>">All Post</a></li>
      <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('add_post'); ?>">Add Post</a></li>
      
      <!-- NEW: Redirection URL - Available to all post managers -->
      <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('redirects'); ?>">Redirection URL</a></li>
      
      <!-- Categories - Only for Admin and Editor -->
      <?php if ($isAdmin || $userRole === 'editor'): ?>
      <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('categories'); ?>">Categories</a></li>
      <?php endif; ?>
      
      <!-- Authors - Only for Admin -->
      <?php if ($isAdmin): ?>
      <li class="nav-item"> <a class="nav-link" href="<?= base_url('admin/author'); ?>">Authors</a></li>
      <?php endif; ?>
    </ul>
  </div>
</li>
<?php endif; ?>

            <!-- ADMIN ONLY SECTIONS -->
            <?php if ($isAdmin): ?>
            
            <!-- Media Section -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
                <span class="menu-title">Media</span>
                <i class="icon-globe menu-icon"></i>
              </a>
              <div class="collapse" id="icons">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('media-library'); ?>">Library</a></li>
                  <li class="nav-item"> <a class="nav-link" href="#">Add New</a></li>
                </ul>
              </div>
            </li>

            <!-- Reviews Section -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#forms" aria-expanded="false" aria-controls="forms">
                <span class="menu-title">Reviews</span>
                <i class="icon-book-open menu-icon"></i>
              </a>
              <div class="collapse" id="forms">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('reviews'); ?>">All Review</a></li>
                </ul>
              </div>
            </li>

            <!-- Settings Section -->
            <li class="nav-item nav-category"><span class="nav-link">Settings</span></li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Events</span>
                <i class="icon-disc menu-icon"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                 <li class="nav-item"> <a class="nav-link" href="<?= base_url('/events/create') ?>"> Add Events </a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('/events') ?>"> Events List </a></li>
                </ul>
              </div>
            </li>

            <!-- Users Section -->
            <li class="nav-item nav-category"><span class="nav-link">Users</span></li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo base_url('user-list'); ?>">
                <span class="menu-title">All User</span>
                <i class="icon-folder-alt menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/author'); ?>">
                <span class="menu-title">Add User</span>
                <i class="icon-folder-alt menu-icon"></i>
              </a>
            </li>

            <?php endif; ?>

            <!-- Access Denied Message for Limited Users -->
            <?php if (!$canManagePosts): ?>
            <li class="nav-item nav-category"><span class="nav-link">Limited Access</span></li>
            <li class="nav-item">
              <div class="nav-link text-muted">
                <small>Contact administrator for additional permissions</small>
              </div>
            </li>
            <?php endif; ?>

            <!-- Logout Button - Available to all users -->
            <li class="nav-item nav-category"><span class="nav-link">Account</span></li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                <span class="menu-title">Logout</span>
                <i class="icon-power menu-icon"></i>
              </a>
            </li>

          </ul>
        </nav>