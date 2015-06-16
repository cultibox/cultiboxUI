<?php

namespace trigger {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the irrigation zone table
    $zone_col = array();
    $zone_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $zone_col["name"]          = array ( 'Field' => "name", 'Type' => "varchar(20)", "default_value" => 'nom', 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'trigger_index';";

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
        $sql = "CREATE TABLE trigger_index ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."name varchar(20) NOT NULL DEFAULT '1');";
            
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
        check_and_update_column_db ("trigger_index", $zone_col);
    }
    
    $db = null;
    
    // Define columns of the irrigation zone table
    $zone_col = array();
    $zone_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $zone_col["idTrigger"]     = array ( 'Field' => "idTrigger", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["state"]         = array ( 'Field' => "state", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["action"]        = array ( 'Field' => "action", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["type"]          = array ( 'Field' => "type", 'Type' => "varchar(20)", "default_value" => 'plug', 'carac' => "NOT NULL");
    $zone_col["value"]         = array ( 'Field' => "value", 'Type' => "varchar(20)", "default_value" => '1 on', 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'trigger_action';";

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
        $sql = "CREATE TABLE trigger_action ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."state int(11) NOT NULL DEFAULT '1',"
            ."idTrigger int(11) NOT NULL DEFAULT '1',"
            ."action int(11) NOT NULL DEFAULT '1',"
            ."type varchar(20) NOT NULL DEFAULT '1',"
            ."value varchar(20) NOT NULL DEFAULT '1');";
            
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
        check_and_update_column_db ("trigger_action", $zone_col);
    }

    $db = null;
    
    // Define columns of the irrigation zone table
    $zone_col = array();
    $zone_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $zone_col["idTrigger"]     = array ( 'Field' => "idTrigger", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["state"]         = array ( 'Field' => "state", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["action"]        = array ( 'Field' => "action", 'Type' => "int(11)", "default_value" => '1', 'carac' => "NOT NULL");
    $zone_col["type"]          = array ( 'Field' => "type", 'Type' => "varchar(20)", "default_value" => 'plug', 'carac' => "NOT NULL");
    $zone_col["sensor"]        = array ( 'Field' => "sensor", 'Type' => "varchar(20)", "default_value" => 'plug', 'carac' => "NOT NULL");
    $zone_col["value"]         = array ( 'Field' => "value", 'Type' => "varchar(20)", "default_value" => '1 on', 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'trigger_condition';";

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
        $sql = "CREATE TABLE trigger_condition ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."idTrigger int(11) NOT NULL DEFAULT '1',"
            ."state int(11) NOT NULL DEFAULT '1',"
            ."action int(11) NOT NULL DEFAULT '1',"
            ."type varchar(20) NOT NULL DEFAULT '1',"
            ."sensor varchar(20) NOT NULL DEFAULT '1',"
            ."value varchar(20) NOT NULL DEFAULT '1');";
            
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
        check_and_update_column_db ("trigger_condition", $zone_col);
    }

    $db = null;
    
}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addInMenu() {

    echo '<li id="menu-trigger" class="level1 item173"><a href="/cultibox/index.php?menu=trigger" class="level1 href-trigger" ><span>Declencheurs</span></a></li>';

}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addJsToLoadMenu() {

}

// {{{ addInStartXMLCultiCore()
// ROLE Add in menu
// Delete it if not used
// RET none
function addInStartXMLCultiCore() {

    $ret_array = array ( 
        'name' => "serverTrigger",
        'waitAfterUS' => "100",
        'pathexe' => "tclsh",
        'path' => "./serverTrigger/serverTrigger.tcl",
        'xmlconf' => "./serverTrigger/conf.xml",
    );

    return $ret_array;
}

// {{{ getTriggers()
// ROLE retrieve triggers
// Delete it if not used
// RET none
function getTriggers() {

    // Select * triggers
    $sql = "SELECT * FROM trigger_index ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    /* J'en suis la */

    return $res;

}

// {{{ createXML()
// ROLE Retrieve sensor information in db with this name
// RET Every information about this element in DB
function createXML () {

    // Add trace level
    $paramServerTriggerXML[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['PLUGIN_irrigation']['TRACE_LEVEL']
    );
    
    // Add every parameters of the database
    $triggerList = getTriggers();
    foreach ($triggerList as $k => $triggerParam) {
        
        $numTrigger = $triggerParam['id'];
        
        $triggerAction = $triggerParam['action'];
        
        foreach ($triggerAction as $key => $value) {
            $paramServerTriggerXML[] = array (
                "key" => $key,
                "value" => $value
            );
        }
        
        $triggerCondition = $triggerParam['condition'];

        foreach ($triggerCondition as $key => $value) {
            $paramServerTriggerXML[] = array (
                "key" => $key,
                "value" => $value
            );
        }
    }

    // Save it
    \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH']. "/serverTrigger/conf.xml" , $paramServerTriggerXML);

    return 0;
    
}
// }}}

}

?>
