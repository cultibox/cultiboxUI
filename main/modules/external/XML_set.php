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
    
    if(isset($_GET['xml']) && !empty($_GET['xml'])) {
        $xml = $_GET['xml'];
    } else  {
        echo "Try again !";
        exit;
    }    
    
    $xmlObj = json_decode($xml);
    print_r($xmlObj);
    
    $path = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/" . $server . "/" . $filename ;

    $newXML = new SimpleXMLElement("<conf></conf>");
    
    foreach ($xmlObj As $key => $value) 
    {
        $item = $newXML->addChild('item');
        $item->addAttribute('name', $key);
        $item->addAttribute('value', $value);
    }
    
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($newXML->asXML());
    $dom->save($path);
    

    echo $newXML->asXML();

    
?>
