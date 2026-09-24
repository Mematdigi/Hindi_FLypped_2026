<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Fetch footer data with split categories
     * This method is used across multiple controllers
     */
    protected function fetchFooterData()
    {
        $db = \Config\Database::connect();

        // ===== Footer Blogs =====
        $footer_blog_query = $db->query("
            SELECT p.ID, p.post_title, p.post_name, p.post_date, 
                   t.name AS category_name, t.slug AS category_slug
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
            GROUP BY p.ID
            ORDER BY p.post_date DESC
            LIMIT 5;
        ");
        $latest_footer_blogs = $footer_blog_query->getResultArray();

        // Take only first 3 blogs
        $latest_footer_blogs = array_slice($latest_footer_blogs, 0, 3);

        // ===== Categories with post counts =====
        $categories_query = $db->query("
            SELECT t.name, t.slug, COUNT(p.ID) as post_count
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_posts p ON tr.object_id = p.ID
            WHERE p.post_status = 'publish' AND tt.taxonomy = 'category'
            GROUP BY t.term_id
            ORDER BY post_count DESC;
        ");
        $categories_with_post_count = $categories_query->getResultArray();

        //  Normalize Tech & Gadgets category
        foreach ($categories_with_post_count as &$cat) {
            if (stripos($cat['name'], 'Tech') !== false && stripos($cat['name'], 'Gadgets') !== false) {
                $cat['name'] = 'Tech & Gadgets';
                $cat['slug'] = 'tech-gadgets';
            }
        }

        //  Split categories into two columns
        $total_categories = count($categories_with_post_count);
        $half = ceil($total_categories / 2);
        $categories_column_1 = array_slice($categories_with_post_count, 0, $half);
        $categories_column_2 = array_slice($categories_with_post_count, $half);

        return [
            'latest_footer_blogs' => $latest_footer_blogs,
            'categories_with_post_count' => $categories_with_post_count,
            'categories_column_1' => $categories_column_1,
            'categories_column_2' => $categories_column_2,
        ];
    }
}
