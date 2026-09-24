<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardController extends BaseController
{
    public function index()
    {
        // Render the dashboard view
        return view('dashboard');
    }
}
