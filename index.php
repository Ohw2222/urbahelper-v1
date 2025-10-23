<?php
	require("conf.php");
	$types = GetTypes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbaHelper - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
    <style>
        #myInput {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>');
            background-repeat: no-repeat;
            background-position: 1rem center;
            padding-left: 3.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-inter text-gray-800 p-4 sm:p-6 lg:p-8">
    
    <!-- Modal de restauration de session -->
    <div id="restore-session-modal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Restaurer la session précédente ?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Nous avons trouvé une session non terminée. Souhaitez-vous continuer là où vous vous étiez arrêté ?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="restore-button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Restaurer
                    </button>
                    <button type="button" id="delete-trigger-button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Supprimer la sauvegarde
                    </button>
                     <button type="button" id="cancel-restore-button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="delete-confirm-modal" class="hidden fixed z-20 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmation requise</h3>
                    <div class="mt-2">
                        <p id="delete-confirm-message" class="text-sm text-gray-600 mb-4">Message de confirmation.</p>
                        <label for="delete-confirm-input" class="block text-sm font-medium text-gray-700">Pour confirmer, veuillez taper "OUI" dans le champ ci-dessous :</label>
                        <input type="text" id="delete-confirm-input" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="delete-confirm-button" disabled class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed sm:ml-3 sm:w-auto sm:text-sm">
                        Supprimer
                    </button>
                    <button type="button" id="delete-cancel-button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="container mx-auto max-w-2xl bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 my-8">
        <h1 class="text-4xl font-bold text-center text-indigo-700 mb-6">UrbaHelper</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Ressources</h3>
                <a href="ressources/" class="w-full text-center inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    Consulter les ressources
                </a>
            </div>
            <div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Historique</h3>
                <a href="historique.php" class="w-full text-center inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    Voir les dossiers sauvegardés
                </a>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Nouveau dossier :</h3>
            <!--
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Rechercher un type..."
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
    -->
            <ul id="list" class="list-none p-0 m-0 space-y-2">
                <?php foreach($types as $t): ?>
                    <li>
                        <a href="go.php?n=<?= htmlspecialchars($t['abbr']); ?>"
                        class="block border border-gray-200 bg-gray-50 hover:bg-gray-100 p-3 rounded-lg text-lg font-medium text-gray-700 no-underline transition duration-200 ease-in-out">
                            <?= htmlspecialchars($t['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function myFunction() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById('myInput');
            filter = input.value.toUpperCase();
            ul = document.getElementById("list");
            li = ul.getElementsByTagName('li');

            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
        
        window.addEventListener('DOMContentLoaded', () => {
            const savedStateJSON = localStorage.getItem('urbaHelperAutosave');
            if (savedStateJSON) {
                const restoreModal = document.getElementById('restore-session-modal');
                const restoreBtn = document.getElementById('restore-button');
                const deleteTriggerBtn = document.getElementById('delete-trigger-button');
                const cancelRestoreBtn = document.getElementById('cancel-restore-button');

                const deleteConfirmModal = document.getElementById('delete-confirm-modal');
                const deleteConfirmMsg = document.getElementById('delete-confirm-message');
                const deleteConfirmInput = document.getElementById('delete-confirm-input');
                const deleteConfirmBtn = document.getElementById('delete-confirm-button');
                const deleteCancelBtn = document.getElementById('delete-cancel-button');

                restoreModal.classList.remove('hidden');

                restoreBtn.addEventListener('click', () => {
                    // La logique de restauration est complexe et reste la même.
                    try {
                        const savedState = JSON.parse(savedStateJSON);
                        if (!savedState || !savedState.url) {
                            throw new Error("Invalid saved state.");
                        }

                        if (savedState.url.includes('fill.php')) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = savedState.url;
                            form.style.display = 'none';

                            for (const key in savedState.formData) {
                                if (Object.prototype.hasOwnProperty.call(savedState.formData, key)) {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = key;
                                    input.value = savedState.formData[key];
                                    form.appendChild(input);
                                }
                            }
                            document.body.appendChild(form);
                            form.submit();
                        } else {
                            window.location.href = savedState.url;
                        }
                    } catch (e) {
                        console.error("Could not parse or restore saved state:", e);
                        localStorage.removeItem('urbaHelperAutosave');
                        restoreModal.classList.add('hidden');
                    }
                });

                cancelRestoreBtn.addEventListener('click', () => {
                    restoreModal.classList.add('hidden');
                });
                
                deleteTriggerBtn.addEventListener('click', () => {
                    try {
                        const savedState = JSON.parse(savedStateJSON);
                        const data = savedState.formData;
                        const year = new Date().getFullYear().toString().substr(-2);
                        const type = (data.type || 'XX').substring(0,2);
                        const commune = data.commune || 'N/A';
                        // formatted_id n'existe pas dans le formulaire initial, on utilise 'id'
                        const id_demande = (data.id || '00000').padStart(5, '0');

                        deleteConfirmMsg.textContent = `Êtes-vous sûr(e) de vouloir supprimer la sauvegarde du dossier ${type} 053 ${commune} ${year} ${id_demande} ?`;
                        
                        restoreModal.classList.add('hidden');
                        deleteConfirmModal.classList.remove('hidden');

                    } catch(e) {
                         console.error("Could not parse data for delete confirmation:", e);
                         deleteConfirmMsg.textContent = "Erreur lors de la lecture des données de la sauvegarde.";
                    }
                });

                deleteCancelBtn.addEventListener('click', () => {
                    deleteConfirmModal.classList.add('hidden');
                    restoreModal.classList.remove('hidden'); // Re-show the first modal
                });

                deleteConfirmInput.addEventListener('input', () => {
                    if (deleteConfirmInput.value === 'OUI') {
                        deleteConfirmBtn.disabled = false;
                    } else {
                        deleteConfirmBtn.disabled = true;
                    }
                });

                deleteConfirmBtn.addEventListener('click', () => {
                    localStorage.removeItem('urbaHelperAutosave');
                    deleteConfirmModal.classList.add('hidden');
                    // Optionally, show a success message here
                });
            }
        });
    </script>
</body>
</html>
