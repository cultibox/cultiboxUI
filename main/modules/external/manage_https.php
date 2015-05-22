<?php

if((isset($_GET['action']))&&(!empty($_GET['action']))) {
    $action=$_GET['action'];
}


if(strcmp("$action","https")==0) {
    if(is_file("/etc/lighttpd/lighttpd.conf.https")) {
        exec("sudo /bin/cp /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.base",$output,$err);
        exec("sudo /bin/mv /etc/lighttpd/lighttpd.conf.https /etc/lighttpd/lighttpd.conf",$output,$err);
    }
} else {
    if(is_file("/etc/lighttpd/lighttpd.conf.base")) {
        exec("sudo /bin/cp /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.https",$output,$err);
        exec("sudo /bin/mv /etc/lighttpd/lighttpd.conf.base /etc/lighttpd/lighttpd.conf",$output,$err);
    }
}

exec("sudo /etc/init.d/lighttpd force-reload",$output,$err);
sleep(12);

?>
