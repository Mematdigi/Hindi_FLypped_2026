<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // If specific role is required, check it
        if (!empty($arguments)) {
            $requiredRole = $arguments[0];
            $userRole = session()->get('user_role');
            
            // Administrator can access everything
            if ($userRole === 'administrator') {
                return;
            }
            
            // Check if user has required role
            if ($userRole !== $requiredRole) {
                // Redirect to dashboard with error message
                session()->setFlashdata('error', 'You do not have permission to access this page.');
                return redirect()->to('/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}