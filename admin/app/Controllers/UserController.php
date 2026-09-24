<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    
    public function userList()
    {
        $userModel = new \App\Models\UserModel();
    
        // Get current page number from query parameter (default is 1)
        $currentPage = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search');
        $perPage = 10; // Number of records per page
        $offset = ($currentPage - 1) * $perPage;
    
        // If search is provided, apply filter
        if ($search) {
            $userModel->groupStart()
                      ->like('username', $search)
                      ->orLike('email', $search)
                      ->orLike('phone', $search)
                      ->groupEnd();
        }
    
        // Get total record count for pagination
        $totalRecords = $userModel->countAllResults(false); // Prevent resetting the query
    
        // Fetch the records for the current page
        $users = $userModel
            ->orderBy('id', 'ASC')
            ->findAll($perPage, $offset);
    
        // Prepare pagination data
        $data = [
            'users' => $users,
            'totalRecords' => $totalRecords,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'search' => $search,
        ];
    
        return view('user_list', $data);
    }
    


    // Delete User
    public function userDelete($id)
    {
        $userModel = new UserModel();

        $user = $userModel->find($id);
        if ($user) {
            $userModel->delete($id);
            return redirect()->to('/user-list')->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to('/user-list')->with('error', 'User not found.');
        }
    }
}
