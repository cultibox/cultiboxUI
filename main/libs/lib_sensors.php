<?php

namespace sensors {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the sensor table
    $sensors_index_col         = array();
    $sensors_index_col["id"]   = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => 'NOT NULL  AUTO_INCREMENT');
    $sensors_index_col["type"] = array ( 'Field' => "type", 'Type' => "varchar(2)", 'default_value' => 0, 'carac' => "NOT NULL");
    $sensors_index_col["detectionAuto"] = array ( 'Field' => "detectionAuto", 'Type' => "varchar(5)", 'default_value' => "true", 'carac' => "NOT NULL");
    $sensors_index_col["name"] = array ( 'Field' => "name", 'Type' => "varchar(20)", 'default_value' => "capteur", 'carac' => "NOT NULL");
    $sensors_index_col["source"] = array ( 'Field' => "source", 'Type' => "varchar(10)", 'default_value' => "rj12", 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'sensors';";
    
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
        $sql = "CREATE TABLE sensors ("
                . "id int(11) NOT NULL  AUTO_INCREMENT PRIMARY KEY, "
                . "type varchar(2) NOT NULL DEFAULT '0', "
                . "detectionAuto varchar(5) NOT NULL DEFAULT 'true', "
                . "name varchar(20) NOT NULL DEFAULT 'capteur'"
             . ");";


        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

        $sql = "INSERT INTO sensors (id, type) VALUES (1, '2'), (2, '2'), (3, '2'), (4, '2'), (5, '0'),(6, '0');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    } else {
        // Check column
        check_and_update_column_db ("sensors", $sensors_index_col);


        //Check value:
        
        //For version > 2.0.02:
        $sql = "ALTER TABLE sensors ADD PRIMARY KEY (id);";

        try {
            $db->exec("$sql");
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
       
        // Check if every sensor has not an incorrect value :
        // Incorrect value : 1 4 5
        check_sensors_def();
    }
    $db = null;
    
    // Define columns of the direct read table
    $direct_read_index_col           = array();
    $direct_read_index_col["id"]     = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => 'NOT NULL AUTO_INCREMENT');
    $direct_read_index_col["sensorIndex"]  = array ( 'Field' => "sensorIndex", 'Type' => "varchar(2)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["input"]  = array ( 'Field' => "input", 'Type' => "varchar(2)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["value"]  = array ( 'Field' => "value", 'Type' => "varchar(2)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["input2"] = array ( 'Field' => "input2", 'Type' => "varchar(2)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["value2"] = array ( 'Field' => "value2", 'Type' => "varchar(2)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["type"]   = array ( 'Field' => "type", 'Type' => "varchar(20)", 'default_value' => "NA", 'carac' => "NOT NULL");
    $direct_read_index_col["statusOK"]  = array ( 'Field' => "statusOK", 'Type' => "int(11)", 'default_value' => 1, 'carac' => "NOT NULL");
    $direct_read_index_col["statusOK2"] = array ( 'Field' => "statusOK2", 'Type' => "int(11)", 'default_value' => 1, 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'sensors_directRead';";
    
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
        $sql = "CREATE TABLE sensors_directRead ("
                . "id int(11) NOT NULL  AUTO_INCREMENT PRIMARY KEY, "
                . "sensorIndex varchar(2) NOT NULL DEFAULT 'NA', "
                . "input varchar(2) NOT NULL DEFAULT 'NA', "
                . "value varchar(2) NOT NULL DEFAULT 'NA'"
                . "input2 varchar(2) NOT NULL DEFAULT 'NA'"
                . "value2 varchar(2) NOT NULL DEFAULT 'NA'"
                . "type varchar(20) NOT NULL DEFAULT 'NA'"
                . "statusOK int(11) NOT NULL DEFAULT 1"
                . "statusOK2 int(11) NOT NULL DEFAULT 1"
             . ");";


        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

    } else {
        // Check column
        check_and_update_column_db ("sensors_directRead", $direct_read_index_col);
    }
    $db = null;
}

// Function used to get sensor list
function getDB($db = "sensors") {

        // Check if table configuration exists
    $sql = "SELECT * FROM " . $db  . ";";
    
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

// {{{ updateTable()
// ROLE Retrieve sensor information in db with this name
// RET Every information about this element in DB
function updateTable ($table, $id , $parameter, $value) {

    // Update position conf
    $sql = "UPDATE ${table} SET ${parameter}='${value}' WHERE id='${id}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    echo $sql . "\n";
    
    return 0;
    
}
// }}}

// {{{ serverAcqSensor_createXMLConf()
// ROLE Create serverAcqSensor configuration XML
// RET
function serverAcqSensor_createXMLConf () {

    $paramListServerAcqSensor[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverAcqSensor']
    );

    $paramListServerAcqSensor[] = array (
        "name" => "simulator",
        "value" => "off"
    );
    
    // Retriev sensor infos 
    $sensorList = getDB("sensors");

    // foreach sensor,add every informations
    foreach ($sensorList as $index => $sensorInfos){
        
        foreach ($sensorInfos as $key => $value){
            $paramListServerAcqSensor[] = array (
                "name" => "sensor," . $sensorInfos["id"] . "," . $key,
                "value" => $value
            );
        }
    }
    
    // Add informations for direct read
    $directReadList = getDB("sensors_directRead");
    
    // foreach sensor,add every informations
    foreach ($directReadList as $index => $sensorInfos){
        
        foreach ($sensorInfos as $key => $value){
            $paramListServerAcqSensor[] = array (
                "name" => "direct_read," . $sensorInfos["sensorIndex"] . "," . $key,
                "value" => $value
            );
        }
    }

    \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverAcqSensor/conf.xml" , $paramListServerAcqSensor);
    
}
// }}}

}

?>
