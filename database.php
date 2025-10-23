<?php

function initializeDatabase() {
    
    $dbFile = __DIR__.'/ressources/urbahelper_resources.sqlite'; // Nom du fichier de la base de données SQLite
    try {
        $db = new PDO('sqlite:' . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table si elle n'existe pas
        $db->exec("CREATE TABLE IF NOT EXISTS resources (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL, -- 'file' ou 'link'
            path TEXT NOT NULL, -- Chemin du fichier ou URL du lien
            filename TEXT,      -- Nom original du fichier (NULL pour les liens)
            description TEXT,
            keywords TEXT,      -- Mots-clés séparés par des virgules
            upload_date DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        
        // Créer la table des dossiers pour l'historique
        $db->exec("CREATE TABLE IF NOT EXISTS dossiers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            form_data TEXT NOT NULL,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL
        )");

        return $db;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
