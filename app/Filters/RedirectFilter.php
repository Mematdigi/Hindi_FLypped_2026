<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RedirectFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim(strtolower($request->getUri()->getPath()), '/');
        $path = str_replace(['index.php', '//'], ['', '/'], $path);
        $path = trim($path, '/');

        if (empty($path)) return null;

        // Skip admin paths
        $skipPrefixes = [
            'login', 'logout', 'dashboard', 'redirects', 'redirect/',
            'api/', 'assets/', 'admin/', 'media-library', 'seo/',
            'add_post', 'save_post', 'all-posts', 'edit_post',
            'categories', 'cron/', 'get-tags',
        ];
        foreach ($skipPrefixes as $prefix) {
            if (strpos($path, $prefix) === 0) return null;
        }

        $db = \Config\Database::connect();

        $redirect = $db->table('url_redirects')
            ->select('new_slug, status_code')
            ->where('old_slug', $path)
            ->get()
            ->getRow();

        if ($redirect && !empty($redirect->new_slug)) {
            $db->table('url_redirects')
                ->where('old_slug', $path)
                ->set('hits', 'hits + 1', false)
                ->update();

            $newSlug = trim($redirect->new_slug);
            $code    = (int)($redirect->status_code ?? 301);

            //  Handle 410 Gone
            if ($newSlug === '__410__') {
                http_response_code(410);
                echo '<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <title>410 - Gone | Flypped Hindi</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 80px 20px; background: #f8f9fa; }
        h1 { font-size: 80px; color: #6c757d; margin: 0; }
        h2 { color: #333; }
        p  { color: #666; }
        a  { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <h1>410</h1>
    <h2>Page Gone</h2>
    <p>This page has been permanently removed.</p>
    <a href="https://flyppedhindi.com">← Back to Homepage</a>
</body>
</html>';
                exit();
            }

            //  Handle homepage
            if ($newSlug === '/' || $newSlug === '') {
                header('Location: https://flyppedhindi.com/', true, $code);
                exit();
            }

            //  Handle full URL
            if (preg_match('#^https?://#i', $newSlug)) {
                header('Location: ' . $newSlug, true, $code);
                exit();
            }

            //  Hindi: strip any domain prefix, destination is just slug
            $newSlug = preg_replace('#^https?://(www\.)?hindi\.flypped\.com/?#i', '', $newSlug);
            $newSlug = preg_replace('#^https?://(www\.)?flypped\.com/?#i', '', $newSlug);
            $newSlug = strtolower(trim($newSlug, '/'));

            header('Location: https://flyppedhindi.com/' . $newSlug, true, $code);
            exit();
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}