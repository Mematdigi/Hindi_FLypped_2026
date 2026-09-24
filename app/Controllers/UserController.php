<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    public function login()
    {
        helper(['form']);
        return view('user_login');
    }

    public function signIn()
    {
        // Load the UserModel
        $userModel = new UserModel();

        // Get email and password from the request
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Find user by email
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set user session
                session()->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'isLoggedIn' => true
                ]);

                return redirect()->to('/')->with('success', 'You are now logged in.');
            } else {
                return redirect()->back()->with('error', 'Invalid password. Please try again.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found with this email.');
        }
    }

    public function signOut()
    {
        // Destroy the session
        session()->destroy();

        return redirect()->to('/')->with('success', 'You have successfully logged out.');
    }

    public function registerUser()
    {
        helper(['form', 'url']); // Load form and URL helpers
    
        // Validation Rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]',
            'phone'    => 'required|numeric|min_length[10]|is_unique[tbs_users.phone]', // Prevent duplicate phone numbers
            'email'    => 'required|valid_email|is_unique[tbs_users.email]', // Prevent duplicate emails
            'password' => 'required|min_length[6]',
        ]);
    
        // If Validation Fails
        if (!$this->validate($validation->getRules())) {
            $errors = $validation->getErrors(); // Get validation errors
            if (isset($errors['email'])) {
                $errorMessage = 'This email is already registered.';
            } elseif (isset($errors['phone'])) {
                $errorMessage = 'This phone number is already registered.';
            } else {
                $errorMessage = 'Please fix the errors in the form.';
            }
    
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    
        // If Validation Passes
        $userModel = new UserModel();
    
        $userData = [
            'username' => $this->request->getPost('username'),
            'phone'    => $this->request->getPost('phone'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT), // Hash Password
        ];
    
        if ($userModel->insert($userData)) {
            return redirect()->to('/login')->with('success', 'Registration successful! You can now log in.');
        } else {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }
    

}
