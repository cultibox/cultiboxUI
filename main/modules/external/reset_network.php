<?php

exec("sudo /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces",$output,$err);
exec("/sbin/lsmod 2>/dev/null|/bin/grep \"ath9k\"",$output,$err);
exec("/sbin/lsmod 2>/dev/null|/bin/grep \"ath9k\"",$output,$err);
if(count($output)>0) {
    //For Atheros drivers :
    exec("sudo /sbin/modprobe -r ath9k_htc",$output,$err);
    exec("sudo /sbin/modprobe -r ath9k_common",$output,$err);
    exec("sudo /sbin/modprobe -r ath9k_hw",$output,$err);
    exec("sudo /sbin/modprobe -r ath",$output,$err);

    exec("sleep 2",$output,$err);

    exec("sudo /sbin/modprobe ath9k_htc",$output,$err);
    exec("sudo /sbin/modprobe ath9k_common",$output,$err);
    exec("sudo /sbin/modprobe ath9k_hw",$output,$err);
    exec("sudo /sbin/modprobe ath",$output,$err);
} else {
    //For driver TP-Link TL-WN725N  and others :
    exec("/sbin/lsmod 2>/dev/null|/bin/grep \"8192cu\"",$output,$err);
    if(count($output)>0) {
        exec("sudo /sbin/modprobe -r 8192cu",$output,$err);
        exec("sleep 2",$output,$err);
        exec("sudo /sbin/modprobe 8192cu",$output,$err);
    } else {
        //For Wipi driver:
        exec("sudo /sbin/modprobe -r rt2800usb",$output,$err);
        exec("sleep 2",$output,$err);
        exec("sudo /sbin/modprobe rt2800usb",$output,$err);
     }
}
sleep(7);
exec("sudo /usr/sbin/invoke-rc.d networking force-reload",$output,$err);
sleep(2);
exec("sudo /bin/mv /var/cache/lighttpd/compress/cultibox /tmp/",$output,$err);
sleep(7);

?>