<?php
require_once __DIR__ . '/../../config.php';
// Middleware is handled in index.php
// require_once __DIR__ . '/../../controllers/Middleware.php';
// Middleware::userOnly(); // Apply user middleware if needed
?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/navbar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/cards.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/messages.css">
<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
    .hero {
        background: linear-gradient(135deg, #f3f6fb, #e0eaff);
        padding: 4rem 2rem;
        text-align: center;
        border-radius: var(--radius);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
    }
    .hero h1 {
        font-size: 3rem;
        color: var(--color-primary-dark);
        margin-bottom: 1rem;
    }
    .hero p {
        font-size: 1.2rem;
        color: var(--color-gray);
        margin-bottom: 1.5rem;
    }
    .empty-state {
        background: #ffe5e9;
        color: var(--color-danger);
        border: 2px solid #f5c6cb;
        padding: 2rem;
        border-radius: var(--radius);
        text-align: center;
        animation: var(--animation-bounce) 0.5s;
    }
    .empty-state .btn {
        margin-top: 1rem;
        padding: 0.8em 2em;
    }
    @media (max-width: 600px) {
        .hero h1 { font-size: 2.2rem; }
        .hero p { font-size: 1rem; }
        .empty-state { padding: 1.5rem; }
    }
</style>
<div class="container">
    <div class="hero">
        <h1><i class="fa-solid fa-house-chimney"></i> Bienvenue sur QuickStay</h1>
        <p>Découvrez des propriétés uniques pour votre prochain séjour.</p>
    </div>
    <?php include __DIR__ . '/../partials/messages.php'; ?>
    <?php if (empty($properties)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-exclamation-circle" style="font-size: 2rem;"></i>
            <h3>Aucune propriété disponible pour le moment.</h3>
            <?php if (isAdmin()): ?>
                <a href="index.php?route=add_property" class="btn">
                    <i class="fa-solid fa-plus"></i> Ajouter une propriété
                </a>
            <?php else: ?>
                <p>Vérifiez à nouveau bientôt ou contactez un administrateur.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="cards-listing">
            <?php foreach ($properties as $p): ?>
                <div class="property-card">
                    <span class="badge">Disponible</span>
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="photo" class="property-image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($p['title'] ?? 'Sans titre') ?></h3>
                    <p><?= nl2br(htmlspecialchars($p['description'] ?? '')) ?></p>
                    <strong><?= htmlspecialchars($p['price'] ?? '0') ?> dt / nuit</strong>
                    <?php if (isUser()): ?>
                        <a href="index.php?route=property_details&id=<?= htmlspecialchars($p['id'] ?? '') ?>" class="btn">
                            <i class="fa-solid fa-info-circle"></i> Voir les détails et réserver
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
