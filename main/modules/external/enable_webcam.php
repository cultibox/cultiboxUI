<?php

$action=$_GET['action'];
$webcam=$_GET['webcam'];
exec("echo $webcam > /tmp/culticam_$action",$output,$err); 
exec("sudo chown cultipi:cultipi /tmp/culticam_$action",$output,$err);
exec("sudo mv /tmp/culticam_$action /var/lock/",$output,$err);
sleep(5);
?>
