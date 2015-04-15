<?php

namespace irrigation {

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


}

?>
