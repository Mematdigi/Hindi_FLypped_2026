<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TrailingSlashFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the current full URL string
        $uriString = (string) $request->getUri();
        
        // We do not want to redirect the absolute home page (e.g., domain.com/)
        $baseUrl = rtrim(base_url(), '/');
        
        // If the URL ends with a '/' and is longer than just the base URL
        if (substr($uriString, -1) === '/' && $uriString !== $baseUrl . '/') {
            
            // Remove the trailing slash
            $newUri = rtrim($uriString, '/');
            
            // Perform a 301 Permanent SEO Redirect to the clean URL
            return redirect()->to($newUri)->setStatusCode(301);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}