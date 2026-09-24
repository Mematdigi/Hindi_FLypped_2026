<?php

namespace App\Controllers;

use CodeIgniter\Controller;
 
class RobotsController extends Controller
{
    public function index()
    {
        $robotsTxt = "User-agent: *\n";
        $robotsTxt .= "Allow: /\n\n";

        $robotsTxt .= "# Block internal search and tag URLs\n";
        $robotsTxt .= "Disallow: /search/\n";
        $robotsTxt .= "Disallow: /*?s=\n";
        $robotsTxt .= "Disallow: /*?q=\n";
        $robotsTxt .= "Disallow: /*?tag=\n"; // Added explicit tag block
        $robotsTxt .= "Disallow: /*search_query=\n\n";

        $robotsTxt .= "# Block Author Pages\n";
        $robotsTxt .= "Disallow: /author/\n\n"; // Stops crawlers from scanning author profiles

        $robotsTxt .= "# Block unnecessary parameters\n";
        $robotsTxt .= "Disallow: /*?filter=\n";
        $robotsTxt .= "Disallow: /*?sort=\n";
        $robotsTxt .= "Disallow: /*?utm_\n\n";

        $robotsTxt .= "# Block system folders\n";
        $robotsTxt .= "Disallow: /writable/\n";
        $robotsTxt .= "Disallow: /vendor/\n";
        $robotsTxt .= "Disallow: /cdn-cgi/\n\n";

        $robotsTxt .= "# Sitemaps\n";
        $robotsTxt .= "Sitemap: https://flyppedhindi.com/sitemap.xml\n";
        
        // Added the new Google News Sitemap here
        $robotsTxt .= "Sitemap: https://flyppedhindi.com/google-news-sitemap.xml\n";
        

        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setBody($robotsTxt);
    }
}