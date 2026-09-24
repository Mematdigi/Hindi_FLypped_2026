// app/Controllers/TagController.php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TagController extends BaseController
{
    public function getTags()
    {
        header('Content-Type: application/json');
        $db = \Config\Database::connect();

        $query = $this->request->getGet('query');
        $tags = [];

        if (!empty($query)) {
            $builder = $db->table('wp_terms');
            $builder->like('name', $query);
            $results = $builder->get()->getResult();

            foreach ($results as $row) {
                $tags[] = $row->name;
            }
        }

        echo json_encode($tags);
    }
}
