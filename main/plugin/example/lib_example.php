<?php

namespace example {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {
    
}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addInMenu() {

    echo '<li id="menu-example" class="level1 item173"><a href="/cultibox/index.php?menu=example" class="level1 href-example" ><span>example</span></a></li>';

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
        'name' => "serverExample",
        'waitAfterUS' => "100",
        'port' => "6011",
        'pathexe' => "tclsh",
        'path' => "./serverExample/serverExample.tcl",
        'xmlconf' => "./serverExample/conf.xml",
    );

    return $ret_array;
}

}

?>
