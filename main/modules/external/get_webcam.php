<?php

    require_once('../../libs/config.php');

    exec("ls -l /dev/video* 2>/dev/null",$output,$err);
    if(count($output)==0) {
        for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
            $return[$i]="0";
        }
        echo json_encode($return);
    } else {
        $return=array();
        $webcam="0";
        for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
            foreach($output as $device) {
                if($i==substr(trim($device), -1)) {
                    if(!is_file("../../../tmp/webcam$i.jpg")) {            
                        $webcam="2";
                    } else {
                        $webcam="1";
                    }
                } 
            }
            $return[$i]="$webcam";
            $webcam="0";
        } 
        echo json_encode($return);
    }
?>
