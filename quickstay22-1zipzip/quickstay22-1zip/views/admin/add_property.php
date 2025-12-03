<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
Middleware::adminOnly(); // Apply admin middleware
?>

<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="form-container">
    <div class="form-card admin-form">
        <div class="form-header">
            <h2><i class="fa-solid fa-plus"></i> Ajouter une Propriété</h2>
            <p class="admin-note"><i class="fa-solid fa-shield-halved"></i> Section Réservée aux Administrateurs</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="flash-message error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php include __DIR__ . '/../partials/messages.php'; ?>
        <form method="post" enctype="multipart/form-data" id="property-form" action="index.php?route=add_property">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="title"><i class="fa-solid fa-heading"></i> Titre <span class="required">*</span></label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description"><i class="fa-solid fa-align-left"></i> Description <span class="required">*</span></label>
                <textarea name="description" id="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Prix par nuit (dt) <span class="required">*</span></label>
                <input type="number" name="price" id="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="image" class="file-label"><i class="fa-solid fa-image"></i> Image <span class="required">*</span></label>
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif" required style="display: none;">
                <span class="file-input-btn">Choisir un fichier</span>
                <span class="file-name">Aucun fichier sélectionné</span>
            </div>
            <button type="submit" class="btn admin-btn"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </form>
        <div class="form-footer">
            <a href="index.php?route=admin_dashboard" class="back-link"><i class="fa-solid fa-arrow-left"></i> Retour au Tableau de Bord</a>
        </div>
    </div>
</div>


<script>
    document.querySelector('.file-input-btn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('image').click();
    });
    document.getElementById('image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Aucun fichier sélectionné';
        document.querySelector('.file-name').textContent = fileName;
    });
</script>

<style>
    /* Container for the form */
    .form-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e4f5 100%);
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        background-image: radial-gradient(circle at 10% 20%, rgba(107, 72, 255, 0.1) 0%, transparent 70%);
        font-family: 'Poppins', sans-serif;
    }

    /* Form card with glassmorphism effect */
    .form-card.admin-form {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 3rem;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        animation: fadeIn 0.8s ease-out;
        border-left: 6px solid #6b48ff;
        overflow: hidden;
    }

    .form-card.admin-form::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(107, 72, 255, 0.1) 0%, transparent 70%);
        opacity: 0.3;
        z-index: 0;
        transition: transform 0.4s ease;
    }

    .form-card.admin-form:hover::before {
        transform: scale(1.2);
    }

    /* Form header styling */
    .form-header {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .form-card h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2a2a2a;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 0.5rem;
    }

    .admin-note {
        font-size: 1.1rem;
        font-weight: 400;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        opacity: 0.9;
    }

    /* Form group styling */
    .form-group {
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .form-group label {
        font-size: 1.2rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: color 0.3s ease;
    }

    .form-group label:hover {
        color: #6b48ff;
    }

    .required {
        color: #ff4757;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Input and textarea styling */
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1.1rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.5);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #6b48ff;
        box-shadow: 0 0 12px rgba(107, 72, 255, 0.3);
        transform: scale(1.02);
        background: #fff;
        outline: none;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* File input styling */
    .file-label {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: 500;
        color: #333;
        transition: color 0.3s ease;
    }

    .file-label:hover {
        color: #6b48ff;
    }

    .file-input-btn {
        display: inline-block;
        padding: 0.9rem 1.8rem;
        background: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);
        color: #fff;
        border-radius: 10px;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-top: 0.5rem;
        margin-right: 1rem;
    }

    .file-input-btn:hover {
        background: linear-gradient(135deg, #5a3de6 0%, #00c4d3 100%);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    .file-name {
        font-size: 1rem;
        color: #666;
    }

    /* Submit button styling */
    button.admin-btn {
        width: 100%;
        padding: 1rem;
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        background: #2ecc71;
        color: #fff;
        border-radius: 10px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    button.admin-btn:hover {
        background: #27ae60;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transform: translateY(-4px);
    }

    /* Form footer styling */
    .form-footer {
        display: flex;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0 0 15px 15px;
        margin-top: 1rem;
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
    }

    /* Back link styling */
    .back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        font-size: 1.1rem;
        font-weight: 500;
        color: #6b48ff;
        text-decoration: none;
        padding: 0.5rem 1.2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.2);
    }

    .back-link:hover {
        color: #fff;
        background: #6b48ff;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
            margin: 1.5rem;
        }

        .form-card.admin-form {
            padding: 2rem;
        }

        .form-card h2 {
            font-size: 2rem;
        }

        .admin-note {
            font-size: 1rem;
        }

        .form-group label {
            font-size: 1.1rem;
        }

        .form-group input,
        .form-group textarea {
            font-size: 1rem;
            padding: 0.9rem 1.2rem;
        }

        .form-footer {
            padding: 1rem;
        }
    }

    @media (max-width: 480px) {
        .form-container {
            padding: 1rem;
            margin: 1rem;
        }

        .form-card.admin-form {
            padding: 1.5rem;
            border-left-width: 4px;
        }

        .form-card h2 {
            font-size: 1.8rem;
        }

        .admin-note {
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-size: 1rem;
        }

        .form-group input,
        .form-group textarea {
            font-size: 0.95rem;
            padding: 0.8rem 1rem;
        }

        .file-input-btn,
        button.admin-btn,
        .back-link {
            font-size: 1rem;
        }
    }
</style>
