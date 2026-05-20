<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('login/login', [
            'title' => 'Connexion',
        ]);
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez remplir tous les champs.');
        }

        $user = $this->userModel->verifyCredentials($email, $password);

        if ($user === null) {
            sleep(1);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Identifiants incorrects. Veuillez réessayer.');
        }

        session()->regenerate(true);
        session()->set([
            'user_id'    => $user['id'],
            'employe_id' => $user['id'],
            'user_nom'   => $user['nom'] . ' ' . $user['prenom'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to($this->dashboardRoute($user['role']));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Vous avez été déconnecté(e).');
    }

    private function dashboardRoute(?string $role = null): string
    {
        $role ??= session()->get('user_role');

        return match ($role) {
            'admin'  => base_url('admin/dashboard'), 
            'rh'     => base_url('rh/dashboard'),
            default  => base_url('employe/dashboard'),
        };
    }
}
