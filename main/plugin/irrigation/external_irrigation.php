<?php

    // Include libraries
    require_once('../../libs/config.php');
    require_once('../../libs/db_get_common.php');
    require_once('../../libs/utilfunc_sd_card.php');
    require_once('lib_irrigation.php');
    
    // Read Action neeeded
    $action = "";
    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    if((isset($_POST['action'])) && (!empty($_POST['action']))) {
        $action=$_POST['action'];
    }
    
    $ret_array = array();
    
    switch ($action) {
        case "reloadUserConfig" :
            irrigation\loadUserConfig();
            break;
        case "save" :
        
            // Save each element
            foreach ($_GET as $key => $value) {
                $table = "";
                // Search which table must be modified
                if (strstr($key,"_localtechnique_"))
                {
                    // This a local technique parameters
                    $table = "irrigation_lt";
                }
                else if (strstr($key,"_engrais_"))
                {
                    // This a engrais parameters
                    $table = "irrigation_engrais";
                }
                else if (strstr($key,"_plateforme_"))
                {
                    // This a plateforme parameters
                    $table = "irrigation_plateforme";
                }
                else if (strstr($key,"_zone_"))
                {
                    // This a zone parameters
                    $table = "irrigation_zone";
                }
                
                if ($table != "")
                {
                    $exploded = explode("_",$key);
                    $parameter = $exploded[0];
                    $id = $exploded[2];
                    
                    // Update the paramter
                    irrigation\updateTable ($table, $id , $parameter, $value);
                }
            }
            break;
        case "applyConf" :
        
            irrigation\createXML();
            
            //irrigation\reloadXMLConfServerIrrigation();
        
            break;
    }

?>
