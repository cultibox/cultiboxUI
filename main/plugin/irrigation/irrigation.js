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
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/sync_conf.php",
                success: function(data) {
                    // Parse result
                    var json = jQuery.parseJSON(data);
                },
                error: function(data) {
                }
            });
            $.unblockUI();
        });
    });
    
    // Load logs
    // Loop for updating
    function updatelogsServerLog() {
    
        $.ajax({
            beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) {
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            },
            cache: false,
            async: true,
            url: "main/modules/external/get_logs_cultipi.php",
            data: {
                action:"logs_server",
                nbLine:20,
                server:"serverIrrigation"
            },
            success: function (data) {
                
                var objJSON = jQuery.parseJSON(data);
                
                // Remove previous logs
                $("#logServerIrrigation").empty();
                
                if(objJSON[0].length>0) {
                    $.each(objJSON[0], function(i, item) {
                        if (item != "" && item != "NULL" )
                        {
                            $("#logServerIrrigation").append(item.substr(11)+"<br />");
                        }
                    });
                }
                $("#logServerIrrigation").scrollTop($("#logServerIrrigation")[0].scrollHeight);


                $.timeout.push(
                    setTimeout(updatelogsServerLog, 3000)
                );
            },error: function (data) {
                $.timeout.push(
                    setTimeout(updatelogsServerLog, 3000)
                );
            }
        });
    }

    // Call the function the first time
    $.timeout.push(
        setTimeout(updatelogsServerLog, 3000)
    );
    
    // Fill cuves
    $('input[name="fill_cuve"]').click(function(e) {
        e.preventDefault()
        
        // Read cuve index 
        idxcuveval = $(this).data( "idxcuve" );
        
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
                action:"fillCuve",
                idxcuve:idxcuveval
            }
        }).done(function (data) {
            $.unblockUI();
        });
    });

});


</script>



