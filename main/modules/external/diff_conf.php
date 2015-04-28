<?php

$diff="";
if(is_dir("/etc/cultipi/conf_tmp")&&is_dir("/etc/cultipi/01_defaultConf_RPi")) {
    $diff=shell_exec("diff -r /etc/cultipi/01_defaultConf_RPi /etc/cultipi/conf_tmp");
}

echo nl2br($diff);

?>
