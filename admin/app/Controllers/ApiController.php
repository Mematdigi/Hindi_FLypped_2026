<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ApiController extends ResourceController
{
    public function getTags()
{
    try {
        $query = $this->request->getGet('query');
        $db = \Config\Database::connect('flypped_blog');

        // Fetch tags matching the query
        $result = $db->query("SELECT name FROM wp_terms WHERE name LIKE ?", ["%$query%"]);
        $tags = array_map(function($row) {
            return $row->name;
        }, $result->getResult());

        return $this->respond($tags);
    } catch (\Exception $e) {
        return $this->fail('Error fetching tags: ' . $e->getMessage());
    }
}

}
