<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MediaController extends BaseController
{
    public function index()
    {
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 50;

        // Connect to the database
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM wp_posts WHERE post_type = 'attachment' ORDER BY post_date DESC");
        $images = $query->getResultArray();

        $totalImages = count($images);
        $paginatedImages = array_slice($images, ($page - 1) * $perPage, $perPage);
        $totalPages = ceil($totalImages / $perPage);

        return view('media-library', [
            'images' => $paginatedImages,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function uploadImage()
    {
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('imageFile');

            if ($file->isValid() && !$file->hasMoved()) {
                // Set the upload directory
                $year = date('Y');
                $month = date('m');

                $uploadsDir = FCPATH . '../wp-content/uploads/' .$year . '/' . $month . '/'; // Using CodeIgniter's writable path

                if(!file_exists($uploadsDir)){
                    mkdir($uploadsDir, 0755, true);
                }

                // --- Keep original name and make it SEO friendly ---
                $originalName = $file->getName();
                $ext = $file->getExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                
                // Clean the file name (lowercase, alphanumeric, and dashes only)
                $cleanName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nameWithoutExt), '-'));
                
                if (empty($cleanName)) {
                    $cleanName = 'media-' . time();
                }
                
                $newName = $cleanName . '.' . $ext;
                
                // Ensure uniqueness inside the specific Year/Month folder
                $counter = 1;
                while (file_exists($uploadsDir . $newName)) {
                    $newName = $cleanName . '-' . $counter . '.' . $ext;
                    $counter++;
                }
                // --------------------------------------------------
                
                $filePath = 'wp-content/uploads/' . $year . '/' . $month . '/' . $newName;

                // Move the file to the uploads directory
                if ($file->move($uploadsDir, $newName)) {
                    // Connect to the database
                    $db = \Config\Database::connect();

                    // Insert image details into the database
                    $data = [
                        'post_title' => $file->getClientName(), // Keeps the original name in the DB title
                        'guid' => base_url($filePath),
                        'post_type' => 'attachment',
                        'post_date' => date('Y-m-d H:i:s')
                    ];

                    $db->table('wp_posts')->insert($data);

                    // Redirect to the media library page after successful upload
                    return redirect()->to(base_url('media-library'))->with('success', 'Image uploaded successfully.');
                } else {
                    return redirect()->to(base_url('media-library'))->with('error', 'File upload failed. Unable to move the file.');
                }
            } else {
                return redirect()->to(base_url('media-library'))->with('error', 'File upload failed. Please try again.');
            }
        } else {
            return redirect()->to(base_url('media-library'));
        }
    }
}