<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
Middleware::adminOnly();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Gestion des Propriétés</title>
    <style>
        /* Define missing CSS variables */
        :root {
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --color-surface: #ffffff;
            --color-border: #e0e0e0;
            --color-primary-light: #f0f4f8;
            --color-primary-dark: #333;
            --color-hover: #f5f5f5;
            --color-secondary: #3498db;
            --color-secondary-dark: #2980b9;
            --color-danger: #e74c3c;
            --color-danger-dark: #c0392b;
            --radius: 8px;
            --radius-small: 4px;
        }

        /* Container for the properties page */
        .properties-container {
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

        /* Header styling with gradient and animation */
        .properties-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);
            color: #fff;
            border-radius: 15px 15px 0 0;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 -5px 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.8s ease-out;
        }

        .properties-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 60%);
            opacity: 0.4;
        }

        .properties-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            margin: 0;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: #fff;
            padding: 0.9rem 1.8rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .add-btn:hover {
            background: linear-gradient(135deg, #219653 0%, #27ae60 100%);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Table container with glassmorphism effect */
        .properties-table-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow-x: auto;
            animation: fadeIn 0.6s ease-out;
        }

        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .data-table th,
        .data-table td {
            padding: 1em;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background-color: #f0f4f8;
            color: #333;
            font-weight: 600;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .btn-small {
            padding: 0.4em 0.8em;
            font-size: 0.9em;
            margin-right: 0.5em;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3em;
        }

        .btn-secondary {
            background-color: #3498db;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .empty-state {
            background: #ffe5e9;
            color: #e74c3c;
            border: 2px solid #f5c6cb;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 2rem;
        }

        .empty-state i {
            margin-bottom: 0.5rem;
        }

        /* Footer styling */
        .properties-footer {
            display: flex;
            justify-content: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 15px 15px;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.1rem;
            font-weight: 500;
            color: #6b48ff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #00ddeb;
            transform: scale(1.05) translateX(-5px);
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .properties-container {
                padding: 1.5rem;
                margin: 1.5rem;
            }

            .properties-header {
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem;
            }

            .properties-header h2 {
                font-size: 2.2rem;
            }

            .add-btn {
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            .properties-table th,
            .properties-table td {
                padding: 1rem;
                font-size: 0.95rem;
            }

            .properties-header h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .properties-container {
                padding: 1rem;
                margin: 1rem;
            }

            .properties-header {
                padding: 1rem;
            }

            .properties-header h2 {
                font-size: 1.5rem;
            }

            .properties-table th,
            .properties-table td {
                padding: 0.8rem;
                font-size: 0.9rem;
            }

            .add-btn,
            .back-link {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="properties-container">
        <div class="properties-header">
            <h2><i class="fa-solid fa-building"></i> Gestion des Propriétés</h2>
            <a href="index.php?route=add_property" class="btn add-btn">
                <i class="fa-solid fa-plus"></i> Ajouter une propriété
            </a>
        </div>

        <?php if (empty($properties)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-circle" style="font-size: 2rem;"></i>
                <h3>Aucune propriété trouvée.</h3>
            </div>
        <?php else: ?>
            <div class="properties-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Localisation</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><?= htmlspecialchars($property['id'] ?? '') ?></td>
                                <td><?= htmlspecialchars($property['title'] ?? '') ?></td>
                                <td><?= htmlspecialchars($property['description'] ?? '') ?></td>
                                <td><?= htmlspecialchars($property['price'] ?? '') ?> dt</td>
                                <td><?= htmlspecialchars($property['location'] ?? '') ?></td>
                                <td><?= htmlspecialchars($property['status'] ?? '') ?></td>
                                <td>
                                    <a href="index.php?route=admin_edit_property&id=<?= htmlspecialchars($property['id'] ?? '') ?>" class="btn-small btn-secondary"><i class="fa-solid fa-edit"></i> Éditer</a>
                                    <a href="index.php?route=delete_property&id=<?= htmlspecialchars($property['id'] ?? '') ?>" class="btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette propriété ?');"><i class="fa-solid fa-trash"></i> Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="properties-footer">
            <a href="index.php?route=admin_dashboard" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Retour au Tableau de Bord
            </a>
        </div>
    </div>
</body>
</html>
