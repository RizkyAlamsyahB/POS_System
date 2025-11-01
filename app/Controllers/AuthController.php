<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

class AuthController extends BaseController
{
    /**
     * Display login page
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (auth()->loggedIn()) {
            return redirect()->to($this->getRedirectUrl());
        }

        return view('auth/login');
    }

    /**
     * Handle login attempt
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $credentials = [
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];

        $remember = (bool) $this->request->getPost('remember');

        $authenticator = auth('session')->getAuthenticator();

        $result = $authenticator->attempt($credentials);

        if (!$result->isOK()) {
            return redirect()->route('login')->withInput()->with('error', $result->reason());
        }

        // Get user info for logging
        $user = $authenticator->getUser();
        
        // Log successful login
        log_message('info', "User {$user->username} logged in successfully");

        return redirect()->to($this->getRedirectUrl())->with('message', 'Login berhasil!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        auth()->logout();
        
        return redirect()->to('/login')->with('message', 'Anda telah logout');
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(): string
    {
        $user = auth()->user();

        if (!$user) {
            return '/login';
        }

        // Check user group/role
        if ($user->inGroup('admin')) {
            return '/admin/dashboard';
        }

        if ($user->inGroup('manager')) {
            return '/manager/dashboard';
        }

        if ($user->inGroup('cashier')) {
            return '/pos';
        }

        return '/dashboard';
    }
}
