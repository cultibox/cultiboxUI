<?php

namespace cultipi {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the synoptic table
    $synoptic_col = array();
    $synoptic_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $synoptic_col["element"]       = array ( 'Field' => "element", 'Type' => "VARCHAR(10)", "default_value" => "other", 'carac' => "NOT NULL");
    $synoptic_col["scale"]         = array ( 'Field' => "scale", 'Type' => "int(11)", "default_value" => 100, 'carac' => "NOT NULL");
    $synoptic_col["x"]             = array ( 'Field' => "x", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["y"]             = array ( 'Field' => "y", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["z"]             = array ( 'Field' => "z", 'Type' => "int(11)", "default_value" => 100, 'carac' => "NOT NULL");
    $synoptic_col["indexElem"]     = array ( 'Field' => "indexElem", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["rotation"]      = array ( 'Field' => "rotation", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["image"]         = array ( 'Field' => "image", 'Type' => "VARCHAR(50)", "default_value" => "", 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'synoptic';";

    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    // If table exists, return
    if ($res == null)
    {
        
        // Buil MySQL command to create table
        $sql = "CREATE TABLE synoptic ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."element varchar(10) NOT NULL DEFAULT 'other',"
            ."scale int(11) NOT NULL DEFAULT '100',"
            ."x int(11) NOT NULL DEFAULT '0',"
            ."y int(11) NOT NULL DEFAULT '0',"
            ."z int(11) NOT NULL DEFAULT '100',"
            ."indexElem int(11) NOT NULL DEFAULT '0',"
            ."rotation int(11) NOT NULL DEFAULT '0',"
            ."image varchar(50) NOT NULL DEFAULT '' );";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
        // Add one tente and one CBX
        addElementInSynoptic("other", 1, "cultipi.png", 850, 450, 2, 74);
        addElementInSynoptic("other", 2, "tente_1_espace.png", 600, 350, 1, 250);
        
    } else {
        // Check column
        check_and_update_column_db ("synoptic", $synoptic_col);
    }
    
    // Database for supervision
    
   // Define columns of the synoptic table
    $supervision_col = array();
    $supervision_col["id"]                  = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $supervision_col["checkPing_en"]        = array ( 'Field' => "checkPing_en", 'Type' => "VARCHAR(3)", "default_value" => "off", 'carac' => "NOT NULL");
    $supervision_col["checkPing_action"]    = array ( 'Field' => "checkPing_action",  'Type' => "VARCHAR(8)", "default_value" => "sendMail", 'carac' => "NOT NULL");
    $supervision_col["checkPing_adress"]    = array ( 'Field' => "checkPing_adress", 'Type' => "VARCHAR(100)", "default_value" => "8.8.8.8", 'carac' => "NOT NULL");
    $supervision_col["dailyReport_en"]      = array ( 'Field' => "dailyReport_en",  'Type' => "VARCHAR(3)", "default_value" => "off", 'carac' => "NOT NULL");
    $supervision_col["monthlyReport_en"]    = array ( 'Field' => "monthlyReport_en",  'Type' => "VARCHAR(3)", "default_value" => "off", 'carac' => "NOT NULL");
    
    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'supervision';";

    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    // If table exists, return
    if ($res == null)
    {
        
        // Buil MySQL command to create table
        $sql = "CREATE TABLE supervision ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."checkPing_en varchar(3) NOT NULL DEFAULT 'off',"
            ."checkPing_action  varchar(8) NOT NULL DEFAULT 'sendMail',"
            ."checkPing_adress varchar(100) NOT NULL DEFAULT '8.8.8.8',"
            ."dailyReport_en  varchar(3) NOT NULL DEFAULT 'off',"
            ."monthlyReport_en varchar(3) NOT NULL DEFAULT 'off');";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
         $sql = "INSERT INTO supervision (id, checkPing_en, checkPing_action, checkPing_adress, dailyReport_en, monthlyReport_en)"
            . "VALUES (1, 'off', 'sendMail', '8.8.8.8', 'off', 'off');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
    } else {
        // Check column
        check_and_update_column_db ("supervision", $supervision_col);
    }
    
    $db = null;
}


// {{{ getSynopticDBElemByname()
// ROLE Retrieve sensor information in db with this name
// IN $element : Type of element (sensor, plug, other)
// IN $indexElem : Index of this element
// RET Every information about this element in DB
function getSynopticDBElemByname ($element, $indexElem) {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE element = '${element}' AND indexElem = '${indexElem}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

// {{{ getSynopticDBElemByID()
// ROLE Retrieve sensor information in db with this ID
// IN $id : id of element 
// RET Every information about this element in DB
function getSynopticDBElemByID ($id) {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE id = '${id}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

// {{{ addElementInSynoptic()
// ROLE Add an element in db
// IN $element : Name of the element
// IN $indexElem : Index of the element
// IN $image : Image name
// IN $x : X Position
// IN $y : Y Position
// IN $z : Z info
// IN $scale : Scale info
// RET 0
function addElementInSynoptic($element, $indexElem, $image, $x=0, $y="", $z=100, $scale = 100) {
    
    if ($x == 0 || $x == "") 
    {
        switch ($element) {
            case "plug":
                if((isset($_COOKIE['CONTENT_LEFT']))&&(!empty($_COOKIE['CONTENT_LEFT']))) {
                    $x=(int)($_COOKIE['CONTENT_LEFT']+$_COOKIE['CONTENT_LEFT']*25/100);
                } else {
                    $x = 300;
                }
                break;
            case "sensor":
                if((isset($_COOKIE['CONTENT_RIGHT']))&&(!empty($_COOKIE['CONTENT_RIGHT']))) {
                    $x=(int)($_COOKIE['CONTENT_RIGHT']-$_COOKIE['CONTENT_RIGHT']*10/100);
                } else {
                    $x = 1100;
                }
                break;
            case "other":
                if((isset($_COOKIE['CONTENT_RIGHT']))&&(!empty($_COOKIE['CONTENT_RIGHT'])) && isset($_COOKIE['CONTENT_LEFT']) && !empty($_COOKIE['CONTENT_LEFT'])) {
                    $x=(int)(( $_COOKIE['CONTENT_RIGHT'] - $_COOKIE['CONTENT_LEFT']) / 2 + $_COOKIE['CONTENT_LEFT']);
                } else {
                    $x = 700;
                }
                break;
            default:
                $x = 500;
                break;
        }
    }
     
    if ($y == 0 || $y == "") 
    {
        $step = ($indexElem + 1 ) * 150;
        if((isset($_COOKIE['CONTENT_TOP']))&&(!empty($_COOKIE['CONTENT_TOP']))) {
            $y=(int)($_COOKIE['CONTENT_TOP']+$step);
        } else {
            $y=$step;
        }
    }

    // Check if table configuration exists
    $sql = "INSERT INTO synoptic (element, indexElem, image, x, y, z, scale) VALUES('${element}' , '${indexElem}' , '${image}' , '${x}' , '${y}' , '${z}' , '${scale}') ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    // Retrieve ID of this element
    $sql = "SELECT * FROM synoptic WHERE element = '${element}' AND indexElem = '${indexElem}' AND image =  '${image}' AND x = '${x}' AND y = '${y}';";
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    return $res;
}
// }}}

// {{{ deleteElementInSynoptic()
// ROLE Remove an element
// IN $id : Name of the element
// RET 0
function deleteElementInSynoptic($id) {
    

    // Check if table configuration exists
    $sql = "DELETE FROM synoptic WHERE id='${id}';"; 
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    return 0;
}
// }}}

// {{{ getSensorOfSynoptic()
// ROLE Retrieve sensor information of the synoptic
// RET All informatons about sensor
function getSensorOfSynoptic () {

    $ret_array = array();

    // Read sensors in db
    $sensorList = \sensors\getDB();
    
    // foreach sensor, get type and position
    foreach ($sensorList as $index => $sensor){

        // Read parameters in db and add it to return array
        $sensorParameters = getSynopticDBElemByname("sensor",$sensor["id"]);

        // If empty create them 
        if (empty($sensorParameters) && $sensor["type"] != "0") {

            switch ($sensor["type"])
            {
                case '2' :
                    $image = "T_RH_sensor.png";
                    break;
                case '3': 
                    $image = "water_T_sensor.png";
                    break;
                case '6': 
                case '7': 
                    $image = "level_sensor.png";
                    break;
                case '8': 
                    $image = "pH-sensor.png";
                    break;
                case '9': 
                    $image = "conductivity-sensor.png";
                    break;
                case '10': 
                    $image = "dissolved-oxygen-sensor.png";
                    break;
                case '11': 
                    $image = "ORP-sensor.png";
                    break;
                default :
                    $image = "T_RH_sensor.png";
                    break;
            }
        
            addElementInSynoptic("sensor", $sensor["id"], $image);
            
            $ret_array[] = getSynopticDBElemByname("sensor",$sensor["id"]);
            
        }
        elseif ($sensor["type"] != "0") 
        {
            $ret_array[] = $sensorParameters;
        }

    }

    return $ret_array;
}
// }}}

// {{{ getPlugOfSynoptic()
// ROLE Retrieve plug information of the synoptic
// RET All informatons about plugs
function getPlugOfSynoptic () {

    $ret_array = array();

    // Read nb plug in database
    $plugNB = \configuration\getConfElem("NB_PLUGS");

    // Read plug parameters
    $plugParam = \plugs\getDB();
        
    // foreach sensor, get type and position
    for ($i = 1; $i <= $plugNB["NB_PLUGS"] && $i <= 100; $i++) {

    
        // Read parameters in db and add it to return array
        $sensorParameters = getSynopticDBElemByname("plug",$i);

        // If empty create them 
        if (empty($sensorParameters)) {
            switch ($plugParam[$i - 1]["PLUG_TYPE"]) 
            {
                case "lamp" :
                    $image = "lampe_OFF.png";
                    break;
                case "extractor" :
                case "intractor" :
                case "ventilator" :
                    $image = "lampe_OFF.png";
                    break;
                case "pump" :
                case "pumpfilling" :
                case "pumpempting" :
                    $image = "pompe_OFF.png";
                    break;
                case "co2" :
                    $image = "CO2_OFF.png";
                    break;
                default :
                    if ($plugParam[$i - 1]["PLUG_POWER_MAX"] ==  "1000") 
                    {
                        $image = "1000W_OFF.png";
                    }
                    else
                    {
                        $image = "3500W_OFF.png";
                    }
                    break;
            }
        
            addElementInSynoptic("plug", $i, $image);
            
            $sensorParameters = getSynopticDBElemByname("plug",$i);
            $sensorParameters["PLUG_NAME"] = $plugParam[$i - 1]["PLUG_NAME"];
            
            $ret_array[] = $sensorParameters;
            
        } 
        else
        { 
            $sensorParameters["PLUG_NAME"] = $plugParam[$i - 1]["PLUG_NAME"];
            $ret_array[] = $sensorParameters;
        }

    }

    return $ret_array;
}
// }}}

// {{{ getSensorSynoptic()
// ROLE Retrieve sensor information in db
// RET Sensors informations
function getOtherOfSynoptic () {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE element != 'sensor' AND element != 'plug' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

// {{{ getAllSensorLiveValue()
// ROLE Retrieve value of each sensor
// RET plug value
function getAllSensorLiveValue() {

    $return_array = array();
    $return_array["error"] = "";
    
    $commandLine = 'tclsh "/opt/cultipi/cultiPi/get.tcl" serverAcqSensor localhost ';
    for ($i = 1; $i <= 6; $i++) {
        $commandLine = $commandLine . ' "::sensor(' . $i . ',value)"';
    }
    
    $ret = "";
    try {
        $ret = exec($commandLine);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);

    for ($i = 0; $i <= 5; $i++) {
        if (array_key_exists($i, $arr)) {
            if ($arr[$i] != "") {
                $return_array[$i + 1] = $arr[$i];
            } else {
                $return_array[$i + 1] = "DEFCOM";
            }
        } else {
            $return_array[$i + 1] = "DEFCOM";
        }
    }

    return $return_array;
}
// }}}

// {{{ getSensorLiveValue()
// ROLE Retrieve value of a sensor
// IN $number : Index of a sensor
// RET Sensor value
function getSensorLiveValue($number) {

    $ret = "";

    $return_array = array();
    $return_array["val1"] = "DEFCOM"; 
    $return_array["val2"] = "DEFCOM";
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/get.tcl" serverAcqSensor localhost "::sensor(' . $number . ',value)"');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode (" ", $ret);
    
    if (array_key_exists(0, $arr)) {
        $return_array["val1"] = $arr[0];
    }
    if (array_key_exists(1, $arr)) {
        $return_array["val2"] = $arr[1];
    }
    
    return $return_array;
}
// }}}

// {{{ getPlugLiveValue()
// ROLE Retrieve value of each plug
// RET plug value
function getAllPlugLiveValue() {

    $return_array = array();
    $return_array["error"] = "";
    
    $commandLine = 'tclsh "/opt/cultipi/cultiPi/get.tcl" serverPlugUpdate localhost ';
    for ($i = 1; $i <= 16; $i++) {
        $commandLine = $commandLine . ' "::plug(' . $i . ',value)"';
    }
    
    $ret = "";
    try {
        $ret = exec($commandLine);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);
    
    for ($i = 0; $i <= 15; $i++) {
        if (array_key_exists($i, $arr)) {
            if ($arr[$i] != "") {
                $return_array[$i + 1] = $arr[$i];
            } else {
                $return_array[$i + 1] = "DEFCOM";
            }
        } else {
            $return_array[$i + 1] = "DEFCOM";
        }
    }

    return $return_array;
}
// }}}

// {{{ getPlugLiveValue()
// ROLE Retrieve value of a plug
// IN $number : Index of a plug
// RET plug value
function getPlugLiveValue($number) {

    $ret = "";

    $return_array = array();
    $return_array["val1"] = "DEFCOM"; 
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/get.tcl" serverPlugUpdate localhost "::plug(' . $number . ',value)"');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);
    
    if (array_key_exists(0, $arr)) {
        $return_array["val1"] = $arr[0];
    }
    
    return $return_array;
}
// }}}

// {{{ updatePosition()
// ROLE Update position of an element
// IN $elem : Element to change
// IN $x : New X info
// IN $y : New Y info
// RET id of the line added
function updatePosition($elem,$x,$y) {

    // Update position conf
    $sql = "UPDATE synoptic SET x='${x}' ,y='${y}' WHERE id='${elem}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return 0;
    
    return $arr[3];
}
// }}}

// {{{ updateZScaleImageRotation()
// ROLE Update informations of an element
// IN $elem : Element to change
// IN $z : New Z info
// IN $scale : New scale info
// IN $image : New image info
// IN $rotation : New rotation info
// RET Return of the SQL command
function updateZScaleImageRotation($elem,$z,$scale,$image,$rotation) {

    // Update position conf
    $sql = "UPDATE synoptic SET z='${z}' ,scale='${scale}' ,image='${image}' ,rotation='${rotation}' WHERE id='${elem}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $ret = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    return $ret;
}
// }}}

// {{{ updateZScaleImageRotation()
// ROLE Update informations of an element
// IN $elem : Element to change
// IN $z : New Z info
// IN $scale : New scale info
// IN $image : New image info
// IN $rotation : New rotation info
// RET Return of the SQL command
function updateImagePlug ($indexPlug,$image) {

    // Update position conf
    $sql = "UPDATE synoptic SET image='${image}' WHERE indexElem='${indexPlug}' AND element='plug' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $ret = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    return $ret;
}
// }}}

// {{{ forcePlug()
// ROLE Force a plug
// IN $number : Index of plug
// IN $time : Time to force
// IN $value : Value to force
// RET empty
function forcePlug($number,$time,$value) {

    $return_array = array();

    try {
        switch(php_uname('s')) {
            case 'Windows NT':
                $return_array["status"] = exec('C:\Tcl\bin\tclsh.exe "D:\CBX\cultipiCore\cultiPi\getCommand.tcl" serverPlugUpdate localhost setGetRepere ' . $number . ' ' . $value . ' ' . $time);
                break;
            default : 
                $return_array["status"] = exec('tclsh "/opt/cultipi/cultiPi/getCommand.tcl" serverPlugUpdate localhost setGetRepere ' . $number . ' ' . $value . ' ' . $time);
                break;
        }
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["status"] = $e->getMessage();
    }

    return $return_array;
}
// }}}

// {{{ getPlugInformation()
// ROLE Retrieve sensor information in db with this ID
// IN $id : id of element 
// RET Every information about this element in DB
function getPlugInformation ($id) {

    // Search informations about the plug
    $synoInfo = getSynopticDBElemByID($id);

    // Read plugs informations
    $plugParam = \plugs\getDB();
    
    // Search plugIndex associated
    $ret_Array = $plugParam[$synoInfo["indexElem"]-1];
    
    return $ret_Array;
}
// }}}

// {{{ getCultiPiStatus()
// ROLE Retrieve CultiPiStatus
// RET CultiPiStatus
function getCultiPiStatus() {

    global $cultipiPath;

    $ret = "";

    $return_array = array();
    $return_array["status"] = "TIMEOUT";
    $return_array["cultihour"] = ""; 
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/get.tcl"  serverCultipi localhost statusInitialisation cultipiActualHour');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }
    
    $arr = explode ("\t", $ret);

    if (array_key_exists(0, $arr)) {
        $return_array["status"] = $arr[0];
    }
    
    if (array_key_exists(1, $arr)) {
        $return_array["cultihour"] = $arr[1];
    }
    
    return $return_array;
}
// }}}

// {{{ get_webcam_conf()
// ROLE Retrieve webcam conf
// RET Web cam conf
function get_webcam_conf() {
        $return=array();

        for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
            if(is_file("/etc/culticam/webcam$i.conf")) {
                $handle = fopen("/etc/culticam/webcam$i.conf", "r");
                if($handle) {
                    while(($line = fgets($handle)) !== false) {
                    // process the line read.
                    if(strpos($line, "resolution")!==false) {
                        $value=explode(" ",$line);
                        $return[$i]['resolution']=trim($value[1]);
                    }

                    if(strpos($line, "brightness")!==false) {
                        $value=explode("=",$line);
                        $value[1]=trim($value[1]);
                        $return[$i]['brightness']=substr($value[1],0,strlen($value[1])-1);
                    }

                    if(strpos($line, "contrast")!==false) {
                        $value=explode("=",$line);
                        $value[1]=trim($value[1]);
                        $return[$i]['contrast']=substr($value[1],0,strlen($value[1])-1);
                    }

                    if(strpos($line, "palette")!==false) {
                        $value=explode(" ",$line);
                        $return[$i]['palette']=trim($value[1]);
                    }

                    if(strpos($line, "title")!==false) {
                        $value=explode("\"",$line);
                        $return[$i]['name']=trim($value[1]);
                    }
                    }
                    fclose($handle);
                } 
                else
                {
                    // error opening the file.
                    $return[$i]['resolution']="640x480";
                    $return[$i]['brightness']="55";
                    $return[$i]['contrast']="33";
                    $return[$i]['palette']="MJPEG";
                    $return[$i]['name']="Webcam $i";
                }
            } 
            else
            {
                // error The file doesnot exists
                $return[$i]['resolution']="640x480";
                $return[$i]['brightness']="55";
                $return[$i]['contrast']="33";
                $return[$i]['palette']="MJPEG";
                $return[$i]['name']="Webcam $i";
            }
        }

        for($i=0;$i<count($return);$i++)
        {
            $name=$i+1;
            
            if(!array_key_exists('name', $return[$i])) 
                $return[$i]['name']="Webcam $name";
            
            if(!array_key_exists('palette', $return[$i]))
                $return[$i]['palette']="AUTO";
            
            if(!array_key_exists('resolution', $return[$i]))
                $return[$i]['resolution']="640x480";
            
            if(!array_key_exists('brightness', $return[$i]))
                $return[$i]['brightness']="55";
            
            if(!array_key_exists('contrast', $return[$i]))
                $return[$i]['contrast']="33";
        }
        
        return $return;
  }
// }}}

// {{{ getSupervisionElem()
// ROLE Retrieve supervision elements
// RET Every information about supervision in DB
function getSupervisionUserConf () {


    // Check if table configuration exists
    $sql = "SELECT * FROM supervision ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

// {{{ saveSupervisionUserConf()
// ROLE Save user supervision configuration
// RET
function saveSupervisionUserConf($param) {
    
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    $str = "";
    foreach($param As $key => $value)
    {
        if ($str != "")
            $str = $str . " , ";
        
        $str = $str . "${key}='${value}'";
        
    }
        
    $sql = "UPDATE supervision SET ${str} ;";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }

}
// }}}

// {{{ serverSupervision_createXMLConf()
// ROLE Create supervision configuration XML
// RET
function serverSupervision_createXMLConf () {
    
    // retrieve user params
    $supervisionUserConf = getSupervisionUserConf();
    $mailUserConf = \configuration\getEmailUserConf();
    
    $processIndex = 0 ;
    
    if (strcmp($supervisionUserConf["checkPing_en"],"on") == 0) 
    {
        $tempProcess = array();
        $tempProcess[] = array (
            "name" => "action",
            "value" => "checkPing"
        );
        $tempProcess[] = array (
            "name" => "nbIP",
            "value" => "1"
        );        
        $tempProcess[] = array (
            "name" => "IP,0",
            "value" => $supervisionUserConf['checkPing_adress']
        );
        $tempProcess[] = array (
            "name" => "timeMax",
            "value" => "60"
        );
        $tempProcess[] = array (
            "name" => "error,action",
            "value" => $supervisionUserConf['checkPing_action'] . " " . $mailUserConf["EMAIL_ADRESS"]
        );
        
        \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverSupervision/process_" . $processIndex . ".xml" , $tempProcess);
        
        $processIndex ++ ;
    }

    if (strcmp($supervisionUserConf["dailyReport_en"],"on") == 0) 
    {
        $tempProcess = array();
        $tempProcess[] = array (
            "name" => "action",
            "value" => "dailyReport"
        );
        \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverSupervision/process_" . $processIndex . ".xml" , $tempProcess);
        
        $processIndex ++ ;
    }
    
    if (strcmp($supervisionUserConf["monthlyReport_en"],"on") == 0) 
    {
        $tempProcess = array();
        $tempProcess[] = array (
            "name" => "action",
            "value" => "monthlyReport"
        );
        \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverSupervision/process_" . $processIndex . ".xml" , $tempProcess);
        
        $processIndex ++ ;
    }
    
    $supervisionConf[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverSupervision']
    );
    $supervisionConf[] = array (
        "name" => "nbProcess",
        "level" => $processIndex
    );
    \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverSupervision/conf.xml" , $supervisionConf);
    
    
}
// }}}

}

?>
