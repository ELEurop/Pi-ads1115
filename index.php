<?php


if( ! file_exists("ADRUN")){
	$datei = fopen("readAD.log","r");
	while(!feof($datei)){
		echo  fgets($datei)."<br />";
	}
	echo  "<a href='config.php' > zur Konfiguration</a><br />";
}

else {

$bezeich0       = array();
$bezeich1       = array();
$datei = fopen("sensors0.conf","r");
$z = 0;
while(!feof($datei)){
        $line           = fgets($datei);
        $bezeich0[$z]   = explode(",",$line)[8];
        $z = $z + 1;}
fclose($datei);
$datei = fopen("sensors1.conf","r");
$z = 0;
while(!feof($datei)){
        $line           = fgets($datei);
        $bezeich1[$z]   = explode(",",$line)[8];
        $z = $z + 1;}
fclose($datei);
$datei          = fopen("messung.conf","r");
$ergebnis       = explode(",",fgets($datei));
fclose($datei);
if(sizeof($ergebnis)>1)$zweiArrays = 1; // wenn zwei AD-Wandler in Betrieb sind
else $zweiArrays = 0;
echo ""
."<html>"
."<body style ='background-color:#EEEEEE;'>"
."<center><table>"
."<tr valign='top' align='right'>"
."<td align='right' valign='bottom'>"
."<form id='lesen' action='db.php' method='post' target='grafik'>";
if($zweiArrays == 1){
	echo "<div style='background-color: #000000; color: #dddddd; padding : 1px; margin: 3px; width: 100px;'>"
	.$bezeich1[0]."<input form='lesen' type='checkbox' name='p11' value='1' checked/></div>"
	."<div style='background-color: #FF00FF; padding : 1px; margin: 3px; width: 100px;'>"
	.$bezeich1[1]."<input form='lesen' type='checkbox' name='p21' value='1' checked/></div>"
	."<div style='background-color: #00FF00; padding : 1px; margin: 3px; width: 100px;'>"
	.$bezeich1[2]."<input form='lesen' type='checkbox' name='p31' value='1' checked/></div>"
	."<div style='background-color: #FFFF00; color: #000000; padding : 1px; margin: 3px; width: 100px;'>"
	.$bezeich1[3]."<input form='lesen' type='checkbox' name='p41' value='1' checked/></div>";
}
echo "<div style='background-color: #0000FF; color: #dddddd; padding : 1px; margin: 3px; width: 100px;'>"
.$bezeich0[0]."<input form='lesen' type='checkbox' name='p10' value='1' checked /></div>"
."<div style='background-color: #FF0000; padding : 1px; margin: 3px; width: 100px;'>"
.$bezeich0[1]."<input form='lesen' type='checkbox' name='p20' value='1' checked /></div>"
."<div style='background-color: #008080; padding : 1px; margin: 3px; width: 100px;'>"
.$bezeich0[2]."<input form='lesen' type='checkbox' name='p30' value='1' checked /></div>"
."<div style='background-color: #FF6000; padding : 1px; margin: 3px; width: 100px;'>"
.$bezeich0[3]."<input form='lesen' type='checkbox' name='p40' value='1' checked /></div>"
."<input form='lesen' type='radio' name='skalierung' value='6' />x6<br />"
."<input form='lesen' type='radio' name='skalierung' value='4' />x4<br />"
."<input form='lesen' type='radio' name='skalierung' value='2' />x2<br />"
."<input form='lesen' type='radio' name='skalierung' value='1' checked='checked' />x1</form>"
."</td>"
."<td align='center' valign='center'>"
."<iframe src='db.php?p10=1&p20=1&p30=1&p40=1&p11=1&p21=1&p31=1&p41=1' width='1100px' height='620px' frameborder='0' name='grafik'></iframe>"
."</td>"
."</tr>"
."<tr><td></td>"
."<td><input form='lesen' type='submit' value='zeichnen' />&nbsp;"
."Zeitraum=<select name='anzahl' form='lesen'>"
."<option value='0' selected>0m</option>"
."<option value='900'>30m</option>"
."<option value='1800'>1h</option>"
."<option value='3600'>2h</option>"
."<option value='7200'>4h</option>"
."<option value='14400'>8h</option>"
."<option value='28800'>16h</option>"
."</select>"
."&nbsp;<select name='anzahlT' form='lesen'>";
for($z = 0; $z < 364; $z++){
	if($z == 1)echo "<option value='".($z * 43200)."' selected>".$z."T</option>";
	else echo "<option value='".($z * 43200)."'>".$z."T</option>";
}
echo "</select>";
echo "&nbsp;Zur&uuml;ckliegend=<select name='backtimeH' form='lesen'>"
."<option value='0' selected>0m</option>"
."<option value='900'>30m</option>"
."<option value='1800'>1h</option>"
."<option value='3600'>2h</option>"
."<option value='7200'>4h</option>"
."<option value='14400'>8h</option>"
."<option value='28800'>16h</option>"
."</select>";
echo "&nbsp;<select name='backtime' form='lesen'>";
for($z = 0; $z < 364; $z++){
	if($z == 0)echo "<option value='".$z."' selected>".$z."T</option>";
	else echo "<option value='".$z."'>".$z."T</option>";
}
echo "</select>&nbsp;&nbsp;&nbsp;<a href='config.php'>Konfiguration</a>";
echo "&nbsp;&nbsp;&nbsp;<a href='readAD.log'>errorlog</a></td></tr></table></center>";

}

?>
