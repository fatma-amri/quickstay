<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
Middleware::adminOnly(); // Apply admin middleware

// Fetch data
$properties = dbQuery("SELECT * FROM properties")->fetchAll();
$pendingBookings = dbQuery("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/navbar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/messages.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fa-solid fa-tachometer-alt"></i> Tableau de Bord Administrateur</h1>
        <p class="welcome-note">Bienvenue, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>! Gérez vos propriétés et réservations avec style.</p>
    </div>
    <div class="dashboard-grid">
        <!-- Stats Card -->
        <div class="dashboard-card stats-card">
            <div class="card-icon">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <h3>Statistiques</h3>
            <p>Total Propriétés: <span class="stat-value"><?= count($properties) ?></span></p>
            <p>Réservations en Attente: <span class="stat-value"><?= $pendingBookings ?></span></p>
        </div>
        <!-- Add Property Card -->
        <a href="index.php?route=add_property" class="dashboard-card action-card">
            <div class="card-icon">
                <i class="fa-solid fa-plus"></i>
            </div>
            <h3>Ajouter une Propriété</h3>
            <p>Ajoutez une nouvelle propriété à la liste.</p>
        </a>
        <!-- Bookings List Card -->
        <a href="index.php?route=admin_bookings" class="dashboard-card action-card">
            <div class="card-icon">
                <i class="fa-solid fa-book"></i>
            </div>
            <h3>Gérer les Réservations</h3>
            <p>Voir et gérer les réservations des utilisateurs.</p>
            <?php if ($pendingBookings > 0): ?>
                <span class="notification-badge pulse"><?= $pendingBookings ?></span>
            <?php endif; ?>
        </a>
        <!-- Properties List Card -->
        <a href="index.php?route=properties_list" class="dashboard-card action-card">
            <div class="card-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <h3>Gérer les Propriétés</h3>
            <p>Consultez et supprimez les propriétés existantes.</p>
        </a>
    </div>
    <div class="dashboard-footer">
        <a href="index.php" class="back-link"><i class="fa-solid fa-home"></i> Retour à l'Accueil</a>
        <a href="index.php?route=logout" class="btn logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</a>
    </div>
</div>

<style>
/* Base styles for the dashboard container */
.dashboard-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    font-family: 'Poppins', sans-serif;
}

/* Header styles with a modern gradient and subtle animation */
.dashboard-header {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);
    color: #fff;
    border-radius: 15px 15px 0 0;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
    opacity: 0.3;
}

.dashboard-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.7rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.8s ease-out;
}

.welcome-note {
    font-size: 1.2rem;
    font-weight: 300;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Grid layout for cards */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 2rem;
}

/* Card styles with glassmorphism effect */
.dashboard-card {
    position: relative;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
}

/* Stats card with a unique gradient */
.stats-card {
    background: linear-gradient(135deg, #a1ffce 0%, #faffd1 100%);
}

/* Action cards with vibrant gradients */
.action-card:nth-child(2) {
    background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
}

.action-card:nth-child(3) {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
}

.action-card:nth-child(4) {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}

/* Card icon styling */
.card-icon {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    transition: transform 0.3s ease;
}

.card-icon i {
    font-size: 2rem;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.dashboard-card:hover .card-icon {
    transform: scale(1.1);
}

/* Card content */
.dashboard-card h3 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.8rem;
}

.dashboard-card p {
    font-size: 1rem;
    color: #666;
    font-weight: 400;
}

.stat-value {
    font-weight: 700;
    color: #6b48ff;
    font-size: 1.2rem;
}

/* Notification badge with pulse animation */
.notification-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #ff4757;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.5rem 0.8rem;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.notification-badge.pulse {
    animation: pulse 2s infinite;
}

/* Footer links */
.dashboard-footer {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    padding: 1rem;
}

.back-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    color: #6b48ff;
    text-decoration: none;
    transition: color 0.3s ease, transform 0.3s ease;
}

.back-link:hover {
    color: #00ddeb;
    transform: scale(1.05);
}

.logout-btn {
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    background: linear-gradient(135deg, #ff4757 0%, #e03131 100%);
    color: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logout-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(255, 71, 87, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 71, 87, 0);
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .dashboard-header h1 {
        font-size: 2rem;
    }

    .welcome-note {
        font-size: 1rem;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .dashboard-card {
        padding: 1.5rem;
    }

    .dashboard-card h3 {
        font-size: 1.2rem;
    }

    .dashboard-footer {
        flex-direction: column;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .dashboard-header {
        padding: 1rem;
    }

    .dashboard-header h1 {
        font-size: 1.6rem;
    }

    .welcome-note {
        font-size: 0.9rem;
    }

    .dashboard-card {
        padding: 1rem;
    }

    .card-icon {
        width: 50px;
        height: 50px;
    }

    .card-icon i {
        font-size: 1.5rem;
    }
}
</style>
