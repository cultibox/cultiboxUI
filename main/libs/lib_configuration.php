<?php

namespace configuration {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $conf_index_col = array();
    $conf_index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $conf_index_col["VERSION"]              = array ( 'Field' => "VERSION", 'Type' => "varchar(30)", 'default_value' => '2.0.12-amd64','carac' => "NOT NULL");
    $conf_index_col["COLOR_HUMIDITY_GRAPH"] = array ( 'Field' => "COLOR_HUMIDITY_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["COLOR_TEMPERATURE_GRAPH"] = array ( 'Field' => "COLOR_TEMPERATURE_GRAPH", 'Type' => "varchar(30)", 'default_value' => "red",'carac' => "NOT NULL");
    $conf_index_col["COLOR_WATER_GRAPH"]    = array ( 'Field' => "COLOR_WATER_GRAPH", 'Type' => "varchar(30)", 'default_value' => "orange",'carac' => "NOT NULL");
    $conf_index_col["COLOR_LEVEL_GRAPH"]    = array ( 'Field' => "COLOR_LEVEL_GRAPH", 'Type' => "varchar(30)", 'default_value' => "pink",'carac' => "NOT NULL");
    $conf_index_col["COLOR_PH_GRAPH"]       = array ( 'Field' => "COLOR_PH_GRAPH", 'Type' => "varchar(30)", 'default_value' => "brown",'carac' => "NOT NULL");
    $conf_index_col["COLOR_EC_GRAPH"]       = array ( 'Field' => "COLOR_EC_GRAPH", 'Type' => "varchar(30)", 'default_value' => "yellow",'carac' => "NOT NULL");
    $conf_index_col["COLOR_OD_GRAPH"]       = array ( 'Field' => "COLOR_OD_GRAPH", 'Type' => "varchar(30)", 'default_value' => "red",'carac' => "NOT NULL");
    $conf_index_col["COLOR_ORP_GRAPH"]      = array ( 'Field' => "COLOR_ORP_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["COLOR_CO2_GRAPH"]      = array ( 'Field' => "COLOR_CO2_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["COLOR_PRESSURE_GRAPH"] = array ( 'Field' => "COLOR_PRESSURE_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["NB_PLUGS"]             = array ( 'Field' => "NB_PLUGS", 'Type' => "int(11)", 'default_value' => 3,'carac' => "NOT NULL");
    $conf_index_col["ALARM_ACTIV"]          = array ( 'Field' => "ALARM_ACTIV", 'Type' => "varchar(4)", 'default_value' => "0000",'carac' => "NOT NULL");
    $conf_index_col["ALARM_VALUE"]          = array ( 'Field' => "ALARM_VALUE", 'Type' => "varchar(5)", 'default_value' => "60.00",'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE"]           = array ( 'Field' => "COST_PRICE", 'Type' => "decimal(6,4)", 'default_value' => 0.1249,'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE_HP"]        = array ( 'Field' => "COST_PRICE_HP", 'Type' => "decimal(6,4)", 'default_value' => 0.1353,'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE_HC"]        = array ( 'Field' => "COST_PRICE_HC", 'Type' => "decimal(6,4)", 'default_value' => 0.0926,'carac' => "NOT NULL");
    $conf_index_col["START_TIME_HC"]        = array ( 'Field' => "START_TIME_HC", 'Type' => "varchar(5)", 'default_value' => "22:30",'carac' => "NOT NULL");
    $conf_index_col["STOP_TIME_HC"]         = array ( 'Field' => "STOP_TIME_HC", 'Type' => "varchar(5)", 'default_value' => "06:30",'carac' => "NOT NULL");
    $conf_index_col["COST_TYPE"]            = array ( 'Field' => "COST_TYPE", 'Type' => "varchar(20)", 'default_value' => "standard",'carac' => "NOT NULL");
    $conf_index_col["STATISTICS"]           = array ( 'Field' => "STATISTICS", 'Type' => "varchar(5)", 'default_value' => "True",'carac' => "NOT NULL");
    $conf_index_col["ADVANCED_REGUL_OPTIONS"] = array ( 'Field' => "ADVANCED_REGUL_OPTIONS", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["RESET_MINMAX"]         = array ( 'Field' => "RESET_MINMAX", 'Type' => "varchar(5)", 'default_value' => "00:00",'carac' => "NOT NULL");
    $conf_index_col["RTC_OFFSET"]           = array ( 'Field' => "RTC_OFFSET", 'Type' => "int(11)", 'default_value' => 0,'carac' => "NOT NULL");
    $conf_index_col["REMOVE_1000_CHANGE_LIMIT"] = array ( 'Field' => "REMOVE_1000_CHANGE_LIMIT", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["REMOVE_5_MINUTE_LIMIT"] = array ( 'Field' => "REMOVE_5_MINUTE_LIMIT", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["DEFAULT_LANG"]         = array ( 'Field' => "DEFAULT_LANG", 'Type' => "varchar(5)", 'default_value' => "fr_FR",'carac' => "NOT NULL");
    $conf_index_col["ENABLE_LED"]           = array ( 'Field' => "ENABLE_LED", 'Type' => "varchar(4)", 'default_value' => "0001",'carac' => "NOT NULL");
    $conf_index_col["EMAIL_ADRESS"]         = array ( 'Field' => "EMAIL_ADRESS", 'Type' => "varchar(40)", 'default_value' => "hercule.poirot@gmail.com",'carac' => "NOT NULL");
    $conf_index_col["EMAIL_PASSWORD"]       = array ( 'Field' => "EMAIL_PASSWORD", 'Type' => "varchar(40)", 'default_value' => "motdepasse",'carac' => "NOT NULL");
    $conf_index_col["EMAIL_PROVIDER"]       = array ( 'Field' => "EMAIL_PROVIDER", 'Type' => "varchar(40)", 'default_value' => "other",'carac' => "NOT NULL");
    $conf_index_col["EMAIL_SMTP"]           = array ( 'Field' => "EMAIL_SMTP", 'Type' => "varchar(40)", 'default_value' => "smtp.gmail.com",'carac' => "NOT NULL");
    $conf_index_col["EMAIL_PORT"]           = array ( 'Field' => "EMAIL_PORT", 'Type' => "int(11)", 'default_value' => 587,'carac' => "NOT NULL");
    $conf_index_col["EMAIL_USE_SSL"]        = array ( 'Field' => "EMAIL_USE_SSL", 'Type' => "varchar(5)", 'default_value' => 'true','carac' => "NOT NULL");

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'configuration';";
    
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
        $sql = "CREATE TABLE `configuration` ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."VERSION varchar(30) NOT NULL DEFAULT '2.0.12-amd64',"
            ."COLOR_HUMIDITY_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."COLOR_TEMPERATURE_GRAPH varchar(30) NOT NULL DEFAULT 'red',"
            ."COLOR_WATER_GRAPH varchar(30) NOT NULL DEFAULT 'orange',"
            ."COLOR_LEVEL_GRAPH varchar(30) NOT NULL DEFAULT 'pink',"
            ."COLOR_PH_GRAPH varchar(30) NOT NULL DEFAULT 'brown',"
            ."COLOR_EC_GRAPH varchar(30) NOT NULL DEFAULT 'yellow',"
            ."COLOR_OD_GRAPH varchar(30) NOT NULL DEFAULT 'red',"
            ."COLOR_ORP_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."COLOR_CO2_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."COLOR_PRESSURE_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."NB_PLUGS int(11) NOT NULL DEFAULT '3',"
            ."ALARM_ACTIV varchar(4) NOT NULL DEFAULT '0000',"
            ."ALARM_VALUE varchar(5) NOT NULL DEFAULT '60.00',"
            ."COST_PRICE decimal(6,4) NOT NULL DEFAULT '0.1249',"
            ."COST_PRICE_HP decimal(6,4) NOT NULL DEFAULT '0.1353',"
            ."COST_PRICE_HC decimal(6,4) NOT NULL DEFAULT '0.0926',"
            ."START_TIME_HC varchar(5) NOT NULL DEFAULT '22:30',"
            ."STOP_TIME_HC varchar(5) NOT NULL DEFAULT '06:30',"
            ."COST_TYPE varchar(20) NOT NULL DEFAULT 'standard',"
            ."STATISTICS varchar(5) NOT NULL DEFAULT 'True',"
            ."ADVANCED_REGUL_OPTIONS VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."RESET_MINMAX VARCHAR(5) NOT NULL DEFAULT '00:00',"
            ."RTC_OFFSET int(11) NOT NULL DEFAULT '0',"
            ."REMOVE_1000_CHANGE_LIMIT VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."REMOVE_5_MINUTE_LIMIT VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."DEFAULT_LANG VARCHAR(5) NOT NULL DEFAULT 'fr_FR',"
            ."ENABLE_LED varchar(4) NOT NULL DEFAULT '0001',"
            ."EMAIL_ADRESS varchar(40) NOT NULL DEFAULT 'hercule.poirot@gmail.com',"
            ."EMAIL_PASSWORD varchar(40) NOT NULL DEFAULT 'motdepasse',"
            ."EMAIL_PROVIDER varchar(40) NOT NULL DEFAULT 'other',"
            ."EMAIL_SMTP varchar(40) NOT NULL DEFAULT 'smtp.gmail.com',"
            ."EMAIL_PORT int(11) NOT NULL DEFAULT '587',"
            ."EMAIL_USE_SSL varchar(5) NOT NULL DEFAULT 'true');";
            
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

         $sql = "INSERT INTO configuration (id, VERSION, COLOR_HUMIDITY_GRAPH, COLOR_TEMPERATURE_GRAPH, COLOR_WATER_GRAPH, COLOR_LEVEL_GRAPH, COLOR_PH_GRAPH, COLOR_EC_GRAPH, COLOR_OD_GRAPH, COLOR_ORP_GRAPH,NB_PLUGS, ALARM_ACTIV, ALARM_VALUE, COST_PRICE, COST_PRICE_HP, COST_PRICE_HC, START_TIME_HC, STOP_TIME_HC, COST_TYPE, STATISTICS,ADVANCED_REGUL_OPTIONS,RESET_MINMAX, RTC_OFFSET, ENABLE_LED) VALUES (1, '2.0.12-amd64', 'blue', 'red', 'orange', 'pink', 'brown', 'yellow', 'red', 'blue', 3, '0000', '60', 0.1225, 0.1353, 0.0926, '22:30', '06:30', 'standard', 'True', 'False', '00:00',0, '0001');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

    } else {
        check_and_update_column_db ("configuration", $conf_index_col);
    } 
    $db=null;
    
}
// }}}

// {{{ getConfElem()
// ROLE Return every element of the configuration
// RET Return list of configuration
function getConfElem($elem) {

        // Check if table configuration exists
    $sql = "SELECT ${elem} FROM configuration;";
    
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

// {{{ getEmailProvider()
// ROLE Return list of providers
// RET Return list of providers
function getEmailProvider() {
    
    $ret_arr = array(
        '9_Telecom' => array(
             'name' => '9 Telecom',
             'smtp' => 'smtp.neuf.fr',
             'port' => '587'),
        'Alice' => array(
             'name' => 'Alice',
             'smtp' => 'smtp.aliceadsl.fr',
             'port' => '587'),
        'AOL' => array(
             'name' => 'AOL',
             'smtp' => 'smtp.aol.com',
             'port' => '587'),
        'AT&T' => array(
             'name' => 'AT&T',
             'smtp' => 'outbound.att.net',
             'port' => '587'),
        'Bluewin' => array(
             'name' => 'Bluewin',
             'smtp' => 'smtpauths.bluewin.ch',
             'port' => '587'),
        'Bouygtel' => array(
             'name' => 'Bouygtel',
             'smtp' => 'smtp.bouygtel.fr',
             'port' => '587'),
        'Club_Internet' => array(
             'name' => 'Club Internet',
             'smtp' => 'mail.club-internet.fr',
             'port' => '587'),
        'Free' => array(
             'name' => 'Free',
             'smtp' => 'smtp.free.fr',
             'port' => '587'),
        'Gmail' => array(
             'name' => 'Gmail',
             'smtp' => 'smtp.gmail.com',
             'port' => '587'),
        'IFra' => array(
             'name' => 'IFra',
             'smtp' => 'smtp.ifrance.fr',
             'port' => '587'),
        'Hotmail' => array(
             'name' => 'Hotmail',
             'smtp' => 'smtp.live.com',
             'port' => '587'),
        'LaPoste' => array(
             'name' => 'LaPoste',
             'smtp' => 'smtp.laposte.fr',
             'port' => '587'),
        'NetCourrier' => array(
             'name' => 'NetCourrier',
             'smtp' => 'smtp.netcourrier.com',
             'port' => '587'),
        'O2' => array(
             'name' => 'O2',
             'smtp' => 'smtp.o2.com',
             'port' => '587'),
        'Orange' => array(
             'name' => 'Orange',
             'smtp' => 'smtp.orange.fr',
             'port' => '587'),
        'Hotmail' => array(
             'name' => 'Hotmail',
             'smtp' => 'smtp.live.com',
             'port' => '587'),
        'Sympatico' => array(
             'name' => 'Sympatico',
             'smtp' => 'smtphm.sympatico.ca',
             'port' => '587'),
        'Tiscali' => array(
             'name' => 'Tiscali',
             'smtp' => 'smtp.tiscali.fr',
             'port' => '587'),
        'Verizon' => array(
             'name' => 'Verizon',
             'smtp' => 'outgoing.verizon.net',
             'port' => '587'),
        'Voila' => array(
             'name' => 'Voila',
             'smtp' => 'smtp.voila.fr',
             'port' => '587'),
        'Wanadoo' => array(
             'name' => 'Wanadoo',
             'smtp' => 'smtp.wanadoo.fr',
             'port' => '587'),
        'Yahoo' => array(
             'name' => 'Yahoo',
             'smtp' => 'mail.yahoo.com',
             'port' => '587'),
        'Other' => array(
             'name' => 'Autre',
             'smtp' => 'other',
             'port' => '')
    );
    
    return $ret_arr;

}
// }}}

// {{{ getEmailUserConf()
// ROLE Retrieve emil user configuration
// RET Email user configuration
function getEmailUserConf() {
    
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    $sql = "SELECT EMAIL_ADRESS,EMAIL_PASSWORD,EMAIL_PROVIDER,EMAIL_SMTP,EMAIL_PORT,EMAIL_USE_SSL FROM configuration;";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    return $row;
}
// }}}

// {{{ saveEmailUserConf()
// ROLE Save user email configuration
// RET
function saveEmailUserConf($param) {
    
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    $str = "";
    foreach($param As $key => $value)
    {
        if ($str != "")
            $str = $str . " , ";
        
        $str = $str . "${key}='${value}'";
        
    }
        
    $sql = "UPDATE configuration SET ${str} ;";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }

}
// }}}

// {{{ serverEmail_createXMLConf()
// ROLE Create mail configuration XML
// RET
function serverEmail_createXMLConf () {
    
    // retrieve user params
    $emailUserConf = getEmailUserConf();
    
    $paramListServerMail[] = array (
        "name" => "verbose",
        "level" => $GLOBALS['CULTIPI']['TRACE_LEVEL']['serverMail']
    );
    $paramListServerMail[] = array (
        "name" => "serverSMTP",
        "level" => $emailUserConf['EMAIL_SMTP']
    );
    $paramListServerMail[] = array (
        "name" => "port",
        "level" => $emailUserConf['EMAIL_PORT']
    );
    $paramListServerMail[] = array (
        "name" => "username",
        "level" => $emailUserConf['EMAIL_ADRESS']
    ); 
    $paramListServerMail[] = array (
        "name" => "password",
        "level" => $emailUserConf['EMAIL_PASSWORD']
    );
    $paramListServerMail[] = array (
        "name" => "useSSL",
        "level" => $emailUserConf['EMAIL_USE_SSL']
    );
    \create_conf_XML($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverMail/conf.xml" , $paramListServerMail);
    
    // Add it in générale conf 
    \create_conf_XML($GLOBALS['CULTIPI_CONF_OUT_PATH'] . "/serverMail/conf.xml" , $paramListServerMail);
    
}
// }}}

// {{{ reloadXMLinServer()
// ROLE reload XML in server
// RET
function reloadXMLinServer ($server) {
    
    $return_array = array();

    try {
        switch(php_uname('s')) {
            case 'Windows NT':
                $return_array["status"] = exec('C:\Tcl\bin\tclsh.exe "D:\CBX\cultipiCore\cultiPi\setCommand.tcl" ' . $server . ' localhost reloadXML');
                break;
            default : 
                $return_array["status"] = exec('tclsh "/opt/cultipi/cultiPi/getCommand.tcl" ' . $server . ' localhost reloadXML');
                break;
        }
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["status"] = $e->getMessage();
    }

    return $return_array;
    
}
// }}}

// {{{ serverEmail_test()
// ROLE Test send mail
// RET
function serverEmail_test ($mail) {
    
    $return_array = array();

    try {
        switch(php_uname('s')) {
            case 'Windows NT':
                $return_array["status"] = exec('C:\Tcl\bin\tclsh.exe "D:\CBX\cultipiCore\cultiPi\getCommand.tcl" -timeout 20000 serverMail localhost sendMailTest ' . $mail . ' "Email test" "Email envoyé automatiquement avec le bouton test"');
                break;
            default : 
                $return_array["status"] = exec('tclsh "/opt/cultipi/cultiPi/getCommand.tcl" -timeout 20000 serverMail localhost sendMailTest ' . $mail . ' "Email test" "Email envoyé automatiquement avec le bouton test"');
                break;
        }
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["status"] = $e->getMessage();
    }

    return $return_array;
    
}
// }}}


}
?>
