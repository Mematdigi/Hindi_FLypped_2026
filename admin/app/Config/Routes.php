<?php
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */

// Public routes (no authentication required)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::processLogin');

// Home route (redirect to login if not logged in, or dashboard if logged in)
$routes->get('/', function() {
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login');
    } else {
        return redirect()->to('/dashboard');
    }
});

// Logout Route
$routes->get('logout', 'AuthController::logout');

// ===== PROTECTED ROUTES =====

// Dashboard Route (all authenticated users)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'authGuard']);

// ===== BLOG POSTING ROUTES =====
$routes->group('', ['filter' => 'authGuard'], function($routes) {
    $routes->get('add_post', 'Home::index');
    $routes->post('save_post', 'Home::savePost');
    // ===== ADD THESE PREVIEW ROUTES =====
    //  PREVIEW ROUTES
    $routes->post('create-preview', 'Home::createPreview');
    $routes->get('preview-post', 'Home::previewPost'); //  NEW ROUTE
    $routes->get('cleanup-previews', 'Home::cleanupPreviews');
    $routes->get('all-posts', 'PostController::allPosts');
    $routes->get('edit_post/(:num)', 'PostController::editPost/$1');
    $routes->post('update_post/(:num)', 'PostController::updatePost/$1');
    $routes->get('confirm_delete_post/(:num)', 'PostController::confirmDeletePost/$1');
    $routes->post('delete_post/(:num)', 'PostController::processDeletePost/$1');

  // Scheduled posts management
    $routes->get('cron/publish', 'PublishScheduledPosts::index');
    

// URL Redirect Management
$routes->get('redirects',                        'RedirectManagementController::index');
$routes->post('redirect/add',                    'RedirectManagementController::addRedirect');
$routes->post('redirect/add-category',           'RedirectManagementController::addCategoryRedirect');
$routes->post('redirect/add-full',               'RedirectManagementController::addFullRedirect');
$routes->post('redirect/add-homepage',           'RedirectManagementController::addHomepageRedirect');
$routes->post('redirect/add-mapping',            'RedirectManagementController::addUrlMapping');
$routes->post('redirect/bulk-upload',            'RedirectManagementController::bulkUpload');
$routes->post('redirect/delete-single-post',     'RedirectManagementController::deleteSinglePost');
// $routes->post('redirect/bulk-delete',            'RedirectManagementController::bulkDeletePosts');
$routes->post('redirect/update',                 'RedirectManagementController::updateRedirect');
$routes->get('redirect/delete/(:num)',            'RedirectManagementController::deleteRedirect/$1');
$routes->post('delete-featured-image/(:num)', 'PostController::deleteFeaturedImage/$1');

    // AJAX endpoint for post title suggestions
    $routes->get('get-post-suggestions', 'PostController::getPostSuggestions');

    // SEO FUNCTIONALITY ROUTES
    $routes->post('seo/analyze', 'PostController::getSEOAnalysis');
    $routes->get('seo/preview/(:num)', 'PostController::getSEOPreview/$1');
    $routes->post('seo/bulk-update', 'PostController::bulkSEOUpdate');
    $routes->get('seo/suggestions', 'PostController::getSEOSuggestions');
    $routes->post('seo/validate-slug', 'PostController::validateSlug');

    // Tags
    $routes->get('get-tags', 'TagController::getTags');
});


// CKEditor image upload route
    $routes->post('upload-editor-image', 'Home::uploadEditorImage');
// ===== SEO MANAGER+ ROUTES =====
$routes->group('seo', ['filter' => 'authGuard:seo_manager'], function($routes) {
    $routes->get('dashboard', 'SEOController::dashboard');
    $routes->get('reports', 'SEOController::reports');
    $routes->get('reports/export', 'SEOController::exportReport');
    $routes->get('audit', 'SEOController::audit');
    $routes->post('audit/run', 'SEOController::runAudit');
    $routes->get('keywords', 'SEOController::keywordResearch');
    $routes->post('keywords/analyze', 'SEOController::analyzeKeywords');
    $routes->get('settings', 'SEOController::settings');
    $routes->post('settings/save', 'SEOController::saveSettings');
    $routes->get('bulk-meta', 'SEOController::bulkMeta');
    $routes->post('bulk-meta/process', 'SEOController::processBulkMeta');
});

// ===== EDITOR+ ROUTES =====
$routes->group('', ['filter' => 'authGuard:editor'], function($routes) {
    $routes->get('categories', 'CategoryController::index');
    $routes->post('categories/add', 'CategoryController::add');
    $routes->get('categories/delete/(:num)', 'CategoryController::delete/$1');
    $routes->get('categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->get('categories/view/(:num)', 'CategoryController::viewCategory/$1');
    $routes->get('categories/seo/(:num)', 'CategoryController::editSEO/$1');
    $routes->post('categories/seo/(:num)', 'CategoryController::updateSEO/$1');
});

// ===== ADMINISTRATOR ONLY ROUTES =====
$routes->group('', ['filter' => 'authGuard:administrator'], function($routes) {
    $routes->get('media-library', 'MediaController::index');
    $routes->post('media-library/upload-image', 'MediaController::uploadImage');
    $routes->get('reviews', 'AdminController::reviews');
    $routes->post('deleteReview', 'AdminController::deleteReview');
    $routes->post('publishReview', 'AdminController::publishReview');
    $routes->get('/events', 'EventsController::index');
    $routes->get('/events/create', 'EventsController::create');
    $routes->post('/events/store', 'EventsController::store');
    $routes->get('/events/edit/(:num)', 'EventsController::edit/$1');
    $routes->post('/events/update/(:num)', 'EventsController::update/$1');
    $routes->get('/events/delete/(:num)', 'EventsController::delete/$1');
    $routes->get('/user-list', 'UserController::userList');
    $routes->get('/user-delete/(:num)', 'UserController::userDelete/$1');

    $routes->group('admin/author', function($routes) {
        $routes->get('/', 'AuthorController::index');
        $routes->post('create', 'AuthorController::create');
        $routes->get('edit/(:num)', 'AuthorController::edit/$1');
        $routes->post('update/(:num)', 'AuthorController::update/$1');
        $routes->get('delete/(:num)', 'AuthorController::delete/$1');
        $routes->get('view-posts/(:num)', 'AuthorController::viewPosts/$1');
    });

    $routes->group('admin/seo', function($routes) {
        $routes->get('/', 'AdminSEOController::index');
        $routes->get('global-settings', 'AdminSEOController::globalSettings');
        $routes->post('global-settings/save', 'AdminSEOController::saveGlobalSettings');
        $routes->get('templates', 'AdminSEOController::templates');
        $routes->post('templates/save', 'AdminSEOController::saveTemplate');
        $routes->delete('templates/(:num)', 'AdminSEOController::deleteTemplate/$1');
        $routes->get('verification', 'AdminSEOController::verification');
        $routes->post('verification/save', 'AdminSEOController::saveVerification');
        $routes->get('robots', 'AdminSEOController::robotsTxt');
        $routes->post('robots/save', 'AdminSEOController::saveRobotsTxt');
        $routes->get('maintenance', 'AdminSEOController::maintenance');
        $routes->post('maintenance/cleanup', 'AdminSEOController::cleanupSEOData');
        $routes->post('maintenance/migrate', 'AdminSEOController::migrateSEOData');
    });
});

// ===== API ROUTES FOR SEO =====
$routes->group('api/seo', ['filter' => 'authGuard'], function($routes) {
    $routes->get('post/(:num)', 'APIController::getPostSEO/$1');
    $routes->get('sitemap', 'APIController::getSitemapData');
    $routes->get('meta/(:segment)', 'APIController::getMetaData/$1');
    $routes->post('keyword-density', 'APIController::calculateKeywordDensity');
    $routes->post('readability', 'APIController::calculateReadability');
    $routes->post('slug-check', 'APIController::checkSlugAvailability');
});

// ===== SITEMAP ROUTES =====
$routes->get('sitemap.xml', 'SitemapController::index');
$routes->get('category-sitemap.xml', 'SitemapController::categories');
$routes->get('sitemap/category/(:segment)', 'SitemapController::categoryPosts/$1');
$routes->get('sitemap-xml', 'SitemapController::xml');
$routes->get('sitemap/posts.xml', 'SitemapController::postsSitemap');
$routes->get('sitemap/images.xml', 'SitemapController::imagesSitemap');
$routes->get('sitemap/news.xml', 'SitemapController::newsSitemap');

// ===== PUBLIC SEO ROUTES =====
$routes->get('robots.txt', 'SEOController::robotsTxt');
$routes->get('tag/(:segment)', 'SEOController::tagRedirect/$1');
$routes->get('author/(:segment)', 'SEOController::authorRedirect/$1');
$routes->get('og/(:segment)', 'SEOController::openGraph/$1');
$routes->get('twitter/(:segment)', 'SEOController::twitterCard/$1');

// ===== DEVELOPMENT/DEBUGGING ROUTES =====
if (ENVIRONMENT === 'development') {
    $routes->group('debug/seo', function($routes) {
        $routes->get('/', 'DebugController::seoOverview');
        $routes->get('post/(:num)', 'DebugController::debugPostSEO/$1');
        $routes->get('meta-dump', 'DebugController::dumpAllMeta');
        $routes->get('performance', 'DebugController::seoPerformance');
    });
}

// Route to serve the Google News Sitemap from the root folder
$routes->get('google-news-sitemap.xml', function() {
    // ROOTPATH CodeIgniter ka shortcut hai root folder tak pahunchne ke liye
    $filePath = ROOTPATH . 'google-news-sitemap.xml';
    
    if (file_exists($filePath)) {
        // Google ko batayein ki ye ek XML file hai
        $this->response->setHeader('Content-Type', 'text/xml');
        // File ka content padh kar browser par dikha dein
        return readfile($filePath);
    } else {
        
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
});