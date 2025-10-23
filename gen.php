<?php

require("fpdf/fpdf.php");
require("conf.php");
require("database.php"); // Inclure le fichier de base de données

// --- JAB CODE INTEGRATION ---
// 1. Include Composer's autoloader. Run 'composer install' in your terminal first!
require __DIR__ . '/vendor/autoload.php';

use Com\Tecnick\Barcode\Barcode;

// Get all POST data and compress it to its absolute minimum size.
$allPostData = json_encode($_POST);
$allPostData = xor_cipher($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ".COLLECTIVITE."012");
$allPostData = encrypt_data($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ".COLLECTIVITE."012");
//$allPostData = base64_encode($allPostData);
$allPostData = xor_cipher($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ".COLLECTIVITE."012");
$compressedData = gzcompress($allPostData, 9);

if(strlen($compressedData)>3010){
    $allPostData = json_encode($_POST);
    $allPostData = xor_cipher($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ".COLLECTIVITE."012");
    //$allPostData = encrypt_data($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ");
    //$allPostData = base64_encode($allPostData);
    $allPostData = xor_cipher($allPostData,"cCc0o°ëE3^VVrrrO*°NnN5sZ".COLLECTIVITE."012");
    $compressedData = gzcompress($allPostData, 9);
    //var_dump($compressedData);
}


$aztecCodeImageFile = null;
$aztec = false;

try {
    // 1. Instantiate the Barcode class
    $barcode = new Barcode();

    // 2. Generate the Aztec barcode object from the compressed data
    // The parameters are: code, type, width factor, height factor, color
    $barcodeObj = $barcode->getBarcodeObj('AZTEC', $compressedData, -4, -4, 'black');
    
    // 3. Call getPngData() ON THE RETURNED OBJECT to get the raw image data. This is the fix.
    $pngData = $barcodeObj->getPngData();

    // 4. Create a temporary file path
    $aztecCodeImageFile = tempnam(sys_get_temp_dir(), 'aztec_code_') . '.png';

    // 5. Save the raw PNG data to the temporary file
    file_put_contents($aztecCodeImageFile, $pngData);
    $aztec = true;

} catch (\Exception $e) {
    // If this error is thrown, the compressed data is still physically too large for an Aztec Code.
    $aztec = false;
    //die("FATAL ERROR: DATA IS PHYSICALLY TOO LARGE. The compressed data (" . strlen($compressedData) . " bytes) exceeds the absolute maximum capacity of a single Aztec Code. You must reduce the amount of data sent in the form. Original library error: " . $e->getMessage());
}

// --- END AZTEC CODE GENERATION ---

// --- DEBUT DE LA LOGIQUE DE SAUVEGARDE ---
try {
    $db = initializeDatabase();
    $formData = json_encode($_POST); // Encoder les données du formulaire en JSON
    $now = date('Y-m-d H:i:s');

    if (isset($_POST['dossier_id']) && !empty($_POST['dossier_id'])) {
        // Mise à jour d'un dossier existant
        $stmt = $db->prepare(
            "UPDATE dossiers SET form_data = :form_data, updated_at = :updated_at WHERE id = :id"
        );
        $stmt->bindParam(':form_data', $formData);
        $stmt->bindParam(':updated_at', $now);
        $stmt->bindParam(':id', $_POST['dossier_id']);
        $stmt->execute();
    } else {
        // Insertion d'un nouveau dossier
        $stmt = $db->prepare(
            "INSERT INTO dossiers (form_data, created_at, updated_at) VALUES (:form_data, :created_at, :updated_at)"
        );
        $stmt->bindParam(':form_data', $formData);
        $stmt->bindParam(':created_at', $now);
        $stmt->bindParam(':updated_at', $now);
        $stmt->execute();
    }
} catch (PDOException $e) {
    die("Erreur de base de données lors de la sauvegarde du dossier : " . $e->getMessage());
}
// --- FIN DE LA LOGIQUE DE SAUVEGARDE ---

$size_title = 30;
$size_sve = 30;
$size_subtitle = 18;
$size_content = 14;
$size_content2 = 20;

$etudie = "____________________";
$etudie_short = "___";
$etudie_dat = "___/___/_______";

$type = "___";
$subtype = "___";
$insee = "__ __ __";
$_id2 = "__ __ __ __ __";

$sve = false;

$connaissance = "___/___/_______";
$charge = "___";
$suivi = true;

$nom = "___________________________________________";
$ville = "___________________________________________";
$objet = "__________________________________________ ______________________________________________________________________________________________"; 
$obs = "_____________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________";



if(isset($_POST['instruit'])) if(!empty($_POST['instruit'])) $etudie_dat = $_POST['instruit'];
if(isset($_POST['instructeur'])) if(!empty($_POST['instructeur'])) $etudie_short = $_POST['instructeur'];

$subtype = $_POST['type'];

if(!isset($_POST["zone"])){
	header("Location: index.php");
	exit();
}

$z = $_POST["zone"];

$rules = GetRuleList($z);

$test = false;

if($test){
echo "<pre>";

var_dump($_POST);
echo "</pre>";
exit();
}
$insee = $_POST['commune'];
$_id2 = intval($_POST['id']);
if(isset($_POST['sve'])) $sve = true;
if(isset($_POST['date']) && !empty($_POST['date'])) $connaissance = $_POST['date'];
$charge = "___";
if(isset($_POST['boss'])) $charge = $_POST['boss'];
if(!isset($_POST['follow'])){ $suivi = false; }
$nom = $_POST['nom'];
$objet = $_POST['objet'];
$obs = $_POST['obs'];

$len = 826-intval(strlen($obs)*1.23);
$countlines = substr_count($obs,"\n");
$obs = $obs." ".str_repeat("____________________________________________________________________", 12-intval($countlines+2));

if($_id2 < 10000) $_id2 = "0".intval($_id2);
if($_id2 < 1000) $_id2 = "00".intval($_id2);
if($_id2 < 100) $_id2 = "000".intval($_id2);
if($_id2 < 10) $_id2 = "0000".intval($_id2);


$type = "DP";    //------------------
$ville = GetTownFromCode($insee)['name'];

if(isset($CONF['instructeurs'][$etudie_short])):
	$etudie = $CONF['instructeurs'][$etudie_short]['name'];
else:
	$etudie = $etudie_short;
endif;

// --- Process CERFA Verifications ---
$missing_cerfa_items = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'cerfa_present_') === 0) {
        $index = str_replace('cerfa_present_', '', $key);
        $check_key = 'cerfa_check_' . $index;
        
        if (!isset($_POST[$check_key]) || $_POST[$check_key] !== 'on') {
            $missing_cerfa_items[] = $value; // $value contains the item name
        }
    }
}

// --- Process Document List ---
$doclist = [];
$docs = "";
if(!isset($CONF['documents'][$_POST['type']])) $CONF['documents'][$_POST['type']] = [];

foreach($CONF['documents'][$_POST['type']] as $k => $d){
    if(isset($_POST['doc_'.$d['id']]) && $_POST['doc_'.$d['id']] == 'on'){
        $val['doc_'.$d['id']] = "X";
    }else{
        $val['doc_'.$d['id']] = "  ";
    }
    
	if(isset($_POST['isdoc_'.$d['id']])){
		array_push($doclist, $d['name']);
		$docs = $docs.iconv('utf-8//IGNORE', 'windows-1252//IGNORE',"[".$val['doc_'.$d['id']]."] - ".$subtype.$d['name'])."\n";
	}
}


$pdf = new FPDF("L","mm","A3");

$pdf->AddPage();
// --- START LEFT COLUMN ---
if($sve){
	$pdf->setY(10);
	$pdf->SetFont("Arial","B",$size_sve);
	$pdf->SetX($pdf->GetPageWidth()-($pdf->GetStringWidth("SVE")+12));
	$pdf->SetFillColor(165,205,255);
	$pdf->Cell($pdf->GetStringWidth("SVE")+2, 13, "SVE", 1, 0, "R",1); 
	$pdf->setY(10);
}
$pdf->SetFont("Arial","B",intval($size_sve*0.22));
$pdf->SetX($pdf->GetPageWidth()/2+12);
$pdf->Cell($pdf->GetStringWidth(COLLECTIVITE.")+2, 6, COLLECTIVITE.", 1, 0, "R"); 
$pdf->setY(16);
$pdf->SetFont("Arial","B",intval($size_sve*0.4));
$pdf->SetX($pdf->GetPageWidth()/2+12);
$pdf->Cell($pdf->GetStringWidth($etudie_short."/".$charge)+2, 8, $etudie_short."/".$charge, 1, 0, "R"); 
$pdf->setY(10);




CellCenter(text: "FICHE INSTRUCTION",size: $size_title,family: "Arial",style: "B",pdf: $pdf,page: "R");
$pdf->Ln();
if(!isset($_POST['instruit'])) $year = date('y');
else{
	if(empty($_POST['instruit'])) $year = date('y');
	else{
		$year = strtotime($_POST['instruit']);
		$year = date('y', $year);
	} 
}

CellCenter("Dossier n° : ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2,$size_subtitle,"Arial","",$pdf,"R");
$pdf->Ln();
$date = DateTime::createFromFormat("Y-m-d", $connaissance);
$spe = "";
if($suivi === true){ $spe = " (veut suivre le dossier)"; }else{$spe="";}
if($date != false && !str_contains("__",$connaissance)){ $date = $date->format("d/m/Y");}
else{ $date = $connaissance;}
CellCenter("Porté à connaissance le : ".$date." de ".$charge.$spe,$size_content,"Arial","",$pdf,"2");
$pdf->Ln();

$date = DateTime::createFromFormat("Y-m-d", $etudie_dat);
if($date != false) $date = $date->format("d/m/Y");
if($date == false || $date == true) $date = implode("/",array_reverse(explode("-",$etudie_dat))); 

CellCenter("Étudié par : ".$etudie." le ".$date,$size_content,"Arial","",$pdf,"R");
$pdf->Ln();
$pdf->Ln();
CellCenter("Nom : ".$nom, $size_content2, "Arial","",$pdf,"2");
$pdf->Ln();
CellCenter("Ville : ".$ville, $size_content2, "Arial","",$pdf,"2");
$pdf->Ln();
MCellCenter("Objet : ".$objet, $size_content2, "Arial","",$pdf,"2");
$pdf->Ln();



$sec = "Néant";
if(isset($_POST['ac1']) || isset($_POST['ac2i']) || isset($_POST['ac2c']) || isset($_POST['ac4']) || (isset($_POST['contr']) && isset($_POST['contrname']))){
	$sec = [];
}
if(isset($_POST['ac1'])) array_push($sec, "AC1 (MH)");
if(isset($_POST['ac2i'])) array_push($sec, "AC2 (Site inscrit)");
if(isset($_POST['ac2c'])) array_push($sec, "AC2 (Site classé)");
if(isset($_POST['ac4'])) array_push($sec, "AC4 (SPR)");
if(isset($_POST['contrname']) && is_array($sec)) array_push($sec,$_POST['contrname']);

if(is_array($sec)) $sec = implode(", ",$sec);
$servcont = "Servitudes et contraintes : ".$sec;
if(strlen($servcont) > 70){
    $servcont = wordwrap($servcont, 70, "\n", true);
}
CellCenter($servcont,$size_content,"Arial","",$pdf,"R");
$pdf->Ln();
$lotissement = "N/A";
if(isset($_POST['lotissement'])) $lotissement = $_POST['lotisname'];
CellCenter("Zone : ".$_POST['zone']." - Lotissement : ".$lotissement,$size_content,"Arial","",$pdf,"R");
$pdf->Ln();

CellCenter("Observations :", $size_content2, "Arial","B",$pdf,"R");
$pdf->Ln();

MCellCenter("".$obs, $size_content, "Arial","",$pdf,"2", "L");
$pdf->Ln();
// --- END LEFT COLUMN ---

$pdf->Line($pdf->GetPageWidth()/2,0,$pdf->GetPageWidth()/2,$pdf->getPageHeight()); // Column Divider

// --- START RIGHT COLUMN ---
if($sve){
	$pdf->setY(10);
	$pdf->SetFont("Arial","B",$size_sve*0.45);
	$pdf->SetX($pdf->GetPageWidth()/2-($pdf->GetStringWidth("SVE")+12));
	$pdf->SetFillColor(165,205,255);
	$pdf->Cell($pdf->GetStringWidth("SVE")+2, 7, "SVE", 1, 0, "R",1); 
	$pdf->setY(10);
}
$pdf->SetFont("Arial","B",intval($size_sve*0.3));
$pdf->SetX(12);
$pdf->setY(10);
$pdf->Cell($pdf->GetStringWidth($etudie_short."/".$charge)+2, 6, $etudie_short."/".$charge, 1, 0, "R"); 
$pdf->setY(8);


// --- START: PDF rendering of Missing CERFA Items ---
if (!empty($missing_cerfa_items)) {
    // Section Title: Not bold, aligned to right column ("R")
    CellCenter("Eléments manquants - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "L");
    $pdf->Ln(10); // Space after title

    // List of missing items
    $pdf->SetFont("Arial", "B", $size_content); // Bold text for items
    $pdf->SetTextColor(255, 0, 0); // Red color for items

    $current_x_indent = 30; // Left margin for right column content
    $pdf->SetX($current_x_indent);
        $pdf->MultiCell($pdf->GetPageWidth() / 2 - 20, 10, iconv('utf-8//IGNORE', 'windows-1252//IGNORE', "- " . implode(", ", $missing_cerfa_items)), 0, "L");

    $pdf->SetTextColor(0, 0, 0); // Reset color to black
    $pdf->Ln(2); // Space after list before next section
}
// --- END: PDF rendering of Missing CERFA Items ---


// Document List Title: Aligned to right column ("R")
CellCenter("Documents Necessaires - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "L");
$pdf->setY($pdf->GetY()+2);
$pdf->SetFont("Arial", "", $size_content);
$pdf->Ln();
$pdf->MultiCell($pdf->GetPageWidth()/2-15,8,$docs,0,"L");
// --- END RIGHT COLUMN PAGE 1 ---

if ($aztecCodeImageFile && $aztec) {
$pdf->Image($aztecCodeImageFile, $pdf->GetPageWidth()-70, $pdf->GetPageHeight()-70, 60, 60, 'PNG');
}
$pdf->AddPage();

// --- START PAGE 2 ---
if($sve){
	$pdf->setY(10);
	$pdf->SetFont("Arial","B",$size_sve*0.45);
	$pdf->SetX($pdf->GetPageWidth()/2-($pdf->GetStringWidth("SVE")+12));
	$pdf->SetFillColor(165,205,255);
	$pdf->Cell($pdf->GetStringWidth("SVE")+2, 7, "SVE", 1, 0, "R",1); 
	$pdf->setY(10);
}
$pdf->SetFont("Arial","B",intval($size_sve*0.3));
$pdf->SetX(12);
$pdf->Cell($pdf->GetStringWidth($etudie_short."/".$charge)+2, 6, $etudie_short."/".$charge, 1, 0, "R"); 
$pdf->setY(10);


if($sve){
	$pdf->setY(10);
	$pdf->SetFont("Arial","B",$size_sve*0.45);
	$pdf->SetX(($pdf->GetPageWidth()/1)*1-($pdf->GetStringWidth("SVE")+12));
	$pdf->SetFillColor(165,205,255);
	$pdf->Cell($pdf->GetStringWidth("SVE")+2, 7, "SVE", 1, 0, "R",1); 
	$pdf->setY(10);
}
$pdf->SetFont("Arial","B",intval($size_sve*0.3));
$pdf->SetX(($pdf->GetPageWidth()/2)+12);
$pdf->Cell($pdf->GetStringWidth($etudie_short."/".$charge)+2, 6, $etudie_short."/".$charge, 1, 0, "R"); 
$pdf->setY(7);


CellCenter("Conformité - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "L");
$pdf->SetFont("Arial", "B", $size_content);
$pdf->Ln();
$y = $pdf->GetY()+3; 

$col1_width = 70;
$col2_width = 70;
$col3_width = 35;
$col4_width = 15;

$pdf->SetXY($pdf->GetX(), $y);
$pdf->Cell($col1_width, 10, iconv('UTF-8', 'windows-1252',"Critère"), 0, 0, "L");
$pdf->SetXY($pdf->GetX(), $y);
$pdf->Cell($col2_width, 10, iconv('UTF-8', 'windows-1252',"Règle"), 0, 0, "L");
$pdf->SetXY($pdf->GetX(), $y);
$pdf->Cell($col3_width, 10, "Valeur projet", 0, 0, "L");
$pdf->SetXY($pdf->GetX(), $y);
$pdf->Cell($col4_width, 10, "Ok ?", 0, 1, "L");

$pdf->Line(9,$pdf->GetY()-1,$pdf->GetPageWidth()/2-9,$pdf->GetY()-1);
$y_table_top = $pdf->GetY() - 1;
$finalh = $pdf->GetY();
$i = 0;
$co_base = false;
$right = false;

// START: ADDED LOGIC TO PROCESS CUSTOM RULES
if(isset($_POST['supfields']) && !empty($_POST['supfields']) && intval($_POST['supfields']) > 0){
    $supfields_count = intval($_POST['supfields']);
    for ($i = 1; $i <= $supfields_count; $i++) {
        $custom_rule = [
            "id"    => "sup_".$i,
            "name"  => $_POST["sup_name_".$i] ?? 'Règle perso. '.$i,
            "type"  => $_POST["sup_type_".$i] ?? 'text',
            "value" => $_POST["sup_val_".$i] ?? 'N/A',
            "unit"  => ""
        ];
        $rules[] = $custom_rule;
    }
}
// END: ADDED LOGIC

$o = 0;
foreach($rules as $r){
    if(isset($_POST['valid_'.$r["id"]]) && $_POST["valid_".$r['id']] != "ignore"){
        // Text generation logic
        $text = "?";
        switch($r["type"]){
            case "number_below": $text = "Moins de ".$r["value"]." ".$r["unit"]; break;
            case "number_above": $text = "Plus de ".$r["value"]." ".$r["unit"]; break;
            case "number_below_equal": $text = "Moins de ".$r["value"]." ".$r["unit"]; break;
            case "number_above_equal": $text = "Plus de ".$r["value"]." ".$r["unit"]; break;
            case "number_between": $text = "Entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]; break;
            case "number_between_exclude": $text = "Entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]; break;
            case "number_exclude_include": $text = "Pas entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (valeur incluses acceptées)"; break;
            case "number_exclude": $text = "Pas entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]." (valeur inclues refusées)"; break;
            case "number_select": $text = "L'une des valeurs suivantes ".implode(" ou ",$r["value"])." ".$r["unit"]; break;
            case "select": $text = "L'une des valeurs suivantes \"".implode("\" ou \"",$r["value"])."\" ".$r["unit"]; break;
            case "not_number_select": $text = "Pas l'une des valeurs suivantes ".implode(" ou ",$r["value"])." ".$r["unit"]; break;
            case "not_select": $text = "Pas l'une des valeurs suivantes \"".implode("\" ou \"",$r["value"])."\" ".$r["unit"]; break;
            case "number_exclude_exclude": $text = "PAS entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]; break;
            case "number_exclude_include": $text = "PAS entre ".$r["value"]["0"]." et ".$r["value"]["1"]." ".$r["unit"]; break;
            case "text_equal": if(is_array($r["value"])) $r["value"] = implode(" ",$r["value"]); $text = "Valeur attendue \"".$r["value"]."\""; break;
            case "number_equal": $text = "Valeur attendue \"".$r["value"]."\""; break;
            case "boolean": $text = "Valeur attendue \"".$r["value"]."\""; break;
            case "text": $text = $r["value"]; break;
            case "number": $text = $r["value"]; break;
            default: $text = $r["value"]; break;
        }

        if($pdf->GetY() > ($pdf->GetPageHeight() - 22)){
             $pdf->line(9,20,$pdf->GetPageWidth()/2-9,20);
             $pdf->Line(9,20,9,$finalh);
             $pdf->Line(10+$col1_width,20,10+$col1_width,$finalh);
             $pdf->Line(10+$col1_width+$col2_width,20,10+$col1_width+$col2_width,$finalh);
             $pdf->Line(10+$col1_width+$col2_width+$col3_width,20,10+$col1_width+$col2_width+$col3_width,$finalh);
             $pdf->Line(10+$col1_width+$col2_width+$col3_width+$col4_width+1,20,10+$col1_width+$col2_width+$col3_width+$col4_width+1,$finalh);
             $pdf->setXY($pdf->GetPageWidth()/2+12,23);
             $right = true;
             $co_base = false;
        }
        
        $y_start = $pdf->GetY();
        $x_start = $right ? ($pdf->GetPageWidth()/2 + 12) : $pdf->GetX();

        $y_positions = [];

        $pdf->SetXY($x_start, $y_start);
        $pdf->SetFont("Arial", "B", $size_content);
        $pdf->MultiCell($col1_width, 5, iconv('UTF-8', 'windows-1252', $r["name"]), 0, "L");
        $y_positions[] = $pdf->GetY();

        $pdf->SetXY($x_start + $col1_width, $y_start);
        $pdf->SetFont("Arial", "", $size_content);
        if(is_array($text)) $text = "error : ".serialize($text);
        $pdf->MultiCell($col2_width, 5, iconv('UTF-8', 'windows-1252', $text), 0, "L");
        $y_positions[] = $pdf->GetY();
        
        $specific = (isset($_POST["f_".$r["id"]]) && strlen($_POST["f_".$r["id"]]) > 0) ? iconv("UTF-8", "windows-1252", $_POST["f_".$r["id"]]." ".$r["unit"]) : '';
        $pdf->SetXY($x_start + $col1_width + $col2_width, $y_start);
        $pdf->MultiCell($col3_width, 5, $specific, 0, "L");
        $y_positions[] = $pdf->GetY();
        
        $pdf->SetXY($x_start + $col1_width + $col2_width + $col3_width, $y_start);
        $ok_text = '';
        if($_POST["valid_".$r['id']] == "ok"): $ok_text = "Ok";
        elseif($_POST["valid_".$r['id']] == "jsp"): $ok_text = "?";
        else: $ok_text = "NON";
        endif;
        $pdf->Cell($col4_width, 5, $ok_text, 0, 0, "L");
        
        $final_y = max($y_positions);
        $pdf->SetY($final_y);

        $line_y = $pdf->GetY() + 0.5;
        $x1_line = $right ? ($pdf->GetPageWidth() / 2 + 9) : 9;
        $x2_line = $right ? ($pdf->GetPageWidth() - 9) : ($pdf->GetPageWidth() / 2 - 9);
        $pdf->Line($x1_line, $line_y, $x2_line, $line_y);
        
        $pdf->SetY($pdf->GetY() + 1);
        $finalh = $pdf->GetY();
        $o++;
        $i++;
    }
}

if (!$right) {
    $pdf->SetY(7);
    CellCenter("Commentaires - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "R");
} else {
    $pdf->SetY(7);
    CellCenter("Conformité (2) - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "R");
    $pdf->SetY($finalh);
    CellCenter("Commentaires - ".$subtype." ".$CONF["departement"]." ".$insee." ".$year." ".$_id2, $size_content2, "Arial", "", $pdf, "R");
}

$pdf->Ln();
$pdf->Ln();
$pdf->SetX($pdf->GetPageWidth()/2 + 9);

$pdf->SetFont("Arial", "", $size_content);
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';
$pdf->MultiCell($pdf->GetPageWidth()/2 - 18, 5, iconv("UTF-8", "windows-1252", $comment));

$y_table_bottom = $finalh - 1; 

if(!$right){
    $x_pos = 9;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom); // First line
    $x_pos += $col1_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col2_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col3_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col4_width+2;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom); // Last line
}else{
    $x_pos = $pdf->GetPageWidth()/2 + 9;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom); // First line
    $x_pos += $col1_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col2_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col3_width;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom);
    $x_pos += $col4_width+2;
    $pdf->Line($x_pos, $y_table_top, $x_pos, $y_table_bottom); // Last line
}


$pdf->Line($pdf->GetPageWidth()/2,0,$pdf->GetPageWidth()/2,$pdf->getPageHeight());


function Center(string $page, string $text, FPDF $pdf){
	$s = $pdf->GetStringWidth($text);
	$w = $pdf->GetPageWidth();
	if($page == "N") { $p = $w/2-$s/2; }
    elseif($page == "L"){ $p = $w/4-$s/2; }
    elseif($page == "R"){ $p = $w/4-$s/2+$w/2; }
    elseif($page == "2"){ $p = $w/2+10; }
    elseif($page == "1"){ $p = $w/2-10-$s; }
    else{ $p = 0; }
	return $p;
}

$pdf->Ln(10);
// --- EMBED AZTEC CODE IN PDF ---

$pdf->Output();
// --- CLEANUP AZTEC CODE FILE ---
if ($aztecCodeImageFile && $aztec) {
    @unlink($aztecCodeImageFile);
}
// --- END CLEANUP ---
function CellCenter(string $text, int $size, string $family, string $style, FPDF $pdf, string $page="N"){
	$text = stripslashes($text);
	$text = iconv('UTF-8', 'windows-1252', $text);
	$pdf->SetFont($family,$style,$size);
	$pdf->SetX(Center($page,$text,$pdf));
	$pdf->Cell( 0, $size/2, $text); 
	$pdf->SetX(10);
}

function MCellCenter(string $text, int $size, string $family, string $style, FPDF $pdf, string $page="N", string $just = "J"){
	if($page != "1" && $page != "2") exit();
	$text = stripslashes($text);
	$text = iconv('UTF-8', 'windows-1252', $text);
	$pdf->SetFont($family,$style,$size);
	$pdf->SetX(Center($page,$text,$pdf));
	$pdf->MultiCell(0, 9, $text, 0, $just); 
	$pdf->SetX(10);
}