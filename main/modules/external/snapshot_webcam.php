<?php

$action=$_GET['action'];
$webcam=$_GET['webcam'];

if(is_file("/var/www/cultibox/tmp/webcam$webcam.jpg")) {
    exec("sudo mv /var/www/cultibox/tmp/webcam$webcam.jpg /tmp/",$output,$err);
}

sleep(1);
exec("wget http://".$_SERVER['SERVER_ADDR'].":8081/?action=snapshot -O /var/www/cultibox/tmp/webcam$webcam.jpg",$output,$err);

?>
