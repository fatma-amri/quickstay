<?php



// Database configuration
$db_host = '127.0.0.1:3307';
$db_user = 'root';
$db_pass = '';
$db_name = 'quickstay';  // Using the project name as database name

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT id, name, email, role FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

?>

<div class="container">
    <h2><i class="fa-solid fa-users"></i> Gestion des Utilisateurs</h2>

    <a href="index.php?route=admin_add_user" class="btn btn-primary" style="margin-bottom: 1.5rem; display: inline-block;">
        <i class="fa-solid fa-plus"></i> Ajouter un utilisateur
    </a>

    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-exclamation-circle" style="font-size: 2rem;"></i>
            <h3>Aucun utilisateur trouvé.</h3>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($user['role'] ?? '') ?></td>
                        <td>
                            <a href="index.php?route=admin_edit_user&id=<?= htmlspecialchars($user['id'] ?? '') ?>" class="btn-small btn-secondary"><i class="fa-solid fa-edit"></i> Éditer</a>
                            <a href="index.php?route=admin_delete_user&id=<?= htmlspecialchars($user['id'] ?? '') ?>" class="btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"><i class="fa-solid fa-trash"></i> Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
/* Basic table styling - you might want to move this to a CSS file */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1.5rem;
    box-shadow: var(--shadow-md);
    background-color: var(--color-surface);
    border-radius: var(--radius);
    overflow: hidden; /* Ensures rounded corners apply to children */
}

.data-table th,
.data-table td {
    padding: 1em;
    text-align: left;
    border-bottom: 1px solid var(--color-border);
}

.data-table th {
    background-color: var(--color-primary-light);
    color: var(--color-primary-dark);
    font-weight: 600;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

.data-table tbody tr:hover {
    background-color: var(--color-hover);
}

.btn-small {
    padding: 0.4em 0.8em;
    font-size: 0.9em;
    margin-right: 0.5em;
    border-radius: var(--radius-small);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3em;
}

.btn-secondary {
    background-color: var(--color-secondary);
    color: white;
}

.btn-secondary:hover {
    background-color: var(--color-secondary-dark);
}

.btn-danger {
    background-color: var(--color-danger);
    color: white;
}

.btn-danger:hover {
    background-color: var(--color-danger-dark);
}

.empty-state {
    background: #ffe5e9;
    color: var(--color-danger);
    border: 2px solid #f5c6cb;
    padding: 2rem;
    border-radius: var(--radius);
    text-align: center;
    margin-top: 2rem;
}

.empty-state i {
    margin-bottom: 0.5rem;
}
</style>
