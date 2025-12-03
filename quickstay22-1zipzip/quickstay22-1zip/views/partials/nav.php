<?php
require_once __DIR__ . '/../../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickStay</title>
    <?php
// Output links after PHP logic
echo '<link rel="stylesheet" href="' . BASE_URL . 'public/css/global.css">';
echo '<link rel="stylesheet" href="' . BASE_URL . 'public/css/navbar.css">';
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />';
?>
</head>
<body>
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <a href="index.php" class="logo" aria-label="QuickStay Home">
        <i class="fa-solid fa-bed"></i> QuickStay
    </a>
    <span class="navbar-toggle" aria-label="Toggle menu" tabindex="0">
        <i class="fa-solid fa-bars menu-icon"></i>
        <i class="fa-solid fa-times close-icon" style="display: none;"></i>
    </span>
    <div class="navbar-links">
        <a href="index.php" class="<?= empty($_GET['route'] ?? '') ? 'active' : '' ?>" aria-current="page">
            <i class="fa-solid fa-home"></i> Accueil
        </a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="index.php?route=admin_dashboard" class="<?= $route === 'admin_dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i> Tableau de bord
                </a>
                <a href="index.php?route=properties_list" class="<?= $route === 'properties_list' ? 'active' : '' ?>">
                    <i class="fa-solid fa-building"></i> Propriétés
                </a>
                <a href="index.php?route=admin_bookings" class="<?= $route === 'admin_bookings' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-check"></i> Réservations
                </a>
                <a href="index.php?route=admin_users" class="<?= $route === 'admin_users' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Utilisateurs
                </a>
            <?php else: ?>
                <a href="index.php?route=user_properties" class="<?= ($_GET['route'] ?? '') === 'user_properties' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i> Propriétés
                </a>
                <a href="index.php?route=my_bookings" class="<?= ($_GET['route'] ?? '') === 'my_bookings' ? 'active' : '' ?>">
                    <i class="fa-solid fa-bookmark"></i> Mes réservations
                </a>
            <?php endif; ?>
            
            <a href="index.php?route=logout" class="btn" aria-label="Logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion
            </a>
        <?php else: ?>
            <a href="index.php?route=login" class="<?= ($_GET['route'] ?? '') === 'login' ? 'active' : '' ?>">
                <i class="fa-solid fa-sign-in-alt"></i> Connexion
            </a>
            <a href="index.php?route=register" class="btn <?= ($_GET['route'] ?? '') === 'register' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-plus"></i> Créer un compte
            </a>
        <?php endif; ?>
        <button class="theme-toggle" aria-label="Toggle dark mode">
            <i class="fa-solid fa-moon"></i>
            <i class="fa-solid fa-sun" style="display: none;"></i>
        </button>
    </div>
</nav>
<script>
document.querySelector('.navbar-toggle').addEventListener('click', () => {
    toggleMenu();
});

document.querySelector('.navbar-toggle').addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleMenu();
    }
});

function toggleMenu() {
    const navbar = document.querySelector('.navbar');
    const menuIcon = document.querySelector('.menu-icon');
    const closeIcon = document.querySelector('.close-icon');
    const isActive = navbar.classList.toggle('active');
    document.querySelector('.navbar-toggle').setAttribute('aria-expanded', isActive);
    menuIcon.style.display = isActive ? 'none' : 'inline';
    closeIcon.style.display = isActive ? 'inline' : 'none';
}

// Theme toggle functionality
document.querySelector('.theme-toggle').addEventListener('click', () => {
    const body = document.body;
    const moonIcon = document.querySelector('.fa-moon');
    const sunIcon = document.querySelector('.fa-sun');
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    moonIcon.style.display = isDark ? 'none' : 'inline';
    sunIcon.style.display = isDark ? 'inline' : 'none';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
});

// Load saved theme on page load
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    const body = document.body;
    const moonIcon = document.querySelector('.fa-moon');
    const sunIcon = document.querySelector('.fa-sun');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        moonIcon.style.display = 'none';
        sunIcon.style.display = 'inline';
    }
});
</script></body>
</html>
