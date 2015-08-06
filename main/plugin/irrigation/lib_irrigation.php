<?php

namespace irrigation {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the irrigation zone table
    $zone_col = array();
    $zone_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $zone_col["motherPlatef"]  = array ( 'Field' => "motherPlatef", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["zoneId"]        = array ( 'Field' => "zoneId", 'Type' => "int(11)", "default_value" => 1, 'carac' => "NOT NULL");
    $zone_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(20)", "default_value" => "other", 'carac' => "NOT NULL");
    $zone_col["prise"]         = array ( 'Field' => "prise", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["tempsOn"]       = array ( 'Field' => "tempsOn", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["tempsOff"]      = array ( 'Field' => "tempsOff", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["active"]        = array ( 'Field' => "active", 'Type' => "VARCHAR(5)", "default_value" => "on", 'carac' => "NOT NULL");
    $zone_col["coef"]          = array ( 'Field' => "coef", 'Type' => "decimal(6,2)", "default_value" => 1.0, 'carac' => "NOT NULL");
    $zone_col["tempsOnNuit"]   = array ( 'Field' => "tempsOnNuit", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["tempsOffNuit"]  = array ( 'Field' => "tempsOffNuit", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["tempsOnApresMidi"]   = array ( 'Field' => "tempsOnApresMidi", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $zone_col["tempsOffApresMidi"]  = array ( 'Field' => "tempsOffApresMidi", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'irrigation_zone';";

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
        $sql = "CREATE TABLE irrigation_zone ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."motherPlatef int(11) NOT NULL DEFAULT '1',"
            ."zoneId int(11) NOT NULL DEFAULT '1',"
            ."name varchar(20) NOT NULL DEFAULT 'other',"
            ."prise int(11) NOT NULL DEFAULT '0',"
            ."tempsOn int(11) NOT NULL DEFAULT '0',"
            ."tempsOff int(11) NOT NULL DEFAULT '0',"
            ."active varchar(5) NOT NULL DEFAULT 'true',"
            ."coef decimal(6,2) NOT NULL DEFAULT '1.0',"
            ."tempsOnNuit int(11) NOT NULL DEFAULT '0',"
            ."tempsOffNuit int(11) NOT NULL DEFAULT '0',"
            ."tempsOnApresMidi int(11) NOT NULL DEFAULT '0',"
            ."tempsOffApresMidi int(11) NOT NULL DEFAULT '0' );";
            
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
        check_and_update_column_db ("irrigation_zone", $zone_col);
    }
    
    $db = null;
    
    
    // Create table for plateforme
    // Define columns of the irrigation zone table
    $plateforme_col = array();
    $plateforme_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $plateforme_col["idPlateforme"]  = array ( 'Field' => "idPlateforme", 'Type' => "int(11)", "default_value" => 1, 'carac' => "NOT NULL");
    $plateforme_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(20)", "default_value" => "other", 'carac' => "NOT NULL");
    $plateforme_col["ip"]            = array ( 'Field' => "ip", 'Type' => "VARCHAR(16)", "default_value" => "1", 'carac' => "NOT NULL");
    $plateforme_col["pompeName"]     = array ( 'Field' => "pompeName", 'Type' => "VARCHAR(20)", "default_value" => "pompe", 'carac' => "NOT NULL");
    $plateforme_col["pompePrise"]    = array ( 'Field' => "pompePrise", 'Type' => "int(11)", "default_value" => 1, 'carac' => "NOT NULL");
    $plateforme_col["active"]        = array ( 'Field' => "active", 'Type' => "VARCHAR(5)", "default_value" => "true", 'carac' => "NOT NULL");
    $plateforme_col["limitDesamorcagePompe"]        = array ( 'Field' => "limitDesamorcagePompe", 'Type' => "VARCHAR(5)", "default_value" => "true", 'carac' => "NOT NULL");
    $plateforme_col["tempsPerco"]    = array ( 'Field' => "tempsPerco", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["tempsMaxRemp"]  = array ( 'Field' => "tempsMaxRemp", 'Type' => "int(11)", "default_value" => 300, 'carac' => "NOT NULL");
    $plateforme_col["tempsMaxRempApresMidi"]  = array ( 'Field' => "tempsMaxRempApresMidi", 'Type' => "int(11)", "default_value" => 300, 'carac' => "NOT NULL");
    $plateforme_col["tempsMaxRempNuit"]     = array ( 'Field' => "tempsMaxRempNuit", 'Type' => "int(11)", "default_value" => 300, 'carac' => "NOT NULL");
    $plateforme_col["priseDansLT"]          = array ( 'Field' => "priseDansLT", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["tempsPercoNuit"]       = array ( 'Field' => "tempsPercoNuit", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["tempsPercoApresMidi"]  = array ( 'Field' => "tempsPercoApresMidi", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["priseEau"]      = array ( 'Field' => "priseEau", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["activeAutoRemplissage"] = array ( 'Field' => "activeAutoRemplissage", 'Type' => "VARCHAR(5)", "default_value" => "false", 'carac' => "NOT NULL");
    $plateforme_col["autoRemplissageDirect"] = array ( 'Field' => "autoRemplissageDirect", 'Type' => "VARCHAR(5)", "default_value" => "true", 'carac' => "NOT NULL");
    $plateforme_col["priseRegulation"]      = array ( 'Field' => "priseRegulation", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["priseRemplissage"]     = array ( 'Field' => "priseRemplissage", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $plateforme_col["tempsAutoRemplissage"]  = array ( 'Field' => "tempsAutoRemplissage", 'Type' => "int(11)", "default_value" => 30, 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'irrigation_plateforme';";

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
        $sql = "CREATE TABLE irrigation_plateforme ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."idPlateforme int(11) NOT NULL DEFAULT '1',"
            ."name varchar(20) NOT NULL DEFAULT 'other',"
            ."ip VARCHAR(16) NOT NULL DEFAULT '1',"
            ."pompeName varchar(20) NOT NULL DEFAULT 'pompe',"
            ."pompePrise int(11) NOT NULL DEFAULT '1',"
            ."active varchar(5) NOT NULL DEFAULT 'true',"
            ."limitDesamorcagePompe varchar(5) NOT NULL DEFAULT 'true',"
            ."tempsPerco int(11) NOT NULL DEFAULT '0',"
            ."tempsMaxRemp int(11) NOT NULL DEFAULT '100',"
            ."tempsMaxRempApresMidi int(11) NOT NULL DEFAULT '100',"
            ."tempsMaxRempNuit int(11) NOT NULL DEFAULT '100',"
            ."priseDansLT int(11) NOT NULL DEFAULT '0',"
            ."tempsPercoNuit int(11) NOT NULL DEFAULT '0'"
            ."tempsPercoApresMidi int(11) NOT NULL DEFAULT '0'"
            ."priseEau int(11) NOT NULL DEFAULT '0',"
            ."activeAutoRemplissage varchar(5) NOT NULL DEFAULT 'false',"
            ."autoRemplissageDirect varchar(5) NOT NULL DEFAULT 'true',"
            ."priseRegulation int(11) NOT NULL DEFAULT '0',"
            ."priseRemplissage int(11) NOT NULL DEFAULT '0',"
            ."tempsAutoRemplissage int(11) NOT NULL DEFAULT '30' );";

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
        check_and_update_column_db ("irrigation_plateforme", $plateforme_col);
    }
    
    $db = null;

    // Create table for localtechnique
    $lt_col = array();
    $lt_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $lt_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(20)", "default_value" => "local_technique", 'carac' => "NOT NULL");
    $lt_col["ip"]            = array ( 'Field' => "ip", 'Type' => "VARCHAR(16)", "default_value" => "1", 'carac' => "NOT NULL");
    $lt_col["pompeName"]     = array ( 'Field' => "pompeName", 'Type' => "VARCHAR(20)", "default_value" => 'pompe', 'carac' => "NOT NULL");
    $lt_col["pompePrise"]    = array ( 'Field' => "pompePrise", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $lt_col["irriActive"]    = array ( 'Field' => "irriActive", 'Type' => "VARCHAR(5)", "default_value" => "on", 'carac' => "NOT NULL");
    $lt_col["timeMatinStarter"] = array ( 'Field' => "timeMatinStarter", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $lt_col["timeApresStarter"] = array ( 'Field' => "timeApresStarter", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $lt_col["timeNuitStarter"] = array ( 'Field' => "timeNuitStarter", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    
    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'irrigation_lt';";

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
        $sql = "CREATE TABLE irrigation_lt ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."name varchar(20) NOT NULL DEFAULT 'local_technique',"
            ."ip VARCHAR(16) NOT NULL DEFAULT '1',"
            ."pompeName varchar(20) NOT NULL DEFAULT 'pompe',"
            ."pompePrise int(11) NOT NULL DEFAULT '1',"
            ."irriActive varchar(5) NOT NULL DEFAULT 'true',"
            ."timeMatinStarter int(11) NOT NULL DEFAULT '0',"
            ."timeApresStarter int(11) NOT NULL DEFAULT '0',"
            ."timeNuitStarter int(11) NOT NULL DEFAULT '0');";

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
        check_and_update_column_db ("irrigation_lt", $lt_col);
    }
    
    $db = null;
    
    // Create table for engrais
    $engrais_col = array();
    $engrais_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $engrais_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(10)", "default_value" => "engrais", 'carac' => "NOT NULL");
    $engrais_col["prise"]         = array ( 'Field' => "prise", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $engrais_col["active"]        = array ( 'Field' => "active", 'Type' => "VARCHAR(5)", "default_value" => "on", 'carac' => "NOT NULL");
    $engrais_col["engraisId"]     = array ( 'Field' => "engraisId", 'Type' => "int(11)", "default_value" => 1, 'carac' => "NOT NULL");
    $engrais_col["useMatinStarter"] = array ( 'Field' => "useMatinStarter", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");
    $engrais_col["useMatinNormal"] = array ( 'Field' => "useMatinNormal", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");
    $engrais_col["useApresStarter"] = array ( 'Field' => "useApresStarter", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");
    $engrais_col["useApresNormal"] = array ( 'Field' => "useApresNormal", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");
    $engrais_col["useNuitStarter"] = array ( 'Field' => "useNuitStarter", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");
    $engrais_col["useNuitNormal"] = array ( 'Field' => "useNuitNormal", 'Type' => "VARCHAR(5)", "default_value" => 'false', 'carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'irrigation_engrais';";

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
        $sql = "CREATE TABLE irrigation_engrais ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."name varchar(20) NOT NULL DEFAULT 'engrais',"
            ."prise int(11) NOT NULL DEFAULT '1',"
            ."active varchar(5) NOT NULL DEFAULT 'true',"
            ."engraisId int(11) NOT NULL DEFAULT '1',"
            ."useMatinStarter varchar(5) NOT NULL DEFAULT 'false',"
            ."useMatinNormal varchar(5) NOT NULL DEFAULT 'false',"
            ."useApresStarter varchar(5) NOT NULL DEFAULT 'false',"
            ."useApresNormal varchar(5) NOT NULL DEFAULT 'false',"
            ."useNuitStarter varchar(5) NOT NULL DEFAULT 'false',"
            ."useNuitNormal varchar(5) NOT NULL DEFAULT 'false')";

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
        check_and_update_column_db ("irrigation_engrais", $engrais_col);
    }
    
    $db = null;
    
}

// This function is used to load user config
function loadUserConfig ()
{
    
    // Delete previous database
    $db = \db_priv_pdo_start("root");
    try {
        $sth = $db->prepare("DROP TABLE irrigation_zone;");
        $sth->execute();
        $sth = $db->prepare("DROP TABLE irrigation_engrais;");
        $sth->execute();
        $sth = $db->prepare("DROP TABLE irrigation_lt;");
        $sth->execute();
        $sth = $db->prepare("DROP TABLE irrigation_plateforme;");
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    $db = null;
    
    // Recreat them 
    check_db();
    
    $db = \db_priv_pdo_start("root");
    // Then load user config
    foreach ($GLOBALS['PLUGIN_irrigation']['plateforme'] as $keyPlateforme => $plateforme)
    {
        // Add the plateform
        $sql = "INSERT INTO irrigation_plateforme (name, idPlateforme, ip, pompeName, pompePrise, active, tempsPerco, tempsMaxRemp, priseDansLT) VALUES"
            . "('{$plateforme['nom']}','{$keyPlateforme}','{$plateforme['ip']}','{$plateforme['pompe']['nom']}','{$plateforme['pompe']['prise']}'"
            . ",'{$plateforme['active']}','{$plateforme['tempsPerco']}','{$plateforme['tempsMaxRemp']}','{$plateforme['priseDansLT']}');";
        $sth = $db->prepare($sql);
        $sth->execute();
        
        // Add every zone
        foreach ($plateforme['zone'] as $keyZone => $zone)
        {
            $sql = "INSERT INTO irrigation_zone (motherPlatef, zoneId, name, prise, tempsOn, tempsOff, active, coef) VALUES"
                . "('{$keyPlateforme}','{$keyZone}','{$zone['nom']}','{$zone['prise']}'"
                . ",'{$zone['tempsOn']}','{$zone['tempsOff']}','{$zone['active']}','{$zone['coef']}');";
            $sth = $db->prepare($sql);
            $sth->execute();
        }
    }

    // Add local technique
    $lt = $GLOBALS['PLUGIN_irrigation']['localtechnique'];
    $sql = "INSERT INTO irrigation_lt (name, ip, pompeName, pompePrise, irriActive) VALUES"
        . "('{$lt['nom']}','{$lt['ip']}','{$lt['pompe']['nom']}','{$lt['pompe']['prise']}'"
        . ",'{$lt['irriActive']}');";
    $sth = $db->prepare($sql);
    $sth->execute();
    
    // Add engrais
    $engraisList = $GLOBALS['PLUGIN_irrigation']['localtechnique']['engrais'];
    foreach ($engraisList as $key => $engrais)
    {
        $sql = "INSERT INTO irrigation_engrais (name, prise, active, engraisId) VALUES"
            . "('{$engrais['nom']}','{$engrais['prise']}','{$engrais['active']}','{$key}');";
        $sth = $db->prepare($sql);
        $sth->execute();
    }

    $db = null;
}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addInMenu() {

    echo '<li id="menu-irrigation" class="level1 item173"><a href="/cultibox/index.php?menu=irrigation" class="level1 href-irrigation" ><span>Irrigation</span></a></li>';

}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addJsToLoadMenu() {

    echo '  $(document).ready(function() {';
    echo '    $(".href-irrigation").click(function(e) {';
    echo '        e.preventDefault();';
    echo '        get_content("irrigation",get_array);';
    echo '    });';
    echo '  });';

}

// {{{ addInStartXMLCultiCore()
// ROLE Add in menu
// RET none
function addInStartXMLCultiCore() {

    $ret_array = array ( 
        'name' => "serverIrrigation",
        'waitAfterUS' => "100",
        'pathexe' => "tclsh",
        'path' => "./serverIrrigation/serverIrrigation.tcl",
        'xmlconf' => "./serverIrrigation/conf.xml",
    );

    return $ret_array;
}

// {{{ getPlateforme()
// ROLE Retrieve sensor information in db with this name
// IN $element : Type of element (sensor, plug, other)
// IN $indexElem : Index of this element
// RET Every information about this element in DB
function getPlateforme () {


    // Check if table configuration exists
    $sql = "SELECT * FROM irrigation_plateforme ;";
    
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

// {{{ getZone()
// ROLE Retrieve sensor information in db with this name
// IN $element : Type of element (sensor, plug, other)
// IN $indexElem : Index of this element
// RET Every information about this element in DB
function getZone ($idMother) {

    // Check if table configuration exists
    $sql = "SELECT * FROM irrigation_zone WHERE motherPlatef = '${idMother}' ;";
    
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

// {{{ getLT()
// ROLE Retrieve sensor information in db with this name
// RET Every information about this element in DB
function getLT () {


    // Check if table configuration exists
    $sql = "SELECT * FROM irrigation_lt ;";
    
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

// {{{ getLT()
// ROLE Retrieve sensor information in db with this name
// RET Every information about this element in DB
function getEngrais () {


    // Check if table configuration exists
    $sql = "SELECT * FROM irrigation_engrais ;";
    
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

// {{{ createXML()
// ROLE Retrieve sensor information in db with this name
// RET Every information about this element in DB
function createXML () {

    // Add trace level
    $paramServerIrrigationXML[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['PLUGIN_irrigation']['TRACE_LEVEL']
    );
    
    // Add every parameters of the database
    $ltList = getLT();
    foreach ($ltList as $k => $lt) {
        foreach ($lt as $key => $value) {
            $paramServerIrrigationXML[] = array (
                "key" => "localtechnique," . $key,
                "value" => $value
            );
        }        
    }
    
    $engraisList = getEngrais();
    // We add number of engrais
    $paramServerIrrigationXML[] = array (
        "key" => "nbEngrais" ,
        "value" => count($engraisList)
    );
    foreach ($engraisList as $k => $engrais) {
        foreach ($engrais as $key => $value) {
            $paramServerIrrigationXML[] = array (
                "key" => "engrais," . $engrais["engraisId"] . "," . $key,
                "value" => $value
            );
        }        
    }
    
    
    $plateformeList = getPlateforme();
    
    // We add numer of plateforme
    $paramServerIrrigationXML[] = array (
        "key" => "nbPlateforme" ,
        "value" => count($plateformeList)
    );
    
    foreach ($plateformeList as $k => $plateforme) {
        foreach ($plateforme as $key => $value) {
            $paramServerIrrigationXML[] = array (
                "key" => "plateforme," . $plateforme["idPlateforme"] . "," . $key,
                "value" => $value
            );
        }
        // Foreach plateforme, we add zones
        $zoneList = getZone($plateforme["idPlateforme"]);
        
        // We add numer of zone
        $paramServerIrrigationXML[] = array (
            "key" => "plateforme," . $plateforme["idPlateforme"] . ",nbZone" ,
            "value" => count($zoneList)
        );
        
        foreach ($zoneList as $key => $zone) {
            foreach ($zone as $key => $value) {
                $paramServerIrrigationXML[] = array (
                    "key" => "plateforme," . $plateforme["idPlateforme"] . ",zone," . $zone["zoneId"] . "," . $key,
                    "value" => $value
                );
            }
        }

    }
    
    // Save it
    \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH']. "/serverIrrigation/conf.xml" , $paramServerIrrigationXML);

    return 0;
    
}
// }}}

// {{{ fillCuve(idxCuve)
// ROLE Fill cuve
// RET 
function fillCuve ($idxCuve) {
    
    $return_array = array();
    $return_array["error"] = "";
    
    $commandLine = 'tclsh "/opt/cultipi/cultiPi/setCommand.tcl" serverIrrigation localhost fillCuve ' . $idxCuve;
    
    try {
        $ret = exec($commandLine);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }
    
    return $return_array;
}


}

?>
