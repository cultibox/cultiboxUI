<?php

// Define constant
define("ERROR_COPY_FILE", "2");
define("ERROR_WRITE_PROGRAM", "3");
define("ERROR_COPY_FIRM", "4");
define("ERROR_COPY_PLUGA", "5");
define("ERROR_COPY_PLUG_CONF", "6");
define("ERROR_COPY_TPL", "7");
define("ERROR_COPY_INDEX", "8");
define("ERROR_WRITE_SD_CONF", "11");
define("ERROR_WRITE_SD", "12");
define("ERROR_SD_NOT_FOUND", "13");
define("ERROR_COPY_PLGIDX", "14");

// {{{ check_and_update_sd_card()
// ROLE If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
// IN   $sd_card    sd card path 
// RET 0 if the sd card is updated, 1 if the sd card has been updated, return > 1 if an error occured
function check_and_update_sd_card($sd_card="",&$main_info_tab,&$main_error_tab,$force_rtc_offset=false) {

    // Check and update path
    $logs = "$sd_card/logs";
    $cnf  = "$sd_card/cnf";
    $plg  = "$cnf/plg";
    $prg  = "$cnf/prg";
    $bin  = "$sd_card/bin";
    $recordfrequency = "1"; 
    $updatefrequency = "1"; 

    // If we are in cultipi mode, create file systeme structure
    if(!is_dir($sd_card))                           mkdir($sd_card);
    if(!is_dir($sd_card . "/cultiPi"))              mkdir($sd_card . "/cultiPi");
    if(!is_dir($sd_card . "/serverAcqSensor"))      mkdir($sd_card . "/serverAcqSensor");
    if(!is_dir($sd_card . "/serverHisto"))          mkdir($sd_card . "/serverHisto");
    if(!is_dir($sd_card . "/serverLog"))            mkdir($sd_card . "/serverLog");
    if(!is_dir($sd_card . "/serverPlugUpdate"))     mkdir($sd_card . "/serverPlugUpdate");
    if(!is_dir($sd_card . "/serverPlugUpdate/prg")) mkdir($sd_card . "/serverPlugUpdate/prg");
    if(!is_dir($sd_card . "/serverPlugUpdate/plg")) mkdir($sd_card . "/serverPlugUpdate/plg");
    
    // Create cultipi conf.xml file
    $paramListCultipiConf[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['cultiPi']
    );
    create_conf_XML($sd_card . "/cultiPi/conf.xml" , $paramListCultipiConf);
    
    // Create cultipi start.xml file
    $paramListCultipiStart[] = array ( 
        'name' => "serverLog",
        'waitAfterUS' => "1000",
        'port' => "6003",
        'pathexe' => "tclsh",
        'path' => "./serverLog/serveurLog.tcl",
        'xmlconf' => "./serverLog/conf.xml",
    );
    $paramListCultipiStart[] = array ( 
        'name' => "serverAcqSensor",
        'waitAfterUS' => "100",
        'port' => "6006",
        'pathexe' => "tclsh",
        'path' => "./serverAcqSensor/serverAcqSensor.tcl",
        'xmlconf' => "./serverAcqSensor/conf.xml",
    );
    $paramListCultipiStart[] = array ( 
        'name' => "serverPlugUpdate",
        'waitAfterUS' => "100",
        'port' => "6004",
        'pathexe' => "tclsh",
        'path' => "./serverPlugUpdate/serverPlugUpdate.tcl",
        'xmlconf' => "./serverPlugUpdate/conf.xml",
    );
    $paramListCultipiStart[] = array ( 
        'name' => "serverHisto",
        'waitAfterUS' => "100",
        'port' => "6009",
        'pathexe' => "tclsh",
        'path' => "./serverHisto/serverHisto.tcl",
        'xmlconf' => "./serverHisto/conf.xml",
    );
    
    // If there are some plugins to add in start.xml , add it
    foreach ($GLOBALS['PLUGIN'] as $plugin) { 
        
        // Check if function exists
        if (function_exists($plugin . '\addInStartXMLCultiCore'))
        {
            // Add parameters
            $paramListCultipiStart[] = call_user_func($plugin . '\addInStartXMLCultiCore');                                        
        } 
    }
    
    create_conf_XML($sd_card . "/cultiPi/start.xml" , $paramListCultipiStart);
    
    // Server acq sensor
    $paramListserverAcqSensor[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverAcqSensor']
    );
    $paramListserverAcqSensor[] = array (
        "name" => "simulator",
        "actif" => "off"
    );
    //  <item name="network_read,1,ip" ip="192.178.0.10" />
    //  <item name="network_read,1,sensor" sensor="2" />
    if ($GLOBALS['CULTIPI']['USE_REMOTE_SENSOR'] == 1)
    {
        foreach ($GLOBALS['CULTIPI']['REMOTE_SENSOR'] as $elemOfArray)
        {
            $paramListserverAcqSensor[] = array (
                "name" => "network_read," . $elemOfArray["SENSOR_INDEX_IN_MASTER"] . ",ip",
                "ip" => $GLOBALS['CULTIPI']['REMOTE_SLAVE']["IP_" . $elemOfArray["REMOTE_SLAVE"]]
            );
            
            $paramListserverAcqSensor[] = array (
                "name" => "network_read," . $elemOfArray["SENSOR_INDEX_IN_MASTER"] . ",sensor",
                "sensor" => $elemOfArray["SENSOR_INDEX_IN_SLAVE"]
            );
        }
    }
    if ($GLOBALS['CULTIPI']['USE_DIRECT_READ'] == 1)
    {
        foreach ($GLOBALS['CULTIPI']['DIRECT_SENSOR'] as $elemOfArray)
        {
            $paramListserverAcqSensor[] = array (
                "name"  => "direct_read," . $elemOfArray["SENSOR_INDEX"] . ",input",
                "input" => $elemOfArray["SENSOR_FIRST_INPUT"] 
            );
            
            $paramListserverAcqSensor[] = array (
                "name"  => "direct_read," . $elemOfArray["SENSOR_INDEX"] . ",value",
                "value" => $elemOfArray["SENSOR_FIRST_VALUE"] 
            );
            
            $paramListserverAcqSensor[] = array (
                "name"  => "direct_read," . $elemOfArray["SENSOR_INDEX"] . ",input2",
                "input" => $elemOfArray["SENSOR_SECOND_INPUT"] 
            );
            
            $paramListserverAcqSensor[] = array (
                "name"  => "direct_read," . $elemOfArray["SENSOR_INDEX"] . ",value2",
                "value" => $elemOfArray["SENSOR_SECOND_VALUE"] 
            );
            
            $paramListserverAcqSensor[] = array (
                "name"  => "direct_read," . $elemOfArray["SENSOR_INDEX"] . ",type",
                "type" => $elemOfArray["SENSOR_TYPE"] 
            );
            
        }
    }
    create_conf_XML($sd_card . "/serverAcqSensor/conf.xml" , $paramListserverAcqSensor);
    
    // Server plug update
    $paramListServerPlugUpdate[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverPlugUpdate']
    );

    $paramListServerPlugUpdate[] = array (
        "name" => "wireless_freq_plug_update",
        "value" => "$updatefrequency"
    );


    $alarmenable    = get_configuration("ALARM_ACTIV",$main_error);
    $alarmvalue     = get_configuration("ALARM_VALUE",$main_error);

    $alarmvalue=$alarmvalue*100;
    while(strlen($alarmvalue)<4) {
        $alarmvalue="0$alarmvalue";
    }

    $paramListServerPlugUpdate[] = array (
        "name" => "alarm_activ",
        "value" => "$alarmenable"
    );

    $paramListServerPlugUpdate[] = array (
        "name" => "alarm_value",
        "value" => "$alarmvalue"
    );

    $paramListServerPlugUpdate[] = array (
        "name" => "alarm_sensor",
        "value" => "T"
    );

    $paramListServerPlugUpdate[] = array (
        "name" => "alarm_sens",
        "value" => "+"
    );


    // Add network slave
    //  <item name="module_CULTIPI,ip,0" ip="192.168.1.10" />
    if ($GLOBALS['CULTIPI']['USE_REMOTE_SLAVE'] == 1)
    {
        for($index = 0 ; $index < $GLOBALS['CULTIPI']['REMOTE_NB_SLAVE']; $index++)
        {
            $paramListServerPlugUpdate[] = array (
                "name" => "module_CULTIPI,ip," . $index,
                "ip" => $GLOBALS['CULTIPI']['REMOTE_SLAVE']["IP_" . $index]
            );
        }
    }
    create_conf_XML($sd_card . "/serverPlugUpdate/conf.xml" , $paramListServerPlugUpdate);
    
    // Server histo
    $paramListServerHisto[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverHisto']
    );
    $paramListServerHisto[] = array (
        "name" => "logPeriode",
        "valInSec" => $recordfrequency*60
    );
    $paramListServerHisto[] = array (
        "name" => "pathMySQL",
        "path" => "/usr/bin/mysql"
    );

    create_conf_XML($sd_card . "/serverHisto/conf.xml" , $paramListServerHisto);
    
    // Server log
     $paramListServerLog[] = array (
        "name" => "logPath",
        "logfile" => "/var/log/cultipi"
    );

    $paramListServerLog[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverLog']
    );

    create_conf_XML($sd_card . "/serverLog/conf.xml" , $paramListServerLog);

    
    $program = "";
    $conf_uptodate = true;

    $program_index = array();
    program\get_program_index_info($program_index);
   

    $confsave_prog=true;
    foreach ($program_index as $key => $value) {
        // Read from database program
        $program = create_program_from_database($main_error,$value['program_idx']);

        if ($GLOBALS['MODE'] == "cultipi") {
            $fileName = $sd_card . "/serverPlugUpdate/prg/" . "plu" . $value['plugv_filename'];
        } else {
            $fileName = "${sd_card}/cnf/prg/" . "plu" . $value['plugv_filename'];
        }

        if(!compare_program($program,$fileName)) {
            $conf_uptodate=false;

            if(!save_program_on_sd($fileName,$program)) {  
                $confsave_prog=false;
            }
        }
    }

    //For plugv
    $program = create_program_from_database($main_error);

    if ($GLOBALS['MODE'] == "cultipi") {
        $fileName = $sd_card . "/serverPlugUpdate/prg/" . "plugv";
    } else {
        $fileName = "${sd_card}/cnf/prg/" . "plugv";
    }
    
    if(!compare_program($program,$fileName)) {
        $conf_uptodate=false;

        if(!save_program_on_sd($fileName,$program)) {
            $confsave_prog=false;
        }
    }

    if(!$confsave_prog) {
        $main_error_tab[]=__('ERROR_WRITE_PROGRAM');
        return ERROR_WRITE_PROGRAM;
    }

    if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") { 
        $ret_firm=check_and_copy_firm($sd_card);
        if(!$ret_firm) {
            $main_error_tab[]=__('ERROR_COPY_FIRM'); 
            return ERROR_COPY_FIRM;
        } else if($ret_firm==1) {
            $conf_uptodate=false;
        }
    }

    if(!compare_pluga($sd_card)) {
        $conf_uptodate=false;
        if(!write_pluga($sd_card,$main_error)) {
            $main_error_tab[]=__('ERROR_COPY_PLUGA');
            return ERROR_COPY_PLUGA;
        }
    }

    $plugconf = create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
    if(count($plugconf)>0) {
        if(!compare_plugconf($plugconf,$sd_card)) {
            $conf_uptodate=false;
            if(!write_plugconf($plugconf,$sd_card)) {
                $main_error_tab[]=__('ERROR_COPY_PLUG_CONF');
                return ERROR_COPY_PLUG_CONF;
            }
        }
    }

    if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") {
        if(!check_and_copy_index($sd_card)) {
            $main_error_tab[]=__('ERROR_COPY_INDEX');
            return ERROR_COPY_INDEX;
        }
    }

    $data=array();
    calendar\read_event_from_db($data);
    $plgidx=create_plgidx($data);
    if(count($plgidx)>0) {
        if(!compare_plgidx($plgidx,$sd_card)) {
            $conf_uptodate=false;
            if(!write_plgidx($plgidx,$sd_card)) {
                $main_error_tab[]=__('ERROR_COPY_PLGIDX');
                return ERROR_COPY_PLGIDX;
            }
        }
    } else {
        if(!check_and_copy_plgidx($sd_card)) {
             $main_error_tab[]=__('ERROR_COPY_TPL');
             return ERROR_COPY_TPL;
        }
    }


    if(!$conf_uptodate) {
        // Infor user that programms have been updated
        $main_info_tab[]=__('UPDATED_PROGRAM');
        return 0;
    }
    
    // Conf was up to date
    return 1; 
}
// }}}


// {{{ compare_pluga()
// ROLE compare pluga and data from databases to check if the file is up to date
// IN   $sd_card      sd card path to save data
// RET false is there is something to write, true else
function compare_pluga($sd_card) {
    $out  = array();
    

    $file = $sd_card . "/serverPlugUpdate/plg/pluga";

    // Check if the file exists
    if(is_file($file)) {
        $nb=0;

        $pluga = Array();
        $nb_plug=get_configuration("NB_PLUGS",$out);
        while(strlen($nb_plug)<2) {
            $nb_plug = "0$nb_plug";
        }

         $pluga[] = $nb_plug;
         for($i=0;$i<$nb_plug;$i++) {
         
            // Get power of the plug
            $tmp_power_max = get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
            
            // Get module of the plug
            $tmp_MODULE = get_plug_conf("PLUG_MODULE",$i+1,$out);
            if ($tmp_MODULE == "") 
                $tmp_MODULE = "wireless";
            
            // Get module number of the plug
            $tmp_NUM_MODULE = get_plug_conf("PLUG_NUM_MODULE",$i+1,$out);
            if ($tmp_NUM_MODULE == "")
                $tmp_NUM_MODULE = 1;

            // Get module options of the plug
            $tmp_MODULE_OPTIONS = get_plug_conf("PLUG_MODULE_OPTIONS",$i+1,$out);

            // Get module output used
            $tmp_MODULE_OUTPUT = get_plug_conf("PLUG_MODULE_OUTPUT",$i+1,$out);
            if ($tmp_MODULE_OUTPUT == "") 
                $tmp_MODULE_OUTPUT = 1;

            // Create adress for this plug
            $tmp_pluga = 0;
            switch ($tmp_MODULE) {
                case "wireless":
                    if ($tmp_power_max == "3500") {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT_3500W'][$i];
                    } else {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT'][$i];
                    }
                    break;
                case "direct":
                    // Direct plug case (Adresse 50 --> 58)
                    $tmp_pluga = $tmp_MODULE_OUTPUT + 49;
                    break;
                case "mcp230xx":
                    // MCP plug case 
                    // Module 1 : (Adresse 60 --> 67)
                    // Module 2 : (Adresse 70 --> 77)
                    // Module 3 : (Adresse 80 --> 87)
                    $tmp_pluga = 60 + 10 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "dimmer":
                    // Dimmer plug case 
                    // Module 1 : (Adresse 90 --> 93)
                    // Module 2 : (Adresse 95 --> 98)
                    // Module 3 : (Adresse 100 --> 103)
                    $tmp_pluga = 90 + 5 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "network":
                    $tmp_pluga = 1000 + 16 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "xmax":
                    // xmax plug case 
                    // Module 1 : (Adresse 105 --> 108)
                    $tmp_pluga = 105 + $tmp_MODULE_OUTPUT - 1;
                    break;                    
            }

            while(strlen($tmp_pluga)<3) {
                $tmp_pluga = "0$tmp_pluga";
            }

            $pluga[] = $tmp_pluga;
        }

        $nbdata = count($pluga);

        if(count($pluga)>0) {
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                $buffer=trim($buffer);

                if(!empty($buffer)) {
                  if(strcmp($pluga[$nb],$buffer)!=0) {
                     return false;
                  }
                  $nb=$nb+1;

                } elseif($nb==$nbdata) {
                  return true;
                } else {
                  return false;
                }
            }
            return true;
       }
    }
    return false;
}
// }}}


// {{{ compare_plgidx()
// ROLE compare plgidx and data from databases to check if the file is up to date
// IN   $sd_card      sd card path to save data
//      $ data        data to be compared to
// RET false is there is something to write, true else
function compare_plgidx($data,$sd_card) {

    $file = $sd_card . "/serverPlugUpdate/prg/plgidx";


    if(!is_file($file)) return false;

    $plgidx=@file("$file");
    if((count($data))!=(count($plgidx))) return false;

    for($i=0;$i<count($data);$i++) {
        if(strcmp(trim(html_entity_decode($data[$i])),trim(html_entity_decode($plgidx[$i])))!=0) return false;
    }
    return true;
}
// }}}


// {{{ write_pluga()
// ROLE write plug_a into the sd card
// IN   $sd_card        the sd card to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_pluga($sd_card,&$out) {

    $file = $sd_card . "/serverPlugUpdate/plg/pluga";

    if($f=@fopen($file,"w+")) {
        $pluga="";
        $nb_plug=get_configuration("NB_PLUGS",$out);
        while(strlen("$nb_plug")<2) {
            $nb_plug="0$nb_plug";
        }
        $pluga = $nb_plug . "\r\n";
      
        for($i=0;$i<$nb_plug;$i++) {
        
         
            // Get power of the plug
            $tmp_power_max = get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
            
            // Get module of the plug
            $tmp_MODULE = get_plug_conf("PLUG_MODULE",$i+1,$out);
            if ($tmp_MODULE == "") 
                $tmp_MODULE = "wireless";
            
            // Get module number of the plug
            $tmp_NUM_MODULE = get_plug_conf("PLUG_NUM_MODULE",$i+1,$out);
            if ($tmp_NUM_MODULE == "")
                $tmp_NUM_MODULE = 1;

            // Get module options of the plug
            $tmp_MODULE_OPTIONS = get_plug_conf("PLUG_MODULE_OPTIONS",$i+1,$out);

            // Get module output used
            $tmp_MODULE_OUTPUT = get_plug_conf("PLUG_MODULE_OUTPUT",$i+1,$out);
            if ($tmp_MODULE_OUTPUT == "") 
                $tmp_MODULE_OUTPUT = 1;

            // Create adress for this plug
            $tmp_pluga = 0;
            switch ($tmp_MODULE) {
                case "wireless":
                    if ($tmp_power_max == "3500") {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT_3500W'][$i];
                    } else {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT'][$i];
                    }
                    break;
                case "direct":
                    // Direct plug case (Adresse 50 --> 58)
                    $tmp_pluga = $tmp_MODULE_OUTPUT + 49;
                    break;
                case "mcp230xx":
                    // MCP plug case 
                    // Module 1 : (Adresse 60 --> 67)
                    // Module 2 : (Adresse 70 --> 77)
                    // Module 3 : (Adresse 80 --> 87)
                    $tmp_pluga = 60 + 10 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "dimmer":
                    // Dimmer plug case 
                    // Module 1 : (Adresse 90 --> 93)
                    // Module 2 : (Adresse 95 --> 98)
                    // Module 3 : (Adresse 100 --> 103)
                    $tmp_pluga = 90 + 5 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "network":
                    $tmp_pluga = 1000 + 16 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    break;
                case "xmax":
                    // xmax plug case 
                    // Module 1 : (Adresse 105 --> 108)
                    $tmp_pluga = 105 + $tmp_MODULE_OUTPUT - 1;
                    break;                    
            }

            while(strlen($tmp_pluga)<3) {
                $tmp_pluga = "0$tmp_pluga";
            }


            $pluga = $pluga . $tmp_pluga . "\r\n";
      }

      if(!@fwrite($f,"$pluga")) {
          fclose($f);
          return false;
      }
   } else {
        return false;
   }
   fclose($f);
   return true;
}
// }}}


// {{{ write_plugconf()
// ROLE write plug_configuration into the sd card
// IN   $data           array containing datas to write
//      $sd_card        the sd card to be written
// RET false is an error occured, true else
function write_plugconf($data,$sd_card) {

    $path = $sd_card . "/serverPlugUpdate/plg/";


   for($i=0;$i<count($data);$i++) {
      $nb=$i+1;
      if($nb<10) {
         $file = $path . "plug0" . $nb;
      } else {
         $file = $path . "plug" . $nb;
      }

      if($f=@fopen("$file","w+")) {
         if(!@fputs($f,"$data[$i]"."\r\n")) {
            fclose($f);
            return false;
         }
      } else {
         return false;
      }
      fclose($f);
   }
   return true;
}
// }}}


// {{{ write_plgidx()
// ROLE write plgidx into the sd card
// IN   $data           array containing datas to write
//      $sd_card        the sd card to be written
// RET false is an error occured, true else
function write_plgidx($data,$sd_card) {

    $file = $sd_card . "/serverPlugUpdate/prg/plgidx";

   if($f=@fopen("$file","w+")) {
      if(!@fputs($f,implode("\r\n", $data))) {
            return false;
      }
      fclose($f);
      return true;
    }
    return false;
}
// }}}


// {{{ compare_plugconf()
// ROLE compare plug's configuration with the database
// IN   $data    array containing plugconf datas
//      sd_card     path to the sd_card
// OUT false is there is a difference, true else
function compare_plugconf($data, $sd_card="") {

    $path = $sd_card . "/serverPlugUpdate/plg/";


   for($i=0;$i<count($data);$i++) {
        $nb=$i+1;
        if($nb<10) {
            $file= $path . "plug0$nb";
        } else {
            $file= $path . "plug$nb";
        }

        if(!is_file($file)) return false;
        $tmp=explode("\r\n",$data[$i]);
        foreach($tmp as $dt) {
           $new_tmp[]=trim($dt);
        }

        $tmp=$new_tmp;

        $buffer=@file("$file");
        $buffer=array_filter($buffer);

        foreach($buffer as $bf) {
           $new_buffer[]=trim($bf);
        }

        $buffer=$new_buffer;

        if(count($buffer)!=count($tmp)) return false;

        for($j=0;$j<count($buffer);$j++) {
            if(strcmp($tmp[$j],$buffer[$j])!=0) {
                    return false;
            }
        }

        unset($tmp);
        unset($buffer);
   }
   return true;
}
// }}}



// {{{ check_and_copy_plgidx()
// ROLE check if cnf/prg/plgidx exists
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_plgidx($sd_card="") {
    $path="";

    //On essaye de déterminer le chemin du fichier de référence:
    if(is_file("tmp/cnf/prg/plgidx")) {
        $path="tmp/cnf/prg/plgidx";
    } else if(is_file("../tmp/cnf/prg/plgidx")) {
        $path="../tmp/cnf/prg/plgidx";
    } else if(is_file("../../tmp/cnf/prg/plgidx")) {
        $path="../../tmp/cnf/prg/plgidx";
    } else if(is_file("../../../tmp/cnf/prg/plgidx")) {
        $path="../../../tmp/cnf/prg/plgidx";
    }


    $dest="$sd_card/serverPlugUpdate/prg/plgidx";

    //Si le fichier sur la carte SD n'existe pas:
    if(!is_file("$sd_card/cnf/prg/plgidx")) {
        //Si le chemin de référence a été trouvé:
        if(strcmp("$path","")!=0) {
            if(!@copy("$path", "$dest")) {
                //Si la copie n'a pas réussie:
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        if(strcmp("$path","")==0) {
           //Si on ne trouve pas le fichier de référence:
           return false;
        } else {
           //On compare si le fichier de référence et le fichier sur la carte SD sont différents:
           if(filesize("$path")!=filesize("$dest")) {
               if(!@copy("$path", "$dest")) {
                   return false;
                } else {
                   return true;
                }
           } else {
               return true;
           }
        }
    }
}
// }}}


// {{{ check_and_copy_id()
// ROLE check if cnf/id file has to be updated
// IN  $sd_card     the sd card pathname 
//     $id          the saved id from the database
// RET false if the id file has to be updated, true else
function check_and_copy_id($sd_card,$id="") {
    if(strcmp("$id","")==0) return true;

    if(is_file("$sd_card/cnf/id")) {
        $id_file=file("$sd_card/cnf/id");
        if(count($id_file)==1) {
            $id_file=trim($id_file[0]);
        } else {
            $id_file=0;
        }
    } else {
        $id_file=0;
    }

    if($id_file!=$id) {
        while(strlen($id)<5) $id="0$id";
        $handle=fopen("$sd_card/cnf/id",'w');
        fwrite($handle,"$id");
        fclose($handle);
        return false;
    }
    return true;
}
// }}}


// {{{ check_and_copy_index()
// ROLE check if the index file exists and if not, create it 
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_index($sd_card) {
    $path="";

    if(is_file("tmp/logs/index")) {
        $path="tmp/logs/index";
    } else if(is_file("../tmp/logs/index")) {
        $path="../tmp/logs/index";
    } else if(is_file("../../tmp/logs/index")) {
        $path="../../tmp/logs/index";
    } else if(is_file("../../../tmp/logs/index")) {
        $path="../../../tmp/logs/index";
    }

    if(!is_file("$sd_card/logs/index")) {
        if(strcmp("$path","")!=0) {
            if(!@copy("$path", "$sd_card/logs/index")) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        if(strcmp("$path","")==0) {
           return false;
        } else {
           if(filesize("$path")!=filesize("$sd_card/logs/index")) {
               if(!@copy("$path", "$sd_card/logs/index")) {
                   return false;
                } else {
                   return true;
                }
           } else {
               return true;
           }
        }
    }
}
// }}}


// {{{ clean_index_file()
// ROLE force the cleaning of the index file
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function clean_index_file($sd_card) {
    $path="";

    if(is_file("tmp/logs/index")) {
        $path="tmp/logs/index";
    } else if(is_file("../tmp/logs/index")) {
        $path="../tmp/logs/index";
    } else if(is_file("../../tmp/logs/index")) {
        $path="../../tmp/logs/index";
    } else if(is_file("../../../tmp/logs/index")) {
        $path="../../../tmp/logs/index";
    }

    if(strcmp("$path","")==0) return false;
    if(!is_dir("$sd_card/logs/")) return false;


    if(!@copy("$path", "$sd_card/logs/index")) return false;
    return true;
}
// }}}


// {{{ find_informations()
// ROLE find some informations from the log.txt file
// IN    $ret       array to return containing informations
//       $log_file  path to the log file
// RET   none
function find_informations($log_file,&$ret) {

    // If file does not exists, return false
    if(!file_exists($log_file)) 
        return false;
        
    // Init return array
    $ret["cbx_id"]      = "";
    $ret["firm_version"]= "";
    $ret["log"]         = "";

    // Read the file
    $buffer_array = file($log_file);
    
    // Foreach line
    foreach($buffer_array as $buffer) {
    
        // Remove space before and after
        $buffer=trim($buffer);

        // If th line is empty, reurn
        if($buffer == "") 
            break;

        // Init log with buffer
        if(strcmp($ret["log"],"")==0) {
            $ret["log"] = $buffer;
        } else {
            $ret["log"] = $ret["log"] . "#" . $buffer;
        }

        switch (substr($buffer,14,1)) {
            case 'I':
                $ret["cbx_id"] = substr($buffer,16,5);
                break;
            case 'V':
                $ret["firm_version"] = substr($buffer,16,7); 
                break;
        }
    }
    
    return true;
}
// }}}


// {{{ check_sd_card()
// ROLE check if the soft can write on a sd card
//  IN      $sd        the sd_card path to be checked
// RET true if we can, false else
function check_sd_card($sd="") {

    // Check to open in write mode
    if($f=@fopen("$sd/test.txt","w+")) {
        // Close file
        fclose($f);
        
        // Delete file
        if(!@unlink("$sd/test.txt")) 
            return false;
        
        // SD card is writable
        return true;
    } else {
        // Not openable in write mode
        return false;
    }
}
// }}}

// {{{ create_conf_XML()
// ROLE Used to creat a conf file
// IN      $file        Path for the conf file
// IN      $paramList       List of params
// RET true if we can, false else
function create_conf_XML($file, $paramList) {

    // Check if directory exists
    if(!is_dir(dirname($file)))
        mkdir(dirname($file));

    // Open in write mode
    $fid = fopen($file,"w+");
    
    // Add header
    fwrite($fid,'<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>' . "\r\n");
    fwrite($fid,'<conf>'. "\r\n");
    
    // Foreach param to write, add it to the file
    foreach ($paramList as $elemOfArray) {
        
        $str = "    <item ";
        
        foreach ($elemOfArray as $key => $value) {
            $str .= $key . '="' . $value . '" ';
        }
        
        $str .= "/>". "\r\n";
    
        fwrite($fid,$str);
    }

    // Add Footer
    fwrite($fid,'</conf>'. "\r\n");
    
    // Close file
    fclose($fid);
    
    return true;
}
// }}}

?>
