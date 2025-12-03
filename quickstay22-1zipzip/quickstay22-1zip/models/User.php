<?php
class User
{
    public static function login($email, $password)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'], // 'admin' or 'user'
                'username' => $user['username'] ?? $user['email']
            ];
            return true;
        }
        return false;
    }

    public static function register($email, $password, $role = 'user')
    {
        global $pdo;
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            return "Cet email est déjà utilisé.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $hashedPassword, $role]);
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    // New method to get all users
    public static function getAll()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT id, name, email, role FROM users"); // Select necessary fields
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
