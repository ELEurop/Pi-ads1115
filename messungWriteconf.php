<?php
$text		= "";
if(isset($_POST['wandler0']))$text=$text.$_POST["adress0"];
if(isset($_POST['wandler1']))$text=$text.",".$_POST["adress1"];
if(isset($_POST['vcc0']))$text=$text."\n".$_POST["vcc0"];
if(isset($_POST['vcc1']))$text=$text.",".$_POST["vcc1"];
$datei = fopen("messung.conf","w");
$ret=fwrite($datei,$text);
fclose($datei);
if(!function_exists("shell_exec"))echo "no exec";
else {
	shell_exec("sudo systemctl restart readAD.service");
	sleep(1);
	shell_exec("sudo chmod a+rw /var/www/html/*");
	header("Location: config.php");}
?>
