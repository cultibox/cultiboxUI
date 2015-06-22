<?php 

    if(isset($_GET['action']) && !empty($_GET['action'])) {
        $action = $_GET['action'];
    }
    
    $nbLine = 100;
    if(isset($_GET['nbLine']) && !empty($_GET['nbLine'])) {
        $nbLine = $_GET['nbLine'];
    }

    $server = "serverIrrigation";
    if(isset($_GET['server']) && !empty($_GET['server'])) {
        $server = $_GET['server'];
    }

    $ret = array();
    $ret[0]="";
    $ret[1]="";
    $err="";
    
    switch ($action) {
        case "logs_mysql" :
            exec("sudo tail -n " . $nbLine . " /var/log/mysql/mysql.log 2>/dev/null",$ret[0],$err);
            exec("sudo tail -n " . $nbLine . " /var/log/mysql/mysql.err 2>/dev/null",$ret[1],$err);
            break;

        case "logs_httpd" :
            $ret[0]="";
            exec("sudo tail -n " . $nbLine . " /var/log/lighttpd/error.log 2>/dev/null",$ret[1],$err);
            break;

        case "logs_cultipi":
            exec("sudo tail -n " . $nbLine . " /var/log/cultipi/cultipi.log 2>/dev/null",$ret[0],$err);
            $ret[1]="";
            break;

        case "logs_service":
            exec("sudo tail -n " . $nbLine . " /var/log/cultipi/cultipi-service.log 2>/dev/null",$ret[0],$err);
            $ret[1]="";
            break;

        case "logs_server":
            exec("sudo cat /var/log/cultipi/cultipi.log 2>/dev/null | grep " . $server . " | tail -n " . $nbLine ,$ret[0],$err);
            $ret[1]="";
            break;
        case "logs_system":
            exec("sudo cat /var/log/messages 2>/dev/null | tail -n " . $nbLine ,$ret[0],$err);
            $ret[1]="";
            break;
        default:
            break;
    }
    
    echo json_encode($ret);
 
?>
