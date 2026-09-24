<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CategoryController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Fetch all categories with slug
        $query = $db->query("SELECT t.term_id, t.name, t.slug, COUNT(tr.object_id) as count 
                             FROM wp_terms t
                             JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
                             LEFT JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                             WHERE tt.taxonomy = 'category'
                             GROUP BY t.term_id, t.name, t.slug");
        $categories = $query->getResult();

        return view('categories', ['categories' => $categories]);
    }

    public function add()
    {
        $db = \Config\Database::connect();
        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug');

        if ($name && $slug) {
            // Sanitize slug: convert to lowercase and replace spaces with hyphens
            $slug = strtolower(trim($slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            // Check if slug already exists
            $checkSlug = $db->query("SELECT term_id FROM wp_terms WHERE slug = ?", [$slug]);
            if ($checkSlug->getNumRows() > 0) {
                // Slug exists, append a number
                $counter = 1;
                $originalSlug = $slug;
                while ($checkSlug->getNumRows() > 0) {
                    $slug = $originalSlug . '-' . $counter;
                    $checkSlug = $db->query("SELECT term_id FROM wp_terms WHERE slug = ?", [$slug]);
                    $counter++;
                }
            }

            // Insert new category
            $db->query("INSERT INTO wp_terms (name, slug) VALUES (?, ?)", [$name, $slug]);
            $term_id = $db->insertID();

            // Insert into term_taxonomy for category
            $db->query("INSERT INTO wp_term_taxonomy (term_id, taxonomy) VALUES (?, 'category')", [$term_id]);

            return redirect()->to('/categories');
        }
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();

        // Fetch the category by ID
        $query = $db->query("SELECT * FROM wp_terms WHERE term_id = ?", [$id]);
        $category = $query->getRow();

        return $this->response->setJSON($category);
    }

    public function update($id)
    {
        $db = \Config\Database::connect();
        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug');

        if ($name && $slug) {
            // Sanitize slug: convert to lowercase and replace spaces with hyphens
            $slug = strtolower(trim($slug));
            $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            // Check if slug already exists for other categories
            $checkSlug = $db->query("SELECT term_id FROM wp_terms WHERE slug = ? AND term_id != ?", [$slug, $id]);
            if ($checkSlug->getNumRows() > 0) {
                // Slug exists, append a number
                $counter = 1;
                $originalSlug = $slug;
                while ($checkSlug->getNumRows() > 0) {
                    $slug = $originalSlug . '-' . $counter;
                    $checkSlug = $db->query("SELECT term_id FROM wp_terms WHERE slug = ? AND term_id != ?", [$slug, $id]);
                    $counter++;
                }
            }

            // Update the category name and slug
            $db->query("UPDATE wp_terms SET name = ?, slug = ? WHERE term_id = ?", [$name, $slug, $id]);

            return redirect()->to('/categories');
        }
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->query("DELETE FROM wp_terms WHERE term_id = ?", [$id]);
        $db->query("DELETE FROM wp_term_taxonomy WHERE term_id = ?", [$id]);
        return redirect()->to('/categories');
    }
    
    public function viewCategory($categoryId)
    {
        $db = \Config\Database::connect();
    
        // Fetch category details
        $categoryQuery = $db->query("SELECT name FROM wp_terms WHERE term_id = ?", [$categoryId]);
        $category = $categoryQuery->getRow();
    
        // Define how many posts per page
        $perPage = 35;
    
        // Get the current page number from the query string
        $currentPage = $this->request->getVar('page') ?? 1;
    
        // Calculate offset for the database query
        $offset = ($currentPage - 1) * $perPage;
    
        // Fetch blog posts under this category
        $postsQuery = $db->query("
            SELECT p.ID, p.post_title, p.post_date, pm.meta_value AS thumbnail_id 
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            WHERE tr.term_taxonomy_id = ?
            AND p.post_status = 'publish'
            ORDER BY p.post_date DESC
            LIMIT ? OFFSET ?
        ", [$categoryId, $perPage, $offset]);
        $posts = $postsQuery->getResult();
    
        // Fetch total number of posts for pagination
        $totalPostsQuery = $db->query("
            SELECT COUNT(p.ID) as total 
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            WHERE tr.term_taxonomy_id = ?
            AND p.post_status = 'publish'
        ", [$categoryId]);
        $totalPosts = $totalPostsQuery->getRow()->total;
    
        // Fetch thumbnail URLs for the blog posts
        foreach ($posts as &$post) {
            $thumbnailQuery = $db->query("SELECT guid FROM wp_posts WHERE ID = ?", [$post->thumbnail_id]);
            $thumbnail = $thumbnailQuery->getRow();
            if ($thumbnail) {
                $post->thumbnail_url = $thumbnail->guid;
            }
        }
    
        // Generate pagination links
        $pager = \Config\Services::pager();
        $pagination = $pager->makeLinks($currentPage, $perPage, $totalPosts);
    
        return view('category_posts', [
            'category' => $category,
            'posts' => $posts,
            'pagination' => $pagination
        ]);
    }
}