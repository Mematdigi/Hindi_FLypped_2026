<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tbs_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'phone', 'email', 'registered_at'];
}
