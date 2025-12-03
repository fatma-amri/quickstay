<?php
class Property
{
    public static function add($title, $description, $image, $price)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO properties (title, description, image, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $image, $price]);
    }

    public static function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to find a property by its ID
    public static function findById($pdo, $id)
    {
        // Using the passed $pdo connection
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $title, $description, $image, $price)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE properties SET title = ?, description = ?, image = ?, price = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $image, $price, $id]);
    }

    public static function delete($id)
    {
        global $pdo;
        // D'abord, récupérer l'image pour la supprimer
        $property = self::findById($pdo, $id);
        if ($property && !empty($property['image'])) {
            $imagePath = __DIR__ . '/../' . $property['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Ensuite, supprimer la propriété de la base de données
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
