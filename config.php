<?php
$datei		= fopen("messung.conf","r");
if($datei){
	$line		= fgets($datei);
	$ergebnis	= explode(",",$line);
	$adress0	= $ergebnis[0];

	$line1		= fgets($datei);
	$ergebnis1	= explode(",",$line1);
	$vcc0		= $ergebnis1[0];
	if(sizeof($ergebnis)>1){$adress1 = $ergebnis[1];}
	else {$adress1 = "";}
	if(sizeof($ergebnis1)>1){$vcc1 = $ergebnis1[1];}
	else {$vcc1 = "";}}
else{ $adress1=""; $aress0="";}
$fsr	= array(0=>"6,144V",2=>"4,096V",4=>"2,048V",6=>"1,024V",8=>"0,512V",10=>"0,256V");
$dr 	= array(0=>"8sps",2=>"16sps",4=>"32sps",6=>"64sps",8=>"125sps",10=>"250sps",12=>"475sps",14=>"860sps");

$fsr0 	= array(1,1,1,1);
$dr0 	= array(1,1,1,1);
$datei 	= fopen("adc0.conf","r");
$fsr1 	= array(1,1,1,1);
$dr1 	= array(1,1,1,1);
$z = 0;
if($datei){
	while(!feof($datei)){
		$line = fgets($datei);
		if($line <> ""){
			$fsr0[$z] = $fsr[str_replace("\n","",explode(",",$line)[1])];
			$dr0[$z] = $dr[str_replace("\n","",explode(",",$line)[3])];}
		$z = $z + 1;}
	fclose($datei);}
$datei = fopen("adc1.conf","r");
$z = 0;
if($datei){
	while(!feof($datei)){
		$line = fgets($datei);
		if($line <> ""){
			$fsr1[$z] = $fsr[str_replace("\n","",explode(",",$line)[1])];
			$dr1[$z] = $dr[str_replace("\n","",explode(",",$line)[3])];}
		$z = $z + 1;}
	fclose($datei);}
$mbu0		= array(0,0,0,0);
$mbu1		= array(0,0,0,0);
$mbo0		= array(0,0,0,0);
$mbo1		= array(0,0,0,0);
$einheit0	= array(0,0,0,0);
$einheit1	= array(0,0,0,0);
$sbu0		= array(0,0,0,0);
$sbu1		= array(0,0,0,0);
$sbo0		= array(0,0,0,0);
$sbo1		= array(0,0,0,0);
$sens0		= array(0,0,0,0);
$sens1		= array(0,0,0,0);
$offset0	= array(0,0,0,0);
$offset1	= array(0,0,0,0);
$faktor0	= array(0,0,0,0);
$faktor1	= array(0,0,0,0);
$bezeich0	= array(0,0,0,0);
$bezeich1	= array(0,0,0,0);
$datei = fopen("sensors0.conf","r");
$z = 0;
if($datei){
	while(!feof($datei)){
		$line		= fgets($datei);
		$mbu0[$z]	= explode(",",$line)[0];
		$mbo0[$z]	= explode(",",$line)[1];
		$einheit0[$z]	= explode(",",$line)[2];
		$sbu0[$z]	= explode(",",$line)[3];
		$sbo0[$z]	= explode(",",$line)[4];
		$sens0[$z]	= explode(",",$line)[5];
		$offset0[$z]	= explode(",",$line)[6];
		$faktor0[$z]	= explode(",",$line)[7];
		$bezeich0[$z]	= explode(",",$line)[8];
		$z = $z + 1;}
	fclose($datei);}
$datei = fopen("sensors1.conf","r");
$z = 0;
if($datei){
	while(!feof($datei)){
		$line		= fgets($datei);
		$mbu1[$z]	= explode(",",$line)[0];
		$mbo1[$z]	= explode(",",$line)[1];
		$einheit1[$z]	= explode(",",$line)[2];
		$sbu1[$z]	= explode(",",$line)[3];
		$sbo1[$z]	= explode(",",$line)[4];
		$sens1[$z]	= explode(",",$line)[5];
		$offset1[$z]	= explode(",",$line)[6];
		$faktor1[$z]	= explode(",",$line)[7];
		$bezeich1[$z]	= explode(",",$line)[8];
		$z = $z + 1;}
	fclose($datei);}
$farben0 = array(0=>"#0000ff",1=>"#ff0000",2=>"#008080",3=>"#ff6000");
$farben1 = array(0=>"#000000",1=>"#ff00ff",2=>"#00ff00",3=>"#ffff00");
$cmd ="sudo /usr/sbin/i2cdetect -y 1";
$handle = popen($cmd,"r");
$text = "";
$count = 0;
while(!feof($handle)){
        $in = fgets($handle);
	if($count > 0){
		$values = explode("--",explode(":",$in)[1]);
		foreach($values as $value){
			if($value > 0){
				$value = explode(" ",$value);
				foreach($value as $valu){
					if($valu > 0){
						if($text != "")$text=$text.", ";
						$text = $text.hexdec($valu);
					}
				}
			}
		}
	}
	$count = $count + 1;
}

echo ""
."<html>"
."<body style ='background-color:#EEEEEE;'>"
."<center><table>"
."<tr valign='top' align='center' style='background-color: #999999;'>"
."<td style='background-color:#EEEEEE;'>Verf&uuml;gbare adr. &nbsp;&nbsp;<br /> ".$text
."</td>"
."<td align='center'>"
."<form id='messung' action='messungWriteconf.php' method='post'>"
."<input form='messung' type='checkbox' name='wandler0' value='1' checked />&nbsp;"
."adr.<input form='messung' type='text' name='adress0' value='".$adress0."' style='width: 70px;' />"
."&nbspVCC <input form='messung' type='text' name='vcc0' value='".$vcc0."' style='width: 70px;' />"
."</td><td></td><td align='center'>";
if($adress1<> "")
echo "<input form='messung' type='checkbox' name='wandler1' value='1' checked />&nbsp;";
else echo "<input form='messung' type='checkbox' name='wandler1' value='1' />&nbsp;";
echo "adr.<input form='messung' type='text' name='adress1' value='".$adress1."' style='width: 70px;' /></form>"
."&nbsp;VCC <input form='messung' type='text' name='vcc1' value='".$vcc1."' style='width: 70px;' /></form>"
."</td><td align='center'>"
."<input type='submit' form='messung' value='speichern'</td></tr>"
."<tr valign='top' align='right'>"
."<td></td>"
."<td align='center'>"
."<form id='sensors0' action='sensors0writeconf.php' method='post'>";
for($z = 0; $z < 4; $z ++){
	echo "<div style='background-color: ".$farben0[$z].";color: #eeeeee; padding: 5px; margin: 5px'>";
	echo "ch ".$z."&nbsp;<input style='width: 190px;' type='text' form='sensors0' name='bezeich0".$z."' value='".$bezeich0[$z]."' /><br />";
	echo "MB &nbsp;<input style='width: 60px;' type='text' form='sensors0' name='mbu0".$z."' value='".$mbu0[$z]."' />-";
	echo "<input style='width: 60px;' type='text' form='sensors0' name='mbo0".$z."' value='".$mbo0[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors0' name='einheit0".$z."' value='".$einheit0[$z]."' /><br />";
	echo "SB &nbsp; <input style='width: 60px;' type='text' form='sensors0' name='sbu0".$z."' value='".$sbu0[$z]."' />-";
	echo "<input style='width: 60px;' type='text' form='sensors0' name='sbo0".$z."' value='".$sbo0[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors0' name='sens0".$z."' value='".$sens0[$z]."' /><br />";
	echo "Kal Offs Fak <input style='width: 60px;' type='text' form='sensors0' name='offset0".$z."' value='".$offset0[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors0' name='faktor0".$z."' value='".$faktor0[$z]."' />";
	echo "</div>";
}
echo "<input type='submit' form='sensors0' name='save' value='speichern' />"
."</form></td>";
//____________________fertig_____________________
echo "<td align='center'>"
."<form id='adc0' action='adc0writeconf.php' method='post'>";
for($z = 0; $z < 4; $z ++){
	echo "<div style='background-color: ".$farben0[$z].";color: #eeeeee; padding: 5px; margin: 3px'>"
	."ch ".$z."<br /><select form='adc0'name='fsr".$z."' style='width: 90px;'>";
	if($fsr0[$z] == "6,144V")
		echo "<option value='0,0.0001875' selected>6,144V</option>";
	else
		echo "<option value='0,0.0001875'>6,144V</option>";
	if($fsr0[$z] == "4,096V")
		echo "<option value='2,0.000125' selected>4,096V</option>";
	else
		echo "<option value='2,0.000125'>4,096V</option>";
	if($fsr0[$z] == "2,048V")
		echo "<option value='4,0.0000625' selected>2,048V</option>";
	else
		echo "<option value='4,0.0000625'>2,048V</option>";
	if($fsr0[$z] == "1,024V")
		echo "<option value='6,0.00003125' selected>1,024V</option>";
	else
		echo "<option value='6,0.00003125'>1,024V</option>";
	if($fsr0[$z] == "0,512V")
		echo "<option value='8,0.000015625' selected>0,512V</option>";
	else
		echo "<option value='8,0.000015625'>0,512V</option>";
	if($fsr0[$z] == "0,256V")
		echo "<option value='10,0.0000078125' selected>0,256V</option>";
	else
		echo "<option value='10,0.0000078125'>0,256V</option>";
	echo "</select><br /><select form='adc0' name='dr".$z."' style='width: 90px;'>";
	if($dr0[$z] == "8sps")
		echo "<option value='0' selected>8sps</option>";
	else
		echo "<option value='0'>8sps</option>";
	if($dr0[$z] == "16sps")
		echo "<option value='2' selected>16sps</option>";
	else
		echo "<option value='2'>16sps</option>";
	if($dr0[$z] == "32sps")
		echo "<option value='4' selected>32sps</option>";
	else
		echo "<option value='4'>32sps</option>";
	if($dr0[$z] == "64sps")
		echo "<option value='6' selected>64sps</option>";
	else
		echo "<option value='6'>64sps</option>";
	if($dr0[$z] == "125sps")
		echo "<option value='8' selected>125sps</option>";
	else
		echo "<option value='8'>125sps</option>";
	if($dr0[$z] == "250sps")
		echo "<option value='10' selected>250sps</option>";
	else
		echo "<option value='10'>250sps</option>";
	if($dr0[$z] == "475sps")
		echo "<option value='12' selected>475sps</option>";
	else
		echo "<option value='12'>475sps</option>";
	if($dr0[$z] == "860sps")
		echo "<option value='14' selected>860sps</option>";
	else
		echo "<option value='14'>860sps</option>";
	echo "</select></div>";
}
echo "<input type='submit' form='adc0' value='speichern' /></form>"
."</td>"
."<td align='center'>"
."<form id='sensors1' action='sensors1writeconf.php' method='post'>";
//_________________NEU______________________
for($z = 0; $z < 4; $z ++){
	if($z < 2)
		echo "<div style='background-color: ".$farben1[$z].";color: #eeeeee; padding: 5px; margin: 5px'>";
	else
		echo "<div style='background-color: ".$farben1[$z].";color: #000000; padding: 5px; margin: 5px'>";
	echo "ch ".$z."&nbsp;<input style='width: 190px;' type='text' form='sensors1' name='bezeich1".$z."' value='".$bezeich1[$z]."' /><br />";
	echo "MB &nbsp;<input style='width: 60px;' type='text' form='sensors1' name='mbu1".$z."' value='".$mbu1[$z]."' />-";
	echo "<input style='width: 60px;' type='text' form='sensors1' name='mbo1".$z."' value='".$mbo1[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors1' name='einheit1".$z."' value='".$einheit1[$z]."' /><br />";
	echo "SB &nbsp; <input style='width: 60px;' type='text' form='sensors1' name='sbu1".$z."' value='".$sbu1[$z]."' />-";
	echo "<input style='width: 60px;' type='text' form='sensors1' name='sbo1".$z."' value='".$sbo1[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors1' name='sens1".$z."' value='".$sens1[$z]."' /><br />";
	echo "Kal Offs Fak <input style='width: 60px;' type='text' form='sensors1' name='offset1".$z."' value='".$offset1[$z]."' />";
	echo "&nbsp;<input style='width: 60px;' type='text' form='sensors1' name='faktor1".$z."' value='".$faktor1[$z]."' />";
	echo "</div>";
}
echo "<input type='submit' form='sensors1' name='save' value='speichern' />";
//_________________ALT______________________
echo "</form></td>"
."<td align='center'>"
."<form action='adc1writeconf.php' id='adc1' method='post'>";
for($z = 0; $z < 4; $z ++){
	if($z < 2)
		echo "<div style='background-color: ".$farben1[$z].";color: #eeeeee; padding: 5px; margin: 3px'>";
	else
		echo "<div style='background-color: ".$farben1[$z].";color: #000000; padding: 5px; margin: 3px'>";
	echo "ch ".$z."<br /><select form='adc1' name='fsr".$z."' style='width: 90px;'>";
	if($fsr1[$z] == "6,144V")
		echo "<option value='0,0.0001875' selected>6,144V</option>";
	else
		echo "<option value='0,0.0001875'>6,144V</option>";
	if($fsr1[$z] == "4,096V")
		echo "<option value='2,0.000125' selected>4,096V</option>";
	else
		echo "<option value='2,0.000125'>4,096V</option>";
	if($fsr1[$z] == "2,048V")
		echo "<option value='4,0.0000625' selected>2,048V</option>";
	else
		echo "<option value='4,0.0000625'>2,048V</option>";
	if($fsr1[$z] == "1,024V")
		echo "<option value='6,0.00003125' selected>1,024V</option>";
	else
		echo "<option value='6,0.00003125'>1,024V</option>";
	if($fsr1[$z] == "0,512V")
		echo "<option value='8,0.000015625' selected>0,512V</option>";
	else
		echo "<option value='8,0.000015625'>0,512V</option>";
	if($fsr1[$z] == "0,256V")
		echo "<option value='10,0.0000078125' selected>0,256V</option>";
	else
		echo "<option value='10,0.0000078125'>0,256V</option>";
	echo "</select><br /><select form='adc1' name='dr".$z."' style='width: 90px;'>";
	if($dr1[$z] == "8sps")
		echo "<option value='0' selected>8sps</option>";
	else
		echo "<option value='0'>8sps</option>";
	if($dr1[$z] == "16sps")
		echo "<option value='2' selected>16sps</option>";
	else
		echo "<option value='2'>16sps</option>";
	if($dr1[$z] == "32sps")
		echo "<option value='4' selected>32sps</option>";
	else
		echo "<option value='4'>32sps</option>";
	if($dr1[$z] == "64sps")
		echo "<option value='6' selected>64sps</option>";
	else
		echo "<option value='6'>64sps</option>";
	if($dr1[$z] == "125sps")
		echo "<option value='8' selected>125sps</option>";
	else
		echo "<option value='8'>125sps</option>";
	if($dr1[$z] == "250sps")
		echo "<option value='10' selected>250sps</option>";
	else
		echo "<option value='10'>250sps</option>";
	if($dr1[$z] == "475sps")
		echo "<option value='12' selected>475sps</option>";
	else
		echo "<option value='12'>475sps</option>";
	if($dr1[$z] == "860sps")
		echo "<option value='14' selected>860sps</option>";
	else
		echo "<option value='14'>860sps</option>";
	echo "</select></div>";
}
echo "<input type='submit' form='adc1' value='speichern' /></form>"
."</td></tr></table><br /><a href='index.php'>zur&uuml;ck</a></center>";

echo "<form action='remove.php' id='rem' method='post'>";
echo "<input type='submit' form='rem' name='expand' value='Speicherkarte voll nutzen' /><br />"
."<br /><br />";
echo "<input type='submit' form='rem' name='remdb0' value='Graphen 0 löschen' /><br />";
echo "<input type='submit' form='rem' name='remdb1' value='Graphen 1 löschen' /></form>";

?>

