<?php 

    require_once('../../libs/config.php');
    
    if(isset($_GET['server']) && !empty($_GET['server'])) {
        $server = $_GET['server'];
    } else  {
        echo "Try again !";
        exit;
    }
    
    if(isset($_GET['filename']) && !empty($_GET['filename'])) {
        $filename = $_GET['filename'];
    } else  {
        echo "Try again !";
        exit;
    }
    

    $xml = simplexml_load_file($GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/" . $server . "/" . $filename );
    //$actionItem = $xml->xpath('/conf/item[@name="action"]')[0]["value"];
    
    $retVal = array();
    foreach ($xml->children() as $child)
    {
        $retVal[(string)$child['name']] = (string)$child["value"];
    }

    echo json_encode($retVal);

?>
