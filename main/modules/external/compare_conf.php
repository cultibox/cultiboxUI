<?php

    require_once('../../libs/config.php');

    $ret = array();
    $err = "";

    $tmp_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";

    $typicalError["serverAcqSensor"]    = "des capteurs" ;
    $typicalError["cultiPi"]            = "générale du pilotage" ;
    $typicalError["serverCultibox"]     = "de l'affichage dans la Cultibox" ;
    $typicalError["serverHisto"]        = "de la sauvegarde en base de donnée" ;
    $typicalError["serverIrrigation"]   = "de l'irrigation" ;
    $typicalError["serverLog"]          = "de l'enregistrement du fichier de suivi" ;
    $typicalError["serverMail"]         = "des mails" ;
    $typicalError["serverPlugUpdate"]   = "du pilotage des prises" ;
    $typicalError["serverSupervision"]  = "de la supervision" ;
    
    
    // Foreach folder in conf_temp
    // - Check if this folder exists in 01_defaultConf_RPi
    //   - Foreach file compare it
    //   - Check if each dir exists
    //      - Foreach file compare it
    $filesAndDirsInConfTemp = array_diff(scandir($tmp_conf), array('..', '.'));
    
    foreach ($filesAndDirsInConfTemp As $fileAndDirInConfTemp)
    {
        $fileTempName = $tmp_conf     . "/" . $fileAndDirInConfTemp;
        $fileConfName = $current_conf . "/" . $fileAndDirInConfTemp;
        
        // Check if this dir exists in 01_defaultConf_RPi
        if (!is_dir($fileConfName)) {
            $err[] = htmlentities("La configuration " . $typicalError[$fileAndDirInConfTemp] . " n'existe pas.");
        }
        else 
        {
            // Compare the tow directories
            $errTemp = "";
            $ret = "";
            exec("diff -rw $fileTempName $fileConfName",$ret,$errTemp);
            
            // If there are some diff 
            if (trim($errTemp) != 0) {
                $err[] = htmlentities("La configuration " . $typicalError[$fileAndDirInConfTemp] . " n'est pas à jour.");
            }
        }
    }

    echo json_encode($err);

?>
