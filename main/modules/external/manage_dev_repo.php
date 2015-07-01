<?php

if((isset($_GET['beta']))&&(!empty($_GET['beta']))) {
    if(strcmp($_GET['beta'],"True")==0) {
        exec("echo 'deb http://www.greenbox-botanic.com/cultibox/repository-dev/armhf/ binary/' > /tmp/cultibox-dev.list",$output,$err);
        exec("sudo /bin/mv /tmp/cultibox-dev.list /etc/apt/sources.list.d/",$output,$err);
    } else {
        if(is_file("/etc/apt/sources.list.d/cultibox-dev.list")) {
            exec("sudo /bin/mv /etc/apt/sources.list.d/cultibox-dev.list /tmp/",$output,$err);
        }
    }
}

?>
