<?php
$text           = "";
if(isset($_POST['remdb0'])){
        shell_exec("rm ads1115_0.db");
        shell_exec("sudo systemctl restart readAD.service");
        sleep(1);
        shell_exec("sudo chmod a+rw /var/www/html/*");
        header("Location: config.php");
}
if(isset($_POST['remdb1'])){
        shell_exec("rm ads1115_1.db");
        shell_exec("sudo systemctl restart readAD.service");
        sleep(1);
        shell_exec("sudo chmod a+rw /var/www/html/*");
        header("Location: config.php");
}
if(isset($_POST['expand'])){
        shell_exec("sudo raspi-config --expand-rootfs");
        sleep(1);
        shell_exec("sudo reboot");
        header("Location: config.php");
}

?>
