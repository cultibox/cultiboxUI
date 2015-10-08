<?php

if ($_GET['type'] == "wep") {
    $type="password_wep";
} else {
    $type="";
}
    

if(is_file("/tmp/resolv.conf")) {
     exec("sudo /bin/cp /etc/resolv.conf /etc/resolv.conf.SAVE");
     exec("sudo /bin/mv /tmp/resolv.conf /etc/");
}

if(is_file("/tmp/interfaces")) {
    exec("sudo /bin/cp /etc/network/interfaces /etc/network/interfaces.SAVE");
    exec("sudo /bin/mv /tmp/interfaces /etc/network/interfaces");
    exec("sudo /bin/chmod 644 /etc/network/interfaces*");
    exec("sudo /sbin/ifup -a --no-act >/dev/null 2>&1 ; echo \"$?\"",$output,$err);
    if((count($output)==1)&&(strcmp($output[0],"0")==0)) {
        exec("sudo /etc/init.d/isc-dhcp-server stop",$output,$err);
        exec("sudo /etc/init.d/dnsmasq stop",$output,$err);
        exec("sudo /etc/init.d/hostapd stop",$output,$err);
        exec("sudo /sbin/iptables -t nat --line-numbers -L | /bin/grep ^[0-9] | /usr/bin/awk '{ print $1 }' | /usr/bin/tac",$return,$err);
        foreach($return as $entry) {
            exec("sudo /sbin/iptables -t nat --delete PREROUTING  $entry",$output,$err);
        }
        if(strcmp("$type","password_wep")==0) {
            exec("sudo /sbin/shutdown -r now",$output,$err);
        } else {
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
                exec("sleep 5",$output,$err);
            } else {
                //For driver TP-Link TL-WN725N  and others : 
                exec("/sbin/lsmod 2>/dev/null|/bin/grep \"8192cu\"",$output,$err);
                if((count($output)>0) {
                    exec("sudo /sbin/modprobe -r 8192cu",$output,$err);
                    exec("sleep 2",$output,$err);
                    exec("sudo /sbin/modprobe 8192cu",$output,$err);
                    exec("sleep 5",$output,$err);
                } else {
                    //For Wipi driver:
                    exec("sudo /sbin/modprobe -r rt2800usb",$output,$err);
                    exec("sleep 2",$output,$err);
                    exec("sudo /sbin/modprobe rt2800usb",$output,$err);
                    exec("sleep 5",$output,$err);
                }
            }   
            exec("sudo /usr/sbin/invoke-rc.d networking force-reload",$output,$err);

            exec("grep 'post-up /sbin/route add default gw' /etc/network/interfaces|grep eth0|sed -e 's/post-up //g'",$out,$err);
            if((count($out)==1)&&(strcmp($out[0],"")!=0)) {
                exec("sudo /sbin/route del default",$output,$err);
                exec("sudo ".$out[0],$output,$err);
            } else {
                unset($out);
                exec("grep 'post-up /sbin/route add default gw' /etc/network/interfaces|grep wlan0|sed -e 's/post-up //g'",$out,$err);
                if((count($out)==1)&&(strcmp($out[0],"")!=0)) {
                    exec("sudo /sbin/route del default",$output,$err);
                    exec("sudo ".$out[0],$output,$err);
                }
            }
        }
        exec("sudo /bin/mv /var/cache/lighttpd/compress/cultibox /tmp/",$output,$err);
        echo json_encode("1");
    } else {
        exec("sudo /bin/mv /etc/network/interfaces.SAVE /etc/network/interfaces");

        if(is_file("/etc/resolv.conf.SAVE")) {
            exec("sudo /bin/mv /etc/resolv.conf.SAVE /etc/resolv.conf");
        }
        echo json_encode("0");
    }
} else {
    echo json_encode("0");
}

?>
