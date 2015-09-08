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

    // Check if SD card has been found
    if(empty($sd_card) || !isset($sd_card)  || $sd_card == "")
    {
        $main_error_tab[]=__('ERROR_SD_CARD');
        return ERROR_SD_NOT_FOUND;
    }

    // Check and update path
    $logs = "$sd_card/logs";
    $cnf  = "$sd_card/cnf";
    $plg  = "$cnf/plg";
    $prg  = "$cnf/prg";
    $bin  = "$sd_card/bin";
    $recordfrequency = "1"; 
    $updatefrequency = "1"; 


    // Alert user than an SD card was found
    $main_info_tab[]=__('INFO_SD_CARD') . ": $sd_card";

    // Check if SD card can be writable
    if(!check_sd_card($sd_card)) return ERROR_WRITE_SD;
    
    if(!is_dir($logs)) mkdir($logs);
    if(!is_dir($cnf)) mkdir($cnf);
    if(!is_dir($plg)) mkdir($plg);
    if(!is_dir($prg)) mkdir($prg);
    if(!is_dir($bin)) mkdir($bin);

    
    $program = "";
    $conf_uptodate = true;

    $program_index = array();
    program\get_program_index_info($program_index);
   

    $confsave_prog=true;
    foreach ($program_index as $key => $value) {
        // Read from database program
        $program = create_program_from_database($main_error,$value['program_idx']);


        $fileName = "${sd_card}/cnf/prg/" . "plu" . $value['plugv_filename'];

        if(!compare_program($program,$fileName)) {
            $conf_uptodate=false;

            if(!save_program_on_sd($fileName,$program)) {  
                $confsave_prog=false;
            }
        }
    }

    //For plugv
    $program = create_program_from_database($main_error);

    $fileName = "${sd_card}/cnf/prg/" . "plugv";

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


    $ret_firm = check_and_copy_firm($sd_card);
    if(!$ret_firm) {
        $main_error_tab[]=__('ERROR_COPY_FIRM'); 
        return ERROR_COPY_FIRM;
    } else if($ret_firm==1) {
        $conf_uptodate=false;
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


    if(!check_and_copy_index($sd_card)) {
        $main_error_tab[]=__('ERROR_COPY_INDEX');
        return ERROR_COPY_INDEX;
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


    // Read value on sd Card
    if(!$force_rtc_offset) {
        $sdConfRtc = read_sd_conf_file($sd_card,"rtc_offset");
        $sdConfRtc = get_decode_rtc_offset($sdConfRtc);

        // Update database
        insert_configuration("RTC_OFFSET",$sdConfRtc,$main_error);
    }


    $powerfrequency = "1";
    $alarmenable    = get_configuration("ALARM_ACTIV",$main_error);
    $alarmvalue     = get_configuration("ALARM_VALUE",$main_error);
    $resetvalue     = get_configuration("RESET_MINMAX",$main_error);
    $rtc            = get_rtc_offset(get_configuration("RTC_OFFSET",$main_error));
    $enableled      = get_configuration("ENABLE_LED",$main_error);
    if($updatefrequency == "-1") {
        $updatefrequency="0";
    }

    if(!compare_sd_conf_file($sd_card,
                         $recordfrequency,
                         $updatefrequency,
                         $powerfrequency,
                         $alarmenable,
                         $alarmvalue,
                         $resetvalue,
                         $rtc,
                         $enableled))
    {
        $conf_uptodate=false;
        if(!write_sd_conf_file($sd_card,
                           $recordfrequency,
                           $updatefrequency,
                           $powerfrequency,
                           $alarmenable,
                           $alarmvalue,
                           $resetvalue,
                           $rtc,
                           $enableled,
                           $main_error))
        {
            $main_error_tab[]=__('ERROR_WRITE_SD_CONF');
            return ERROR_WRITE_SD_CONF;
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

// {{{ sd_card_update_log_informations()
// ROLE copy an empty file to a new file destination
// IN $sd_card     destination path
// RET true if the copy is errorless, false else
function sd_card_update_log_informations ($sd_card="") {

    if(empty($sd_card) || !isset($sd_card) || $sd_card == "") return ERROR_SD_NOT_FOUND;


    // The informations part to send statistics to debug the cultibox: 
    //      if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations will be send for debug
    $informations["cbx_id"]="";
    $informations["firm_version"]="";
    $informations["log"]="";

    
    // Read log.txt file and clear it
    find_informations("$sd_card/log.txt",$informations);
    copy_template_file("empty_file_big.tpl", "$sd_card/log.txt");

    // If informations are defined in log.txt copy them into database
    if($informations["cbx_id"] != "")  
        insert_informations("cbx_id",$informations["cbx_id"]);
        
    if($informations["firm_version"] != "") 
        insert_informations("firm_version",$informations["firm_version"]);
        
    if($informations["log"] != "") 
        insert_informations("log",$informations["log"]);

    return 1;
}


// {{{ compare_pluga()
// ROLE compare pluga and data from databases to check if the file is up to date
// IN   $sd_card      sd card path to save data
// RET false is there is something to write, true else
function compare_pluga($sd_card) {
    $out  = array();

    $file = "${sd_card}/cnf/plg/pluga";


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
                case "pwm":
                    // pwm plug case 
                    // Module 1 : (Adresse 31 --> 38)
                    // Module 2 : (Adresse 39 --> 44)
                    // Module 3 : (Adresse 45 --> 49)
                    $base = 31;
                    if ($tmp_NUM_MODULE == 2)
                    {
                        $base = 39;
                    }
                    if ($tmp_NUM_MODULE == 3)
                    {
                        $base = 45;
                    }
                    $tmp_pluga = $base + $tmp_MODULE_OUTPUT - 1;
                    break; 
                case "bulcky":
                    // bulcky plug case 
                    // Module 1 : (Adresse 2000 --> 2004)
                    // Module 2 : (Adresse 2010 --> 2014)
                    // Module 3 : (Adresse 2020 --> 2024)
                    //$tmp_pluga = 2000 + 10 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUTPUT - 1;
                    $tmp_pluga = 2000 + $tmp_MODULE_OUTPUT - 1;
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

    if ($GLOBALS['MODE'] == "cultipi") {
        $file = $sd_card . "/serverPlugUpdate/prg/plgidx";
    } else {
        $file = "${sd_card}/cnf/prg/plgidx";
    }

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

    if ($GLOBALS['MODE'] == "cultipi") {
        $file = $sd_card . "/serverPlugUpdate/plg/pluga";
    } else {
        $file="$sd_card/cnf/plg/pluga";
    }
    

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

    if ($GLOBALS['MODE'] == "cultipi") {
        $path = $sd_card . "/serverPlugUpdate/plg/";
    } else {
        $path = "${sd_card}/cnf/plg/";
    }

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

    if ($GLOBALS['MODE'] == "cultipi") {
        $file = $sd_card . "/serverPlugUpdate/prg/plgidx";
    } else {
        $file = "${sd_card}/cnf/prg/plgidx";
    }

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

    if ($GLOBALS['MODE'] == "cultipi") {
        $path = $sd_card . "/serverPlugUpdate/plg/";
    } else {
        $path = "${sd_card}/cnf/prg/";
    }

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

    if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") {
        $dest="$sd_card/cnf/prg/plgidx";
    } else {
        $dest="$sd_card/serverPlugUpdate/prg/plgidx";
    }

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

// {{{ write_calendar()
// ROLE save calendar informations into the SD card
// IN $sd_card         sd card location
//    $data            data to write into the sd card (come from calendar\read_event_from_db )
//    $out             error or warning messages
//    $start           write calendar between two dates (ms format)
//    $end             if start and end are not set: write full calendar (ms format)
// RET false if an error occured, true else
function write_calendar($sd_card,$data,&$out,$start="",$end="") {

    // If sd card is not defined, return
    if(!isset($sd_card) || empty($sd_card)) {
        return false;
    }

    $status=true;

    // If there are some events
    if(count($data)>0) {
        // If not defined Use today
        if ($start == "")
            $date =  strtotime(date("Y-m-d"));
        else
            $date =  $start;

        // Use today + 3 month
        if ($end == "")
            $endSearch  = strtotime("+3 months", $date);
        else
            $endSearch =  $end;

        while($date <= $endSearch)
        {
        
            $val = calendar\concat_entries($data,date("Y-m-d",$date));

            // Create filename
            $month = date("m",$date);
            $day = date("d",$date);
            $file = "$sd_card/logs/$month/cal_$day";

            // If there is something to write
            if($val) {
                // If file can be opened
                if($fid = fopen($file,"w+")) {
                
                    // If there is an Lune event, write symbols at top
                    foreach($val as $value) {
                        // Search if symbols exists
                        if (array_key_exists("cbx_symbol",$value))
                        {
                            $outSymbol = "";
                            
                            // Foreach symbol, add it
                            foreach (explode(" ",$value['cbx_symbol']) as $symbol)
                            {
                                // COnvert into binary string
                                $outSymbol = $outSymbol . hex2bin(substr($symbol,-2));
                            }
                            
                            // rite t
                            fputs($fid,$outSymbol . "\r\n");
                        }
                    }
                
                    // Foreach event to write
                    foreach($val as $value) {
                    
                        $sub  =  clean_calendar_message($value["subject"]);
                        $desc =  clean_calendar_message($value["description"]);

                        if(!fputs($fid, $sub . "\r\n")) 
                            $status=false;
                            
                        if(!fputs($fid, $desc . "\r\n")) 
                            $status=false;
                            
                    }
                    
                    // Close file
                    fclose($fid);
                } else {  
                    $status=false;
                }
            } else {
                // Delete file if present
                
                if (file_exists($file))               
                    unlink($file);
                    
            }
        
            // Incr date
            $date = strtotime("+1 day", $date);
            
            // Clear val
            unset($val);
            
        }
    }
 
    return $status;
}
//}}}

?>
