<?php
function num_format($numVal,$afterPoint=3,$minAfterPoint=0,$thousandSep=",",$decPoint="."){ // Copy and Paste entfernt Nullen
	$ret = number_format($numVal,$afterPoint,$decPoint,$thousandSep);
	if($afterPoint!=$minAfterPoint){
		while(($afterPoint>$minAfterPoint) && (substr($ret,-1) =="0") ){
		$ret = substr($ret,0,-1);
		$afterPoint = $afterPoint-1;}}
	if(substr($ret,-1)==$decPoint) {$ret = substr($ret,0,-1);}
	return $ret;}

// Formulardaten einlesen. Kontrollächstchen (Checkboxen) werden direkt bei der Linieneichnung verwendet
if(isset($_POST['anzahl']))$anzahl = $_POST['anzahl']; // 43200 Datensätze sind 1 Tag. Zeitdifferenz zwischen den Datensätzen beträgt 2 Sekunden
else	$anzahl = 0;
if(isset($_POST['anzahlT']))$anzahlT = $_POST['anzahlT'];
else	$anzahlT = 43200;
if(isset($_POST['backtime']))$backTime = $_POST['backtime']*43200;  // zur zurückliegenden Datenanzeige
else	$backTime = 0;
if(isset($_POST['backtimeH']))$backTimeH = $_POST['backtimeH'];  // zur zurückliegenden Datenanzeige
else	$backTimeH = 0;
if(isset($_POST['skalierung']))$skalierung = $_POST['skalierung']; // Zur Graphen- und Y-Achsenspreizung
else $skalierung = 1;
$anzahl		= $anzahl + $anzahlT;
$backTime	= $backTime + $backTimeH;
// Variablendeklaration
$bildHoehe	= 600;
$bildBreite	= 1400;
$skalenBreite	= 500; // Y-Achsenbeschriftung reservieren
$xdivider	= 10; // Anzahl der Linien auf der X-Achse
$ydivider	= 10; // Anzahl der Linien auf der Y-Achse
$werteAnzahl	= $anzahl; // Variablen um die Anzahl der  Werte auf die Anzahl der Pixel in X-Richtung zu mitteln
$werteProPixel	= $anzahl / ($bildBreite-$skalenBreite);
$bild		= imagecreate($bildBreite,$bildHoehe);
// Bild- und Farbendefinition
$grau		= imagecolorallocate($bild,192,192,192);
$dgrau		= imagecolorallocate($bild,128,128,128);
$blau		= imagecolorallocate($bild,0,0,255);
$hblau		= imagecolorallocate($bild,0,128,128);
$rot		= imagecolorallocate($bild,255,0,0);
$orange		= imagecolorallocate($bild,255,96,0);
$gelb		= imagecolorallocate($bild,255,255,0);
$gruen		= imagecolorallocate($bild,0,255,0);
$lila		= imagecolorallocate($bild,255,0,255);
$schwarz	= imagecolorallocate($bild,0,0,0);
imagefill($bild,0,0,$grau);
// Überprüfen ob zwei AD-Wandler abgefragt werden sollen
$datei		= fopen("messung.conf","r");
$ergebnis	= explode(",",fgets($datei));
$ergebnis1	= explode(",",fgets($datei));
$vcc0		= $ergebnis1[0];
$vcc1		= $ergebnis1[1];
fclose($datei);
if(sizeof($ergebnis)>1)$zweiArrays = 1;
else $zweiArrays = 0;

// Graphenarray [ mbu, mbo, einheit, sbu, sbo sens, farbe, cal.offst, cal.faktor]
//$sensorarray [datenbak_id, VCC_des_ADC, graphenarrays,..]
$farben0	= array(0=>$blau, 1=>$rot, 2=>$hblau, 3=>$orange);
$farben1	= array(0=>$schwarz, 1=>$lila, 2=>$gruen, 3=>$gelb);
$sensors0	= array("ads1115_0.db",$vcc0);
if($zweiArrays == 1){
	$sensors1	= array("ads1115_1.db",$vcc1);
}
$datei		= fopen("sensors0.conf","r");
$z		= 0;
while(!feof($datei)){
	$tmp		= array();
	$line           = fgets($datei);
	for($y = 0; $y < 6; $y++)$tmp[$y] = str_replace("\n","",explode(",",$line)[$y]);
	if( $z < 4){
		$tmp[6] = $farben0[$z];
		$tmp[7] = str_replace("\n","",explode(",",$line)[6]);
		$tmp[8] = str_replace("\n","",explode(",",$line)[7]);
		$sensors0[($z+2)] = $tmp;}
	$z = $z + 1;}
fclose($datei);
if($zweiArrays == 1){
	$datei		= fopen("sensors1.conf","r");
	$z		= 0;
	while(!feof($datei)){
		$tmp		= array();
		$line           = fgets($datei);
		for($y = 0; $y < 6; $y++)$tmp[$y] = str_replace("\n","",explode(",",$line)[$y]);
		if( $z < 4){
			$tmp[6] = $farben1[$z];
			$tmp[7] = str_replace("\n","",explode(",",$line)[6]);
			$tmp[8] = str_replace("\n","",explode(",",$line)[7]);
			$sensors1[($z+2)] = $tmp;}
		$z = $z + 1;}
	fclose($datei);
}
// Y-Achsenbeschriftung
$skalen		= array();
$farbenProSkala	= array();
for($z = 2; $z < 6; $z++){ // Messbereiche Skalieren und in $skalen speichern zugehörige Farbwerte indexgleich in $farbenProSkala speichern
	$mbu0	= $sensors0[$z][0]; // Unterer Messbereich
	$mbo0	= $sensors0[$z][1]; // Oberer Messbereich
	$skmbu0	= ($mbo0 + $mbu0)/2 - ($mbo0 - $mbu0)/(2 * $skalierung); // Skalierter unterer Messbereich
	$skmbo0	= ($mbo0 + $mbu0)/2 + ($mbo0 - $mbu0)/(2 * $skalierung); // Skalierter oberer Messbereich
	$tmp0	= array(array($skmbu0,$skmbo0),$sensors0[$z][2]); // Skalierten Messbereich und Einheit in array speichern
	if(! in_array($tmp0,$skalen)){ // Überprüfung ob Messbereich und Einheit nicht schon vorhanden sind
		$skalen[count($skalen)] = $tmp0; // Wenn nicht schon vorhanden in $skalen speichern
		$farbenProSkala[count($farbenProSkala)] = array($sensors0[$z][6]); // und neues Array mit Farbe des Graphen speichern
	}else{ // Wenn Messbereich und Einheit schon vorhanden ist ...
		$index = array_search($tmp0,$skalen); // ... Index des Messbereichts ermitteln
		$farbenProSkala[$index][count($farbenProSkala[$index])] = $sensors0[$z][6];} // und Farbe Farbe indexgleich Speichern
	// Das gleiche nochmal mit den Sensordaten des zweiten ADC's
	if($zweiArrays == 1){
		$mbu0	= $sensors1[$z][0];
		$mbo0	= $sensors1[$z][1];
		$skmbu0	= ($mbo0 + $mbu0)/2 - ($mbo0 - $mbu0)/(2 * $skalierung);
		$skmbo0	= ($mbo0 + $mbu0)/2 + ($mbo0 - $mbu0)/(2 * $skalierung);
		$tmp0	= array(array($skmbu0,$skmbo0),$sensors1[$z][2]);
		if(! in_array($tmp0,$skalen)){
			$skalen[count($skalen)] = $tmp0;
			$farbenProSkala[count($farbenProSkala)] = array($sensors1[$z][6]);
		}else{
			$index = array_search($tmp0,$skalen);
			$farbenProSkala[$index][count($farbenProSkala[$index])] = $sensors1[$z][6];}
	}
}
$neubreite = 500;
$yh	= intdiv($bildHoehe, $ydivider); // Anzahl der Pixel zwischen den Linien
for($z = 0; $z <= $ydivider; $z++){ // Werte pro Y-Achse berechnen und zugehörige Farbwerte speichern
	$text	= ""; // Beschriftung der Linie
	$count	= 0;
	$farben	= array();
	foreach($skalen as $skala){ // Skalen berechnen
		$farbentmp=array();
		foreach($farbenProSkala[$count] as $color){
			$farbentmp[count($farbentmp)] = $color;
		}
		$xpos 			= imagefontwidth(2) * strlen($text); // Position des Farbkästchen bestimmen
		$farben[count($farben)] = array($farbentmp,$xpos); // Farben mit Position speichern
		$schritt		= ($skala[0][1] - $skala[0][0]) / $ydivider; // Schrittweite der Skala pro Y-Achsenlinie
		if($count > 0)$text 	= $text." | ".num_format($skala[0][0] + $z * $schritt,3,1).$skala[1]; // Text pro Linie
		else $text		= $text.num_format($skala[0][0] + $z * $schritt,3,1).$skala[1]; //Erste Skala ohne "|"
		$count++;}
	$offset = 13; // Damit der Text nicht direkt auf er Linie sitzt
	if($z == $ydivider)$offset = 0; // Obere beschriftung brauch keinen offset
	$xpos	= 495 - imagefontwidth(2) * strlen($text); // X-Position des Textes
	if($xpos < $neubreite) $neubreite = $xpos;
	$ypos	= $bildHoehe - $z * $yh - $offset; // Y-Position des Textes
	imagestring($bild, 2, $xpos, $ypos, $text, $dgrau); // Beschriftung zeichnen
	if($z == 0){ // Farbkästchen der Skalen nur auf der untersten Linie setzten
		$scalecount = 0; // Skalenzähler
		foreach($farben as $farbe){
			$farbcount = 0; // Farbkästchenzähler
			foreach($farbe[0] as $color){
				if($scalecount == 0)$xcord = $xpos + $farbcount * 8 + $farbe[1]; // Erste Skala ohne X-Offset
				else $xcord = $xpos + $farbcount * 8 + $farbe[1]+3*imagefontwidth(2); // Zweite Skala mit X-Offset
				$ycord = $ypos - (imagefontheight(2)-4); // Y-Position der Farbkästchen
				imagefilledrectangle($bild,$xcord,$ycord,$xcord+8,$ycord+8,$color); // Farbkästchen zeichnen
				$farbcount++;}
			$scalecount++;}}}
for($z = 1; $z < $ydivider; $z++) imageline($bild, 0, $yh * $z, $bildBreite, $yh * $z, $dgrau); // Y-Achsenlininen zeichnen

// X-Achsenbeschriftung
$xb		= intdiv($bildBreite-$skalenBreite, $xdivider); // Anzahl der Pixel zwischen den Linien
for($z = 0; $z < $xdivider; $z++) imageline($bild, $skalenBreite+$xb * $z, 0, $skalenBreite+$xb * $z, $bildHoehe, $dgrau); // Linien zeichnen
$backtage	= (intdiv($backTime * 2,86400));
$backstunden	= intdiv((($backTime * 2) % 86400),3600);
$backminuten	= intdiv((($backTime * 2) % 3600),60);
for($z = 1; $z <= $xdivider; $z++){
	$tage		= intdiv(($werteAnzahl / ($xdivider / 2)) * $z ,86400) + $backtage;
	$stunden	= intdiv(((($werteAnzahl / ($xdivider / 2)) * $z) % 86400),3600) + $backstunden;
	$minuten	= intdiv(((($werteAnzahl / ($xdivider / 2)) * $z) % 3600),60) + $backminuten;
	$sekunden	= intdiv(((($werteAnzahl / ($xdivider / 2)) * $z) % 60),1);
	$text		="-";
	if($minuten >= 60){$minuten = $minuten - 60; $stunden = $stunden + 1;}
	if($stunden >= 24){$stunden = $stunden - 24; $tage = $tage + 1;}
	if($tage>0)	$text = $text.$tage."T";
	if($stunden>0)	$text = $text.$stunden."h";
	if($minuten>0)	$text = $text.$minuten."m";
	if($sekunden>0)	$text = $text.$sekunden."s";
	imagestring($bild,2,(($xdivider - $z) * $xb)+$skalenBreite,$bildHoehe-imagefontheight(2),$text,$dgrau);} // Beschriftung zeichnen

// Graphen zeichnen
if($zweiArrays == 1)$adcs = array($sensors0,$sensors1);
else $adcs = array($sensors0);
$adccount = 0;
foreach($adcs as $adc){
	$db	= new SQLite3($adc[0]); // ID - des aktuellesten Datensatzes ermittel
	$sql	="SELECT MAX(ID) AS max_ID FROM WERTE";
	try{
		$result	= $db->query($sql);}
	catch(Exception $error){ die("Datenbank in Benutzung");}
	if ($result){
		if($row = $result->fetchArray(SQLITE3_ASSOC)){
			$minID = $row["max_ID"]-($werteAnzahl+$backTime);}} // ID des jüngsten abzufragenden  Datensatzes
	$x		= 0; // Zählvariable der Whileschlife zur Mittelwertbildung
	$xzaehler	= 0; // Zählvariable zum Datensatzzählen der Mittelwertbildung in der Whileschlife
	$yfaktor	= $bildHoehe/$adc[1]; // zur Skalierung der Werte auf die Bildhöhe
	$werte		= array(); // Aktuelle Y-werte der Pixel
	$vorWerte	= array(); // Y-Werte der Vorgängerpixel zur Linienzeichnung von Pixel zu Pixel
	$dWerte 	= array(); // Mittelwerte zur Anzeige der letzten Werte der Graphen
	$summen		= array(0,0,0,0); // Summenwerte pro Pixel zur Mittelwertbildung
	$channels	= array(0=>"p1", 1=>"p2", 2=>"p3", 3=>"p4"); // Zeiger für Datenbankrückgabewerte
	if($adccount < 1)$colors = $farben0;
	else $colors = $farben1;
	// Datenbankabfrage von $minID bis ($minID + $werteAnzahl) und Graphen zeichnen
	$sql1 = "SELECT ID, p1, p2, p3, p4 FROM WERTE WHERE ID >= ".$minID." AND ID <= ".($minID+$werteAnzahl);
	try{
		$result1 = $db->query($sql1);}
	catch(Exception $error){ die("Datenbank in Benutzung");}
	if($result1){
		while($row1 = $result1->fetchArray(SQLITE3_ASSOC)){
			if ($x < ($werteProPixel - 1)){
				for($z = 0; $z < 4; $z++)$summen[$z] = $summen[$z] + $row1[$channels[$z]]; // Werte pro Pixel addieren
				$x = $x + 1;
			}else{
				for($z = 0; $z < 4; $z++)$dWerte[$z] = ((($summen[$z] + $row1[$channels[$z]]) / $werteProPixel) * $adc[$z+2][8]) + $adc[$z+2][7] * $adc[$z+2][5]; // Letzte Werte addieren und Mittelwerte bilden sowie Kalibrierfaktor multiplizieren und Kalibrieroffset addieren
				for($z = 0; $z < 4; $z++)$werte[$z] = $bildHoehe - ($adc[1]/2 + ($dWerte[$z] - $adc[1]/2) * ($adc[1] / (($adc[$z+2][1] - $adc[$z+2][0]) * $adc[$z+2][5]))) * $yfaktor; // Werte auf Y-Achse skalieren
				if($xzaehler > 0){ // Linien von Pixel zu Pixel der Graphen zeichnen
					for($z=0;$z<4;$z++){
						if(isset($_POST[$channels[$z].$adccount]))$post = $_POST[$channels[$z].$adccount];
						else $post = NULL;
						if(isset($_GET[$channels[$z].$adccount]))$get = $_GET[$channels[$z].$adccount];
						else $get = NULL;
						if($post||$get) // Nur zeichen wenn Kontrollkästchen in Webseite aktiviert
						imageline($bild,$xzaehler+($skalenBreite-1),$vorWerte[$z]*$skalierung - $bildHoehe*(($skalierung-1)/2),$xzaehler+$skalenBreite,$werte[$z]*$skalierung-$bildHoehe*(($skalierung-1)/2),$colors[$z]);}} // Graph zeichnen
				for($z = 0; $z < 4; $z++){ // Werte in Vorwerte übernehmen und Summenwerte zurücksetzten
					$vorWerte[$z] = $werte[$z];
					$summen[$z] = 0;}
				$x  = 0;
				$xzaehler = $xzaehler + 1;}}}
	$adccount++;
	if($adccount == 1) $rohWerte0 = $dWerte;
	if($adccount == 2) $rohWerte1 = $dWerte;
	$db->close();}
// Letzte Werte der Graphen dezimal anzeigen
$adccount = 0;
foreach($adcs as $adc){
	if($adccount == 0)$rohWerte = $rohWerte0;
	else $rohWerte = $rohWerte1;
	for($z = 2; $z < 6; $z++){
		if($adc[$z][0] < 0){ // Wenn der unter Messbereich negativ ist wird eine andere Gleichung zur Berechnung des Messergebnisses herangezogen
			imagestring($bild,2,$bildBreite-50*(1.7+$adccount),($z-2)*13,number_format(($rohWerte[$z-2] - $adc[1] / 2)/($adc[$z][5]),3).$adc[$z][2],$adc[$z][6]);
		}else{
			imagestring($bild,2,$bildBreite-50*(1.7+$adccount),($z-2)*13,number_format((1/$adc[$z][5])*$rohWerte[$z-2],3).$adc[$z][2],$adc[$z][6]);}}
	$adccount++;}
//Bild Übergeben
$sizex = $neubreite-500;
$dest = imagecreatetruecolor(1400-$neubreite,600);
imagecopy($dest,$bild,0,0,$neubreite,0,1400-$neubreite,600);
header("Content-Type: image/png");
imagepng($dest);
imagedestroy($bild);
?>
