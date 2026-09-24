<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SubscribeController extends Controller
{
 public function save()
{
    $db = \Config\Database::connect();
    $request = service('request');
    $data = json_decode($request->getBody(), true);

    $email = isset($data['email']) ? trim($data['email']) : '';

    //  1. Validate empty email
    if (empty($email)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email is required.'
        ]);
    }

    //  2. Validate proper format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid email address.'
        ]);
    }

    //  3. Check if already subscribed
    $exists = $db->table('email_subscriptions')
                 ->where('email', $email)
                 ->get()
                 ->getRow();

    if ($exists) {
        return $this->response->setJSON([
            'status' => 'exists',
            'message' => 'You are already subscribed!'
        ]);
    }

    //  4. Save new subscription
    try {
        $db->table('email_subscriptions')->insert([
            'email' => $email
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Thank you for subscribing!'
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Something went wrong. Please try again later.'
        ]);
    }
}



    public function check()
{
    $db = \Config\Database::connect();
    $request = service('request');
    $email = trim($request->getGet('email'));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $this->response->setJSON(['subscribed' => false]);
    }

    $exists = $db->table('email_subscriptions')->where('email', $email)->get()->getRow();

    return $this->response->setJSON([
        'subscribed' => $exists ? true : false
    ]);
}

public function notifyNewPost($postId)
    {
        $db = \Config\Database::connect();

        // Fetch post details
        $post = $db->table('wp_posts')
            ->select('post_title, post_content, post_name')
            ->where('ID', $postId)
            ->get()
            ->getRow();

        if (!$post) {
            return "Post not found.";
        }

        // Prepare email content
        $subject = "🚀 New Post Published: " . $post->post_title;
        $message = "
            <h2>{$post->post_title}</h2>
            <p>" . substr(strip_tags($post->post_content), 0, 200) . "...</p>
            <a href='" . base_url('news/' . $post->post_name) . "'>👉 Read Full Post</a>";

        // Fetch all subscribers
        $subscribers = $db->table('email_subscriptions')->select('email')->get()->getResultArray();

        if (!$subscribers) {
            return "No subscribers yet.";
        }

        $email = Services::email();
        $email->setFrom('no-reply@flypped.com', 'Flypped News');

        foreach ($subscribers as $subscriber) {
            $email->setTo($subscriber['email']);
            $email->setSubject($subject);
            $email->setMessage($message);
            $email->send(); // you can log failures if needed
        }

        return "Emails sent successfully.";
    }


}
