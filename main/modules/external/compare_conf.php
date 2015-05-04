<?php 

    require_once('../../libs/config.php');


    $ret = array();
    $err="";

    $tmp_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";

    if((isset($_GET['show']))&&(!empty($_GET['show']))) {
        $diff="";
        if(is_dir("$tmp_conf")&&is_dir("$current_conf")) {
            $diff=shell_exec("diff -ruw $current_conf $tmp_conf");
        }

        echo nl2br($diff);
    } else {
        exec("diff -rw $current_conf $tmp_conf",$ret,$err);
        echo json_encode($err);
    }

?>
