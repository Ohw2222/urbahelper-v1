<?php
require("conf.php");
require("database.php");

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: historique.php");
    exit();
}

$dossier_id = $_GET['id'];
$data = [];

// Récupérer les données du dossier depuis la base de données
try {
    $db = initializeDatabase();
    $stmt = $db->prepare("SELECT form_data FROM dossiers WHERE id = :id");
    $stmt->bindParam(':id', $dossier_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $data = json_decode($result['form_data'], true);
    } else {
        header("Location: historique.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération du dossier : " . $e->getMessage());
}

$n = $data['type'] ?? '';
if (empty($n)) {
    die("Type de dossier non trouvé dans les données sauvegardées.");
}

$all_docs_for_type = $CONF['documents'][$n] ?? [];
$all_cerfa_items = $CONF['CERFA'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbaHelper - Édition du dossier #<?= htmlspecialchars($dossier_id) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            transition: opacity 0.3s ease;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 0.5rem;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .modal.show .modal-content {
            transform: scale(1);
        }
        .autocomplete-active {
            background-color: #bfdbfe;
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
<main class="w-full max-w-xl mx-auto bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 my-8">
    <form method="POST" action="fill.php">
        <a href="historique.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mb-4">Retour à l'historique</a>
        <h1 class="text-3xl font-bold text-center text-indigo-700 mb-6">Édition du dossier <?= $n; ?></h1>
        
        <input type="hidden" name="type" value="<?= htmlspecialchars($n) ?>">
        <input type="hidden" name="dossier_id" value="<?= htmlspecialchars($dossier_id) ?>">
        <input type="hidden" name="existing_data" value="<?= htmlspecialchars(json_encode($data)) ?>">

        <div class="mt-4 flex items-center mb-4">
            <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" name="sve" id="sve" <?= isset($data['sve']) && $data['sve'] == 'on' ? 'checked' : '' ?>>
            <label class="ml-2 text-gray-700" for="sve">SVE</label>
        </div>
        <hr class="my-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="instructeur" class="block text-gray-700 text-sm font-bold mb-2">Instructeur*</label>
                <select id="instructeur" name="instructeur" class="w-full p-3 border rounded" required>
                    <?php foreach($CONF['instructeurs'] as $c): ?>
                        <option value="<?= htmlspecialchars($c['abbr']) ?>" <?= ($data['instructeur'] ?? '') === $c['abbr'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="instruit" class="block text-gray-700 text-sm font-bold mb-2">Instruit le</label>
                <input type="date" class="w-full p-3 border rounded" id="instruit" name="instruit" value="<?= htmlspecialchars($data['instruit'] ?? '') ?>">
            </div>
        </div>
        <hr class="my-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Porté à connaissance le</label>
                <input type="date" class="w-full p-3 border rounded" id="date" name="date" value="<?= htmlspecialchars($data['date'] ?? '') ?>">
            </div>
            <div>
                <label for="boss" class="block text-gray-700 text-sm font-bold mb-2">Superviseur</label>
                <select id="boss" name="boss" class="w-full p-3 border rounded">
                     <option value="" disabled <?= empty($data['boss']) ? 'selected' : '' ?>>Sélectionnez un superviseur</option>
                    <?php
                     foreach($CONF['responsables'] as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= ($data['boss'] ?? '') === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= (implode("-",$CONF["responsables"])) === $c ? 'selected' : '' ?>><?= implode(", ",$CONF["responsables"]) ?></option>

                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <input class="h-4 w-4" type="checkbox" name="follow" id="follow" <?= isset($data['follow']) && $data['follow'] == 'on' ? 'checked' : '' ?>>
            <label class="ml-2" for="follow">Veut suivre le dossier</label>
        </div>
        <hr class="my-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="commune" class="block text-gray-700 text-sm font-bold mb-2">Commune*</label>
                <select id="commune" name="commune" class="w-full p-3 border rounded" required>
                    <?php foreach($CONF['communes'] as $c): ?>
                        <option value="<?= htmlspecialchars($c['code']) ?>" <?= ($data['commune'] ?? '') === $c['code'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div>
                <label for="id_demande" class="block text-gray-700 text-sm font-bold mb-2">Numéro de demande</label>
                <input type="number" class="w-full p-3 border rounded" id="id_demande" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
            </div>
        </div>
        <div class="mt-4">
            <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">Nom pétitionnaire</label>
            <input type="text" class="w-full p-3 border rounded" id="nom" name="nom" value="<?= htmlspecialchars($data['nom'] ?? '') ?>">
        </div>
        <div class="mt-4">
            <label for="objet" class="block text-gray-700 text-sm font-bold mb-2">Objet</label>
            <input type="text" class="w-full p-3 border rounded" id="objet" name="objet" value="<?= htmlspecialchars($data['objet'] ?? '') ?>">
        </div>
        <div class="mt-4">
            <label for="observations" class="block text-gray-700 text-sm font-bold mb-2">Observations</label>
            <textarea class="w-full p-3 border rounded" name="obs" id="observations" rows="4"><?= htmlspecialchars($data['obs'] ?? '') ?></textarea>
        </div>
        <div class="mt-4">
            <label for="zone" class="block text-gray-700 text-sm font-bold mb-2">Zone*</label>
            <select id="zone" name="zone" class="w-full p-3 border rounded" required>
                <?php foreach($CONF['zones'] as $k => $c): ?>
                    <option value="<?= htmlspecialchars($c['name']) ?>" <?= ($data['zone'] ?? '') === $c['name'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <hr class="my-6">
        <div class="text-lg font-bold text-gray-800 mb-4">Périmètres et contraintes :</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-2">
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" value="on" name="ac1" id="ac1" <?= isset($data['ac1']) && $data['ac1'] == 'on' ? 'checked' : '' ?>>
                <label class="ml-2 text-gray-700" for="ac1">AC1 (MH)</label>
            </div>
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" value="on" name="ac2i" id="ac2i" <?= isset($data['ac2i']) && $data['ac2i'] == 'on' ? 'checked' : '' ?>>
                <label class="ml-2 text-gray-700" for="ac2i">AC2 (Site Inscrit)</label>
            </div>
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" value="on" name="ac2c" id="ac2c" <?= isset($data['ac2c']) && $data['ac2c'] == 'on' ? 'checked' : '' ?>>
                <label class="ml-2 text-gray-700" for="ac2c">AC2 (Site Classé)</label>
            </div>
            <div class="flex items-center">
                <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" value="on" name="ac4" id="ac4" <?= isset($data['ac4']) && $data['ac4'] == 'on' ? 'checked' : '' ?>>
                <label class="ml-2 text-gray-700" for="ac4">AC4 (SPR)</label>
            </div>
            <div class="flex items-center col-span-full sm:col-span-2 lg:col-span-3">
                <input class="h-4 w-4 text-indigo-600 rounded" type="checkbox" name="contr" id="contr_checkbox" <?= isset($data['contr']) && $data['contr'] == 'on' ? 'checked' : '' ?>>
                <label class="ml-2 text-gray-700 flex-shrink-0" for="contr_checkbox">
                    Autres servitudes et contraintes :
                </label>
                <input onkeyup="var v = String(this.value+''); if(v != '' && v != ' ' && v.length > 0){document.getElementById('contr_checkbox').checked=true;}else{document.getElementById('contr_checkbox').checked=false;}"
                    class="w-full p-3 border rounded-lg ml-2 flex-grow" type="text" id="contrname" name="contrname" placeholder="Liste des servitudes et contraintes le cas échéant" value="<?= htmlspecialchars($data['contrname'] ?? '') ?>">
            </div>
        </div>

        <hr class="border-t border-gray-300 my-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Vérifications CERFA</h2>
        <div id="cerfa-container" class="space-y-3">
            </div>
        <button type="button" id="add-cerfa-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-black font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-300 ease-in-out text-sm">Ajouter une vérification CERFA</button>
        <hr class="my-6">

        <h2 class="text-lg font-bold text-gray-800 mb-4">Documents</h2>
        <div id="document-checkboxes-container" class="space-y-2">
            </div>
        <button type="button" id="add-doc-btn" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">Ajouter un document</button>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg w-full mt-8">Continuer vers la vérification des règles &rarr;</button>
    </form>
</main>

<div id="add-doc-modal" class="modal">
    <div class="modal-content">
        <h3 class="text-xl font-bold mb-4">Ajouter un document</h3>
        <input type="text" id="doc-autocomplete-input" placeholder="Tapez pour rechercher un document..." class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <div id="autocomplete-list" class="mt-2 border border-gray-200 rounded-lg max-h-60 overflow-y-auto"></div>
        <div class="mt-4 flex justify-end">
            <button type="button" id="cancel-add-doc" class="bg-gray-300 hover:bg-gray-400 text-black font-semibold py-2 px-4 rounded-lg mr-2">Annuler</button>
            <button type="button" id="confirm-add-doc" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Valider la sélection</button>
        </div>
    </div>
</div>

<div id="add-cerfa-modal" class="modal">
    <div class="modal-content">
        <h3 class="text-xl font-bold mb-4">Ajouter une vérification CERFA</h3>
        <div id="cerfa-selection-list" class="space-y-2 max-h-60 overflow-y-auto">
            </div>
        <div class="mt-4 flex justify-end">
            <button type="button" id="cancel-add-cerfa" class="bg-gray-300 hover:bg-gray-400 text-black font-semibold py-2 px-4 rounded-lg mr-2">Annuler</button>
        </div>
    </div>
</div>

<script>
    // --- SOURCES OF TRUTH ---
    const savedData = <?= json_encode($data) ?>;
    const allDocsForType = <?= json_encode($all_docs_for_type) ?>;
    const allCerfaItems = <?= json_encode(array_values($all_cerfa_items)); ?>;
    const n = "<?= htmlspecialchars($n) ?>";

    // --- ELEMENT SELECTORS ---
    // Document Elements
    const docContainer = document.getElementById('document-checkboxes-container');
    const addDocBtn = document.getElementById('add-doc-btn');
    const docModal = document.getElementById('add-doc-modal');
    const cancelDocBtn = document.getElementById('cancel-add-doc');
    const confirmDocBtn = document.getElementById('confirm-add-doc');
    const autocompleteInput = document.getElementById('doc-autocomplete-input');
    const autocompleteList = document.getElementById('autocomplete-list');

    // CERFA Elements
    const cerfaContainer = document.getElementById('cerfa-container');
    const addCerfaBtn = document.getElementById('add-cerfa-btn');
    const cerfaModal = document.getElementById('add-cerfa-modal');
    const cerfaSelectionList = document.getElementById('cerfa-selection-list');
    const cancelCerfaBtn = document.getElementById('cancel-add-cerfa');

    // --- STATE VARIABLES ---
    let displayedDocIds = new Set();
    let displayedCerfaIds = new Set();
    let currentFocus = -1;

    // --- CERFA FUNCTIONS ---

    function createCerfaElement(item, index) {
        if (displayedCerfaIds.has(index)) return;

        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2 rounded border border-gray-200 bg-white shadow-sm';
        div.id = `cerfa-item-${index}`;

        const checkLabelDiv = document.createElement('div');
        checkLabelDiv.className = 'flex items-center flex-grow';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.id = `cerfa_check_${index}`;
        input.name = `cerfa_check_${index}`;
        input.value = 'on';
        input.className = 'h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500';

        const label = document.createElement('label');
        label.htmlFor = `cerfa_check_${index}`;
        label.textContent = item.name;
        label.className = 'ml-3 text-gray-700';

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `cerfa_present_${index}`;
        hiddenInput.value = item.name;

        checkLabelDiv.appendChild(input);
        checkLabelDiv.appendChild(label);

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.className = 'ml-4 text-red-500 hover:text-red-700 font-bold text-lg leading-none px-2 rounded hover:bg-red-100 flex-shrink-0';
        deleteBtn.title = 'Supprimer cette vérification';
        deleteBtn.dataset.index = index;
        deleteBtn.addEventListener('click', () => {
            document.getElementById(`cerfa-item-${index}`).remove();
            displayedCerfaIds.delete(index);
            updateAddCerfaButtonVisibility();
        });

        div.appendChild(checkLabelDiv);
        div.appendChild(hiddenInput);
        div.appendChild(deleteBtn);
        cerfaContainer.appendChild(div);
        displayedCerfaIds.add(index);
    }

    function updateAddCerfaButtonVisibility() {
        const hasMoreToAdd = allCerfaItems.length > displayedCerfaIds.size;
        addCerfaBtn.style.display = hasMoreToAdd ? 'inline-block' : 'none';
    }

    function openCerfaModal() {
        cerfaSelectionList.innerHTML = '';
        let itemsAvailable = false;
        allCerfaItems.forEach((item, index) => {
            if (!displayedCerfaIds.has(index)) {
                itemsAvailable = true;
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = `+ ${item.name}`;
                button.className = 'w-full text-left p-3 rounded hover:bg-gray-100 transition duration-150 ease-in-out';
                button.addEventListener('click', () => {
                    createCerfaElement(item, index);
                    updateAddCerfaButtonVisibility();
                    cerfaModal.style.display = 'none';
                });
                cerfaSelectionList.appendChild(button);
            }
        });
        
        if (itemsAvailable) {
            cerfaModal.style.display = 'block';
        }
    }

    // --- DOCUMENT FUNCTIONS (MODIFIED FOR DELETE BUTTON) ---

    /**
     * Creates and displays a document item row with a delete button.
     * @param {object} doc - The document object {id: string, name: string, default: boolean}.
     */
    function createCheckbox(doc) {
        if (!doc || displayedDocIds.has(doc.id)) return;

        const containerDiv = document.createElement('div');
        containerDiv.className = 'flex items-center justify-between p-2 rounded border border-gray-200 bg-white shadow-sm';
        containerDiv.id = `doc-container-${doc.id}`;

        const checkLabelDiv = document.createElement('div');
        checkLabelDiv.className = 'flex items-center flex-grow overflow-hidden mr-2';

        const input = document.createElement('input');
        input.className = 'h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500 flex-shrink-0';
        input.type = 'checkbox';
        input.name = `doc_${doc.id}`;
        input.id = `doc_${doc.id}`;
        input.value = 'on';

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `isdoc_${doc.id}`;
        hiddenInput.value = "1";

        const label = document.createElement('label');
        label.className = 'ml-3 text-gray-700';
        label.htmlFor = `doc_${doc.id}`;
        label.textContent = n + doc.name;
        label.title = n + doc.name;

        checkLabelDiv.appendChild(input);
        checkLabelDiv.appendChild(label);

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.className = 'ml-4 text-red-500 hover:text-red-700 font-bold text-lg leading-none px-2 rounded hover:bg-red-100 flex-shrink-0';
        deleteBtn.title = 'Supprimer ce document';
        deleteBtn.addEventListener('click', () => {
            containerDiv.remove();
            displayedDocIds.delete(doc.id);
        });

        containerDiv.appendChild(checkLabelDiv);
        containerDiv.appendChild(hiddenInput);
        containerDiv.appendChild(deleteBtn);
        docContainer.appendChild(containerDiv);
        displayedDocIds.add(doc.id);
    }

    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (items.length - 1);
        items[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove("autocomplete-active");
        }
    }

    function validateAndAddSelectedItem() {
        const activeItem = autocompleteList.querySelector(".autocomplete-active");
        if (activeItem) {
            const selectedDocId = activeItem.dataset.id;
            if (selectedDocId) {
                const docToAdd = allDocsForType[selectedDocId];
                if (docToAdd && !displayedDocIds.has(docToAdd.id)) {
                    createCheckbox(docToAdd);
                }
                closeDocModal();
            }
        }
    }

    function populateAutocompleteList(filter = '') {
        autocompleteList.innerHTML = '';
        currentFocus = -1;
        
        const availableDocs = Object.values(allDocsForType).filter(doc => !displayedDocIds.has(doc.id));
        const filteredDocs = availableDocs.filter(doc => doc.name.toLowerCase().includes(filter.toLowerCase()));

        filteredDocs.forEach(doc => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'p-3 hover:bg-gray-100 cursor-pointer';
            itemDiv.textContent = doc.name;
            itemDiv.dataset.id = doc.id;
            itemDiv.addEventListener('click', function() {
                createCheckbox(allDocsForType[this.dataset.id]);
                closeDocModal();
            });
            autocompleteList.appendChild(itemDiv);
        });

        if(autocompleteList.children.length > 0) {
            currentFocus = 0;
            addActive(autocompleteList.children);
        }
    }

    function closeDocModal() {
        docModal.style.display = 'none';
        autocompleteInput.value = '';
    }

    // --- INITIALIZATION AND EVENT LISTENERS ---

    document.addEventListener('DOMContentLoaded', () => {
        // --- Initial load of documents from saved data ---
        Object.keys(savedData).forEach(key => {
            if (key.startsWith('isdoc_')) {
                const docId = key.split('_')[1];
                if (allDocsForType[docId] && !displayedDocIds.has(docId)) {
                    const doc = allDocsForType[docId];
                    createCheckbox(doc);
                    
                    const checkbox = document.getElementById(`doc_${docId}`);
                    if (checkbox) {
                        checkbox.checked = (savedData[`doc_${docId}`] === 'on');
                    }
                }
            }
        });

        // --- Initial load of CERFA items from saved data ---
        Object.keys(savedData).forEach(key => {
            if (key.startsWith('cerfa_present_')) {
                const index = parseInt(key.split('_')[2], 10);
                if (!isNaN(index) && allCerfaItems[index]) {
                    createCerfaElement(allCerfaItems[index], index);
                    const checkbox = document.getElementById(`cerfa_check_${index}`);
                    if (checkbox) {
                        checkbox.checked = (savedData[`cerfa_check_${index}`] === 'on');
                    }
                }
            }
        });
        updateAddCerfaButtonVisibility();

        // Document Modal Listeners
        addDocBtn.addEventListener('click', () => {
            populateAutocompleteList();
            docModal.style.display = 'block';
            autocompleteInput.focus();
        });
        cancelDocBtn.addEventListener('click', closeDocModal);
        confirmDocBtn.addEventListener('click', validateAndAddSelectedItem);

        // CERFA Modal Listeners
        addCerfaBtn.addEventListener('click', openCerfaModal);
        cancelCerfaBtn.addEventListener('click', () => { cerfaModal.style.display = 'none'; });

        // General Modal Close Listener
        window.addEventListener('click', (event) => {
            if (event.target == docModal) closeDocModal();
            if (event.target == cerfaModal) cerfaModal.style.display = 'none';
        });

        // Keyboard listeners for document autocomplete
        autocompleteInput.addEventListener('input', function() { populateAutocompleteList(this.value); });
        autocompleteInput.addEventListener('keydown', function(e) {
            let items = autocompleteList.getElementsByTagName('div');
            if (e.keyCode == 40) { currentFocus++; addActive(items); }
            else if (e.keyCode == 38) { currentFocus--; addActive(items); }
            else if (e.keyCode == 13) { e.preventDefault(); if (currentFocus > -1) { validateAndAddSelectedItem(); } }
        });
    });
</script>
</body>
</html>