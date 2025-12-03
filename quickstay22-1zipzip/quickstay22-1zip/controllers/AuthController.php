<?php
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public static function login()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (User::login($email, $password)) {
                if (isAdmin()) {
                    redirect('index.php?route=admin_dashboard');
                } else {
                    redirect('index.php?route=user_properties');
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    public static function register()
    {
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            
            $role = $_POST['role'] ?? 'user'; // ou 'admin' si un formulaire spécial admin

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email invalide.";
            } elseif (strlen($password) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif ($password !== $password2) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                $result = User::register($email, $password, $role);
                if ($result === true) {
                    // Auto-login après inscription
                    User::login($email, $password);
                    if ($role === 'admin') {
                        redirect('index.php?route=admin_dashboard');
                    } else {
                        redirect('index.php?route=user_properties');
                    }
                    exit;
                } else {
                    $error = $result;
                }
            }
        }
        include __DIR__ . '/../views/auth/register.php';
    }

    public static function logout()
    {
        User::logout();
        redirect('index.php?route=login');
    }
}
?>
