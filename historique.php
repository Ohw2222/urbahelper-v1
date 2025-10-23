<?php
require("conf.php");
require("database.php");

$db = initializeDatabase();
$message = '';

// Gérer la suppression d'un dossier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['id'])) {
        try {
            $stmt = $db->prepare("DELETE FROM dossiers WHERE id = :id");
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->execute();
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">Dossier supprimé avec succès.</div>';
        } catch (PDOException $e) {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">Erreur lors de la suppression : ' . $e->getMessage() . '</div>';
        }
    }
}

// Récupérer tous les dossiers
$dossiers = [];
try {
    $stmt = $db->query("SELECT * FROM dossiers ORDER BY updated_at DESC");
    $dossiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">Erreur de lecture des dossiers : ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbaHelper - Historique des dossiers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-inter text-gray-800 p-4 sm:p-6 lg:p-8">
<div class="container mx-auto max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-4xl font-bold text-indigo-700">Historique des dossiers</h1>
        <a href="index.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">
            &larr; Retour à l'accueil
        </a>
    </div>

    <?php if ($message) echo '<div class="mb-4">' . $message . '</div>'; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Dossier</th>
                    <th scope="col" class="px-6 py-3">Pétitionnaire / Instructeur</th>
                    <th scope="col" class="px-6 py-3">Commune</th>
                    <th scope="col" class="px-6 py-3">Dernière modification</th>
                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dossiers)): ?>
                <tr class="bg-white border-b"><td colspan="7" class="px-6 py-4 text-center">Aucun dossier sauvegardé pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($dossiers as $dossier):
                    $data = json_decode($dossier['form_data'], true);
                    $commune_info = isset($data['commune']) ? GetTownFromCode($data['commune']) : ['name' => 'N/A'];
                    

                    $iddata = htmlspecialchars(
                        $data['formatted_id'] ?? (
                            $data['id'] ?? (
                                json_decode($data["existing_data"],true)["id"] 
                                ??'00000'
                            )
                        )
                    );

                    switch(strlen($iddata)){
                        case 1: $iddata = "0000".$iddata; break;
                        case 2: $iddata = "000".$iddata; break;
                        case 3: $iddata = "00".$iddata; break;
                        case 4: $iddata = "0".$iddata; break;
                        default: break;
                    }
                ?>
                <tr class="bg-white border-b hover:bg-gray-50">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($dossier['id']) ?></th>
                    <td class="px-6 py-4"><?= substr(htmlspecialchars($data['type'] ?? 'XX'),0,2)." 053 ".htmlspecialchars($data['commune'] ?? 'N/A')." ".date("y")." ".$iddata; ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($data['nom'] ?? 'N/A') ?> / <?= htmlspecialchars($data['instructeur'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($commune_info['name']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars(date("d/m/Y H:i", strtotime($dossier['updated_at']))) ?></td>
                    <td class="px-6 py-4 text-center">
                        <a href="edit.php?id=<?= $dossier['id'] ?>" class="font-medium text-indigo-600 hover:underline mr-4">Éditer</a>
                        <form method="POST" action="historique.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ? Cette action est irréversible.');" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $dossier['id'] ?>">
                            <button type="submit" class="font-medium text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
