<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PageCacheFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only cache GET requests
        if ($request->getMethod() !== 'get') {
            return;
        }
        
        $cache = \Config\Services::cache();
        $uri = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        
        // Skip caching for these paths
        $skipPaths = ['admin', 'search', 'login', 'logout', 'api', 'sitemap'];
        foreach ($skipPaths as $skip) {
            if (strpos($uri, $skip) !== false) {
                return;
            }
        }
        
        // Create cache key
        $cacheKey = 'page_' . md5($uri . ($query ? '?' . $query : ''));
        
        // Try to get cached page
        if ($cached = $cache->get($cacheKey)) {
            $response = \Config\Services::response();
            $response->setBody($cached['body']);
            $response->setStatusCode(200);
            $response->setHeader('Content-Type', 'text/html; charset=UTF-8');
            $response->setHeader('X-Cache-Status', 'HIT');
            $response->setHeader('Cache-Control', 'public, max-age=3600');
            

            return $response;
        }
	return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only cache successful GET requests
        if ($request->getMethod() !== 'get' || $response->getStatusCode() !== 200) {
            return;
        }
        
        $cache = \Config\Services::cache();
        $uri = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        
        // Skip caching for these paths
        $skipPaths = ['admin', 'search', 'login', 'logout', 'api', 'sitemap'];
        foreach ($skipPaths as $skip) {
            if (strpos($uri, $skip) !== false) {
                return;
            }
        }
        
        // Create cache key
        $cacheKey = 'page_' . md5($uri . ($query ? '?' . $query : ''));
        
        // Determine cache duration based on URL pattern
        $cacheDuration = 3600; // Default: 1 hour
        
        // Blog posts: cache for 6 hours (they rarely change)
        if (preg_match('#^/[a-z0-9-]+/[a-z0-9-]+$#i', $uri)) {
            $cacheDuration = 21600; // 6 hours
        }
        
        // Homepage and category pages: cache for 30 minutes
        if ($uri === '/' || preg_match('#^/[a-z0-9-]+$#i', $uri)) {
            $cacheDuration = 1800; // 30 minutes
        }
        
        // Save to cache
        $cacheData = [
            'body' => $response->getBody(),
            'time' => time(),
        ];
        
        $cache->save($cacheKey, $cacheData, $cacheDuration);
        
        // Add header to show cache status
        $response->setHeader('X-Cache-Status', 'MISS');
        $response->setHeader('Cache-Control', 'public, max-age=' . $cacheDuration);
        
        return $response;
    }
}
