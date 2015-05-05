<?php

    // Load libraries
    require_once('../../libs/config.php');
    require_once('../../libs/db_get_common.php');
    require_once('../../libs/db_set_common.php');
    require_once('../../libs/utilfunc.php');
    require_once('../../libs/utilfunc_sd_card.php');

    // Init variables
    $ret = array();
    $err = array();

    // Init directroy of configuration
    $tmp_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";

    // For each directories, defin a text for en user
    $typicalError["serverAcqSensor"]    = __('DIFF_CONF_SERVERACQSENSOR') ;
    $typicalError["cultiPi"]            = __('DIFF_CONF_CULTIPI') ;
    $typicalError["serverIrrigation"]   = __('DIFF_CONF_SERVERIRRIGATION') ;
    $typicalError["serverMail"]         = __('DIFF_CONF_SERVERMAIL') ;
    $typicalError["serverPlugUpdate"]   = __('DIFF_CONF_SERVERPLUGUPDATE') ;
    $typicalError["serverSupervision"]  = __('DIFF_CONF_SERVERSUPERVISION') ;
    
    
    // Foreach folder in conf_temp
    // - Check if this folder exists in 01_defaultConf_RPi
    // - Check difference
    $filesAndDirsInConfTemp = array_diff(scandir($tmp_conf), array('..', '.'));

    foreach ($filesAndDirsInConfTemp As $fileAndDirInConfTemp)
    {
        $fileTempName = $tmp_conf     . "/" . $fileAndDirInConfTemp;
        $fileConfName = $current_conf . "/" . $fileAndDirInConfTemp;

        // We don't care about folowing folder 
        // serverLog
        // serverHisto
        // serverCultibox
        if (strcmp($fileAndDirInConfTemp,"serverLog") != 0 &&
            strcmp($fileAndDirInConfTemp,"serverHisto") != 0 && 
            strcmp($fileAndDirInConfTemp,"serverCultibox") != 0)
        {
            // Check if this dir exists in 01_defaultConf_RPi
            if (!is_dir($fileConfName)) {
                $err[] = htmlentities("La configuration " . $typicalError[$fileAndDirInConfTemp] . " n'existe pas.",ENT_HTML5,"ISO-8859-1");
            }
            else 
            {
                // Compare the tow directories
                $errTemp = "";
                $ret = "";
                exec("diff -rw $fileTempName $fileConfName",$ret,$errTemp);
                
                // If there are some diff 
                if (trim($errTemp) != 0) {
                    $err[] = htmlentities("La configuration ".$typicalError[$fileAndDirInConfTemp] . " n'est pas à jour.",ENT_HTML5,"ISO-8859-1");
                }
            }
        }
    }

    // Return array to JS
    echo json_encode($err);

?>
