<?php

$action=$_GET['action'];
if((!isset($_GET['webcam']))||(empty($_GET['webcam']))) {
    $webcam=0;
} else {
    $webcam=$_GET['webcam'];
}
exec("echo $webcam > /tmp/culticam_$action",$output,$err);
exec("sudo chown cultipi:cultipi /tmp/culticam_$action",$output,$err);
exec("sudo mv /tmp/culticam_$action /var/lock/",$output,$err);

$count=0;
while(true) {
    sleep(1);
	$count=$count+1;
    exec("ls /var/lock/culticam_${action} 2>/dev/null",$output,$err);

    if((strcmp("$err","0")!=0)||($count>=10)) break;
}

?>
