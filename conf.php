<?php 
$debug = false;

// NE PAS TOUCHER
if($debug){
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
}
//FIN NE PAS TOUCHER


require("config/conf.php");

/**
 * Chiffre ou déchiffre des données en utilisant un simple algorithme XOR.
 * La même fonction est utilisée pour chiffrer et déchiffrer.
 *
 * ATTENTION : NON SÉCURISÉ. Ne pas utiliser pour des données sensibles.
 * Cette méthode est vulnérable à de nombreuses attaques cryptanalytiques.
 * Elle est fournie uniquement pour répondre à la contrainte "ne pas agrandir le texte".
 *
 * @param string $data Les données à traiter (texte clair ou chiffré).
 * @param string $key La clé secrète.
 * @return string Les données résultantes (chiffrées ou déchiffrées).
 */
function xor_cipher($data, $key) {
    $keyLength = strlen($key);
    $dataLength = strlen($data);
    $output = '';

    for ($i = 0; $i < $dataLength; $i++) {
        // Applique l'opération XOR entre le caractère des données
        // et le caractère correspondant de la clé (en boucle).
        $output .= $data[$i] ^ $key[$i % $keyLength];
    }

    return $output;
}
/**
 * Chiffre des données en utilisant AES-256-CBC.
 * C'est une méthode standard et sécurisée.
 * La sortie est encodée en Base64 pour un stockage facile.
 *
 * @param string $plaintext Le texte clair à chiffrer.
 * @param string $key       La clé de chiffrement (doit faire 32 octets pour AES-256).
 * @return string           Les données chiffrées (IV + ciphertext), encodées en Base64.
 * @throws Exception        Si la génération de l'IV échoue ou si la clé est de mauvaise taille.
 */
function encrypt_data($plaintext, $key) {
    $cipherMethod = "AES-256-CBC";
    $keyLength = 32; // AES-256 requiert une clé de 32 octets (256 bits)
/*
    if (strlen($key) !== $keyLength) {
        throw new Exception("La clé de chiffrement doit avoir une longueur de $keyLength octets.");
    }*/

    // 1. Générer un vecteur d'initialisation (IV) unique
    $ivLength = openssl_cipher_iv_length($cipherMethod);
    $iv = openssl_random_pseudo_bytes($ivLength);

    if ($iv === false) {
        throw new Exception("Impossible de générer un IV sécurisé.");
    }

    // 2. Chiffrement
    $ciphertext = openssl_encrypt(
        $plaintext,
        $cipherMethod,
        $key,
        OPENSSL_RAW_DATA, // Important pour obtenir des données brutes
        $iv
    );

    // 3. Combiner l'IV et le texte chiffré, puis encoder en Base64
    // L'IV doit être stocké avec le texte chiffré pour permettre le déchiffrement.
    return base64_encode($iv . $ciphertext);
}

/**
 * Déchiffre des données chiffrées avec AES-256-CBC.
 *
 * @param string $base64EncryptedData Les données chiffrées (Base64) provenant de la fonction encrypt_data.
 * @param string $key                 La clé de chiffrement (doit faire 32 octets pour AES-256).
 * @return string|false              Le texte clair original, ou false en cas d'échec (mauvaise clé, données corrompues).
 * @throws Exception                 Si la clé est de mauvaise taille.
 */
function decrypt_data($base64EncryptedData, $key) {
    $cipherMethod = "AES-256-CBC";
    $keyLength = 8;
/*
    if (strlen($key) !== $keyLength) {
        throw new Exception("La clé de chiffrement doit avoir une longueur de $keyLength octets.");
    }*/

    // 1. Décoder Base64 et extraire l'IV et le texte chiffré
    $data = base64_decode($base64EncryptedData);
    $ivLength = openssl_cipher_iv_length($cipherMethod);
    $iv = substr($data, 0, $ivLength);
    $ciphertext = substr($data, $ivLength);

    // 2. Déchiffrement
    $plaintext = openssl_decrypt(
        $ciphertext,
        $cipherMethod,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $plaintext;
}


function GetTypes(){
	global $CONF;
	$t1s = $CONF['types'];
	$types = [];

	foreach($t1s as $t1){
		if(isset($t1["subt"])){
			if(sizeof($t1["subt"]) == 0){
				array_push($types, $t1);
			}else{
				$types = array_merge($types, $t1['subt']);
			}
		}else{
			$types = array_merge($types, $t1['subt']);
		}
	}
	return $types;
}

function GetTypesAbbr(){
	global $CONF;
	$t1s = $CONF['types'];
	$types = [];

	foreach($t1s as $t1){
		if(isset($t1["subt"])){
			if(sizeof($t1["subt"]) == 0){
				array_push($types, $t1['abbr']);
			}else{
				foreach($t1['subt'] as $sub){
					array_push($types, $sub['abbr']);
				}
			}
		}else{
			foreach($t1['subt'] as $sub){
				array_push($types, $sub['abbr']);
			}
		}
	}
	return $types;
}

function GetDocList($type){
	global $CONF;
	$doclist = [];
	if(isset($CONF['documents'][$type])){
		foreach($CONF['documents'][$type] as $k => $d){
			if($d['default'] == true){
				array_push($doclist, $d);
			}
		}
	}
	return $doclist;
}
function GetRuleList($zone){
	global $CONF;
	return $CONF['zones'][$zone]["rules"];
}
function GetTownFromCode($code){
	global $CONF;
	foreach($CONF['communes'] as $k => $d){
		if($code == $d['code']){
			return $d;
		}
	}
}