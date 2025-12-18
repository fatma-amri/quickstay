#!/bin/bash

echo "🚀 Configuration de QuickStay avec MySQL (XAMPP)"
echo "================================================"
echo ""

# Vérifier si MySQL est accessible
echo "📡 Vérification de la connexion MySQL..."
if /Applications/XAMPP/bin/mysql -u root -e "SELECT 1" > /dev/null 2>&1; then
    echo "✅ MySQL est accessible"
else
    echo "❌ Erreur : MySQL n'est pas accessible"
    echo "   Assurez-vous que XAMPP MySQL est démarré"
    exit 1
fi

# Créer la base de données
echo ""
echo "🗄️  Création de la base de données 'quickstay'..."
/Applications/XAMPP/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS quickstay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "✅ Base de données créée"

# Créer le schéma
echo ""
echo "📋 Création des tables..."
cd /Users/fatmaamri/Downloads/quickstay22-1zipzipzip/quickstay22-1zipzip/quickstay22-1zip/symfony
php bin/console doctrine:schema:create
echo "✅ Tables créées"

# Charger les fixtures
echo ""
echo "📦 Chargement des données de test..."
php bin/console doctrine:fixtures:load --no-interaction
echo "✅ Données chargées"

# Vérifier
echo ""
echo "🔍 Vérification de la configuration..."
php bin/console doctrine:schema:validate

echo ""
echo "🎉 Configuration terminée !"
echo ""
echo "Comptes de test :"
echo "  - Admin : admin@quickstay.tn / admin123"
echo "  - User  : user1@quickstay.tn / user123"
echo ""
echo "Démarrez le serveur avec :"
echo "  php -S 0.0.0.0:8000 -t public"
echo ""
