<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');

$main_error=array();

if((isset($_GET['number']))&&(!empty($_GET['number']))) {
    $number=$_GET['number'];
}

if((isset($_GET['power']))&&(!empty($_GET['power']))) {
    $power=$_GET['power'];
}


if(!empty($power) && isset($power) && !empty($number) && isset($number)) {
    insert_plug_conf("PLUG_POWER",$number,$power,$main_error);
} 

?>
