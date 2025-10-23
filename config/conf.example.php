<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



//CONFIGURATION

$CONF = [
	/**
	 * Code Département
	 */
	"departement" => "075",

	/**
	 * Types de demandes, et leurs sous-types si necessaire
	 * Attention, si un type contient des sous-types (ex:  PC, PCMI), dans les sous-types pensez à integrer le type originel dans les sous-types
	 * Exemple : PC (type) contient PC et PCMI
	 * La valeur abbr => Abbréviation courante
	 * La valeur name => Nom complet
	 * La valeur call => Sur deux charactères, l'abbréviation affichée dans la feuille d'instruction / le numéro de demande (DP, PC, PA)
	 * 
	 * Structure : 
	 * "types" => [
	 * 		"##ABBREVIATION TYPE 1##" => [
	 * 			"name" => "##NOM COMPLET TYPE TYPE 1##",
	 * 			"abbr" => "##ABBREVIATION TYPE 1##",
	 * 			"call" => "##ABBREVIATION LEGALE TYPE 1##",
	 * 			"subt" => []
	 * 		],
	 * 		"##ABBREVIATION##" => [
	 * 			"name" => "##NOM COMPLET TYPE##",
	 * 			"abbr" => "##ABBREVIATION##",
	 * 			"call" => "##ABBREVIATION LEGALE##",
	 * 			"subt" => [
	 * 				"##ABBREV. SOUS-TYPE 1##" => [
	 * 					"name" => "##NOM COMPLET SOUS-TYPE 1##",
	 * 					"abbr" => "##ABBREV. SOUS-TYPE 1##",
	 * 					"call" => "##ABBREV. LEGALE SOUS-TYPE 1##",
	 * 				],
	 * 				"##ABBREV. SOUS-TYPE 2##" => [
	 * 					"name" => "##NOM COMPLET SOUS-TYPE 2##",
	 * 					"abbr" => "##ABBREV. SOUS-TYPE 2##",
	 * 					"call" => "##ABBREV. LEGALE SOUS-TYPE 2##",
	 * 				],
	 * 			]
	 * 		],
	 * ]
	 * 
	 * 
	 * Dans l'exemple ci-dessus, ainsi que dans tout le fichier, veuillez-veiller à avoir des abbréviations constantes.
	 */
	"types" => [
		"CUB" => [
			"name" => "Certificat d'Urbanisme B",
			"abbr" => "CUB",
			"call" => "CUB",
			"subt" => [],
		],
		"DP" => [
			"name" => "Déclaration préalable de travaux",
			"abbr" => "DP",
			"call" => "DP",
			"subt" => [
				"DPC" => [
					"name" => "Déclaration préalable de travaux construction",
					"abbr" => "DPC",
					"call" => "DPC",
				],
				"DPA" => [
					"name" => "Déclaration préalable de travaux aménagement",
					"abbr" => "DPA",
					"call" => "DPA",
				],
			],
		],
		"PC" => [
			"name" => "Permis de Construire",
			"abbr" => "PC",
			"call" => "PC",
			"subt" => [
				"PC" => [
					"name" => "Permis de Construire",
					"abbr" => "PC",
					"call" => "PC"
				],
				"PCMI" => [
					"name" => "Permis de Construire pour Maison Individuelle",
					"abbr" => "PCMI",
					"call" => "PCMI"
				]
			]
		],
		"PA" => [
			"name" => "Permis d'Amenager",
			"abbr" => "PA",
			"cal" => "PA",
			"subt" => []
		],
		"PDD" => [
			"name" => "Permis de Démolir",
			"abbr" => "PDD",
			"call" => "PDD",
			"subt" => []
		],
	],

	"CERFA" => [
		[
			"name" => "Cerfa présent",
			"default" => true,
		],
		[
			"name" => "Surface de plancher",
			"default" => false,
		],
	],

	/**
	 * Documents : liste des documents. Chaque document est entre crochets [], dispose d'un ID (nombre unique, dédié a cette ligne), d'un nom (name) et d'un champ "default" qui dit s'il est affiché par défault.
	 * 
	 * exemple : 
	 * 
	 * "documents" => [
	 * 
	 * 		"DPC" => [ <= abbréviation "abbr"
	 * 
	 *	 		1 => [
	 *	 			"id" => "1",
	 *				"name" => "##NOM DU DOCUMENT 1",
	 *	 			"default" => true,
	 *	 		],
	 *	 
	 *	 
	 *	 
	 *	 		2 => [
	 *	 			"id" => "2",
	 *				"name" => "##NOM DU DOCUMENT 2",
	 *	 			"default" => false,
	 *	 		],
	 *	 		3 => [
	 *	 			"id" => "3",
	 *				"name" => "##NOM DU DOCUMENT 3",
	 *	 			"default" => false,
	 *	 		],
	 *	 	],
	 *		"DPA" => [
	 * 
	 *	 		1 => [
	 *	 			"id" => "1",
	 *				"name" => "##NOM DU DOCUMENT 1",
	 *	 			"default" => true,
	 *	 		],
	 *	 
	 *	 
	 *	 
	 *	 		2 => [
	 *	 			"id" => "2",
	 *				"name" => "##NOM DU DOCUMENT 2",
	 *	 			"default" => false,
	 *	 		],
	 *	 		3 => [
	 *	 			"id" => "3",
	 *				"name" => "##NOM DU DOCUMENT 3",
	 *	 			"default" => false,
	 *	 		],
	 *	 	],
	 *		"PCMI" => [
	 * 
	 *	 		1 => [
	 *	 			"id" => "1",
	 *				"name" => "##NOM DU DOCUMENT 1",
	 *	 			"default" => true,
	 *	 		],
	 *	 
	 *	 
	 *	 
	 *	 		2 => [
	 *	 			"id" => "2",
	 *				"name" => "##NOM DU DOCUMENT 2",
	 *	 			"default" => false,
	 *	 		],
	 *	 		3 => [
	 *	 			"id" => "3",
	 *				"name" => "##NOM DU DOCUMENT 3",
	 *	 			"default" => false,
	 *	 		],
	 *	 	],
	 * ]
	 * 
	 */

	"documents" => [
		"DPA" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 431-36 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "3. Un plan en coupe précisant l'implantation de la construction par rapport au profil du terrain [Art. R.431-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "4. Un plan des façades et des toitures [Art. R.431-10 a) du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "9. Un plan sommaire des lieux indiquant, le cas échéant, les bâtiments de toute nature existant sur le terrain [Art. R. 441-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "10. Un croquis et un plan coté dans les trois dimensions faisant apparaître la ou les divisions projetées [Art. R. 441-10 c) du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "10-1. L'attestation de l'accord du lotisseur [Art. R. 442-21 b) du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "27. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 441-5 1º du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "27-1. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale... [Art. R. 441-5 2º du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => "28. Le dossier d'évaluation des incidences prévu à l'Art. R. 414-23 du code de l'environnement... [Art. R. 441-6 a) du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "30. L'attestation prévue à l'article R. 171-35 du code de la construction et de l'habitation [Art. R.441-8-4 du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "31. L'attestation mentionnée à l'article R. 111-25-19 du code de l'urbanisme [Art. R.441-8-4 du code de l'urbanisme]",
			"default" => false,
			],
		],
		"DPC" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 431-36 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Un plan de masse coté dans les 3 dimensions [Art. R.431-36 b) du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Un plan en coupe précisant l'implantation de la construction par rapport au profil du terrain [Art. R.431-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "4. Un plan des façades et des toitures [Art. R.431-10 a) du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "5. Une représentation de l'aspect extérieur de la construction faisant apparaître les modifications projetées [Art. R.431-36 c) du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "6. Un document graphique permettant d'apprécier l'insertion du projet de construction dans son environnement [Art. R. 431-10 c du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "7. Une photographie permettant de situer le terrain dans l'environnement proche [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "8. Une photographie permettant de situer le terrain dans le paysage lointain [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => " 8-1. Une note précisant la nature de la ou des dérogations demandées... [Art. R. 431-31-2 du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "11. Une notice faisant apparaître les matériaux utilisés et les modalités d'exécution des travaux [Art. R. 431-14, R. 431-14-1 et R. 441-8-1 du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "11-1. Le dossier prévu au II de l'article R. 331-19 du code de l'environnement [Art. R. 431-14-1 et R. 441-8-1 du code de l'urbanisme]",
			"default" => false,
			],
			12 => 
			[
			"id" => 12,
			"name" => "11-1-1. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			13 => 
			[
			"id" => 13,
			"name" => "11-1-2. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale... [Art. R. 431-16 b) du code de l'urbanisme]",
			"default" => false,
			],
			14 => 
			[
			"id" => 14,
			"name" => "11-2. Le dossier d'évaluation des incidences prévu à l'art. R. 414-23 du code de l'environnement... [Art. R. 431-16 c) du code de l'urbanisme]",
			"default" => false,
			],
			15 => 
			[
			"id" => 15,
			"name" => "12. Une notice précisant l'activité économique qui doit être exercée dans le bâtiment [Art. R. 431-16 h) du code de l'urbanisme]",
			"default" => false,
			],
			16 => 
			[
			"id" => 16,
			"name" => "12-1. Un document... attestant que la construction fait preuve d'exemplarité énergétique... [Art. R. 431-18 du code de l'urbanisme]",
			"default" => false,
			],
			17 => 
			[
			"id" => 17,
			"name" => "12-2. Un document par lequel le demandeur s'engage à installer des dispositifs conformes... [Art. R. 431-18-1 du code de l'urbanisme]",
			"default" => false,
			],
			18 => 
			[
			"id" => 18,
			"name" => "14. Une note précisant la nature des travaux pour lesquels une dérogation est sollicitée... [Art. R. 431-31 du code de l'urbanisme]",
			"default" => false,
			],
			19 => 
			[
			"id" => 19,
			"name" => "14-1. Une demande de dérogation comprenant le document... attestant que la construction fait preuve d'exemplarité environnementale [Art. R.431-31-3 du code de l'urbanisme]",
			"default" => false,
			],
			20 => 
			[
			"id" => 20,
			"name" => "15. Une copie du contrat ou de la décision judiciaire relatif à l'institution de ces servitudes [Art. R. 431-32 du code de l'urbanisme]",
			"default" => false,
			],
			21 => 
			[
			"id" => 21,
			"name" => "16. Une copie du contrat ayant procédé au transfert des possibilités de construction... [Art. R. 431-33 du code de l'urbanisme]",
			"default" => false,
			],
			22 => 
			[
			"id" => 22,
			"name" => "16-1. Le justificatif de dépôt de la demande d'autorisation prévue à l'article R. 244-1 du code de l'aviation civile [Art. R. 431-36 d) du code de l'urbanisme]",
			"default" => false,
			],
			23 => 
			[
			"id" => 23,
			"name" => "17. Un document graphique faisant apparaître l'état initial et l'état futur de chacun des éléments... [Art. R. 431-37 du code de l'urbanisme]",
			"default" => false,
			],
			24 => 
			[
			"id" => 24,
			"name" => "18. L'attestation assurant le respect des règles d'hygiène, de sécurité... [Art. R. 441-10]",
			"default" => false,
			],
			25 => 
			[
			"id" => 25,
			"name" => "21. Le formulaire de déclaration de la redevance bureaux [Art. A. 520-1 du code de l'urbanisme]",
			"default" => false,
			],
			26 => 
			[
			"id" => 26,
			"name" => "22. L'extrait de la convention précisant le lieu du projet urbain partenarial... [Art. R. 431-23-2 du code de l'urbanisme]",
			"default" => false,
			],
			27 => 
			[
			"id" => 27,
			"name" => "23. La copie de l'agrément [Art. R. 431-16 g) du code de l'urbanisme]",
			"default" => false,
			],
			28 => 
			[
			"id" => 28,
			"name" => "25. Le dossier de demande d'autorisation de travaux [Art. L.126-20 et L. 183-14 du code de la construction...]",
			"default" => false,
			],
			29 => 
			[
			"id" => 29,
			"name" => "26. Un document contenant la mention et les éléments prévus au 1) de l'article R. 324-1-7 du code du tourisme",
			"default" => false,
			],
			30 => 
			[
			"id" => 30,
			"name" => "29. La décision prise sur la demande de dérogation à l'obligation de raccordement à un réseau de chaleur et de froid... [Art. R.431-16 q) du code de l'urbanisme]",
			"default" => false,
			],
			31 => 
			[
			"id" => 31,
			"name" => "32. L'attestation prévue à l'article R. 171-35 du code de la construction et de l'habitation [Art. R.431-16 r) du code de l'urbanisme]",
			"default" => false,
			],
			32 => 
			[
			"id" => 32,
			"name" => "33. L'attestation mentionnée à l'article R. 111-25-19 du code de l'urbanisme [Art. R.431-16 r) du code de l'urbanisme]",
			"default" => false,
			],
			33 => 
			[
			"id" => 33,
			"name" => "34. Un document permettant de justifier le respect des critères prévus à l'article R. 111-20-1 du code de l'urbanisme [Art. R. 431-27 I du code de l'urbanisme]",
			"default" => false,
			],
			34 => 
			[
			"id" => 34,
			"name" => "35. Un document permettant de justifier que l'installation des serres, des hangars et des ombrières... [Art. R. 431-27 II du code de l'urbanisme]",
			"default" => false,
			],
			35 => 
			[
			"id" => 35,
			"name" => "36. Un dossier présentant les justifications détaillées du respect des conditions prévues à l'article L.314-36 du code de l'énergie [Art. R. 431-27 III du code de l'urbanisme]",
			"default" => false,
			],
			36 => 
			[
			"id" => 36,
			"name" => "37. Un document précisant l'état initial du terrain et de ses abords... [Art. R. 431-8 1º du code de l'urbanisme]",
			"default" => false,
			],
		],
		"PCMI" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 431-7 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Un plan de masse des constructions à édifier ou à modifier [Art. R. 431-9 du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Un plan en coupe du terrain et de la construction [Article R. 431-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "4. Une notice décrivant le terrain et présentant le projet [Art. R. 431-8 du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "5. Un plan des façades et des toitures [Art. R. 431-10 a) du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "6. Un document graphique permettant d'apprécier l'insertion du projet de construction dans son environnement [Art. R. 431-10 c) du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "7. Une photographie permettant de situer le terrain dans l'environnement proche [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "8. Une photographie permettant de situer le terrain dans le paysage lointain [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => "9. Le certificat indiquant la surface constructible attribuée à votre lot [Art. R. 442-11 1er al du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "10. Le certificat attestant l'achèvement des équipements desservant le lot [Art. R. 431-22-1 a) du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "11. Une copie des dispositions du cahier des charges de cession de terrain... [Art. R. 431-23 a) du code de l'urbanisme]",
			"default" => false,
			],
			12 => 
			[
			"id" => 12,
			"name" => "12. La convention entre la commune ou l'établissement public et vous... [Art. R. 431-23 b) du code de l'urbanisme]",
			"default" => false,
			],
			13 => 
			[
			"id" => 13,
			"name" => "12-1. Le dossier d'évaluation des incidences prévu à l'Art. R. 414-23... [Art. R. 431-16 c) du code de l'urbanisme]",
			"default" => false,
			],
			14 => 
			[
			"id" => 14,
			"name" => "12-1-1. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			15 => 
			[
			"id" => 15,
			"name" => "12-1-2. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale... [Art. R. 431-16 b) du code de l'urbanisme]",
			"default" => false,
			],
			16 => 
			[
			"id" => 16,
			"name" => "12-2. L'attestation de conformité du projet d'installation d'assainissement non collectif [Art. R. 431-16 d) du code de l'urbanisme]",
			"default" => false,
			],
			17 => 
			[
			"id" => 17,
			"name" => "13. L'attestation relative au respect des règles de construction parasismique... [Art. R. 431-16 e) du code de l'urbanisme]",
			"default" => false,
			],
			18 => 
			[
			"id" => 18,
			"name" => "14. L'attestation de l'architecte ou de l'expert certifiant que l'étude a été réalisée... [Art. R. 431-16 f) du code de l'urbanisme]",
			"default" => false,
			],
			19 => 
			[
			"id" => 19,
			"name" => "14-1. L'attestation de respect de la réglementation thermique... [Art. R. 431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			20 => 
			[
			"id" => 20,
			"name" => "14-2. L'attestation de respect des exigences de performance énergétique et environnementale... [Art. R. 431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			21 => 
			[
			"id" => 21,
			"name" => "15. Un document... attestant que la construction fait preuve d'exemplarité énergétique... [Art. R. 431-18 du code de l'urbanisme]",
			"default" => false,
			],
			22 => 
			[
			"id" => 22,
			"name" => "16. Un document par lequel le demandeur s'engage à installer des dispositifs conformes... [Art. R. 431-18-1 du code de l'urbanisme]",
			"default" => false,
			],
			23 => 
			[
			"id" => 23,
			"name" => "17. La copie de la lettre du préfet qui vous fait savoir que votre demande d'autorisation de défrichement est complète... [Art. R. 431-19 du code de l'urbanisme]",
			"default" => false,
			],
			24 => 
			[
			"id" => 24,
			"name" => "18. La justification du dépôt de la demande de permis de démolir [Art. R. 431-21 a) du code de l'urbanisme]",
			"default" => false,
			],
			25 => 
			[
			"id" => 25,
			"name" => "19. Les pièces à joindre à une demande de permis de démolir... [Art. R. 431-21 b) du code de l'urbanisme]",
			"default" => false,
			],
			26 => 
			[
			"id" => 26,
			"name" => "20. L'accord du gestionnaire du domaine pour engager la procédure d'autorisation d'occupation temporaire... [Art. R. 431-13 du code de l'urbanisme]",
			"default" => false,
			],
			27 => 
			[
			"id" => 27,
			"name" => "21. Une notice faisant apparaître les matériaux utilisés et les modalités d'exécution des travaux [Art. R. 431-14 et R. 431-14-1 du code de l'urbanisme]",
			"default" => false,
			],
			28 => 
			[
			"id" => 28,
			"name" => "21-1. Le dossier prévu au II de l'article R. 331-19 du code de l'environnement [Art. R. 431-14-1 du code de l'urbanisme]",
			"default" => false,
			],
			29 => 
			[
			"id" => 29,
			"name" => "22. Le plan de situation du terrain sur lequel seront réalisées les aires de stationnement... [Art. R. 431-26 a) du code de l'urbanisme]",
			"default" => false,
			],
			30 => 
			[
			"id" => 30,
			"name" => "23. La promesse synallagmatique de concession ou d'acquisition [Art. R. 431-26 b) du code de l'urbanisme]",
			"default" => false,
			],
			31 => 
			[
			"id" => 31,
			"name" => "23-1. Une note précisant la nature des travaux pour lesquels une dérogation est sollicitée... [Art. R. 431-31 du code de l'urbanisme]",
			"default" => false,
			],
			32 => 
			[
			"id" => 32,
			"name" => "23-2. Une demande de dérogation comprenant les précisions et les justifications... [Art. R. 431-31-1 du code de l'urbanisme]",
			"default" => false,
			],
			33 => 
			[
			"id" => 33,
			"name" => "23-3. Une note précisant la nature de la ou des dérogations demandées... [Art. R. 431-31-2 du code de l'urbanisme]",
			"default" => false,
			],
			34 => 
			[
			"id" => 34,
			"name" => "23-4. Une demande de dérogation comprenant le document... attestant que la construction fait preuve d'exemplarité environnementale [Art. R.431-31-3 du code de l'urbanisme]",
			"default" => false,
			],
			35 => 
			[
			"id" => 35,
			"name" => "24. Une copie du contrat ou de la décision judiciaire relatifs à l'institution de ces servitudes [Art. R. 431-32 du code de l'urbanisme]",
			"default" => false,
			],
			36 => 
			[
			"id" => 36,
			"name" => "25. Une copie du contrat ayant procédé au transfert des possibilités de construction... [Art. R. 431-33 du code de l'urbanisme]",
			"default" => false,
			],
			37 => 
			[
			"id" => 37,
			"name" => "26. L'extrait de la convention précisant le lieu du projet urbain partenarial... [Art. R. 431-23-2 du code de l'urbanisme]",
			"default" => false,
			],
			38 => 
			[
			"id" => 38,
			"name" => "28. Le dossier de demande d'autorisation de travaux [Art. L. 126-20 et L.183-14 du code de la construction...]",
			"default" => false,
			],
		],
		"PC" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 431-7 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Un plan de masse des constructions à édifier ou à modifier [Art. R. 431-9 du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Un plan en coupe du terrain et de la construction [Article R. 431-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "4. Une notice décrivant le terrain et présentant le projet [Art. R. 431-8 du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "5. Un plan des façades et des toitures [Art. R. 431-10 a) du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "6. Un document graphique permettant d'apprécier l'insertion du projet de construction dans son environnement [Art. R. 431-10 c) du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "7. Une photographie permettant de situer le terrain dans l'environnement proche [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "8. Une photographie permettant de situer le terrain dans le paysage lointain [Art. R. 431-10 d) du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => "9. Un document graphique faisant apparaître l'état initial et l'état futur de chacune des parties du bâtiment faisant l'objet des travaux. [Art. R. 431-11 du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "10. L'accord du gestionnaire du domaine pour engager la procédure d'autorisation d'occupation temporaire du domaine public [Art. R. 431-13 du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "10-1. Une notice complémentaire indiquant les matériaux utilisés et les modalités d'exécution des travaux [Art. R. 431-14 et R. 431-14-1 du code de l'urbanisme]",
			"default" => false,
			],
			12 => 
			[
			"id" => 12,
			"name" => "10-2. Le dossier prévu au II de l'article R. 331-19 du code de l'environnement [Art. R. 431-14-1 du code de l'urbanisme]",
			"default" => false,
			],
			13 => 
			[
			"id" => 13,
			"name" => "11. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			14 => 
			[
			"id" => 14,
			"name" => "11-1. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale, des collectivités territoriales et leurs groupements intéressés par le projet [Art. R. 431-16 b) du code de l'urbanisme]",
			"default" => false,
			],
			15 => 
			[
			"id" => 15,
			"name" => "11-2. Le dossier d'évaluation des incidences prévu à l'Art. R. 414-23 du code de l'environnement ou l'étude d'impact en tenant lieu [Art. R. 431-16 c) du code de l'urbanisme]",
			"default" => false,
			],
			16 => 
			[
			"id" => 16,
			"name" => "11-3. L'attestation de conformité du projet d'installation [Art. R. 431-16 d) du code de l'urbanisme]",
			"default" => false,
			],
			17 => 
			[
			"id" => 17,
			"name" => "12. L'attestation relative au respect des règles de construction parasismique au stade de la conception [Art. R. 431-16 e) du code de l'urbanisme]",
			"default" => false,
			],
			18 => 
			[
			"id" => 18,
			"name" => "13. L'attestation de l'architecte ou de l'expert certifiant que l'étude a été réalisée et que le projet la prend en compte [Art. R. 431-16 f) du code de l'urbanisme]",
			"default" => false,
			],
			19 => 
			[
			"id" => 19,
			"name" => "14. La copie de l'agrément [Art. R. 431-16 g) du code de l'urbanisme]",
			"default" => false,
			],
			20 => 
			[
			"id" => 20,
			"name" => "15. Une notice précisant l'activité économique qui doit être exercée dans le bâtiment [Art. R. 431-16 h) du code de l'urbanisme]",
			"default" => false,
			],
			21 => 
			[
			"id" => 21,
			"name" => "16. L'étude de sécurité [Art. R. 431-16 i) du code de l'urbanisme]",
			"default" => false,
			],
			22 => 
			[
			"id" => 22,
			"name" => "16-1. L'attestation de respect de la réglementation thermique, lorsqu'elle est exigée en application de l'article R.122-22 du code de la construction et de l'habitation [Art. R. 431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			23 => 
			[
			"id" => 23,
			"name" => "16-1-1. L'attestation de respect des exigences de performance énergétique et environnementale, lorsqu'elle est exigée en application de l'article R.122-24-1 du code de la construction et de l'habitation [Art. R.431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			24 => 
			[
			"id" => 24,
			"name" => "16-2. L'analyse de compatibilité du projet avec la canalisation du point de vue de la sécurité des personnes, prévue à l'art. R. 555-31 du code de l'environnement [Art. R. 431-16 k) du code de l'urbanisme]",
			"default" => false,
			],
			25 => 
			[
			"id" => 25,
			"name" => "16-3. Le récépissé de transmission du dossier à la commission départementale de la sécurité des transports de fonds [Art. R. 431-16 l) du code de l'urbanisme]",
			"default" => false,
			],
			26 => 
			[
			"id" => 26,
			"name" => "16-4. Le bilan de la concertation et le document conclusif [Art. R. 431-16 m) du code de l'urbanisme]",
			"default" => false,
			],
			27 => 
			[
			"id" => 27,
			"name" => "16-5. Une attestation établie par un bureau d'études certifié dans le domaine des sites et sols pollués... [Art. R. 431-16 n) du code de l'urbanisme]",
			"default" => false,
			],
			28 => 
			[
			"id" => 28,
			"name" => "16-6. Une attestation établie par un bureau d'études certifié dans le domaine des sites et sols pollués... [Art. R.431-16 o) du code de l'urbanisme]",
			"default" => false,
			],
			29 => 
			[
			"id" => 29,
			"name" => "17. Un tableau indiquant la surface de plancher des logements créés... [Art. R. 431-16-1 du code de l'urbanisme]",
			"default" => false,
			],
			30 => 
			[
			"id" => 30,
			"name" => "17-1. Un tableau indiquant la proportion de logements de la taille minimale imposée... [Art. R. 431-16-2 du code de l'urbanisme]",
			"default" => false,
			],
			31 => 
			[
			"id" => 31,
			"name" => "17-2. Un tableau indiquant le nombre de logements familiaux et la part de ces logements... [Art. R. 431-16-3 du code de l'urbanisme]",
			"default" => false,
			],
			32 => 
			[
			"id" => 32,
			"name" => "22. Un document prévu aux articles R. 171-1 à R. 171-5 du code de la construction et de l'habitation attestant que la construction fait preuve d'exemplarité énergétique ou environnementale... [Art. R. 431-18 du code de l'urbanisme]",
			"default" => false,
			],
			33 => 
			[
			"id" => 33,
			"name" => "23. Un document par lequel le demandeur s'engage à installer des dispositifs conformes... [Art. R. 431-18-1 du code de l'urbanisme]",
			"default" => false,
			],
			34 => 
			[
			"id" => 34,
			"name" => "24. La copie de la lettre du préfet qui vous fait savoir que votre demande d'autorisation de défrichement est complète... [Art. R. 431-19 du code de l'urbanisme]",
			"default" => false,
			],
			35 => 
			[
			"id" => 35,
			"name" => "25. Une justification du dépôt de la déclaration au titre de la législation relative aux Installations Classées pour la Protection de l'Environnement [Art. R. 431-20 du code de l'urbanisme]",
			"default" => false,
			],
			36 => 
			[
			"id" => 36,
			"name" => "25-1. Le récépissé de la demande d'enregistrement... [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			37 => 
			[
			"id" => 37,
			"name" => "26. La justification du dépôt de la demande de permis de démolir [Art. R. 431-21 a) du code de l'urbanisme]",
			"default" => false,
			],
			38 => 
			[
			"id" => 38,
			"name" => "27. Les pièces à joindre à une demande de permis de démolir, selon l'annexe page 22 [Art. R. 431-21 b) du code de l'urbanisme]",
			"default" => false,
			],
			39 => 
			[
			"id" => 39,
			"name" => "28. Le certificat indiquant la surface constructible attribuée à votre lot [Art. R. 442-11 1er al.) du code de l'urbanisme]",
			"default" => false,
			],
			40 => 
			[
			"id" => 40,
			"name" => "29. Le certificat attestant l'achèvement des équipements desservant le lot [Art. R. 431-22-1 a) du code de l'urbanisme]",
			"default" => false,
			],
			41 => 
			[
			"id" => 41,
			"name" => "29-1. L'attestation de l'accord du lotisseur, en cas de subdivision de lot [Art. R. 431-22-1 b) du code de l'urbanisme]",
			"default" => false,
			],
			42 => 
			[
			"id" => 42,
			"name" => "30. La copie des dispositions du cahier des charges de cession de terrain... [Art. R. 431-23 a) du code de l'urbanisme]",
			"default" => false,
			],
			43 => 
			[
			"id" => 43,
			"name" => "31. La convention entre la commune ou l'établissement public et vous... [Art. R. 431-23 b) du code de l'urbanisme]",
			"default" => false,
			],
			44 => 
			[
			"id" => 44,
			"name" => "31-1. L'attestation de l'aménageur certifiant qu'il a réalisé ou prendra en charge l'intégralité des travaux... [Art. R. 431-23-1 du code de l'urbanisme]",
			"default" => false,
			],
			45 => 
			[
			"id" => 45,
			"name" => "31-2. L'extrait de la convention précisant le lieu du projet urbain partenarial... [Art. R. 431-23-2 du code de l'urbanisme]",
			"default" => false,
			],
			46 => 
			[
			"id" => 46,
			"name" => "32. Le plan de division du terrain [Art. R. 431-24 du code de l'urbanisme]",
			"default" => false,
			],
			47 => 
			[
			"id" => 47,
			"name" => "33. Le projet de constitution d'une association syndicale des futurs propriétaires [Art. R. 431-24 du code de l'urbanisme]",
			"default" => false,
			],
			48 => 
			[
			"id" => 48,
			"name" => "33-1. Le formulaire de déclaration de la redevance bureaux [Art. R. 431-25-2 du code de l'urbanisme]",
			"default" => false,
			],
			49 => 
			[
			"id" => 49,
			"name" => "34. Le plan de situation du terrain sur lequel sont réalisées les aires de stationnement... [Art. R. 431-26 a) du code de l'urbanisme]",
			"default" => false,
			],
			50 => 
			[
			"id" => 50,
			"name" => "35. La promesse synallagmatique de concession ou d'acquisition [Art. R. 431-26 b) du code de l'urbanisme]",
			"default" => false,
			],
			51 => 
			[
			"id" => 51,
			"name" => "36. Une notice précisant la nature du commerce projeté et la surface de vente [Art. R. 431-27-1 du code de l'urbanisme]",
			"default" => false,
			],
			52 => 
			[
			"id" => 52,
			"name" => "37. La copie de la lettre du préfet attestant que le dossier de demande est complet. [Art. R. 431-28 du code de l'urbanisme]",
			"default" => false,
			],
			53 => 
			[
			"id" => 53,
			"name" => "38. Le récépissé de dépôt en préfecture de la demande d'autorisation prévue à l'article R. 146-14 du code de la construction et de l'habitation [Art. R. 431-29 du code de l'urbanisme]",
			"default" => false,
			],
			54 => 
			[
			"id" => 54,
			"name" => "39. Le dossier spécifique permettant de vérifier la conformité du projet avec les règles d'accessibilité aux personnes handicapées... [Art. R. 431-30 a) du code de l'urbanisme]",
			"default" => false,
			],
			55 => 
			[
			"id" => 55,
			"name" => "40. Le dossier spécifique permettant de vérifier la conformité du projet avec les règles de sécurité... [Art. R. 431-30 b) du code de l'urbanisme]",
			"default" => false,
			],
			56 => 
			[
			"id" => 56,
			"name" => "40-1. Une note précisant la nature des travaux pour lesquels une dérogation est sollicitée... [Art. R. 431-31 du code de l'urbanisme]",
			"default" => false,
			],
			57 => 
			[
			"id" => 57,
			"name" => "40-2. Une demande de dérogation comprenant les précisions et les justifications... [Art. R. 431-31-1 du code de l'urbanisme]",
			"default" => false,
			],
			58 => 
			[
			"id" => 58,
			"name" => "40-3. Une note précisant la nature de la ou des dérogations demandées... [Art. R. 431-31-2 du code de l'urbanisme]",
			"default" => false,
			],
			59 => 
			[
			"id" => 59,
			"name" => "40-4. Une demande de dérogation comprenant le document prévu à l'article R.171-3 du code de la construction et de l'habitation... [Art. R.431-31-3 du code de l'urbanisme]",
			"default" => false,
			],
			60 => 
			[
			"id" => 60,
			"name" => "41. Une copie du contrat ou de la décision judiciaire relatif à l'institution de ces servitudes [Art. R. 431-32 du code de l'urbanisme]",
			"default" => false,
			],
			61 => 
			[
			"id" => 61,
			"name" => "42. Une copie du contrat ayant procédé au transfert de possibilité de construction... [Art. R. 431-33 du code de l'urbanisme]",
			"default" => false,
			],
			62 => 
			[
			"id" => 62,
			"name" => "43. Le dossier d'autorisation d'exploitation commerciale [Art. R. 431-33-1 du code de l'urbanisme]",
			"default" => false,
			],
			63 => 
			[
			"id" => 63,
			"name" => "44. Le dossier de demande d'autorisation de travaux [Art. L.126-20 et L.183-14 du code de la construction et de l'habitation...]",
			"default" => false,
			],
			64 => 
			[
			"id" => 64,
			"name" => "45. Un document contenant la mention et les éléments prévus au 1) de l'article R. 324-1-7 du code du tourisme.",
			"default" => false,
			],
			65 => 
			[
			"id" => 65,
			"name" => "46. La décision prise sur la demande de dérogation à l'obligation de raccordement à un réseau de chaleur et de froid... [Art. R.431-16 q) du code de l'urbanisme]",
			"default" => false,
			],
			66 => 
			[
			"id" => 66,
			"name" => "47. L'attestation prévue à l'article R. 171-35 du code de la construction et de l'habitation [Art. R.431-16 r) du code de l'urbanisme]",
			"default" => false,
			],
			67 => 
			[
			"id" => 67,
			"name" => "48. L'attestation mentionnée à l'article R. 111-25-19 du code de l'urbanisme [Art. R.431-16 r) du code de l'urbanisme]",
			"default" => false,
			],
			68 => 
			[
			"id" => 68,
			"name" => "49. Un document permettant de justifier le respect des critères prévus à l'article R. 111-20-1 du code de l'urbanisme [Art. R. 431-27 I du code de l'urbanisme]",
			"default" => false,
			],
			69 => 
			[
			"id" => 69,
			"name" => "50. Un document permettant de justifier que l'installation des serres, des hangars et des ombrières à usage agricole est nécessaire... [Art. R. 431-27 II du code de l'urbanisme]",
			"default" => false,
			],
			70 => 
			[
			"id" => 70,
			"name" => "51. Un dossier présentant les justifications détaillées du respect des conditions prévues à l'article L.314-36 du code de l'énergie [Art. R. 431-27 III du code de l'urbanisme]",
			"default" => false,
			],
		],
		"CUB" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation [Art. R. 410-1 al 1 du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Une note descriptive succincte [Art. R. 410-1 al 2 du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Un plan du terrain, s'il existe des constructions.",
			"default" => false,
			],
		],
		"PA" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 441-2 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Une notice décrivant le terrain et le projet d'aménagement prévu [Art. R. 441-3 du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Un plan de l'état actuel du terrain à aménager et de ses abords [Art. R. 441-4 1º du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "4. Un plan de composition d'ensemble du projet coté dans les trois dimensions [Art. R. 441-4 2º du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "4-1. Le bilan de la concertation [Art. L 300-2 du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "5. Deux vues et coupes faisant apparaître la situation du projet dans le profil du terrain naturel [Art. R. 442-5 a) du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "6. Une photographie permettant de situer le terrain dans l'environnement proche [Art. R. 442-5 b) du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "7. Une photographie permettant de situer le terrain dans le paysage lointain [Art. R. 442-5 b) du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => "8. Le programme et les plans des travaux d'aménagement [Art. R. 442-5 c) du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "9. Un document graphique faisant apparaître une ou plusieurs hypothèses d'implantation des bâtiments [Art. R. 442-5 d) du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "10. Un projet de règlement s'il est envisagé d'apporter des compléments aux règles d'urbanisme en vigueur [Art. R. 442-6 a) du code de l'urbanisme]",
			"default" => false,
			],
			12 => 
			[
			"id" => 12,
			"name" => "11. Si nécessaire, l'attestation de la garantie d'achèvement des travaux... [Art. R. 442-6 b) du code de l'urbanisme]",
			"default" => false,
			],
			13 => 
			[
			"id" => 13,
			"name" => "12. L'engagement du lotisseur de constituer une association syndicale des acquéreurs de lots [Art. R. 442-7 du code de l'urbanisme]",
			"default" => false,
			],
			14 => 
			[
			"id" => 14,
			"name" => "12-1. Une attestation établie par un bureau d'études certifié... [Art. R. 442-8-1 du code de l'urbanisme]",
			"default" => false,
			],
			15 => 
			[
			"id" => 15,
			"name" => "12-2. L'attestation de l'accord du lotisseur [Art. R. 442-21 b) du code de l'urbanisme]",
			"default" => false,
			],
			16 => 
			[
			"id" => 16,
			"name" => "13. Un engagement d'exploiter le terrain selon le mode de gestion... [Art. R. 443-4 du code de l'urbanisme]",
			"default" => false,
			],
			17 => 
			[
			"id" => 17,
			"name" => "14. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 441-5 1º du code de l'urbanisme]",
			"default" => false,
			],
			18 => 
			[
			"id" => 18,
			"name" => "14-1. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale... [Art. R. 441-5 2º du code de l'urbanisme]",
			"default" => false,
			],
			19 => 
			[
			"id" => 19,
			"name" => "15-1. Le dossier d'évaluation des incidences prévu à l'Art. R. 414-23... [Art. R. 441-6 a) du code de l'urbanisme]",
			"default" => false,
			],
			20 => 
			[
			"id" => 20,
			"name" => "15-2. L'attestation de conformité du projet d'installation [Art. R. 441-6 b) du code de l'urbanisme]",
			"default" => false,
			],
			21 => 
			[
			"id" => 21,
			"name" => "15-3. L'attestation assurant le respect des règles d'hygiène, de sécurité... [Art. R. 441-6-1 du code de l'urbanisme]",
			"default" => false,
			],
			22 => 
			[
			"id" => 22,
			"name" => "16. La copie de la lettre du préfet... [Art. R. 441-7 du code de l'urbanisme]",
			"default" => false,
			],
			23 => 
			[
			"id" => 23,
			"name" => "16-1. Le dossier prévu au II de l'article R. 331-19 du code de l'environnement [Art. R. 441-8-1 du code de l'urbanisme]",
			"default" => false,
			],
			24 => 
			[
			"id" => 24,
			"name" => "16-2. Une attestation établie par un bureau d'études certifié... [Art. R. 441-8-3 du code de l'urbanisme]",
			"default" => false,
			],
			25 => 
			[
			"id" => 25,
			"name" => "17. L'extrait de la convention précisant le lieu du projet urbain partenarial... [Art. R. 431-23-2 du code de l'urbanisme]",
			"default" => false,
			],
			26 => 
			[
			"id" => 26,
			"name" => "18. Un plan de masse des constructions à édifier ou à modifier [Art. R. 431-9 du code de l'urbanisme]",
			"default" => false,
			],
			27 => 
			[
			"id" => 27,
			"name" => "19. Un plan des façades et des toitures [Art. R. 431-10 a) du code de l'urbanisme]",
			"default" => false,
			],
			28 => 
			[
			"id" => 28,
			"name" => "20. Un plan en coupe du terrain et de la construction [Art. R. 431-10 b) du code de l'urbanisme]",
			"default" => false,
			],
			29 => 
			[
			"id" => 29,
			"name" => "21. Un document graphique faisant apparaître l'état initial et l'état futur... [Art. R. 431-11 du code de l'urbanisme]",
			"default" => false,
			],
			30 => 
			[
			"id" => 30,
			"name" => "22. L'accord du gestionnaire du domaine... [Art. R. 431-13 du code de l'urbanisme]",
			"default" => false,
			],
			31 => 
			[
			"id" => 31,
			"name" => "23. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			32 => 
			[
			"id" => 32,
			"name" => "23-1. L'étude d'impact actualisée... [Art. R. 431-16 b) du code de l'urbanisme]",
			"default" => false,
			],
			33 => 
			[
			"id" => 33,
			"name" => "23-2. Le dossier d'évaluation des incidences... [Art. R. 431-16 c) du code de l'urbanisme]",
			"default" => false,
			],
			34 => 
			[
			"id" => 34,
			"name" => "23-3. L'attestation de conformité du projet d'installation [Art. R. 431-16 d) du code de l'urbanisme]",
			"default" => false,
			],
			35 => 
			[
			"id" => 35,
			"name" => "24. L'attestation relative au respect des règles de construction parasismique... [Art. R. 431-16 e) du code de l'urbanisme]",
			"default" => false,
			],
			36 => 
			[
			"id" => 36,
			"name" => "25. L'attestation de l'architecte ou de l'expert... [Art. R. 431-16 f) du code de l'urbanisme]",
			"default" => false,
			],
			37 => 
			[
			"id" => 37,
			"name" => "26. La copie de l'agrément [Art. R. 431-16 g) du code de l'urbanisme]",
			"default" => false,
			],
			38 => 
			[
			"id" => 38,
			"name" => "27. Une notice précisant l'activité économique... [Art. R. 431-16 h) du code de l'urbanisme]",
			"default" => false,
			],
			39 => 
			[
			"id" => 39,
			"name" => "28. L'étude de sécurité [Art. R. 431-16 i) du code de l'urbanisme]",
			"default" => false,
			],
			40 => 
			[
			"id" => 40,
			"name" => "28-1. L'attestation de respect de la réglementation thermique... [Art. R. 431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			41 => 
			[
			"id" => 41,
			"name" => "28-1-1. L'attestation de respect des exigences de performance énergétique... [Art. R. 431-16 j) du code de l'urbanisme]",
			"default" => false,
			],
			42 => 
			[
			"id" => 42,
			"name" => "28-2. Le bilan de la concertation... [Art. R. 431-16 m) du code de l'urbanisme]",
			"default" => false,
			],
			43 => 
			[
			"id" => 43,
			"name" => "28-3. Une attestation établie par un bureau d'études certifié... [Art. R. 431-16 n) du code de l'urbanisme]",
			"default" => false,
			],
			44 => 
			[
			"id" => 44,
			"name" => "28-4. Une attestation établie par un bureau d'études certifié... [Art. R. 431-16-o) du code de l'urbanisme]",
			"default" => false,
			],
			45 => 
			[
			"id" => 45,
			"name" => "29. Un tableau indiquant la surface de plancher des logements créés... [Art. R. 431-16-1 du code de l'urbanisme]",
			"default" => false,
			],
			46 => 
			[
			"id" => 46,
			"name" => "29-1. Un tableau indiquant la proportion de logements de la taille minimale... [Art. R. 431-16-2 du code de l'urbanisme]",
			"default" => false,
			],
			47 => 
			[
			"id" => 47,
			"name" => "34. Un document prévu aux articles R. 171-1 à R. 171-5... [Art. R. 431-18 du code de l'urbanisme]",
			"default" => false,
			],
			48 => 
			[
			"id" => 48,
			"name" => "35. Un document par lequel le demandeur s'engage à installer des dispositifs conformes... [Art. R. 431-18-1 du code de l'urbanisme]",
			"default" => false,
			],
			49 => 
			[
			"id" => 49,
			"name" => "36. La copie de la lettre du préfet... [Art. R. 431-19 du code de l'urbanisme]",
			"default" => false,
			],
			50 => 
			[
			"id" => 50,
			"name" => "37. Une justification du dépôt de la déclaration... [Art. R. 431-20 du code de l'urbanisme]",
			"default" => false,
			],
			51 => 
			[
			"id" => 51,
			"name" => "37-1. Le récépissé de la demande d'enregistrement... [Art. R. 431-16 a) du code de l'urbanisme]",
			"default" => false,
			],
			52 => 
			[
			"id" => 52,
			"name" => "38. Une justification du dépôt de la demande de permis de démolir [Art. R. 431-21 a) du code de l'urbanisme]",
			"default" => false,
			],
			53 => 
			[
			"id" => 53,
			"name" => "39. Les pièces à joindre à une demande de permis de démolir... [Art. R. 431-21 b) du code de l'urbanisme]",
			"default" => false,
			],
			54 => 
			[
			"id" => 54,
			"name" => "40. Le certificat indiquant la surface constructible attribuée à votre lot [Art. R. 442-11 1er al du code de l'urbanisme]",
			"default" => false,
			],
			55 => 
			[
			"id" => 55,
			"name" => "41. Le certificat attestant l'achèvement des équipements desservant le lot [Art. R. 431-22-1 a) du code de l'urbanisme]",
			"default" => false,
			],
			56 => 
			[
			"id" => 56,
			"name" => "41-1. L'attestation de l'accord du lotisseur... [Art. R. 431-22-1 b) du code de l'urbanisme]",
			"default" => false,
			],
			57 => 
			[
			"id" => 57,
			"name" => "42. Une copie des dispositions du cahier des charges de cession de terrain... [Art. R. 431-23 a) du code de l'urbanisme]",
			"default" => false,
			],
			58 => 
			[
			"id" => 58,
			"name" => "43. La convention entre la commune ou l'établissement public... [Art. R. 431-23 b) du code de l'urbanisme]",
			"default" => false,
			],
			59 => 
			[
			"id" => 59,
			"name" => "44. Le plan de division du terrain [Art. R. 431-24 du code de l'urbanisme]",
			"default" => false,
			],
			60 => 
			[
			"id" => 60,
			"name" => "45. Le projet de constitution d'une association syndicale... [Art. R. 431-24 du code de l'urbanisme]",
			"default" => false,
			],
			61 => 
			[
			"id" => 61,
			"name" => "46. Le plan de situation du terrain sur lequel seront réalisées les aires de stationnement... [Art. R. 431-26 a) du code de l'urbanisme]",
			"default" => false,
			],
			62 => 
			[
			"id" => 62,
			"name" => "47. La promesse synallagmatique de concession ou acquisition [Art. R. 431-26 b) du code de l'urbanisme]",
			"default" => false,
			],
			63 => 
			[
			"id" => 63,
			"name" => "48. Une notice précisant la nature du commerce projeté... [Art. R. 431-27-1 du code de l'urbanisme]",
			"default" => false,
			],
			64 => 
			[
			"id" => 64,
			"name" => "49. La copie de la lettre du préfet... [Art. R. 431-28 du code de l'urbanisme]",
			"default" => false,
			],
			65 => 
			[
			"id" => 65,
			"name" => "50. Le récépissé de dépôt en préfecture... [Art. R. 431-29 du code de l'urbanisme]",
			"default" => false,
			],
			66 => 
			[
			"id" => 66,
			"name" => "51. Le dossier spécifique... (accessibilité) [Art. R. 431-30 a) du code de l'urbanisme]",
			"default" => false,
			],
			67 => 
			[
			"id" => 67,
			"name" => "52. Le dossier spécifique... (sécurité) [Art. R. 431-30 b) du code de l'urbanisme]",
			"default" => false,
			],
			68 => 
			[
			"id" => 68,
			"name" => "52-1. Une note précisant la nature des travaux pour lesquels une dérogation est sollicitée... [Art. R. 431-31 du code de l'urbanisme]",
			"default" => false,
			],
			69 => 
			[
			"id" => 69,
			"name" => "52-2. Une note précisant la nature de la ou des dérogations demandées... [Art. R. 431-31-2 du code de l'urbanisme]",
			"default" => false,
			],
			70 => 
			[
			"id" => 70,
			"name" => "53. Une copie du contrat ou de la décision judiciaire... [Art. R. 431-32 du code de l'urbanisme]",
			"default" => false,
			],
			71 => 
			[
			"id" => 71,
			"name" => "54. Une copie du contrat ayant procédé au transfert de possibilité de construction... [Art. R. 431-33 du code de l'urbanisme]",
			"default" => false,
			],
			72 => 
			[
			"id" => 72,
			"name" => "58. Le formulaire de déclaration de la redevance bureaux [Art. A. 520-1 du code de l'urbanisme]",
			"default" => false,
			],
			73 => 
			[
			"id" => 73,
			"name" => "59. La décision prise sur la demande de dérogation à l'obligation de raccordement... [Art. R.431-16 q du code de l'urbanisme]",
			"default" => false,
			],
			74 => 
			[
			"id" => 74,
			"name" => "60. L'attestation prévue à l'article R. 171-35 du code de la construction... [Art. R.441-8-4 du code de l'urbanisme]",
			"default" => false,
			],
			75 => 
			[
			"id" => 75,
			"name" => "61. L'attestation mentionnée à l'article R. 111-25-19 du code de l'urbanisme [Art. R.441-8-4 du code de l'urbanisme]",
			"default" => false,
			],
			76 => 
			[
			"id" => 76,
			"name" => "62. Un document permettant de justifier le respect des critères prévus... [Art. R. 431-27 I du code de l'urbanisme]",
			"default" => false,
			],
			77 => 
			[
			"id" => 77,
			"name" => "63. Un document permettant de justifier que l'installation des serres... [Art. R. 431-27 II du code de l'urbanisme]",
			"default" => false,
			],
			78 => 
			[
			"id" => 78,
			"name" => "64. Un dossier présentant les justifications détaillées... [Art. R. 431-27 III du code de l'urbanisme]",
			"default" => false,
			],
		],
		"PDD" => 
		[
			1 => 
			[
			"id" => 1,
			"name" => "1. Un plan de situation du terrain [Art. R. 451-2 a) du code de l'urbanisme]",
			"default" => true,
			],
			2 => 
			[
			"id" => 2,
			"name" => "2. Un plan de masse des constructions à démolir ou s'il y a lieu à conserver [Art. R. 451-2 b) du code de l'urbanisme]",
			"default" => false,
			],
			3 => 
			[
			"id" => 3,
			"name" => "3. Une photographie du ou des bâtiments à démolir [Art. R. 451-2 c) du code de l'urbanisme]",
			"default" => false,
			],
			4 => 
			[
			"id" => 4,
			"name" => "4. Une notice expliquant les raisons pour lesquelles la conservation du bâtiment ne peut plus être assurée [Art. R. 451-3 a) du code de l'urbanisme]",
			"default" => false,
			],
			5 => 
			[
			"id" => 5,
			"name" => "5. Des photographies des façades et toitures du bâtiment et de ses dispositions intérieures [Art. R. 451-3 b) du code de l'urbanisme]",
			"default" => false,
			],
			6 => 
			[
			"id" => 6,
			"name" => "8. Le descriptif des moyens mis en œuvre pour éviter toute atteinte aux parties conservées du bâtiment [Art. R. 451-3 c) du code de l'urbanisme]",
			"default" => false,
			],
			7 => 
			[
			"id" => 7,
			"name" => "9. Le descriptif des moyens mis en œuvre pour éviter toute atteinte au patrimoine protégé [Art. R. 451-4 du code de l'urbanisme]",
			"default" => false,
			],
			8 => 
			[
			"id" => 8,
			"name" => "10. Le dossier prévu au II de l'article R. 331-19 du code de l'environnement [Art. R. 451-5 du code de l'urbanisme]",
			"default" => false,
			],
			9 => 
			[
			"id" => 9,
			"name" => "11. Le dossier d'évaluation des incidences prévu à l'article R. 414-23 du code de l'environnement [Art. R. 451-6 du code de l'urbanisme]",
			"default" => false,
			],
			10 => 
			[
			"id" => 10,
			"name" => "13. L'étude d'impact ou la décision de dispense d'une telle étude [Art. R. 451-6-1 a) du code de l'urbanisme]",
			"default" => false,
			],
			11 => 
			[
			"id" => 11,
			"name" => "13-1. L'étude d'impact actualisée ainsi que les avis de l'autorité environnementale... [Art. R. 451-6-1 b) du code de l'urbanisme]",
			"default" => false,
			],
		],
		],

	/**
	 * Liste des instructeurs (nom + initiales) (l'ID sera l'abréviation)
	 * 
	 */



	"instructeurs" => [
		"JP" => [
			"name" => "Jean Pierre",
			"abbr" => "JP",
		],
	],

	/**
	 * Liste des responsables
	 * Juste les initiales
	 * 
	 */

	"responsables" => [
		"LL",
	],


	/**
	 * Communes et leurs codes insee (code) / code postaux (zip)
	 */

	"communes" => [
	    [
	        "name" => "Paris",
	        "zip" => "001",
	        "code" => "001"
	    ],
	],

	"zones" => [
		/**
		 * Format zone :
		 * (nom zone) => [
		 * 		"name" => (nom zone),
		 * 		"rules" => (règles, voir ci-dessous)
		 * ],
		 */
		"UA" => [
			"name" => "UA",
			"rules" => [
				/**
				 * REGLES : 
				 * Format [
				 * 		"id" => "(identifiant unique)",
				 * 		"name" => "(nom de la règle)",
				 * 		"unit" => "(unité, ex : mètres, degrés, etc...)",
				 * 		"type" => "(type de contrainte PLUI)",
				 * 		"valeur" => "(valeur)", // "valeur" => [(valeur1), (valeur2), (valeur3)],
				 * ],
				 * 
				 * TYPES DE CONTRAINTES : 
				 * 		Simples (pas de vérification indicative)
				 * 			number (dans ce cas, "valeur" sera juste une indication pour une vérification manuelle par l'instructeur, exemple : voir article L-42 du code civil belge)
				 * 			text (dans ce cas, "valeur" sera juste une indication pour une vérification manuelle par l'instructeur, exemple : voir article L-42 du code civil belge)
				 * 		À un paramètre
				 * 			number_below (doit être inférieur à...)
				 * 			number_above (doit être supérieur à...)
				 * 			number_below_equal (doit être inférieur ou égal à...)
				 * 			number_above_equal (doit être supérieur ou égal à...)
				 * 			text_equal (la valeur doit correspondre exactement à...)
				 * 			boolean ("Oui" / "Non")
				 * 		À deux paramètres
				 * 			number_between (la valeur doit se situer entre X et Y (X et Y inclus))
				 * 			number_between_exclude (la valeur doit se situer entre X et Y (X et Y exclus)))
				 * 			number_exclude_exclude (la valeur ne DOIT PAS être comprise entre X et Y (X et Y exclus))
				 * 			number_exclude_include (la valeur ne DOIT PAS être comprise entre X et Y (X et Y inclus))
				 * 		Multi-paramètres
				 * 			number_select (la valeur doit etre soit X, soit Y, soit Z, ... (nombres))
				 * 			select (la valeur doite être soit A, soit B, soit C, ... (texte))
				 * 			not_number_select (la valeur ne doit être ni X, ni Y, ni Z, ... (nombres))
				 * 			not_select (la valeur ne doit être ni A, ni B, ni C, ... (texte))
				 * 			
				 *
				 */
				[	
					"id" => "1",
					"name" => "Distance max à la voie publique",
					"unit" => "m",
					"type" => "number_equal",
					"value" => "0"
				],
			]
		],
		"UB" => [
			"name" => "UB",
			"rules" => [
				[	
					"id" => "1",
					"name" => "Acces",
					"unit" => "",
					"type" => "text",
					"value" => "Est obligatoire"
				],
			]
		],
	]
];

/**
 * 
 * 
 * FIN DE LA CONFIG
 * 
 * => POUR OLIVIER TODO : Convertisseur a l'entrée des valeurs de check (deg <-> % /// deg <-> RAD /// M <-> cm /// m² <-> cm²)
 * 
 * 
 */

