<script>

title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

$(document).ready(function(){

    
    $('#reload').click(function(e) {

        // Block user interface
        $.blockUI({
            message: "",
            centerY: 0,
            css: {
                top: '20%',
                border: 'none',
                padding: '5px',
                backgroundColor: 'grey',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
                color: '#fffff'
            }
        });
    
        $.ajax({
            cache: false,
            async: true,
            url: "main/plugin/irrigation/external_irrigation.php", 
            data: {
                action:"reloadUserConfig"
            }
        }).done(function (data) {
            $.unblockUI();
            location.reload();
        });
    });
    
    
    // Save
    $('#save').click(function(e) {

        // Block user interface
        $.blockUI({
            message: "",
            centerY: 0,
            css: {
                top: '20%',
                border: 'none',
                padding: '5px',
                backgroundColor: 'grey',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
                color: '#fffff'
            }
        });
    
        // Creata data to send
        var dataTosend = new Object();
        $( "input" ).each(function( index ) {
            // If this is a checkbox, use .prop('checked', true);
            value = $( this ).val();
            if ($( this ).prop('type') == "checkbox")
            {
                value = $( this ).prop('checked');
            }
            
          dataTosend[$( this ).attr('id')] = value;
        });
        dataTosend["action"] = "save";
    
        $.ajax({
            cache: false,
            async: true,
            url: "main/plugin/irrigation/external_irrigation.php", 
            data: dataTosend
        }).done(function (data) {
            $.unblockUI();
        });
    });
    
    // Apply values
    $('#applyConf').click(function(e) {

        // Block user interface
        $.blockUI({
            message: "",
            centerY: 0,
            css: {
                top: '20%',
                border: 'none',
                padding: '5px',
                backgroundColor: 'grey',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
                color: '#fffff'
            }
        });
    
    
        $.ajax({
            cache: false,
            async: true,
            url: "main/plugin/irrigation/external_irrigation.php", 
            data: {
                action:"applyConf"
            }
        }).done(function (data) {
            $.unblockUI();
        });
    });    

});


</script>



