<?php
$fsrs		= array();
$drs		= array();
$channels	= array(0xc, 0xd, 0xe, 0xf);
$text		= "";
for($z = 0; $z < 4; $z++)$text = $text.$channels[$z].",".$_POST["fsr".$z].",".$_POST["dr".$z]."\n";
$datei = fopen("adc1.conf","w");
$ret=fwrite($datei,$text);
fclose($datei);
header("Location: config.php");
?>
