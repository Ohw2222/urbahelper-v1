<?php
// Fichier: fill.php
require("conf.php"); // Assurez-vous que ce fichier contient GetRuleList($z) et $CONF.

// NOUVELLE LOGIQUE: Vérifier si on est en mode édition
$is_edit_mode = isset($_POST['dossier_id']) && !empty($_POST['dossier_id']);
$existing_data = [];
if ($is_edit_mode && isset($_POST['existing_data'])) {
    $existing_data = json_decode($_POST['existing_data'], true);
}

// Vérifie si la zone est définie dans les données POST
if (!isset($_POST["type"],$_POST["zone"])) {
    header("Location: index.php");
    exit();
}

$z = htmlspecialchars($_POST["zone"]); // Nettoie la variable de zone
$rules = GetRuleList($z);

// Détermine l'année à afficher
if (!isset($_POST['instruit']) || empty($_POST['instruit'])) {
    $year = date('y');
} else {
    $year = date('y', strtotime($_POST['instruit']));
}

// Formatte l'ID de la demande
$id = $_POST['id'] ?? 0;
$_id2 = str_pad(intval($id), 5, '0', STR_PAD_LEFT);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbaHelper - Règles de Zone</title>
    <link rel="apple-touch-icon" sizes="57x57" href="apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
    <main class="w-full max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 my-8">
        <a href="go.php?n=<?= htmlspecialchars($_POST['type']) ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mb-4">Retour</a>
        <form method="POST" action="gen.php" onsubmit="clearAutosave()" id="myForm">
            <input type="hidden" name="instruit" value="<?= htmlspecialchars($_POST['instruit']) ?>">
            <?php foreach($_POST as $k => $v): ?>
                <input type="hidden" value="<?= htmlspecialchars($v); ?>" name="<?= htmlspecialchars($k); ?>">
            <?php endforeach; ?>
            
            <h1 class="text-3xl font-bold text-center text-indigo-700 mb-4">
                Demande <?= htmlspecialchars($_POST['type']); ?> <?= htmlspecialchars($CONF["departement"]); ?> <?= htmlspecialchars($_POST['commune']) . " " . htmlspecialchars($year) . " " . htmlspecialchars($_id2); ?>
            </h1>
            <h4 class="text-xl font-semibold text-red-700 text-center mb-8">
                Attention : le logiciel ne fait pas foi. La vérification humaine des règlements est nécessaire.
            </h4>

            <?php foreach($rules as $r):
                $text = "?";
                $default_script_classes = 'bg-red-500 text-white font-bold py-2 px-3 rounded-md text-center';
                $default_script = "{ document.getElementById('check_".$r["id"]."').innerHTML = '<p class=\'".$default_script_classes."\'>?</p>'; }else{document.getElementById('check_".$r["id"]."').innerHTML='';}";
                $script = "";

                switch($r["type"]){
                    case "number_below": $script = "if(this.value >= ".floatval($r['value']).")".$default_script; $text = "Moins de ".$r["value"]." ".$r["unit"]." (exclus)"; break;
                    case "number_above": $script = "if(this.value <= ".floatval($r['value']).")".$default_script; $text = "Plus de ".$r["value"]." ".$r["unit"]." (exclus)"; break;
                    case "number_below_equal": $script = "if(this.value > ".floatval($r['value']).")".$default_script; $text = "Moins de ".$r["value"]." ".$r["unit"]." (inclus)"; break;
                    case "number_above_equal": $script = "if(this.value < ".floatval($r['value']).")".$default_script; $text = "Plus de ".$r["value"]." ".$r["unit"]." (inclus)"; break;
                    case "number_between": $script = "if(!(this.value >= ".floatval($r['value'][0])." && this.value <= ".floatval($r['value'][1])."))".$default_script; $text = "Entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (inclus)"; break;
                    case "number_between_exclude": $script = "if(!(this.value > ".floatval($r['value'][0])." && this.value < ".floatval($r['value'][1])."))".$default_script; $text = "Entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (exclus)"; break;

                    case "number_exclude_include": $script = "if((this.value >= ".floatval($r['value'][0])." && this.value <= ".floatval($r['value'][1])."))".$default_script; $text = "Tout sauf entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (inclus)"; break;
                    case "number_exclude": $script = "if((this.value > ".floatval($r['value'][0])." && this.value < ".floatval($r['value'][1])."))".$default_script; $text = "Tout sauf entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (exclus)"; break;
                    case "number_select": $cond = "parseFloat(this.value) == ".implode(" || parseFloat(this.value) == ",$r["value"]); $script = "if(!(".$cond."))".$default_script; $text = "L'une des valeurs suivantes ".implode(" ou ",$r["value"])." ".$r["unit"]; break;
                    case "select": $cond = "this.value == '".implode("' || this.value == '",$r["value"])."'"; $script = "if(!(".$cond."))".$default_script; $text = "L'une des valeurs suivantes \"".implode("\" ou \"",$r["value"])."\" ".$r["unit"]; break;
                    case "not_number_select": $cond = "parseFloat(this.value) == ".implode(" || parseFloat(this.value) == ",$r["value"]); $script = "if((".$cond."))".$default_script; $text = "Pas l'une des valeurs suivantes ".implode(" ou ",$r["value"])." ".$r["unit"]; break;
                    case "not_select": $cond = "this.value == '".implode("' || this.value == '",$r["value"])."'"; $script = "if((".$cond."))".$default_script; $text = "Pas l'une des valeurs suivantes \"".implode("\" ou \"",$r["value"])."\" ".$r["unit"]; break;
                    case "text_equal": $script = "if(this.value != '".addslashes($r['value'])."')".$default_script; $text = "Valeur attendue \"".htmlspecialchars($r["value"])."\""; break;
                    case "number_equal": $script = "if(parseFloat(this.value) != ".floatval($r['value']).")".$default_script; $text = "Valeur attendue \"".htmlspecialchars($r["value"])."\""; break;
                    default: if(is_string($r["value"])) { $text = htmlspecialchars($r["value"]); } else { $text = "Erreur de format"; } break;
                }
            ?>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center mb-4">
                <div class="md:col-span-4">
                    <div class="mt-3">
                        <?php
                        $field_type = str_contains($r['type'], "number") ? "number" : "text";
                        $field_value = $existing_data['f_' . $r['id']] ?? '';
                        ?>
                        <label for="id_<?= htmlspecialchars($r["id"]); ?>" class="block text-gray-700 text-sm font-bold mb-2"><?= htmlspecialchars($r["name"]); ?> :</label>
                        <input onchange="<?= $script; ?>" type="<?= $field_type; ?>" step="0.01" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="id_<?= htmlspecialchars($r["id"]); ?>" name="f_<?= htmlspecialchars($r["id"]); ?>" placeholder="<?= htmlspecialchars($r["name"]); ?>" value="<?= htmlspecialchars($field_value) ?>">
                    </div>
                </div>
                <div class="md:col-span-1" id="check_<?= htmlspecialchars($r["id"]); ?>"></div>
                <div class="md:col-span-5">
                    <p class="text-gray-700 mt-3 md:mt-0"><?= $text; ?></p>
                </div>
                <div class="md:col-span-2">
                    <?php
                    $validity_value = $existing_data['valid_' . $r['id']] ?? 'nok';
                    ?>
                    <select name="valid_<?= htmlspecialchars($r["id"]); ?>" id="valid_<?= htmlspecialchars($r["id"]); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="nok" <?= $validity_value === 'nok' ? 'selected' : '' ?>>Non validé</option>
                        <option value="ok" <?= $validity_value === 'ok' ? 'selected' : '' ?>>Validé</option>
                        <option value="jsp" <?= $validity_value === 'jsp' ? 'selected' : '' ?>>Je ne sais pas</option>
                        <option value="ignore" <?= $validity_value === 'ignore' ? 'selected' : '' ?>>Ignorer le champ</option>
                    </select>
                </div>
            </div>
            <hr class="border-t border-gray-200 my-4">
            <?php endforeach; ?>

            <div id="additional-rules-container"></div>
            <input type="hidden" name="supfields" id="supfields" value="0">
            <div class="my-6">
                 <button type="button" id="add-rule-btn" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                    <i class="fas fa-plus-circle mr-2"></i>Ajouter une règle personnalisée
                </button>
            </div>
            <hr class="border-t border-gray-300 my-4">
            <div class="mt-4">
                <label for="comment" class="block text-gray-700 text-sm font-bold mb-2">Commentaires :</label>
                <?php
                $comment_value = $existing_data['comment'] ?? '';
                ?>
                <textarea name="comment" id="comment" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Commentaires" rows="6"><?= htmlspecialchars($comment_value) ?></textarea>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105 w-full mt-8">Valider et Générer PDF</button>
            <p class="mt-8 mb-3 text-center text-gray-500 text-sm">Fait avec &#9749; par OA</p>
        </form>
    </main>
    
    <script>
        const AUTOSAVE_KEY = 'urbaHelperAutosave';
        const AUTOSAVE_INTERVAL = 15000;
        let autosaveIntervalId = null; 

        const addRuleBtn = document.getElementById('add-rule-btn');
        const rulesContainer = document.getElementById('additional-rules-container');
        const supFieldsInput = document.getElementById('supfields');
        let ruleCounter = 0;
        
        function addRuleBlock(id, data = {}) {
            const newRuleHTML = `
                <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg mb-4" id="sup_rule_${id}">
                    <h3 class="text-lg font-semibold text-green-800 mb-3">Règle personnalisée #${id}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nom du critère :</label>
                            <input type="text" name="sup_name_${id}" placeholder="Ex: Hauteur façade" class="w-full p-3 border border-gray-300 rounded-lg" value="${data.name || ''}">
                        </div>
                        <div class="md:col-span-1"></div>
                        <div class="md:col-span-5">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Règle à afficher :</label>
                            <input type="text" name="sup_val_${id}" placeholder="Ex: 10m maximum" class="w-full p-3 border border-gray-300 rounded-lg" value="${data.val || ''}">
                            <input type="hidden" name="sup_type_${id}" value="text">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Validation :</label>
                            <select name="valid_sup_${id}" class="w-full p-3 border border-gray-300 rounded-lg">
                                <option value="nok" ${data.valid === 'nok' ? 'selected' : ''}>Non validé</option>
                                <option value="ok" ${data.valid === 'ok' ? 'selected' : ''}>Validé</option>
                                <option value="jsp" ${data.valid === 'jsp' ? 'selected' : ''}>Je ne sais pas</option>
                                <option value="ignore" ${data.valid === 'ignore' ? 'selected' : ''}>Ignorer</option>
                            </select>
                        </div>
                    </div>
                    <div class="md:col-span-12 mt-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Valeur du projet :</label>
                        <input type="text" name="f_sup_${id}" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Valeur constatée dans le projet" value="${data.f || ''}">
                    </div>
                </div>
            `;
            rulesContainer.insertAdjacentHTML('beforeend', newRuleHTML);
        }
        
        addRuleBtn.addEventListener('click', function () {
            ruleCounter++;
            supFieldsInput.value = ruleCounter;
            addRuleBlock(ruleCounter);
        });

        function recreateCustomRules(dataSource) {
            if (!dataSource || !dataSource.supfields) return;
            const count = parseInt(dataSource.supfields, 10);
            if (isNaN(count) || count <= 0) return;
            for (let i = 1; i <= count; i++) {
                const ruleData = {
                    name:  dataSource[`sup_name_${i}`] || '',
                    val:   dataSource[`sup_val_${i}`] || '',
                    valid: dataSource[`valid_sup_${i}`] || 'nok',
                    f:     dataSource[`f_sup_${i}`] || ''
                };
                addRuleBlock(i, ruleData);
            }
            ruleCounter = count;
            supFieldsInput.value = count;
        }

        function serializeFillForm() {
            const form = document.querySelector('form[action="gen.php"]');
            const obj = {};
            if (!form) return {};
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                obj[key] = value;
            }
            return obj;
        }

        function saveFormState() {
            const formData = serializeFillForm();
            if (Object.keys(formData).length > 0) {
                 const state = {
                    url: window.location.href,
                    formData: formData
                };
                localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(state));
            }
        }
        
        function validateAllFields() {
            const form = document.getElementById('myForm');
            const inputsToValidate = form.querySelectorAll('input[onchange]');
            inputsToValidate.forEach(input => {
                if (input.onchange) {
                    input.onchange({ target: input });
                }
            });
        }

        // ***THIS FUNCTION CONTAINS THE FIX***
        function loadFormState() {
            const savedStateJSON = localStorage.getItem(AUTOSAVE_KEY);
            if (!savedStateJSON) return;
            try {
                const savedState = JSON.parse(savedStateJSON);
                if (!savedState.formData) return;
                
                const data = savedState.formData;
                recreateCustomRules(data);
                
                const form = document.getElementById('myForm');
                for (const key in data) {
                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                        let element = form.elements[key];
                        if (element) {
                           // If element exists, populate it
                           element.value = data[key];
                        } else {
                           // **FIX**: If element does not exist, it's a pass-through field from the previous page.
                           // We must create a hidden input to preserve it during autosave cycles on this page.
                           if (key.startsWith('doc_') || key.startsWith('isdoc_') || key.startsWith('cerfa_present_') || key.startsWith('cerfa_check_')) {
                               const hiddenInput = document.createElement('input');
                               hiddenInput.type = 'hidden';
                               hiddenInput.name = key;
                               hiddenInput.value = data[key];
                               form.appendChild(hiddenInput);
                           }
                        }
                    }
                }
                validateAllFields(); 
            } catch(e) {
                console.error("Error loading form state from autosave:", e);
                localStorage.removeItem(AUTOSAVE_KEY);
            }
        }

        function clearAutosave() {
            if (autosaveIntervalId) {
                clearInterval(autosaveIntervalId);
            }
            localStorage.removeItem(AUTOSAVE_KEY);
            console.log('Autosave stopped and data cleared upon form submission.');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const isEditMode = <?= json_encode($is_edit_mode) ?>;
            const existingData = <?= json_encode($existing_data) ?>;
            
            if (isEditMode) {
                recreateCustomRules(existingData);
                validateAllFields(); 
            } else {
                loadFormState();
            }
            
            autosaveIntervalId = setInterval(saveFormState, AUTOSAVE_INTERVAL);
        });

        const myForm = document.getElementById('myForm');
        myForm.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName === 'INPUT') {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>