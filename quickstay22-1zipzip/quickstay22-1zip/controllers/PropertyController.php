<?php
require_once __DIR__ . '/../models/Property.php';

class PropertyController
{
    public static function dashboard()
    {
        $properties = Property::all();
        include __DIR__ . '/../views/admin/properties_list.php';
    }

    public static function add()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Log incoming data for debugging
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);

            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
                    $imgName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $imgPath = __DIR__ . '/../public/images/' . $imgName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
                        $image = 'public/images/' . $imgName;
                    } else {
                        $error = "Erreur lors de l'upload de l'image. Vérifiez les permissions.";
                        error_log("Upload failed: " . error_get_last()['message']);
                    }
                } else {
                    $error = "Type de fichier invalide ou taille dépasse 5MB.";
                }
            } else {
                $error = "Erreur d'upload: " . ($_FILES['image']['error'] ?? 'Aucun fichier');
                error_log("Upload error code: " . ($_FILES['image']['error'] ?? 'No file'));
            }

            if (!$title || !$description || !$price || !$image) {
                $error = "Tous les champs sont obligatoires.";
                error_log("Validation failed: title=$title, description=$description, price=$price, image=$image");
            } else {
                try {
                    error_log("Attempting to add property: $title, $price, $image");
                    Property::add($title, $description, $image, $price);
                    $_SESSION['flash_message'] = "Propriété ajoutée avec succès !";
                    $_SESSION['flash_type'] = "success";
                    redirect('index.php?route=admin_dashboard');
                } catch (Exception $e) {
                    $error = "Erreur lors de l'ajout : " . $e->getMessage();
                    error_log("Property add failed: " . $e->getMessage());
                }
            }
        }
        include __DIR__ . '/../views/admin/add_property.php';
    }

    public static function edit()
    {
        if (!isAdmin()) {
            redirect('index.php?route=login');
            return;
        }

        $error = '';
        $property_id = $_GET['id'] ?? null;

        if (!$property_id) {
            $_SESSION['flash_message'] = "ID de propriété non spécifié.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=properties_list');
            return;
        }

        global $pdo;
        $property = Property::findById($pdo, $property_id);

        if (!$property) {
            $_SESSION['flash_message'] = "Propriété non trouvée.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=properties_list');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $image = $property['image']; // Garder l'image existante par défaut

            // Gérer le nouvel upload d'image si présent
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
                    $imgName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $imgPath = __DIR__ . '/../public/images/' . $imgName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
                        // Supprimer l'ancienne image si elle existe
                        if (!empty($property['image']) && file_exists(__DIR__ . '/../' . $property['image'])) {
                            unlink(__DIR__ . '/../' . $property['image']);
                        }
                        $image = 'public/images/' . $imgName;
                    } else {
                        $error = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $error = "Type de fichier invalide ou taille dépasse 5MB.";
                }
            }

            if (!$title || !$description || !$price) {
                $error = "Tous les champs sont obligatoires.";
            } else {
                try {
                    Property::update($property_id, $title, $description, $image, $price);
                    $_SESSION['flash_message'] = "Propriété mise à jour avec succès !";
                    $_SESSION['flash_type'] = "success";
                    redirect('index.php?route=properties_list');
                } catch (Exception $e) {
                    $error = "Erreur lors de la mise à jour : " . $e->getMessage();
                }
            }
        }

        include __DIR__ . '/../views/admin/edit_property.php';
    }

    public static function delete()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            try {
                Property::delete($id);
                $_SESSION['flash_message'] = "Propriété supprimée avec succès !";
                $_SESSION['flash_type'] = "success";
            } catch (Exception $e) {
                $_SESSION['flash_message'] = "Erreur lors de la suppression : " . $e->getMessage();
                $_SESSION['flash_type'] = "error";
                error_log("Property delete failed: " . $e->getMessage());
            }
        }
        redirect('index.php?route=admin_dashboard');
    }

    public static function showPropertyDetails($id)
    {
        global $pdo; // Access the global database connection
        // Assuming Property model has a static findById method that accepts the pdo connection
        $property = Property::findById($pdo, $id);

        if ($property) {
            // Make $property available to the included view
            $property_id = $property['id']; // Pass property ID to the reservation form
            include __DIR__ . '/../views/property/show.php';
        } else {
            $_SESSION['flash_message'] = "Propriété introuvable.";
            $_SESSION['flash_type'] = "error";
            self::redirect('index.php?route=home'); // Redirect if property not found
        }
    }

    public static function listForUser()
    {
        $properties = Property::all();
        include __DIR__ . '/../views/user/properties.php';
    }

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
