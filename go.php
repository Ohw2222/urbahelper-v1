<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
$interface_old = false;
if($interface_old){
	require("conf.php");

	if(!isset($_GET['n'])){
		header("Location: index.php");
		exit();
	}

	if(!in_array($_GET['n'], GetTypesAbbr())){
		header("Location: index.php");
		exit();
	}

	$n = $_GET['n'];

	
	if(isset($CONF['documents'][$n])) {
		$alldocs = $CONF['documents'][$n];
		$docs = GetDocList($n);

	}else{
		$docs = [];
		$alldocs = [];
	}



	?>

	<!doctype html>
	<html lang="fr" data-bs-theme="auto">
		<head><script src="color-modes.js"></script>

			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="">
			<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
			<meta name="generator" content="Hugo 0.122.0">
			<title>Signin Template · Bootstrap v5.3</title>


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

	<script>
		const document = <?= json_encode($alldocs); ?>
	</script>

			<link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">



			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

	<link href="bootstrap.min.css" rel="stylesheet">

			<style>
				.bd-placeholder-img {
					font-size: 1.125rem;
					text-anchor: middle;
					-webkit-user-select: none;
					-moz-user-select: none;
					user-select: none;
				}

				@media (min-width: 768px) {
					.bd-placeholder-img-lg {
						font-size: 3.5rem;
					}
				}

				.b-example-divider {
					width: 100%;
					height: 3rem;
					background-color: rgba(0, 0, 0, .1);
					border: solid rgba(0, 0, 0, .15);
					border-width: 1px 0;
					box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
				}

				.b-example-vr {
					flex-shrink: 0;
					width: 1.5rem;
					height: 100vh;
				}

				.bi {
					vertical-align: -.125em;
					fill: currentColor;
				}

				.nav-scroller {
					position: relative;
					z-index: 2;
					height: 2.75rem;
					overflow-y: hidden;
				}

				.nav-scroller .nav {
					display: flex;
					flex-wrap: nowrap;
					padding-bottom: 1rem;
					margin-top: -1px;
					overflow-x: auto;
					text-align: center;
					white-space: nowrap;
					-webkit-overflow-scrolling: touch;
				}

				.btn-bd-primary {
					--bd-violet-bg: #712cf9;
					--bd-violet-rgb: 112.520718, 44.062154, 249.437846;

					--bs-btn-font-weight: 600;
					--bs-btn-color: var(--bs-white);
					--bs-btn-bg: var(--bd-violet-bg);
					--bs-btn-border-color: var(--bd-violet-bg);
					--bs-btn-hover-color: var(--bs-white);
					--bs-btn-hover-bg: #6528e0;
					--bs-btn-hover-border-color: #6528e0;
					--bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
					--bs-btn-active-color: var(--bs-btn-hover-color);
					--bs-btn-active-bg: #5a23c8;
					--bs-btn-active-border-color: #5a23c8;
				}

				.bd-mode-toggle {
					z-index: 1500;
				}

				.bd-mode-toggle .dropdown-menu .active .bi {
					display: block !important;
				}
			</style>


			<link href="sign-in.css" rel="stylesheet">
		</head>
		<body class="d-flex align-items-center py-4 bg-body-tertiary">
			<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
				<symbol id="check2" viewBox="0 0 16 16">
					<path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
				</symbol>
				<symbol id="circle-half" viewBox="0 0 16 16">
					<path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
				</symbol>
				<symbol id="moon-stars-fill" viewBox="0 0 16 16">
					<path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
					<path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
				</symbol>
				<symbol id="sun-fill" viewBox="0 0 16 16">
					<path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
				</symbol>
			</svg>

			<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
				<button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
								id="bd-theme"
								type="button"
								aria-expanded="false"
								data-bs-toggle="dropdown"
								aria-label="Toggle theme (auto)">
					<svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
					<span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
				</button>
				<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
					<li>
						<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
							<svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#sun-fill"></use></svg>
							Light
							<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						</button>
					</li>
					<li>
						<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
							<svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
							Dark
							<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						</button>
					</li>
					<li>
						<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
							<svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#circle-half"></use></svg>
							Auto
							<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
						</button>
					</li>
				</ul>
			</div>


	<main class="w-200 m-auto">
		<form method="POST" action="fill.php">
			<h1 class="h3 mb-3 fw-normal">Demande <?= $n; ?></h1>
			<input type="hidden" name="type" value="<?= $n ?>">

			<div class="form-check text-start my-3">
				<input class="form-check-input" type="checkbox" value="on" name="sve" id="sve">
				<label class="form-check-label" for="sve">
					SVE
				</label>
			</div>

			<div class="row">
				<div class="col-6">
					<div class="form-floating mt-2">
						<select id="floatingInput" class="form-select form-control" aria-label="instructeur" name="instructeur"  required>
							<option selected disabled>Instructeur</option>
							<?php foreach($CONF['instructeurs'] as $c): ?>
								<option value="<?= $c['abbr']; ?>"><?= $c['name']; ?></option>
							<?php endforeach; ?>
						</select>
						<label for="floatingInput">Instructeur<span style="color:red">*</span></label>
					</div>
				</div>
				<div class="col">
					<div class="form-floating mt-2">
						<input type="date" class="form-control" id="instruit" name="instruit" placeholder="Date" >
						<label for="instruit">Instruit le ...</label>
					</div>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-6">
					<div class="form-floating mt-2">
						<input type="date" class="form-control" id="date" name="date" placeholder="Date" >
						<label for="date">Porté à connaissance du superviseur le ...</label>
					</div>
				</div>
				<div class="col">
					<div class="form-floating mt-2">
						<select id="floatingInput" class="form-select form-control" aria-label="boss" name="boss" >
							<option selected disabled>Superviseur (porté à connaissance de...)</option>
							<?php
							foreach($CONF['responsables'] as $c):
							?>
								<option value="<?= $c; ?>"><?= $c; ?></option>
							<?php endforeach; ?>
							<option value="<?= implode("-", $CONF['responsables']); ?>"><?= implode(", ", $CONF['responsables']); ?></option>
						</select>
						<label for="floatingInput">Superviseur (porté à connaissance de...)</label>
					</div>
				</div>
				<div class="col-12">
					<div class="form-check text-start my-3">
						<input class="form-check-input" type="checkbox" value="on" name="follow" id="flexCheckDefault2">
						<label class="form-check-label" for="flexCheckDefault2">
							Veut suivre le dossier
						</label>
					</div>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col">
					<div class="form-floating mt-3">
						<select id="floatingInput" class="form-select form-control" aria-label="Commune" name="commune"  required>
							<option selected disabled>Commune</option>
							<?php foreach($CONF['communes'] as $c): ?>
								<option value="<?= $c['code']; ?>"><?= $c['name']; ?></option>
							<?php endforeach; ?>
						</select>
						<label for="floatingInput">Commune<span style="color:red">*</span></label>
					</div>
				</div>
				<div class="col">
					<div class="form-floating mt-3">
						<input type="number" min="1" max="99999" class="form-control" id="floatingPassword" name="id" placeholder="Numéro demande (#####)" >
						<label for="floatingPassword">Numéro demande (#####)</label>
					</div>
				</div>
			</div>


			<div class="form-floating mt-2">
				<input type="text" class="form-control" id="floatingNom" name="nom" placeholder="Nom et prénom pétitionnaire" >
				<label for="floatingNom">Nom et prénom pétitionnaire</label>
			</div>
			<div class="form-floating mt-2">
				<input type="text" class="form-control" id="floatingufo" name="objet" placeholder="Objet" >
				<label for="floatingufo">Objet</label>
			</div>
			<div class="form-floating mt-2">
				<textarea class="form-control" placeholder="Observations" name="obs" id="floatingTextarea2" style="height: 150px"></textarea>
				<label for="floatingTextarea2">Observations</label>
			</div>
			<div class="form-floating mt-3">
				<select id="zone" class="form-select form-control" aria-label="Zone" name="zone" required>
					<option selected disabled>Zone</option>
					<?php foreach($CONF['zones'] as $k => $c): ?>
						<option value="<?= $c['name']; ?>"><?= $c['name']; ?></option>
					<?php endforeach; ?>
				</select>
				<label for="zone">Zone<span style="color:red">*</span></label>
			</div>
			<div class="form-check text-start my-3">
				<input class="form-check-input" type="checkbox" name="lotissement" id="Lotissement" style="margin-top:0.75rem !important">
				<label class="form-check-label" for="Lotissement">
					Lotissement :
				</label><input onkeyup="var v = String(this.value+''); if(v != '' && v != ' ' && v.length > 4){document.getElementById('Lotissement').checked=true;}else{document.getElementById('Lotissement').checked=false;}" style="display:inline-block !important;width: 285px;margin-left: 6px;" type="text" class="form-control" id="lotisname" name="lotisname" placeholder="Nom lotissement le cas échéant">
			</div>
			<div class="row">
				<div class="col-12" style="font-weight:bold">Perimètres et contraintes :</div>
				<div class="form-check text-start my-3 col-4">
					<input class="form-check-input" type="checkbox" value="on" name="ac1" id="ac1">
					<label class="form-check-label" for="ac1">
						AC1 (MH)
					</label>
				</div>
				<div class="form-check text-start my-3 col-4">
					<input class="form-check-input" type="checkbox" value="on" name="ac2i" id="ac2i">
					<label class="form-check-label" for="ac2i">
						AC2 (Site Inscrit)
					</label>
				</div>
				<div class="form-check text-start my-3 col-4">
					<input class="form-check-input" type="checkbox" value="on" name="ac2c" id="ac2c">
					<label class="form-check-label" for="ac2c">
						AC2 (Site Classé)
					</label>
				</div>
				<div class="form-check text-start my-3 col-4">
					<input class="form-check-input" type="checkbox" value="on" name="ac4" id="ac4">
					<label class="form-check-label" for="ac4">
						AC4 (SPR)
					</label>
				</div>
				<div class="form-check text-start my-3 col-8">
					<input class="form-check-input" type="checkbox" name="contr" id="contr" style="margin-top:0.75rem !important">
					<label class="form-check-label" for="contr">
						Autres servitudes et contraintes :
					</label><input onkeyup="var v = String(this.value+''); if(v != '' && v != ' ' && v.length > 0){document.getElementById('contr').checked=true;}else{document.getElementById('contr').checked=false;}" style="display:inline-block !important;width: 285px;margin-left: 6px;" type="text" class="form-control" id="contrname" name="contrname" placeholder="Liste des servitudes et contraintes le cas échéant">
				</div>
			</div>
			<hr>
			<h4>Documents</h4>
			<?php foreach($docs as $d): ?>
				<div class="form-check text-start my-3">
					<input class="form-check-input" type="checkbox" name="doc_<?= $d['id']; ?>" id="doc_<?= $d['id']; ?>">
					<label class="form-check-label" for="doc_<?= $d['id']; ?>">
						<?= $n.$d['name']; ?>
					</label>
				</div>
			<?php endforeach; ?>

			<button class="btn btn-primary w-100 py-2 mt-5" type="submit">Valider</button>
			<p class="mt-5 mb-3 text-body-secondary">Fait avec &#9749; par OA</p>
		</form>
	</main>
	<script src="bootstrap.bundle.min.js"></script>

			</body>
	</html>
	<?php
}else{
	//new interface start
	?>
	<?php
	require("conf.php");

	if (!isset($_GET['n']) || !in_array($_GET['n'], GetTypesAbbr())) {
		header("Location: index.php");
		exit();
	}

	$n = htmlspecialchars($_GET['n']);
	$all_documents_for_type = $CONF['documents'][$n] ?? [];
	$all_cerfa_items = $CONF['CERFA'] ?? [];
	?>

	<!DOCTYPE html>
	<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>UrbaHelper - Demande <?= $n; ?></title>

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
		<style>
			body {
				font-family: 'Inter', sans-serif;
			}
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
            modal.show .modal-content {
                transform: scale(1);
            }
            .autocomplete-active {
                background-color: #bfdbfe; /* A light blue color for highlighting */
            }
		</style>
	</head>
	<body class="bg-gray-100 p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
		<main class="w-full max-w-xl mx-auto bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 my-8">
			<form method="POST" action="fill.php" id="main-form">
        		<a href="index.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mb-4">Retour</a>
				<h1 class="text-3xl font-bold text-center text-indigo-700 mb-6">Demande <?= $n; ?></h1>
				<input type="hidden" name="type" value="<?= $n ?>">

				<div class="mt-4 flex items-center mb-4">
					<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="sve" id="sve">
					<label class="ml-2 text-gray-700" for="sve">
						SVE
					</label>
				</div>

				<hr class="border-t border-gray-300 my-6">

				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div class="mt-4">
						<label for="instructeur" class="block text-gray-700 text-sm font-bold mb-2">Instructeur<span style="color:red">*</span></label>
						<select id="instructeur" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" name="instructeur" required>
							<option value="" disabled selected>Sélectionnez un instructeur</option>
							<?php foreach($CONF['instructeurs'] as $c): ?>
								<option value="<?= htmlspecialchars($c['abbr']); ?>"><?= htmlspecialchars($c['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mt-4">
						<label for="instruit" class="block text-gray-700 text-sm font-bold mb-2">Instruit le</label>
						<input type="date" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="instruit" name="instruit" >
					</div>
				</div>

				<hr class="border-t border-gray-300 my-6">

				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div class="mt-4">
						<label for="date" class="block text-gray-700 text-sm font-bold mb-2">Porté à connaissance du superviseur le</label>
						<input type="date" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="date" name="date">
					</div>
					<div class="mt-4">
						<label for="boss" class="block text-gray-700 text-sm font-bold mb-2">Superviseur (porté à connaissance de...)</label>
						<select id="boss" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" name="boss">
							<option value="" disabled selected>Sélectionnez un superviseur</option>
							<?php foreach($CONF['responsables'] as $c): ?>
								<option value="<?= htmlspecialchars($c); ?>"><?= htmlspecialchars($c); ?></option>
							<?php endforeach; ?>
								<option value="<?= implode("-",$CONF['responsables']); ?>"><?= implode(", ",$CONF['responsables']); ?></option>
						</select>
					</div>
				</div>
				<div class="mt-4 flex items-center">
					<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="follow" id="follow">
					<label class="ml-2 text-gray-700" for="follow">
						Veut suivre le dossier
					</label>
				</div>

				<hr class="border-t border-gray-300 my-6">

				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div class="mt-4">
						<label for="commune" class="block text-gray-700 text-sm font-bold mb-2">Commune<span style="color:red">*</span></label>
						<select id="commune" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" name="commune"  required>
							<option value="" disabled selected>Sélectionnez une commune</option>
							<?php foreach($CONF['communes'] as $c): ?>
								<option value="<?= htmlspecialchars($c['code']); ?>"><?= htmlspecialchars($c['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mt-4">
						<label for="id_demande" class="block text-gray-700 text-sm font-bold mb-2">Numéro de demande (#####) :</label>
						<input type="number" min="1" max="99999" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="id_demande" name="id" placeholder="Ex: 12345" >
					</div>
				</div>

				<div class="mt-4">
					<label for="nom" class="block text-gray-700 text-sm font-bold mb-2">Nom et prénom du pétitionnaire :</label>
					<input type="text" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="nom" name="nom" placeholder="Nom et prénom du pétitionnaire" >
				</div>
				<div class="mt-4">
					<label for="objet" class="block text-gray-700 text-sm font-bold mb-2">Objet :</label>
					<input type="text" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="objet" name="objet" placeholder="Objet de la demande" >
				</div>
				<div class="mt-4">
					<label for="observations" class="block text-gray-700 text-sm font-bold mb-2">Observations :</label>
					<textarea class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Entrez vos observations ici..." name="obs" id="observations" rows="5"></textarea>
				</div>
				<div class="mt-4">
					<label for="zone" class="block text-gray-700 text-sm font-bold mb-2">Zone<span style="color:red">*</span></label>
					<select id="zone" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" name="zone"  required>
						<option value="" disabled selected>Sélectionnez une zone</option>
						<?php foreach($CONF['zones'] as $k => $c): ?>
							<option value="<?= htmlspecialchars($c['name']); ?>"><?= htmlspecialchars($c['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="mt-4 flex items-center">
					<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" name="lotissement" id="lotissement_checkbox">
					<label class="ml-2 text-gray-700 flex-shrink-0" for="lotissement_checkbox">
						Lotissement :
					</label>
					<input onkeyup="var v = String(this.value+''); if(v != '' && v != ' ' && v.length > 4){document.getElementById('lotissement_checkbox').checked=true;}else{document.getElementById('lotissement_checkbox').checked=false;}"
						class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-2 flex-grow" type="text" id="lotisname" name="lotisname" placeholder="Nom du lotissement le cas échéant">
				</div>

				<hr class="border-t border-gray-300 my-6">

				<div class="text-lg font-bold text-gray-800 mb-4">Périmètres et contraintes :</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-2">
					<div class="flex items-center">
						<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="ac1" id="ac1">
						<label class="ml-2 text-gray-700" for="ac1">AC1 (MH)</label>
					</div>
					<div class="flex items-center">
						<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="ac2i" id="ac2i">
						<label class="ml-2 text-gray-700" for="ac2i">AC2 (Site Inscrit)</label>
					</div>
					<div class="flex items-center">
						<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="ac2c" id="ac2c">
						<label class="ml-2 text-gray-700" for="ac2c">AC2 (Site Classé)</label>
					</div>
					<div class="flex items-center">
						<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" value="on" name="ac4" id="ac4">
						<label class="ml-2 text-gray-700" for="ac4">AC4 (SPR)</label>
					</div>
					<div class="flex items-center col-span-full sm:col-span-2 lg:col-span-3">
						<input class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500" type="checkbox" name="contr" id="contr_checkbox">
						<label class="ml-2 text-gray-700 flex-shrink-0" for="contr_checkbox">
							Autres servitudes et contraintes :
						</label>
						<input onkeyup="var v = String(this.value+''); if(v != '' && v != ' ' && v.length > 0){document.getElementById('contr_checkbox').checked=true;}else{document.getElementById('contr_checkbox').checked=false;}"
							class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-2 flex-grow" type="text" id="contrname" name="contrname" placeholder="Liste des servitudes et contraintes le cas échéant">
					</div>
				</div>

                <hr class="border-t border-gray-300 my-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Vérifications CERFA</h2>
                <div id="cerfa-container" class="space-y-3">
                    </div>
                <button type="button" id="add-cerfa-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-black font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-300 ease-in-out text-sm">Ajouter une vérification CERFA</button>
                <hr class="border-t border-gray-300 my-6">

				<h2 class="text-lg font-bold text-gray-800 mb-4">Documents</h2>
				<div id="document-checkboxes-container" class="space-y-2">
					</div>
				<button type="button" id="add-doc-btn" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">Ajouter un document</button>
				
				<button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105 w-full mt-8">Valider</button>
				<p class="mt-8 mb-3 text-center text-gray-500 text-sm">Fait avec &#9749; par OA</p>
			</form>
		</main>

        <div id="add-doc-modal" class="modal">
			<div class="modal-content">
				<h3 class="text-xl font-bold mb-4">Ajouter un document</h3>
                <input type="text" id="doc-autocomplete-input" placeholder="Tapez pour rechercher un document..." class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <div id="autocomplete-list" class="mt-2 border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                    </div>
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
    const allDocuments = <?= json_encode($all_documents_for_type); ?>;
    const allCerfaItems = <?= json_encode(array_values($all_cerfa_items)); ?>;

    // --- ELEMENT SELECTORS ---
    const n = "<?= $n; ?>";
    const mainForm = document.getElementById('main-form');

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
    const AUTOSAVE_KEY = 'urbaHelperAutosave';
    const AUTOSAVE_INTERVAL = 15000;

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
        input.checked = item.default;

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
        checkLabelDiv.className = 'flex items-center flex-grow overflow-hidden mr-2'; // Added overflow protection

        const input = document.createElement('input');
        input.className = 'h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500 flex-shrink-0';
        input.type = 'checkbox';
        input.name = `doc_${doc.id}`;
        input.id = `doc_${doc.id}`;
        input.value = 'on';
        input.checked = false; // Default state for added documents

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `isdoc_${doc.id}`;
        hiddenInput.value = "1";

        const label = document.createElement('label');
        label.className = 'ml-3 text-gray-700'; // Added truncate for long names
        label.htmlFor = `doc_${doc.id}`;
        label.textContent = n + doc.name;
        label.title = n + doc.name; // Show full name on hover

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

    function populateAutocompleteList(filter = '') {
        autocompleteList.innerHTML = '';
        currentFocus = -1;
        
        const availableDocs = Object.values(allDocuments).filter(doc => !displayedDocIds.has(doc.id));
        const filteredDocs = availableDocs.filter(doc => doc.name.toLowerCase().includes(filter.toLowerCase()));

        filteredDocs.forEach(doc => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'p-3 hover:bg-gray-100 cursor-pointer';
            itemDiv.textContent = doc.name;
            itemDiv.dataset.id = doc.id;
            itemDiv.addEventListener('click', function() {
                createCheckbox(allDocuments[this.dataset.id]);
                closeDocModal();
            });
            autocompleteList.appendChild(itemDiv);
        });

        if (autocompleteList.children.length > 0) {
            currentFocus = 0;
            addActive(autocompleteList.children);
        }
    }
    
    function closeDocModal() {
        docModal.style.display = 'none';
        autocompleteInput.value = '';
    }
    
    function validateAndAddSelectedItem() {
        const activeItem = autocompleteList.querySelector(".autocomplete-active");
        if (activeItem) {
            const selectedDocId = activeItem.dataset.id;
            if (selectedDocId) {
                const docToAdd = allDocuments[selectedDocId];
                if (docToAdd) { 
                    createCheckbox(docToAdd);
                }
                closeDocModal();
            }
        }
    }

    function addActive(items) {
        if (!items || items.length === 0) return false;
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

    // --- AUTOSAVE LOGIC ---

    function serializeForm(form) {
        const obj = {};
        const formData = new FormData(form);
        for (const [key, value] of formData.entries()) {
            obj[key] = value;
        }
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            if (!obj.hasOwnProperty(cb.name)) {
                obj[cb.name] = 'off';
            }
        });
        return obj;
    }

    function saveFormState() {
        if (!mainForm) return;
        const formData = serializeForm(mainForm);
        const state = {
            url: window.location.href,
            formData: formData
        };
        localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(state));
    }

    function loadFormState() {
        const savedStateJSON = localStorage.getItem(AUTOSAVE_KEY);
        if (!savedStateJSON) return;

        try {
            const savedState = JSON.parse(savedStateJSON);
            
            if (savedState.url !== window.location.href || savedState.formData.supfields) {
                return;
            }

            if (savedState.formData && mainForm) {
                const formData = savedState.formData;
                
                // Recreate dynamic documents from saved data
                Object.keys(formData).forEach(key => {
                    if (key.startsWith('isdoc_')) {
                        const docId = key.split('_')[1];
                        if (allDocuments[docId]) {
                            createCheckbox(allDocuments[docId]);
                        }
                    }
                });

                // Recreate dynamic CERFA items from saved data
                Object.keys(formData).forEach(key => {
                    if (key.startsWith('cerfa_present_')) {
                        const index = parseInt(key.split('_')[2], 10);
                        if (!isNaN(index) && allCerfaItems[index]) {
                            createCerfaElement(allCerfaItems[index], index);
                        }
                    }
                });

                // Populate all form fields from saved data
                for (const key in formData) {
                    if (Object.prototype.hasOwnProperty.call(formData, key)) {
                        const element = mainForm.elements[key];
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = (formData[key] === 'on');
                            } else {
                                element.value = formData[key];
                            }
                        }
                    }
                }
            }
        } catch(e) {
            console.error("Error loading saved state:", e);
            localStorage.removeItem(AUTOSAVE_KEY);
        }
    }

    // --- INITIALIZATION AND EVENT LISTENERS ---

    document.addEventListener('DOMContentLoaded', () => {
        // Initial display of default documents
        Object.values(allDocuments).forEach(doc => {
            if (doc.default) {
                createCheckbox(doc);
            }
        });

        // Initial display of default CERFA items
        allCerfaItems.forEach((item, index) => {
            if (item.default) {
                createCerfaElement(item, index);
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
        autocompleteInput.addEventListener('input', () => populateAutocompleteList(autocompleteInput.value));
        autocompleteInput.addEventListener('keydown', (e) => {
            let items = autocompleteList.children;
            if (e.key === "ArrowDown") {
                e.preventDefault(); currentFocus++; addActive(items);
            } else if (e.key === "ArrowUp") {
                e.preventDefault(); currentFocus--; addActive(items);
            } else if (e.key === "Enter") {
                e.preventDefault(); if (currentFocus > -1) validateAndAddSelectedItem();
            }
        });

        loadFormState();
        setInterval(saveFormState, AUTOSAVE_INTERVAL);
    });

</script>
	</body>
	</html>
<?php } ?>