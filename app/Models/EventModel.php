<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';  // Ensure this matches your database table
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 
        'sub_title', 
        'description', 
        'date', 
        'time', 
        'location', 
        'price', 
        'image', 
        'total_tickets'
    ];
    

    protected $returnType = 'array';
}
