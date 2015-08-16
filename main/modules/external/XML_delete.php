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
    

    $path = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/" . $server . "/" . $filename ;

    unlink($path);

?>
