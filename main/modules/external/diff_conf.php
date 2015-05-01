<?php

require_once('../../libs/config.php');
$tmp_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
$current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";
$diff="";

if(is_dir("$tmp_conf")&&is_dir("$current_conf")) {
    $diff=shell_exec("diff -ruw $current_conf $tmp_conf");
}

echo nl2br($diff);

?>
