<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function reviews()
    {
        // Number of items per page
        $limit = 50;
    
        // Get the current page from the query string (default to 1 if not set)
        $page = $this->request->getGet('page') ?? 1;
    
        // Database connection
        $db = \Config\Database::connect();
    
        // Get the total number of reviews
        $total_reviews = $db->table('wp_comments')->countAllResults();
    
        // Calculate the offset based on the current page and limit
        $offset = ($page - 1) * $limit;
    
        // Fetch reviews with limit and offset
        $query = $db->table('wp_comments')
                    ->select('comment_ID, comment_author, comment_author_email, comment_content')
                    ->orderBy('comment_ID', 'DESC')
                    ->limit($limit, $offset)
                    ->get();
        $data['reviews'] = $query->getResultArray();
    
        // Pass pagination data to the view
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_reviews / $limit);
    
        // Load the review page view with fetched data
        return view('review_page', $data);
    }
    

    public function publishReview()
    {
        // Database connection
        $db = \Config\Database::connect();

        // Retrieve the comment_id from POST data
        $comment_id = $this->request->getPost('comment_id');

        if ($comment_id) {
            // Fetch the review details
            $review = $db->table('wp_comments')
                         ->select('comment_author, comment_content')
                         ->where('comment_ID', $comment_id)
                         ->get()
                         ->getRowArray();

            if ($review) {
                
                $post_title = 'Review by ' . $review['comment_author'] . ' - ' . $comment_id;
                // Generate a basic slug for this post 
                $post_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $post_title), '-'));

                // Insert review as a new blog post in the wp_posts table with a unique title
                $postData = [
                    'post_author' => 1, // Set a default author ID, or use a specific author ID
                    'post_date' => date('Y-m-d H:i:s'),
                    'post_content' => $review['comment_content'],
                    'post_title' => $post_title, // Unique title with comment ID
                    'post_name' => $post_slug, // Assigning generated slug
                    'post_status' => 'publish',
                    'post_type' => 'post',
                    'post_excerpt' => substr($review['comment_content'], 0, 100) // Optional excerpt
                ];

                $db->table('wp_posts')->insert($postData);

                // =======================================================
                //  TRIGGER INDEXNOW ON NEW REVIEW POST CREATION
                // =======================================================
                $post_url = "https://flyppedhindi.com/" . $post_slug; 
                $this->submitToIndexNow([$post_url]);
                log_message('info', 'IndexNow triggered for new review post: ' . $post_url);

                session()->setFlashdata('success', 'Review published successfully as a blog post.');
            } else {
                session()->setFlashdata('error', 'Failed to fetch review details.');
            }
        } else {
            session()->setFlashdata('error', 'Failed to publish the review.');
        }

        // Redirect back to the review page
        return redirect()->to(base_url('/reviews'));
    }

    public function deleteReview()
    {
        // Database connection
        $db = \Config\Database::connect();

        // Retrieve the comment_id from POST data
        $comment_id = $this->request->getPost('comment_id');

        if ($comment_id) {
            
            // =======================================================
            //  GET URL BEFORE DELETION FOR INDEXNOW DE-INDEXING
            // =======================================================
            $post_title_to_match = 'Review by % - ' . $comment_id;
            $post_to_delete = $db->table('wp_posts')
                                 ->select('post_name')
                                 ->where('post_type', 'post')
                                 ->like('post_title', $post_title_to_match)
                                 ->get()
                                 ->getRow();

            $deleted_url = null;
            if ($post_to_delete && !empty($post_to_delete->post_name)) {
                $deleted_url = "https://flyppedhindi.com/" . $post_to_delete->post_name;
            }

            // Delete the review from `wp_comments`
            $db->table('wp_comments')->where('comment_ID', $comment_id)->delete();

            // Delete the corresponding review post from `wp_posts` if it exists
            $delete_post_status = $db->table('wp_posts')
                                     ->where('post_type', 'post')
                                     ->like('post_title', $post_title_to_match) // Ensures it only matches the specific review post
                                     ->delete();

            // =======================================================
            //  TRIGGER INDEXNOW AFTER DELETION
            // =======================================================
            if ($delete_post_status && $deleted_url) {
                $this->submitToIndexNow([$deleted_url]);
                log_message('info', 'IndexNow triggered for deleted review post: ' . $deleted_url);
            }

            session()->setFlashdata('success', 'Review and corresponding published post deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete the review.');
        }

        // Redirect back to the review page
        return redirect()->to(base_url('/reviews'));
    }

}