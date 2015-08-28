<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) {
    if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    if((!is_dir($sd_card."/serverAcqSensor"))||(!is_dir($sd_card."/serverHisto"))||(!is_dir($sd_card."/serverPlugUpdate"))||(!is_dir($sd_card."/serverLog"))) {
        if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.100")!==false) {
            check_and_update_sd_card($sd_card,$info,$error,false);
        }
    }
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}


$webcam_conf = cultipi\get_webcam_conf();

for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++)
{
    if(is_file("tmp/webcam$i.jpg"))
    {
        $screen{$i} = "tmp/webcam$i.jpg";
    }
    else 
    {
        $screen{$i} = "";
    }
}


//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) {
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." secondes.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}

?>
