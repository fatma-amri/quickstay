<?php include __DIR__ . '/../partials/nav.php'; ?>
<link rel="stylesheet" href="public/css/global.css">
<link rel="stylesheet" href="public/css/navbar.css">
<link rel="stylesheet" href="public/css/messages.css">
<?php
// Connexion à la base (à adapter selon ton projet)
require_once __DIR__ . '/../../config.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Vérifications côté serveur
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $password2) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Insertion de l'utilisateur
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
            if ($stmt->execute([$email, $hash])) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>
<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<div class="container">
    <div class="form-card">
        <h2><i class="fa-solid fa-user-plus"></i> Inscription</h2>
        <?php if (!empty($error)): ?>
            <div class="flash-message error">
                <i class="fa-solid fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="flash-message success">
                <i class="fa-solid fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php include __DIR__ . '/../partials/messages.php'; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="password2"><i class="fa-solid fa-lock"></i> Répétez le mot de passe</label>
                <input type="password" name="password2" id="password2" required>
            </div>
            <button type="submit" class="btn"><i class="fa-solid fa-user-plus"></i> S'inscrire</button>
        </form>
        <p class="login-link">
        </p>
    </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
<style>
.form-card {
    background: var(--color-surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    padding: 2.5rem;
    max-width: 500px;
    margin: 2rem auto;
    animation: fadeIn 0.5s ease-in-out;
}

.form-card h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    color: var(--color-primary-dark);
    text-align: center;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5em;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    font-weight: 500;
    color: var(--color-text);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5em;
}

.form-group input {
    width: 100%;
    padding: 0.9em 1.3em;
    border: 2px solid var(--color-border);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: all var(--transition);
}

.form-group input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 10px rgba(33, 82, 250, 0.25);
    transform: scale(1.01);
}

button.btn {
    width: 100%;
    padding: 0.9em;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5em;
}

.login-link {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 1rem;
    color: var(--color-gray);
}

.login-link a {
    color: var(--color-primary);
    font-weight: 500;
    position: relative;
    transition: all var(--transition);
}

.login-link a:hover {
    color: var(--color-primary-dark);
}

.login-link a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background: var(--color-primary);
    bottom: -2px;
    left: 0;
    transition: width var(--transition);
}

.login-link a:hover::after {
    width: 100%;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 600px) {
    .form-card {
        padding: 1.5rem;
        margin: 1rem;
    }
    .form-card h2 {
        font-size: 1.8rem;
    }
}
</style>
