<?php
// Fichier: index.php
// Nom de l'application: UrbaHelper Ressources
// Description: Un simple gestionnaire de connaissances partagées permettant d'uploader des fichiers ou d'ajouter des liens,
//              avec une description et des mots-clés pour faciliter la recherche.

// --- Configuration ---
$uploadDirectory = 'uploads/'; // Répertoire pour stocker les fichiers uploadés

// --- Initialisation de la base de données ---
include_once('../database.php');
$db = initializeDatabase();

// --- Function to display custom messages ---
function showCustomMessage($msg, $type = 'success') {
    echo "<div class='message {$type}'>" . htmlspecialchars($msg) . "</div>";
}

// --- Gestion de l'upload de fichier ou de l'ajout de lien, modification, suppression ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_resource':
                $description = trim($_POST['description'] ?? '');
                $keywords = trim($_POST['keywords'] ?? '');
                $type = '';
                $path = '';
                $filename = null;
                $message = '';

                if (!empty($_FILES['file_upload']['name'])) {
                    // Traitement de l'upload de fichier
                    $type = 'file';
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true); // Créer le répertoire si inexistant
                    }

                    $originalFilename = basename($_FILES['file_upload']['name']);
                    $sanitizedFilename = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $originalFilename); // Nettoyer le nom de fichier
                    $uniqueFilename = uniqid() . '_' . $sanitizedFilename; // Nom unique pour éviter les conflits
                    $destinationPath = $uploadDirectory . $uniqueFilename;

                    if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $destinationPath)) {
                        $path = $destinationPath;
                        $filename = $originalFilename;
                        $message = "Fichier '$originalFilename' uploadé avec succès.";
                    } else {
                        $message = "Erreur lors de l'upload du fichier.";
                    }
                } elseif (!empty($_POST['link_url'])) {
                    // Traitement de l'ajout de lien
                    $type = 'link';
                    $path = trim($_POST['link_url']);
                    if (!filter_var($path, FILTER_VALIDATE_URL)) {
                        $message = "Le lien fourni n'est pas une URL valide.";
                        $path = ''; // Réinitialiser le chemin si invalide
                    } else {
                        $message = "Lien ajouté avec succès.";
                    }
                } else {
                    $message = "Veuillez soit uploader un fichier, soit fournir un lien.";
                }

                if (!empty($path) && !empty($description)) {
                    try {
                        $stmt = $db->prepare("INSERT INTO resources (type, path, filename, description, keywords) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$type, $path, $filename, $description, $keywords]);
                        $message .= " Ressource enregistrée dans la base de données.";
                    } catch (PDOException $e) {
                        $message = "Erreur lors de l'enregistrement de la ressource : " . $e->getMessage();
                    }
                } elseif (empty($description) && !empty($path)) {
                    $message = "Veuillez fournir une description pour la ressource.";
                }
                echo "<script>window.onload = function() { showCustomMessage('" . addslashes($message) . "', '" . (strpos($message, 'Erreur') !== false ? 'error' : 'success') . "'); }</script>";
                break;

            case 'edit_resource':
                $resourceId = $_POST['resource_id'] ?? null;
                $description = trim($_POST['description'] ?? '');
                $keywords = trim($_POST['keywords'] ?? '');
                $response = ['success' => false, 'message' => ''];

                if ($resourceId && !empty($description)) {
                    try {
                        $stmt = $db->prepare("UPDATE resources SET description = ?, keywords = ? WHERE id = ?");
                        $stmt->execute([$description, $keywords, $resourceId]);
                        $response['success'] = true;
                        $response['message'] = "Ressource mise à jour avec succès.";
                        $response['description'] = $description;
                        $response['keywords'] = $keywords;
                    } catch (PDOException $e) {
                        $response['message'] = "Erreur lors de la mise à jour de la ressource : " . $e->getMessage();
                    }
                } else {
                    $response['message'] = "ID de ressource ou description manquante.";
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit; // Stop script execution after sending JSON response
                break;

            case 'delete_resource':
                $resourceId = $_POST['resource_id'] ?? null;
                $response = ['success' => false, 'message' => ''];

                if ($resourceId) {
                    try {
                        // Get resource path to delete file if it's a file type
                        $stmt = $db->prepare("SELECT type, path FROM resources WHERE id = ?");
                        $stmt->execute([$resourceId]);
                        $resource = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($resource && $resource['type'] === 'file') {
                            $filePath = $resource['path'];
                            if (file_exists($filePath)) {
                                if (!unlink($filePath)) {
                                    $response['message'] = "Erreur lors de la suppression du fichier sur le serveur.";
                                    // Don't delete from DB if file deletion failed
                                    header('Content-Type: application/json');
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        // Delete from database
                        $stmt = $db->prepare("DELETE FROM resources WHERE id = ?");
                        $stmt->execute([$resourceId]);
                        $response['success'] = true;
                        $response['message'] = "Ressource supprimée avec succès.";
                    } catch (PDOException $e) {
                        $response['message'] = "Erreur lors de la suppression de la ressource : " . $e->getMessage();
                    }
                } else {
                    $response['message'] = "ID de ressource manquant.";
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit; // Stop script execution after sending JSON response
                break;
        }
    }
}

// --- Gestion de la recherche ---
$searchResults = [];
if (isset($_GET['action']) && $_GET['action'] === 'search_resource') {
    $searchQuery = trim($_GET['search_query'] ?? '');
    $searchType = $_GET['search_type'] ?? 'description'; // 'description' ou 'keywords'

    if (!empty($searchQuery)) {
        $searchTerm = '%' . $searchQuery . '%';
        $sql = "";
        if ($searchType === 'description') {
            $sql = "SELECT * FROM resources WHERE description LIKE :searchTerm ORDER BY upload_date DESC";
        } elseif ($searchType === 'keywords') {
            $sql = "SELECT * FROM resources WHERE keywords LIKE :searchTerm ORDER BY upload_date DESC";
        }

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "Erreur lors de la recherche : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez entrer un terme de recherche.";
    }
}

// --- Récupérer toutes les ressources pour affichage initial ---
$allResources = [];
try {
    $stmt = $db->query("SELECT * FROM resources ORDER BY upload_date DESC");
    $allResources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des ressources : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbaHelper - Ressources</title>
    <link rel="apple-touch-icon" sizes="57x57" href="../apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="manifest" href="../manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 900px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 0.75rem; /* rounded-xl */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-md */
        }
        .btn-primary {
            background-color: #4f46e5; /* indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem; /* rounded-lg */
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #4338ca; /* indigo-700 */
        }
        .btn-secondary {
            background-color: #6b7280; /* gray-500 */
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }
        .btn-secondary:hover {
            background-color: #4b5563; /* gray-600 */
        }
        input[type="text"], input[type="url"], textarea, input[type="file"] {
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            width: 100%;
            box-sizing: border-box;
        }
        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .message.success {
            background-color: #d1fae5; /* green-100 */
            color: #065f46; /* green-800 */
        }
        .message.error {
            background-color: #fee2e2; /* red-100 */
            color: #991b1b; /* red-800 */
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s;
        }
        .modal-overlay.open {
            visibility: visible;
            opacity: 1;
        }
        .modal-content {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            position: relative;
            max-width: 90vw; /* Max width 90% of viewport width */
            max-height: 90vh; /* Max height 90% of viewport height */
            overflow: auto; /* Enable scrolling for content that overflows */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }
        .modal-close:hover {
            color: #4b5563;
        }
        .modal-body {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1; /* Allows it to take available space */
        }
        .modal-body img, .modal-body iframe, .modal-body object {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            display: block; /* Remove extra space below image */
        }
        .modal-body iframe, .modal-body object {
            min-width: 80vw; /* Ensure iframe takes up a good portion of the modal */
            min-height: 70vh;
            border: 1px solid #e5e7eb; /* Light border for iframe/object */
        }
        /* Responsive adjustments for modal */
        @media (max-width: 768px) {
            .modal-content {
                padding: 1rem;
            }
            .modal-body iframe, .modal-body object {
                min-width: 95vw;
                min-height: 60vh;
            }
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem; /* Space between buttons */
        }
        .action-buttons button, .action-buttons a {
            padding: 0.5rem 0.75rem; /* Smaller padding for action buttons */
            border-radius: 0.5rem;
            font-size: 0.875rem; /* text-sm */
            line-height: 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }
        .action-buttons button i, .action-buttons a i {
            margin-right: 0.25rem; /* Small margin for icon */
        }
        .action-buttons button:last-child i, .action-buttons a:last-child i {
            margin-right: 0; /* No margin for single icon buttons */
        }
        .open-btn {
            background-color: #10b981; /* green-500 */
            color: white;
        }
        .open-btn:hover {
            background-color: #059669; /* green-600 */
        }
        .preview-btn {
            background-color: #6366f1; /* indigo-500 */
            color: white;
            padding: 0.5rem; /* Smaller padding for icon-only button */
        }
        .preview-btn:hover {
            background-color: #4f46e5; /* indigo-600 */
        }
        .edit-btn {
            background-color: #f59e0b; /* amber-500 */
            color: white;
        }
        .edit-btn:hover {
            background-color: #d97706; /* amber-600 */
        }
        .delete-btn {
            background-color: #ef4444; /* red-500 */
            color: white;
        }
        .delete-btn:hover {
            background-color: #dc2626; /* red-600 */
        }
        /* Custom message box */
        #customMessageContainer {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1001;
        }
        .custom-message {
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: fadeOut 5s forwards;
        }
        .custom-message.success {
            background-color: #d1fae5; /* green-100 */
            color: #065f46; /* green-800 */
        }
        .custom-message.error {
            background-color: #fee2e2; /* red-100 */
            color: #991b1b; /* red-800 */
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; display: none; }
        }

        /* Tooltip Styles */
        .tooltip-container {
            position: relative;
            display: inline-flex; /* Ensures it wraps content and allows positioning of tooltip */
            align-items: center;
            justify-content: center;
        }

        .tooltip-text {
            visibility: hidden;
            opacity: 0;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 0.25rem;
            padding: 0.5rem 0.75rem;
            position: absolute;
            z-index: 1;
            bottom: 125%; /* Position the tooltip above the button */
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap; /* Prevent text wrapping */
            transition: opacity 0.3s, visibility 0.3s;
            font-size: 0.75rem; /* Smaller font for tooltip */
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%; /* At the bottom of the tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body class="p-4">
    <div class="container mx-auto p-6 bg-gray-100 rounded-xl shadow-lg">
        <a href="../" class="btn-secondary">Retour</a>
        <h1 class="text-4xl font-bold text-center text-indigo-700 mb-8">UrbaHelper Ressources</h1>

        <!-- Custom Message Container -->
        <div id="customMessageContainer"></div>

        <!-- Section Ajouter une Ressource -->
        <div class="card p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Ajouter une Nouvelle Ressource</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="add_resource">

                <div>
                    <label for="file_upload" class="block text-gray-700 text-sm font-bold mb-2">Uploader un Fichier :</label>
                    <input type="file" id="file_upload" name="file_upload" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                </div>

                <div class="text-center text-gray-600">
                    — OU —
                </div>

                <div>
                    <label for="link_url" class="block text-gray-700 text-sm font-bold mb-2">Ajouter un Lien (URL) :</label>
                    <input type="url" id="link_url" name="link_url" placeholder="Ex: https://www.exemple.com/document" class="focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                <div>
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description de la Ressource :</label>
                    <textarea id="description" name="description" rows="3" placeholder="Décrivez le contenu de la ressource..." required class="focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="keywords" class="block text-gray-700 text-sm font-bold mb-2">Mots-clés (séparés par des virgules) :</label>
                    <input type="text" id="keywords" name="keywords" placeholder="Ex: urbanisme, plan, développement durable" class="focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="btn-primary">Ajouter la Ressource</button>
                </div>
            </form>
        </div>

        <!-- Section Rechercher des Ressources -->
        <div class="card p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Rechercher des Ressources</h2>
            <form action="index.php" method="GET" class="space-y-4">
                <input type="hidden" name="action" value="search_resource">
                <div>
                    <label for="search_query" class="block text-gray-700 text-sm font-bold mb-2">Terme de Recherche :</label>
                    <input type="text" id="search_query" name="search_query" placeholder="Entrez votre recherche..." class="focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>"/>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rechercher par :</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="search_type" value="description" class="form-radio text-indigo-600" <?php echo (!isset($_GET['search_type']) || $_GET['search_type'] === 'description') ? 'checked' : ''; ?>>
                            <span class="ml-2 text-gray-700">Description</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="search_type" value="keywords" class="form-radio text-indigo-600" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] === 'keywords') ? 'checked' : ''; ?>>
                            <span class="ml-2 text-gray-700">Mots-clés</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="btn-primary">Rechercher</button>
                </div>
            </form>
        </div>

        <!-- Section Résultats de la Recherche -->
        <?php if (!empty($searchResults)): ?>
            <div class="card p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Résultats de la Recherche</h2>
                <div class="space-y-4">
                    <?php foreach ($searchResults as $resource): ?>
                        <div class="border border-gray-200 p-4 rounded-lg bg-gray-50 flex justify-between items-center" id="resource-<?php echo htmlspecialchars($resource['id']); ?>">
                            <div>
                                <p class="text-lg font-semibold text-indigo-600">
                                    <?php if ($resource['type'] === 'file'): ?>
                                        <a href="<?php echo htmlspecialchars($resource['path']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($resource['filename'] ?? 'Fichier'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($resource['path']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($resource['path']); ?></a>
                                    <?php endif; ?>
                                </p>
                                <p class="text-gray-700 mt-1 resource-description"><strong>Description :</strong> <?php echo htmlspecialchars($resource['description']); ?></p>
                                <p class="text-gray-600 text-sm mt-1 resource-keywords"><strong>Mots-clés :</strong> <?php echo htmlspecialchars($resource['keywords']); ?></p>
                                <p class="text-gray-500 text-xs mt-1">Ajouté le : <?php echo htmlspecialchars(date("d/m/Y à H:i",strtotime($resource['upload_date']))); ?></p>
                            </div>
                            <div class="action-buttons flex-shrink-0">
                                <!-- Open Button -->
                                <a href="<?php echo htmlspecialchars($resource['type'] === 'file' ? $uploadDirectory . basename($resource['path']) : $resource['path']); ?>" target="_blank" class="open-btn">
                                    <i class="fas fa-eye"></i> Ouvrir
                                </a>
                                <!-- Preview Button -->
                                <button class="preview-btn tooltip-container"
                                        data-path="<?php echo htmlspecialchars($resource['path']); ?>"
                                        data-type="<?php echo htmlspecialchars($resource['type']); ?>"
                                        data-filename="<?php echo htmlspecialchars($resource['filename'] ?? ''); ?>">
                                    <i class="fas fa-question"></i>
                                    <span class="tooltip-text">Aperçu</span>
                                </button>
                                <!-- Edit Button -->
                                <button class="edit-btn tooltip-container"
                                        data-id="<?php echo htmlspecialchars($resource['id']); ?>"
                                        data-description="<?php echo htmlspecialchars($resource['description']); ?>"
                                        data-keywords="<?php echo htmlspecialchars($resource['keywords']); ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                    <span class="tooltip-text">Modifier</span>
                                </button>
                                <!-- Delete Button -->
                                <button class="delete-btn tooltip-container" data-id="<?php echo htmlspecialchars($resource['id']); ?>">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltip-text">Supprimer</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (isset($_GET['action']) && $_GET['action'] === 'search_resource' && !empty($_GET['search_query'])): ?>
            <div class="card p-6 mb-8 text-center text-gray-600">
                Aucun résultat trouvé pour votre recherche.
            </div>
        <?php endif; ?>

        <!-- Section Toutes les Ressources -->
        <div class="card p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Toutes les Ressources</h2>
            <?php if (empty($allResources)): ?>
                <p class="text-gray-600 text-center">Aucune ressource n'a encore été ajoutée.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($allResources as $resource): ?>
                        <div class="border border-gray-200 p-4 rounded-lg bg-gray-50 flex justify-between items-center" id="resource-<?php echo htmlspecialchars($resource['id']); ?>">
                            <div>
                                <p class="text-lg font-semibold text-indigo-600">
                                    <?php if ($resource['type'] === 'file'): ?>
                                        <a href="<?php echo htmlspecialchars($resource['path']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($resource['filename'] ?? 'Fichier'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($resource['path']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($resource['path']); ?></a>
                                    <?php endif; ?>
                                </p>
                                <p class="text-gray-700 mt-1 resource-description"><strong>Description :</strong> <?php echo htmlspecialchars($resource['description']); ?></p>
                                <p class="text-gray-600 text-sm mt-1 resource-keywords"><strong>Mots-clés :</strong> <?php echo htmlspecialchars($resource['keywords']); ?></p>
                                <p class="text-gray-500 text-xs mt-1">Ajouté le: <?php echo htmlspecialchars(date("d/m/Y à H:i",strtotime($resource['upload_date']))); ?></p>
                            </div>
                            <div class="action-buttons flex-shrink-0">
                                <!-- Open Button -->
                                <a href="<?php echo htmlspecialchars($resource['type'] === 'file' ? $uploadDirectory . basename($resource['path']) : $resource['path']); ?>" target="_blank" class="open-btn">
                                    <i class="fas fa-eye"></i> Ouvrir
                                </a>
                                <!-- Preview Button -->
                                <button class="preview-btn tooltip-container"
                                        data-path="<?php echo htmlspecialchars($resource['path']); ?>"
                                        data-type="<?php echo htmlspecialchars($resource['type']); ?>"
                                        data-filename="<?php echo htmlspecialchars($resource['filename'] ?? ''); ?>">
                                    <i class="fas fa-question"></i>
                                    <span class="tooltip-text">Aperçu</span>
                                </button>
                                <!-- Edit Button -->
                                <button class="edit-btn tooltip-container"
                                        data-id="<?php echo htmlspecialchars($resource['id']); ?>"
                                        data-description="<?php echo htmlspecialchars($resource['description']); ?>"
                                        data-keywords="<?php echo htmlspecialchars($resource['keywords']); ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                    <span class="tooltip-text">Modifier</span>
                                </button>
                                <!-- Delete Button -->
                                <button class="delete-btn tooltip-container" data-id="<?php echo htmlspecialchars($resource['id']); ?>">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltip-text">Supprimer</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Preview Modal Structure -->
    <div id="previewModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" id="closePreviewModal">&times;</button>
            <div class="modal-body" id="previewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- The Edit Modal Structure -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" id="closeEditModal">&times;</button>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Modifier la Ressource</h2>
            <form id="editResourceForm" class="space-y-4 w-full">
                <input type="hidden" name="action" value="edit_resource">
                <input type="hidden" id="editResourceId" name="resource_id">

                <div>
                    <label for="editDescription" class="block text-gray-700 text-sm font-bold mb-2">Description de la Ressource :</label>
                    <textarea id="editDescription" name="description" rows="3" required class="focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="editKeywords" class="block text-gray-700 text-sm font-bold mb-2">Mots-clés (séparés par des virgules) :</label>
                    <input type="text" id="editKeywords" name="keywords" class="focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="btn-primary">Enregistrer les Modifications</button>
                </div>
            </form>
        </div>
    </div>

    <!-- The Confirmation Modal Structure (for Delete) -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-content max-w-sm">
            <button class="modal-close" id="closeConfirmModal">&times;</button>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirmer la Suppression</h2>
            <p class="text-gray-700 mb-6 text-center">Êtes-vous sûr de vouloir supprimer cette ressource ? Cette action est irréversible.</p>
            <div class="flex justify-center space-x-4">
                <button id="cancelDeleteBtn" class="btn-secondary">Annuler</button>
                <button id="confirmDeleteBtn" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">Supprimer</button>
            </div>
        </div>
    </div>

    <script>
        // Function to display custom messages (replaces alert())
        function showCustomMessage(message, type = 'success') {
            const container = document.getElementById('customMessageContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `custom-message ${type}`;
            messageDiv.textContent = message;
            container.appendChild(messageDiv);

            // Remove the message after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- Preview Modal Elements ---
            const previewButtons = document.querySelectorAll('.preview-btn');
            const previewModal = document.getElementById('previewModal');
            const closePreviewModalBtn = document.getElementById('closePreviewModal');
            const previewModalBody = document.getElementById('previewModalBody');

            // --- Edit Modal Elements ---
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModal = document.getElementById('editModal');
            const closeEditModalBtn = document.getElementById('closeEditModal');
            const editResourceIdInput = document.getElementById('editResourceId');
            const editDescriptionInput = document.getElementById('editDescription');
            const editKeywordsInput = document.getElementById('editKeywords');
            const editResourceForm = document.getElementById('editResourceForm');

            // --- Delete Confirmation Modal Elements ---
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const confirmModal = document.getElementById('confirmModal');
            const closeConfirmModalBtn = document.getElementById('closeConfirmModal');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let resourceIdToDelete = null; // To store the ID of the resource to be deleted

            // Function to get the full URL for uploaded files
            function getFullUrl(relativePath) {
                // This assumes the PHP script is in the web root or a known subdirectory
                // and 'uploads/' is relative to that.
                // Adjust this base URL if your 'uploads' directory is not directly accessible
                // relative to the current page's URL.
                const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
                return baseUrl + relativePath;
            }

            // --- Preview Modal Logic ---
            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const path = this.dataset.path;
                    const type = this.dataset.type;
                    const filename = this.dataset.filename;

                    previewModalBody.innerHTML = ''; // Clear previous content

                    let contentHtml = '';
                    let resourceUrl = path;

                    if (type === 'file') {
                        // For uploaded files, construct the full URL
                        resourceUrl = getFullUrl(path);
                        const fileExtension = filename.split('.').pop().toLowerCase();

                        if (['jpg', 'jpeg', 'png', 'gif', 'svg'].includes(fileExtension)) {
                            contentHtml = `<img src="${resourceUrl}" alt="Preview" class="rounded-lg shadow-md max-w-full max-h-full object-contain">`;
                        } else if (fileExtension === 'pdf') {
                            contentHtml = `<object data="${resourceUrl}" type="application/pdf" width="100%" height="100%" class="rounded-lg shadow-md">
                                <p>Votre navigateur ne supporte pas l'affichage des PDF. Vous pouvez le <a href="${resourceUrl}" target="_blank" class="text-blue-600 hover:underline">télécharger ici</a>.</p>
                            </object>`;
                        } else if (['odt', 'ods', 'doc', 'docx', 'txt'].includes(fileExtension)) {
                            // Use Google Docs Viewer for other document types if they are publicly accessible
                            contentHtml = `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(resourceUrl)}&embedded=true" frameborder="0" width="100%" height="100%" class="rounded-lg shadow-md">
                                <p>Impossible d'afficher le document. Vous pouvez le <a href="${resourceUrl}" target="_blank" class="text-blue-600 hover:underline">télécharger ici</a>.</p>
                            </iframe>`;
                        } else {
                            contentHtml = `<p class="text-gray-700 text-center">Type de fichier non pris en charge pour l'aperçu direct. Veuillez <a href="${resourceUrl}" target="_blank" class="text-blue-600 hover:underline">télécharger le fichier</a> pour le visualiser.</p>`;
                        }
                    } else if (type === 'link') {
                        // For links, embed in an iframe
                        contentHtml = `<iframe src="${resourceUrl}" frameborder="0" width="100%" height="100%" sandbox="allow-same-origin allow-scripts allow-popups allow-forms" class="rounded-lg shadow-md">
                            <p>Votre navigateur ne supporte pas les iframes. Vous pouvez ouvrir le lien <a href="${resourceUrl}" target="_blank" class="text-blue-600 hover:underline">directement ici</a>.</p>
                        </iframe>`;
                    }

                    previewModalBody.innerHTML = contentHtml;
                    previewModal.classList.add('open');
                });
            });

            closePreviewModalBtn.addEventListener('click', function() {
                previewModal.classList.remove('open');
                previewModalBody.innerHTML = ''; // Clear content when closing
            });

            // Close preview modal when clicking outside the content
            previewModal.addEventListener('click', function(event) {
                if (event.target === previewModal) {
                    previewModal.classList.remove('open');
                    previewModalBody.innerHTML = ''; // Clear content when closing
                }
            });

            // --- Edit Modal Logic ---
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const description = this.dataset.description;
                    const keywords = this.dataset.keywords;

                    editResourceIdInput.value = id;
                    editDescriptionInput.value = description;
                    editKeywordsInput.value = keywords;

                    editModal.classList.add('open');
                });
            });

            closeEditModalBtn.addEventListener('click', function() {
                editModal.classList.remove('open');
            });

            // Close edit modal when clicking outside the content
            editModal.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    editModal.classList.remove('open');
                }
            });

            editResourceForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(this);

                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCustomMessage(data.message, 'success');
                        editModal.classList.remove('open');

                        // Update the displayed resource card directly
                        const resourceCard = document.getElementById(`resource-${data.resource_id}`);
                        if (resourceCard) {
                            resourceCard.querySelector('.resource-description').innerHTML = `<strong>Description :</strong> ${data.description}`;
                            resourceCard.querySelector('.resource-keywords').innerHTML = `<strong>Mots-clés :</strong> ${data.keywords}`;
                        }
                    } else {
                        showCustomMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCustomMessage('Une erreur est survenue lors de la mise à jour.', 'error');
                });
            });

            // --- Delete Confirmation Modal Logic ---
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    resourceIdToDelete = this.dataset.id;
                    confirmModal.classList.add('open');
                });
            });

            closeConfirmModalBtn.addEventListener('click', function() {
                confirmModal.classList.remove('open');
                resourceIdToDelete = null; // Clear the ID
            });

            cancelDeleteBtn.addEventListener('click', function() {
                confirmModal.classList.remove('open');
                resourceIdToDelete = null; // Clear the ID
            });

            // Close confirm modal when clicking outside the content
            confirmModal.addEventListener('click', function(event) {
                if (event.target === confirmModal) {
                    confirmModal.classList.remove('open');
                    resourceIdToDelete = null; // Clear the ID
                }
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (resourceIdToDelete) {
                    const formData = new FormData();
                    formData.append('action', 'delete_resource');
                    formData.append('resource_id', resourceIdToDelete);

                    fetch('index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showCustomMessage(data.message, 'success');
                            confirmModal.classList.remove('open');
                            // Remove the resource card from the DOM
                            const resourceCard = document.getElementById(`resource-${resourceIdToDelete}`);
                            if (resourceCard) {
                                resourceCard.remove();
                            }
                        } else {
                            showCustomMessage(data.message, 'error');
                        }
                        resourceIdToDelete = null; // Clear the ID
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showCustomMessage('Une erreur est survenue lors de la suppression.', 'error');
                        resourceIdToDelete = null; // Clear the ID
                    });
                }
            });
        });
    </script>
</body>
</html>