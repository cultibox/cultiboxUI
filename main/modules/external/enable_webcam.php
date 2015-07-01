<?php

$action=$_GET['action'];
$webcam=$_GET['webcam'];
exec("echo $webcam > /tmp/culticam_$action",$output,$err);
exec("sudo chown cultipi:cultipi /tmp/culticam_$action",$output,$err);
exec("sudo mv /tmp/culticam_$action /var/lock/",$output,$err);
if(strcmp("$action","stream")==0) {
    sleep(10);
} else {
    if(strcmp("$action","enable")==0) {
        if(is_file("/var/www/cultibox/tmp/webcam$webcam.jpg")) {
            exec("sudo mv /var/www/cultibox/tmp/webcam$webcam.jpg /tmp/",$output,$err);
        }
        $count=0;
        while(true) {
            sleep(1);
            $count=$count+1;
            if((is_file("/var/www/cultibox/tmp/webcam$webcam.jpg"))||($count>10)) break;
        }
        sleep(2);
    } else {
        $count=0;
        while(true) {
            sleep(1);
            $count=$count+1;
            if((!is_file("/var/www/culticam_$action"))||($count>10)) break;
        }
        sleep(2);
    }
}

?>
