<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tbs_users'; // Correct table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'phone', 'email', 'password', 'registered_at'];
}
