<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RedirectController extends Controller
{
    /**
     * Handle 2-segment slugs like:  health-fitness/my-article
     * This runs BEFORE BlogDetails::view so we can redirect old slugs.
     */
    public function check(string $seg1, string $seg2)
    {
        $slug = strtolower(trim($seg1 . '/' . $seg2, '/'));

        $db = \Config\Database::connect();
        $row = $db->table('url_redirects')
                  ->select('new_slug,status_code')
                  ->where('old_slug', $slug)
                  ->get()->getRow();

        if ($row) {
            // optional: count hits
            $db->table('url_redirects')
               ->where('old_slug', $slug)
               ->set('hits', 'hits + 1', false)
               ->update();

            $code = (int)($row->status_code ?? 301);
            // base_url() already includes /flypped if your baseURL is set that way
            return redirect()->to(base_url($row->new_slug), $code);
        }

        // Not a redirect → render the normal blog detail page (no loops)
        $controller = new \App\Controllers\BlogDetails();
        return $controller->view($seg1, $seg2);
    }

    /**
     * Handle 1-segment slugs like:  fashion  (categories)
     * If you don’t keep category redirects in the table, we just forward to Categories.
     */
    public function checkOne(string $seg1)
    {
        $slug = strtolower(trim($seg1, '/'));

        $db = \Config\Database::connect();
        $row = $db->table('url_redirects')
                  ->select('new_slug,status_code')
                  ->where('old_slug', $slug)
                  ->get()->getRow();<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RedirectController extends Controller
{
    /**
     * Handle 2-segment slugs like:  health-fitness/my-article
     * This runs BEFORE BlogDetails::view so we can redirect old slugs.
     */
    public function check(string $seg1, string $seg2)
    {
        $slug = strtolower(trim($seg1 . '/' . $seg2, '/'));

        $db = \Config\Database::connect();
        $row = $db->table('url_redirects')
                  ->select('new_slug,status_code')
                  ->where('old_slug', $slug)
                  ->get()->getRow();

        if ($row) {
            // optional: count hits
            $db->table('url_redirects')
               ->where('old_slug', $slug)
               ->set('hits', 'hits + 1', false)
               ->update();

            $code = (int)($row->status_code ?? 301);
            // base_url() already includes /flypped if your baseURL is set that way
            return redirect()->to(base_url($row->new_slug), $code);
        }

        // Not a redirect → render the normal blog detail page (no loops)
        $controller = new \App\Controllers\BlogDetails();
        return $controller->view($seg1, $seg2);
    }

    /**
     * Handle 1-segment slugs like:  fashion  (categories)
     * If you don’t keep category redirects in the table, we just forward to Categories.
     */
    public function checkOne(string $seg1)
    {
        $slug = strtolower(trim($seg1, '/'));

        $db = \Config\Database::connect();
        $row = $db->table('url_redirects')
                  ->select('new_slug,status_code')
                  ->where('old_slug', $slug)
                  ->get()->getRow();

        if ($row) {
            $db->table('url_redirects')
               ->where('old_slug', $slug)
               ->set('hits', 'hits + 1', false)
               ->update();

            $code = (int)($row->status_code ?? 301);
            return redirect()->to(base_url($row->new_slug), $code);
        }

        // Not a redirect → render the normal category page
        $controller = new \App\Controllers\Categories();
        return $controller->index($seg1);
    }
}


        if ($row) {
            $db->table('url_redirects')
               ->where('old_slug', $slug)
               ->set('hits', 'hits + 1', false)
               ->update();

            $code = (int)($row->status_code ?? 301);
            return redirect()->to(base_url($row->new_slug), $code);
        }

        // Not a redirect → render the normal category page
        $controller = new \App\Controllers\Categories();
        return $controller->index($seg1);
    }
}
