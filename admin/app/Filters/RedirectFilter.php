<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RedirectFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $currentPath = trim($uri->getPath(), '/');
        
        // Normalize path: remove index.php if present
        $currentPath = preg_replace('#^index\.php/?#i', '', $currentPath);
        $currentPath = strtolower(trim($currentPath, '/'));
        
        log_message('info', '🔥 REDIRECT FILTER: Checking path: ' . $currentPath);
        
        if (empty($currentPath)) {
            return null;
        }
        
        // Skip admin/system paths
        $skipPrefixes = [
            'login', 'logout', 'dashboard', 'redirects', 'redirect/',
            'api/', 'assets/', 'admin/', 'media-library', 'seo/'
        ];
        
        foreach ($skipPrefixes as $prefix) {
            if (strpos($currentPath, $prefix) === 0) {
                log_message('debug', '⏭️ Skipping admin path: ' . $currentPath);
                return null;
            }
        }
        
        $db = \Config\Database::connect();
        
        try {
            // Try to find redirect - check multiple variations
            $pathVariations = [
                $currentPath,                           // news/article-slug
                'index.php/' . $currentPath,           // index.php/news/article-slug
            ];
            
            log_message('debug', '🔍 Trying path variations: ' . json_encode($pathVariations));
            
            $redirect = null;
            foreach ($pathVariations as $pathVariant) {
                $redirect = $db->table('url_redirects')
                    ->where('LOWER(old_slug)', strtolower($pathVariant))
                    ->get()
                    ->getRow();
                
                if ($redirect) {
                    log_message('info', ' REDIRECT FOUND with variant: ' . $pathVariant);
                    break;
                }
            }
            
            if ($redirect && !empty($redirect->new_slug)) {
                log_message('info', ' REDIRECT MATCH: ' . $currentPath . ' → ' . $redirect->new_slug);
                
                // Increment hit counter
                try {
                    $db->table('url_redirects')
                        ->where('id', $redirect->id)
                        ->set('hits', 'hits + 1', false)
                        ->update();
                } catch (\Exception $e) {
                    log_message('error', 'Hit counter failed: ' . $e->getMessage());
                }
                
                // Clean the new_slug and build target URL
$newSlug = trim($redirect->new_slug);
$code    = (in_array($redirect->status_code, ['301', '302', '410']))
    ? (int)$redirect->status_code
    : 301;

//  Handle 410 Gone
if ($newSlug === '__410__') {
    http_response_code(410);
    echo '<!DOCTYPE html><html><head><meta name="robots" content="noindex,nofollow">
    <title>410 Gone</title></head><body>
    <h1>410 - Page Gone</h1>
    <p>This page has been permanently removed.</p>
    <a href="https://hi.flypped.com">← Back to Homepage</a>
    </body></html>';
    exit();
}

//  Handle homepage
if ($newSlug === '/' || $newSlug === '') {
    header('Location: https://hi.flypped.com/', true, $code);
    exit();
}

//  Handle full URL already
if (preg_match('#^https?://#i', $newSlug)) {
    header('Location: ' . $newSlug, true, $code);
    exit();
}

                        //  Strip any domain prefix (hindi or english)
                        $newSlug = preg_replace('#^https?://(www\.)?hindi\.flypped\.com/?#i', '', $newSlug);
                        $newSlug = preg_replace('#^https?://(www\.)?hi\.flypped\.com/?#i', '', $newSlug);  //  add
                        $newSlug = preg_replace('#^https?://(www\.)?flypped\.com/?#i', '', $newSlug);
                        $newSlug = preg_replace('#^(www\.)?hindi\.flypped\.com/?#i', '', $newSlug);
                        $newSlug = preg_replace('#^(www\.)?flypped\.com/?#i', '', $newSlug);
                        $newSlug = strtolower(trim($newSlug, '/'));

                        //  Hindi: destination is just slug — no category
                        $targetUrl = 'https://hi.flypped.com/' . $newSlug;

                        log_message('info', '🚀 REDIRECTING ' . $code . ': ' . $targetUrl);

                        header('Location: ' . $targetUrl, true, $code);
                        exit();
            } else {
                log_message('debug', '❌ No redirect found for: ' . $currentPath);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'RedirectFilter Exception: ' . $e->getMessage());
        }
        
        return null;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}