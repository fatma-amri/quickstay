<?php
require_once __DIR__ . '/../config.php';

class Middleware
{
    
    public static function adminOnly()
    {
        if (!isAdmin()) {
            $_SESSION['flash_message'] = "Accès non autorisé.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=home');
        }

        // CSRF check for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
                $_SESSION['flash_message'] = "Requête invalide. Veuillez réessayer.";
                $_SESSION['flash_type'] = "error";
                self::redirect('index.php?route=admin_dashboard');
                return; // Ensure execution stops after redirect
            }
        }
    }

    /**
     * Restricts access to user-only routes
     */
    public static function userOnly()
    {
        if (!isUser()) {
            $_SESSION['flash_message'] = "Vous devez être connecté pour accéder à cette page.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=login');
        }
    }

    /**
     * Helper method to check if the current user is an admin
     * @return bool
     */
    private static function isAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    /**
     * Helper method to check if the current user is a logged-in user
     * @return bool
     */
    private static function isUser()
    {
        return isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['user', 'admin']);
    }

    /**
     * Redirects to a specified URL
     * @param string $url The URL to redirect to
     */
    private static function redirect($url)
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        } else {
            echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '";</script>';
            exit;
        }
    }
}
