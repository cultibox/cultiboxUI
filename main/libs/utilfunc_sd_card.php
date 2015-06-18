<?php

if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") {
    require_once('utilfunc_cbx.php');
} else {
    require_once('utilfunc_cultipi.php');
}


// {{{ get_error_sd_card_update_message()
// ROLE transoform a check sd card configuration code into a an error message
// IN   $id    id of the message
// RET "" if there is no message to display, the message else
function get_error_sd_card_update_message($id=0) {
    switch($id) { //Id to check to get the current error message:
        case ERROR_COPY_FILE:  return __('ERROR_COPY_FILE');
        case ERROR_WRITE_PROGRAM:  return __('ERROR_WRITE_PROGRAM');
        case ERROR_COPY_FIRM:  return __('ERROR_COPY_FIRM');
        case ERROR_COPY_PLUGA:  return __('ERROR_COPY_PLUGA');
        case ERROR_COPY_PLUG_CONF:  return __('ERROR_COPY_PLUG_CONF');
        case ERROR_COPY_TPL:  return __('ERROR_COPY_TPL');
        case ERROR_COPY_INDEX:  return __('ERROR_COPY_INDEX');
        case ERROR_WRITE_SD_CONF: return __('ERROR_WRITE_SD_CONF');
        case ERROR_WRITE_SD: return __('ERROR_WRITE_SD');
        case ERROR_COPY_PLGIDX: return __('ERROR_COPY_PLGIDX');
        default: return "";
    }
}
// }}}


// {{{ copy_template_file()
// ROLE copy an empty file to a new file destination
// IN  $name     name of the file to be copied
//     $file     destination of the file
// RET true if the copy is errorless, false else
function copy_template_file($name="", $file) {
   if(strcmp("$name","")==0) return false;
   //Trying to find the template file from the current path:
   if(is_file("main/templates/data/$name")) {
        $filetpl = "main/templates/data/$name";
   } else if(is_file("../main/templates/data/$name")) {
        $filetpl = "../main/templates/data/$name";
   } else if(is_file("../../main/templates/data/$name")) {
        $filetpl = "../../main/templates/data/$name";
   } else {
        $filetpl = "../../../main/templates/data/$name";
   }

   //Copying the template file if one has been found:
   if(!@copy($filetpl, $file)) return false;
   return true;
}
//}}}


// {{{ get_sd_card()
// ROLE get the sd card place to record configuration
// IN  $hdd     list of hdd available which could be configured as cultibox SD card
// RET false if nothing is found, the sd card place else
function get_sd_card(&$hdd="") {
    //For Linux
    $ret=false;
    $dir="";
    $os=php_uname('s');
    //Retrieve SD path depending of the current OS:
    switch($os) {
        case 'Linux':
            //In Ubuntu Quantal mounted folders are now in /media/$USER directory
            $user=get_current_user();
            if((isset($user))&&(!empty($user))) {
                $dir="/media/".$user;
                if(is_dir($dir)) {
                    $rep = @opendir($dir);
                    if($rep) {
                        while ($f = @readdir($rep)) {
                            if(is_dir("$dir/$f")) {
                                if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                    $hdd[]="$dir/$f";
                                    if(check_cultibox_card("$dir/$f")) {
                                        $ret="$dir/$f";
                                    }
                                }
                            }
                        }
                        closedir($rep);
                    }
                }
            }
            break;
        case 'Mac':
        case 'Darwin':
            $dir="/Volumes";
            if(is_dir($dir)) {
                $rep=@opendir($dir);
                if($rep) {
                    while ($f=@readdir($rep)) {
                        if(is_dir("$dir/$f")) {
                            if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                $hdd[]="$dir/$f";
                                if(check_cultibox_card("$dir/$f")) {
                                    $ret="$dir/$f";
                                }
                            }
                        }
                    }
                    closedir($rep);
                }
            }
            break;
        case 'Windows NT':
        
            // There is a bug in php, this is why we stop and restart session
            // For mor information, see :
            // http://php.net/manual/fr/function.exec.php : Comment write by  "elwiz at 3e dot pl"
            // https://bugs.php.net/bug.php?id=44942
            session_write_close();
            $vol=`MountVol`;
            session_start();

            $vol=explode("\n",$vol);
            $dir=Array();
            foreach($vol as $value) {
                // repérer les deux derniers segments du nom de l'hôte
                preg_match('/[D-Z]:/', $value,$matches);
                foreach($matches as $val) {
                    $dir[]=$val;
                }
            }

            foreach($dir as $disque) {
                $check=`dir $disque`;
                if(strlen($check)>0) {
                    $hdd[]="$disque";
                    if(check_cultibox_card("$disque")) {
                        $ret="$disque";
                    }
                }
            }

            break;
    }
    return $ret;
}
// }}}


// {{{ check_cultibox_card()
// ROLE check if the directory is a cultibox directory to write configuration
// IN   $dir         directory to check
// RET true if it's a cultibox directory, false else
function check_cultibox_card($dir="") {
/* TO BE DELETED */
   if((is_file("$dir/plugv"))&&(is_file("$dir/pluga"))&&(is_dir("$dir/logs"))) {
       if((is_file("$dir/plugv"))&&(is_file("$dir/pluga"))&&(is_dir("$dir/logs"))) {
            return true;
        }
   }
/* ********* */

    if((is_file("$dir/cnf/prg/plugv"))&&(is_file("$dir/cnf/plg/pluga"))&&(is_dir("$dir/logs"))) {
        return true;
    }
    
    return false;
}
// }}}


// {{{ save_program_on_sd()
// ROLE write programs into the sd card
// IN   $sd_card        path to the sd card to save datas
//      $program        the program to be save in the sd card 
// RET true if data correctly written, false else
function save_program_on_sd($file,$program) {
    $out=array();

    // Init out program file contants
    $prog="";
    $nbPlug=count($program);
    $shorten=false;

    // Check if there are some plugs
    if($nbPlug == 0)
        return false;
   
    // Limit nb plugs to max allowed
    if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
        if($nbPlug>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
            $nbPlug=$GLOBALS['PLUGV_MAX_CHANGEMENT'];
            $shorten=true;
        }
    } 

    // Complet nbPlug variable up to 3 digits
    while(strlen($nbPlug)<5)
        $nbPlug="0$nbPlug";
    
    // Add header of the file
    $prog=$nbPlug."\r\n";

    // If we have to reduce number of change
    if($shorten) {
        // For each line of the program add it to file
        for($i=0; $i<$nbPlug-1; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    } else {
        for($i=0; $i<$nbPlug; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    }

    // If the programm has been cut (too many change) add an last entry
    if($shorten) {
        $last=count($program)-1;
        $prog=$prog."$program[$last]"."\r\n";
    }

    // Write it on SD card
    if($f = @fopen($file,"w+")) {
        if(!@fwrite($f,"$prog")) 
        { 
            fclose($f);
            return false;
        }
            fclose($f);
    }

   return true;
}
// }}}


// {{{ compare_program()
// ROLE compare programs and data to check if they are up to date
// IN   $data         array containing datas to check
//      $sd_card      sd card path to save data
//      $file         file to be compared to
// RET false is there is something to write, true else
function compare_program($data,$file) {

    $out=array();

    if(is_file("${file}")) {
        $nb=0;
        //On compte le nombre d'entrée dans la base des programmes:
        $nbdata=count($data);

        //Si les changements de la base dépassent ceux de maximum définit, on coupe le tableau des programmes pour le faire
        //correspondre au nombre maximal
        if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
            if($nbdata>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
                $tmp_array=array_slice($data, 0, $GLOBALS['PLUGV_MAX_CHANGEMENT']-1);
                $tmp_array[]=$data[$nbdata-1];
                $data=$tmp_array;
                $nbdata=count($data);
            }
        }

        
         while(strlen($nbdata)<5) {
            $nbdata="0$nbdata";
         }

        if(count($data)>0) {
            //On récupère les informations du fichier courant plugv
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                  $buffer=trim($buffer); //On supprime les caractères invisibles
                  if(!empty($buffer)) {
                     if($nb==0) {
                        if(strcmp("$nbdata","$buffer")!=0) { //S'il s'agit de la première ligne, qui contient le nombre d'entrée, on compare le nombre d'entrée du fichier avec le nombre d'entrée du tableau
                         return false;
                        }
                     } else {
                        if(strcmp($data[$nb-1],$buffer)!=0) { //Sinon on compare le contenu du fichier et celui de la ligne correspondante dans le tableau
                           return false;
                        }
                     }
                     $nb=$nb+1;
                  } else if($nb==0) {
                    return false;
                  }
            }
            return true; //Tout est égal, on renvoie true
        }
    }
    return false;
}
// }}}


// {{{ create_plgidx()
// ROLE create plgidx file
// IN  $data            data to write into the sd card (come from calendar\read_event_from_db )
// RET array containing plgidx
function create_plgidx($data) {
    $plgidx = array();
    $return=array();

    // If there is not event , return false
    if(count($data) == 0) 
        return $return;

    // Open database connexion
    $db = \db_priv_pdo_start();
    
    // Foreach event
    foreach($data as $event)
    {
        // If this is a program index event
        if ($event['program_index'] != "")
        {

            // Query plugv filename associated
            try {
                $sql = "SELECT plugv_filename FROM program_index WHERE id = \"" . $event['program_index'] . "\";";
                $sth = $db->prepare($sql);
                $sth->execute();
                $res = $sth->fetch();
            } catch(\PDOException $e) {
                $ret=$e->getMessage();
            }
        
            //
            $today = strtotime(date("Y-m-d"));
            $nextYear  = strtotime("+1 year", strtotime(date("Y-m-d")));
        
            // Start date
            $date = $event['start_year'] . "-" . $event['start_month']  . "-" . $event['start_day'];
            // End date
            $end_date = $event['end_year'] . "-" . $event['end_month']  . "-" . $event['end_day'];
            
            while (strtotime($date) <= strtotime($end_date)) {

                // Save only for futur element
                if (strtotime($date) >= $today && strtotime($date) < $nextYear)
                    $plgidx[$date] = $res['plugv_filename'];
                  
                // Incr date                  
                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
            }
        }
    }

    // Close connexion
    $db = null;
    
    // For each day
    for ($month = 1; $month <= 13; $month++) 
    {
        for ($day = 1; $day <= 31; $day++) 
        {
            // Format day and month
            $monthToWrite = $month;
            if (strlen($monthToWrite) < 2) {
                $monthToWrite="0$monthToWrite";
            }
            
            $dayToWrite = $day;
            if (strlen($dayToWrite) < 2) {
                $dayToWrite="0$dayToWrite";
            }
            
            // Date to search in event
            $dateToSearch = date("Y") . "-" . $monthToWrite . "-" . $dayToWrite;
            
            $plugvToUse = "00";
            if (array_key_exists($dateToSearch, $plgidx)) {
                $plugvToUse = $plgidx[$dateToSearch];
                    
                if (strlen($plugvToUse) < 2)
                    $plugvToUse = "0$plugvToUse";
            }

            // Write the day
            $return[]=$monthToWrite . $dayToWrite . $plugvToUse;
        }
    }
    return $return;
}
//}}}


// {{{ compare_sd_conf_file()
// ROLE  compare conf file data with the database
// IN   $sd_card      location of the sd card to save data
//      $record_frequency   record frequency value
//      $update_frequency   update frequency value
//      $power_frequency    record of the power frequency value
//      $alarm_enable       enable or disable the alarm system
//      $alarm_value        value to trigger the alarm
//      $reset_value        value for the sensor's reset min/max
//      $rtc                RTC_OFFSET value
//      $enable_led         Allow backlight
// RET false if there is a difference, true else
function compare_sd_conf_file($sd_card="",
                              $record_frequency,
                              $update_frequency,
                              $power_frequency,
                              $alarm_enable,
                              $alarm_value,
                              $reset_value,
                              $rtc,
                              $enable_led) {

    if(!is_file($sd_card."/cnf/conf")) 
        return false;

    $file="${sd_card}/cnf/conf";

    $record=$record_frequency*60;
    $power=$power_frequency*60;
    $update="000$update_frequency";


    while(strlen($alarm_enable)<4) {
        $alarm_enable="0$alarm_enable";
    }

    $alarm_value=$alarm_value*100;
    while(strlen($alarm_value)<4) {
        $alarm_value="0$alarm_value";
    }

    while(strlen($record)<4) {
        $record="0$record";
    }

   while(strlen($power)<4) {
      $power="0$power";
   }

   while(strlen($rtc)<4) {
      $rtc="0$rtc";
   }
   
    $reset_value=str_replace(":","",$reset_value);
    if((strlen($reset_value)!=4)||($reset_value<0)) {
        $reset_value="0000";
    } 
   
    while(strlen($enable_led)<4) {
        $enable_led="0$enable_led";
    }
   
    $conf[]="PLUG_UPDATE:$update";
    $conf[]="LOGS_UPDATE:$record";
    $conf[]="POWR_UPDATE:$power";
    $conf[]="ALARM_ACTIV:$alarm_enable";
    $conf[]="ALARM_VALUE:$alarm_value";
    $conf[]="ALARM_SENSO:000T";
    $conf[]="ALARM_SENSS:000+";
    $conf[]="RTC_OFFSET_:$rtc";
    $conf[]="RESET_MINAX:$reset_value";
    $conf[]="ENABLE_LEDs:$enable_led";

    $buffer=@file("$file");

    if(count($conf)!=count($buffer)) 
        return false;
        
    for($nb=0;$nb<count($conf);$nb++) {
        if(strcmp(trim($conf[$nb]),trim($buffer[$nb]))!=0) {
            return false;
        }
    }
    return true;
}
// }}}

// {{{ read_sd_conf_file()
// ROLE   Read one variable of conffile
// IN   $sd_card      location of the sd card to save data
//      $variable      Variable to read    
//      $out                error or warning messages
// RET Value read
function read_sd_conf_file($sd_card,$variable,$out="") {
   // Check if sd card is defined
    if (empty($sd_card))
        return false;

    $file="$sd_card/cnf/conf";

    if(!is_file("$sd_card/cnf/conf")) return false;
    
    // Open file
    $fid = @fopen($file,"r+");
    $offset = "";
    
    switch ($variable) {
        case "update_plugs_frequency":
            $offset = 18 * 0 + 12;
            
            if ($value == -1)
                $value = 0;
            
            break;
        case "record_frequency":
            $offset = 18 * 1 + 12;
            
            $value = $value * 60;
            
            break;
        case "power_frequency":
            $offset = 18 * 2 + 12;
            
            $value = $value * 60;
            
            break;
        case "alarm_activ":
            $offset = 18 * 3 + 12;
            break; 
        case "alarm_value":
            $offset = 18 * 4 + 12;
            break; 
        case "rtc_offset":
            $offset = 18 * 7 + 12;
            break; 
        case "minmax":
            $offset = 18 * 8 + 12;
            break;             
    }
    
    $val = "";
    
    if(($offset != "") && ($fid)) {
        fseek($fid, $offset);
        $val = fread($fid,4);
    }
    
    // Close
    if($fid) fclose($fid);
    return $val;
    
}
//}}}


// {{{ write_sd_conf_file()
// ROLE   save configuration into the SD card
// IN   $sd_card      location of the sd card to save data
//   $record_frequency   record frequency value
//   $update_frequency   update frequency value
//   $power_frequency    record of the power frequency value
//   $alarm_enable       enable or disable the alarm system
//   $alarm_value        value to trigger the alarm
//   $reset_value        min/max reset value
//   $rtc                value for the RTC_OFFSET
//   $enable_led         Allow backlight of LCD screen
//   $out                error or warning messages
// RET false if an error occured, true else  
function write_sd_conf_file($sd_card,
                            $record_frequency=1,
                            $update_frequency=1,
                            $power_frequency=1,
                            $alarm_enable="0000",
                            $alarm_value="50.00",
                            $reset_value,
                            $rtc="0000",
                            $enable_led="0001",
                            $out="") {
   $alarm_senso="000T";
   $alarm_senss="000+";
   $record=$record_frequency*60;
   $power=$power_frequency*60;

   
    while(strlen($alarm_enable)<4) {
        $alarm_enable="0$alarm_enable";
    }
  
   $alarm_value=$alarm_value*100;
   while(strlen($alarm_value)<4) {
      $alarm_value="0$alarm_value";
   }


   while(strlen($record)<4) {
      $record="0$record";
   }

    while(strlen($power)<4) {
        $power="0$power";
    }

    while(strlen($rtc)<4) {
        $rtc="0$rtc";
    }

    $reset_value=str_replace(":","",$reset_value);
    if((strlen($reset_value)!=4)||($reset_value<0)) {
        $reset_value="0000";
    }
   
    while(strlen($enable_led)<4) {
        $enable_led="0$enable_led";
    }

   $update="000$update_frequency";
   $file="$sd_card/cnf/conf";
   $check=true;
   if($f=@fopen("$file","w+")) {
      if(!@fputs($f,"PLUG_UPDATE:$update\r\n")) $check=false;
      if(!@fputs($f,"LOGS_UPDATE:$record\r\n")) $check=false;
      if(!@fputs($f,"POWR_UPDATE:$power\r\n")) $check=false; 
      if(!@fputs($f,"ALARM_ACTIV:$alarm_enable\r\n")) $check=false;
      if(!@fputs($f,"ALARM_VALUE:$alarm_value\r\n")) $check=false;
      if(!@fputs($f,"ALARM_SENSO:$alarm_senso\r\n")) $check=false;
      if(!@fputs($f,"ALARM_SENSS:$alarm_senss\r\n")) $check=false;
      if(!@fputs($f,"RTC_OFFSET_:$rtc\r\n")) $check=false;
      if(!@fputs($f,"RESET_MINAX:$reset_value\r\n")) $check=false;
      if(!@fputs($f,"ENABLE_LEDs:$enable_led\r\n")) $check=false;
      fclose($f);

      if(!$check) {
        return false;
      }
   } else {
      return false;
   }
   return true;
}
//}}}


// {{{ clean_calendar()
// ROLE delete all calc_XX files 
// IN $sd_card         sd card location
//    $start           start and end: to clean just a part of the calendar
//    $end             if empty: clean all the calendar
// RET false if an error occured, true else
function clean_calendar($sd_card="",$start="",$end="") {
    if(strcmp("$sd_card","")==0) return false;

    $path="$sd_card/logs";
    if(is_dir($path)) {
        if((strcmp("$start","")==0)&&(strcmp("$end","")==0)) {
            for($i=1;$i<=12;$i++) {
                if(strlen("$i")<2) {
                    $i="0".$i;
                }
        
                $sq=@opendir($path."/".$i); 
                while ($f=@readdir($sq)) {
                    if("$f" != "." && "$f" != "..") {
                        if(preg_match('/^cal_/', $f)) {
                            @unlink($path."/".$i."/".$f);
                        }
                    }
                }
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")==0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);

            if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")!=0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);
            $edmon=substr($end,5,2);
            $edday=substr($end,8,2);

            if(strlen("$stday")<2) {
                $stday="0".$stday;
            }

            if(strlen("$stmon")<2) {
                $stmon="0".$stmon;
            }

            if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
            }

            while(($stday!=$edday)||($stmon!=$edmon)) {
                $stday=$stday+1;
                if($stday>31) {
                    $stmon=$stmon+1;
                    $stday=1;
                }

                if($stmon>12) {
                    $stmon=1;
                }

                if(strlen("$stday")<2) {
                    $stday="0".$stday;
                }

                if(strlen("$stmon")<2) {
                    $stmon="0".$stmon;
                }

                if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                    @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
                }
            }
        }
    }
    return true;
}
// }}}

// {{{ check_and_copy_firm()
// ROLE check if firmwares (firm.hex,emetteur.hex) has to be copied and do the copy into the sd card
// IN  $sd_card     the sd card pathname 
// RET 1 if at least one firmware has been copied, 0 if an error occured, -1 else
function check_and_copy_firm($sd_card) {
   $new_firm="";
   $current_firm="";
   $new_file="";
   $copy=-1;


   //Liste des firmawares à vérifier et à copier:
   $firm_to_test[]="firm.hex";
   $firm_to_test[]="bin/emetteur.hex";
   $firm_to_test[]="bin/sht.hex";
   $firm_to_test[]="bin/wlevel_5.hex";
   $firm_to_test[]="bin/wlevel_6.hex";
   $firm_to_test[]="bin/ec_2.hex";
   $firm_to_test[]="bin/ec_3.hex";
   $firm_to_test[]="bin/ec_4.hex";
   $firm_to_test[]="bin/ec_5.hex";
   $firm_to_test[]="bin/ec_6.hex";
   $firm_to_test[]="bin/ph_2.hex";
   $firm_to_test[]="bin/ph_3.hex";
   $firm_to_test[]="bin/ph_4.hex";
   $firm_to_test[]="bin/ph_5.hex";
   $firm_to_test[]="bin/ph_6.hex";
   $firm_to_test[]="bin/or_2.hex";
   $firm_to_test[]="bin/or_3.hex";
   $firm_to_test[]="bin/or_4.hex";
   $firm_to_test[]="bin/or_5.hex";
   $firm_to_test[]="bin/or_6.hex";
   $firm_to_test[]="bin/od_2.hex";
   $firm_to_test[]="bin/od_3.hex";
   $firm_to_test[]="bin/od_4.hex";
   $firm_to_test[]="bin/od_5.hex";
   $firm_to_test[]="bin/od_6.hex";


   //Pour chaque firmware on procède de la même façon:
   foreach($firm_to_test as $firm) { 
        //Vérification de la présence du firmware:
        if(is_file("tmp/$firm")) {
            $new_file="tmp/$firm";
        } else if(is_file("../tmp/$firm")) {
            $new_file="../tmp/$firm";
        } else if(is_file("../../tmp/$firm")) {
            $new_file="../../tmp/$firm";
        } else {
            $new_file="../../../tmp/$firm";
        } 

        //Chemin du firmware à comparer sur la carte SD:
        $current_file="$sd_card/$firm";

        //Si on trouve le firmware de référence on récupère le contenue de la première ligne ou la version est présente:
        if(is_file($new_file)) {
            $handle = @fopen($new_file, 'r');
            if($handle) {
                $new_firm = fgets($handle);
            } else {
                $copy=0;
            }
            fclose($handle);
        } 

        //Même chose avec le firmware sur la carte SD:
        if(is_file("$current_file")) {
            $handle=@fopen("$current_file", 'r');
            if($handle) {
                $current_firm = fgets($handle);
            } else {
                $copy=0;
            }
            fclose($handle);
        } 


        //Si le firmware sur la carte SD et le firmware de référence ont été trouvé, on compare le numéro de version du firmware
        //Si les numéro diffèrent (numéro firmware de référence > numéro firmware sur la carte SD) on copiera le firmware de référence sur la carte SD
        if( isset($new_firm) &&
            !empty($new_firm) &&
            isset($current_firm) &&
            !empty($current_firm)) {

            $current_firm=trim("$current_firm");
            $new_firm=trim("$new_firm");

            if((strlen($current_firm)==43)&&(strlen($new_firm)==43)) {   
                $new_firm=substr($new_firm,9,4); 
                $current_firm=substr($current_firm,9,4);

                if(hexdec($new_firm) > hexdec($current_firm)) {
                    copy($new_file, $current_file);
                    if($copy) $copy=1;
                } 
            } else {
                $copy=0;
            }
        } elseif(!is_file("$current_file") &&
                 is_file("$new_file")) {
            //S'il n'y a pas de firmware sur la carte SD, on copie le firmware de référence:
            copy($new_file, $current_file);
            if($copy) $copy=1;
        } else {
            $copy=0;
        }

        unset($new_file);
        unset($current_file);
        unset($handle);
        unset($current_firm);
        unset($new_firm); 
    }
    return $copy;
}
// }}}


// {{{ copy_firm_sd()
// ROLE copy the wifi firmware into the SD card
// IN  $sd_card     the sd card pathname 
//     $reverse     copy or reverse a copy
// RET true if it's ok, false if an error occured
function copy_firm_sd($sd_card,$reverse=false) {
  if($reverse) {
    if(is_file("$sd_card/firm.hex.old")) {
        copy("$sd_card/firm.hex.old","$sd_card/firm.hex");
        unlink("$sd_card/firm.hex.old");
    } 
    return true;
  } else {
    if(strcmp("$sd_card","")==0) return false;

    if(is_file("main/templates/data/cultibox_firmware_wifi/firm.hex")) {
        $filetpl = "main/templates/data/cultibox_firmware_wifi/firm.hex";
    } else if(is_file("../main/templates/data/cultibox_firmware_wifi/firm.hex")) {
        $filetpl = "../main/templates/data/cultibox_firmware_wifi/firm.hex";
    } else if(is_file("../../main/templates/data/cultibox_firmware_wifi/firm.hex")) {
        $filetpl = "../../main/templates/data/cultibox_firmware_wifi/firm.hex";
    } else if(is_file("../../../main/templates/data/cultibox_firmware_wifi/firm.hex")) {
        $filetpl = "../../../main/templates/data/cultibox_firmware_wifi/firm.hex";
    } else {
        return false;
    }

    if(!is_file("$sd_card/firm.hex")) { 
        return false;
    } else {
        if(!is_file("$sd_card/firm.hex.old")) {
            copy("$sd_card/firm.hex","$sd_card/firm.hex.old");
        }
    }
    
    if(!@copy("$filetpl", "$sd_card/firm.hex")) return false;

    return true;
  }
}
//}}}

?>
