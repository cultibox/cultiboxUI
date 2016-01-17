<?php

exec("sudo /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces",$output,$err);
//exec("sudo /usr/sbin/hub-ctrl -h 0 -P 2 -p 0",$output,$err);
//exec("sudo /usr/bin/ pkill -9 wpa_supplicant",$output,$err);
//sleep(3);
//exec("sudo /usr/sbin/hub-ctrl  -h 0 -P 2 -p 1",$output,$err);
//sleep(7);
//exec("sudo /usr/sbin/invoke-rc.d networking force-reload",$output,$err);
//sleep(2);
exec("sudo /bin/mv /var/cache/lighttpd/compress/cultibox /tmp/",$output,$err);
exec("sudo /sbin/shutdown -r now",$output,$err);
//sleep(7);

?>
