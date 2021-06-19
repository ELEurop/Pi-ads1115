<?php
$text	= "";
for($z = 0; $z < 4; $z++){
	$text = $text.$_POST["mbu1".$z].",".$_POST["mbo1".$z].",".$_POST["einheit1".$z]
	.",".$_POST["sbu1".$z].",".$_POST["sbo1".$z].",".$_POST["sens1".$z]
	.",".$_POST["offset1".$z].",".$_POST["faktor1".$z].",".$_POST["bezeich1".$z];
	if($z < 3) $text = $text."\n";}

$datei = fopen("sensors1.conf","w");
$ret=fwrite($datei,$text);
fclose($datei);
header("Location: config.php");
?>
