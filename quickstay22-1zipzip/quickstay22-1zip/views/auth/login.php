<?php
require_once __DIR__ . '/../../config.php';

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<link rel="stylesheet" href="public/css/global.css">
<link rel="stylesheet" href="public/css/navbar.css">
<link rel="stylesheet" href="public/css/messages.css">
<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<div class="container">
    <div class="form-card">
        <h2><i class="fa-solid fa-sign-in-alt"></i> Connexion</h2>
        <?php if (!empty($error)): ?>
            <div class="flash-message error">
                <i class="fa-solid fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php include __DIR__ . '/../partials/messages.php'; ?>
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn"><i class="fa-solid fa-sign-in-alt"></i> Connexion</button>
        </form>
        <p class="register-link">
            Pas de compte ? <a href="index.php?route=register">Inscrivez-vous</a>
        </p>
    </div>
</div>

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

.register-link {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 1rem;
    color: var(--color-gray);
}

.register-link a {
    color: var(--color-primary);
    font-weight: 500;
    position: relative;
    transition: all var(--transition);
}

.register-link a:hover {
    color: var(--color-primary-dark);
}

.register-link a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background: var(--color-primary);
    bottom: -2px;
    left: 0;
    transition: width var(--transition);
}

.register-link a:hover::after {
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
