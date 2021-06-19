<?php
$text	= "";
for($z = 0; $z < 4; $z++){
	$text = $text.$_POST["mbu0".$z].",".$_POST["mbo0".$z].",".$_POST["einheit0".$z]
	.",".$_POST["sbu0".$z].",".$_POST["sbo0".$z].",".$_POST["sens0".$z]
	.",".$_POST["offset0".$z].",".$_POST["faktor0".$z].",".$_POST["bezeich0".$z];
	if($z < 3) $text = $text."\n";}

$datei = fopen("sensors0.conf","w");
$ret=fwrite($datei,$text);
fclose($datei);
header("Location: config.php");
?>
