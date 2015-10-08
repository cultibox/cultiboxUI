<script>


<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


var rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;
var advanced_regul =  <?php echo json_encode($advanced_regul); ?>;
var ajax_format;
var sd_wizard="";


formatCard = function(hdd,pourcent) {
    ajax_format = $.ajax({ 
        cache: false,
        url: "main/modules/external/format.php",
        data: {hdd:hdd, progress: parseInt(pourcent)}
    }).done(function (data) {
        $("#progress_bar").progressbar({ value: 4*parseInt(data) });
        if(data==100) { 
            $("#success_format").show();
            $("#btnCancel").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
            return true;
        } else if(data>=0) { 
                formatCard(hdd,parseInt(data)); 
        } else {
            $("#error_format").show();
            $("#btnCancel").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
        }
    });
}

// {{{
function check_rpi_update() {
        $.blockUI({
            message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
           },
           onBlock: function() {
               $.ajax({
                    cache: false,
                    url: "main/modules/external/check_rpi_update.php",
                    async: false
               }).done(function (data) {
                     $.unblockUI();
                     var objJSON = jQuery.parseJSON(data)
                     if(objJSON=="1") {
                        $("#cultipi_access_update").dialog({
                            resizable: false,
                            height:170,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_error",
                            buttons: [{
                                text: CLOSE_button, 
                                id: 'btnCloseUp',
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     } else if(objJSON=="") {
                        $("#cultipi_no_update").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                id: 'btnCloseUp',
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     } else {
                        $("#cultipi_confirm_update").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "dialog_cultibox",
                            buttons: [{
                                text: OK_button,
                                click: function () {
                                    $( this ).dialog("close");
                                    $.blockUI({
                                        message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                                        },
                                        onBlock: function() {
                                            $.ajax({
                                                cache: false,
                                                url: "main/modules/external/upgrade_rpi.php",
                                                async: false
                                            }).done(function (data) {
                                                $.unblockUI();
                                                $("#cultipi_updated").dialog({
                                                    resizable: false,
                                                    height:150,
                                                    width: 500,
                                                    modal: true,
                                                    closeOnEscape: false,
                                                    dialogClass: "popup_message",
                                                    buttons: [{
                                                        text: CLOSE_button,
                                                        click: function () {
                                                            $( this ).dialog("close");
                                                            window.location = "/cultibox"
                                                        }
                                                    }]
                                                });
                                            });
                                        }
                                    });
                                }}, {
                                text: CLOSE_button,
                                id: "btnCloseUp",
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     }
               });
            }
        });
}
// }}}

// {{{ getAlarm()
// ROLE display or not the alarm part from the configuration menu
// IN  input value: display or not 
// HOW IT WORKS: get id from div to be displayed the alarm configuration or not and display it (or not) depending the input value
// USED BY: templates/configuration.html
function getAlarm(i) {
      var divAval = document.getElementById('div_alarm_value');
      var labelAvalue = document.getElementById('label_alarm_value');

      switch(i) {
         case 0 : divAval.style.display = ''; labelAvalue.style.display = ''; break;
         case 1 : divAval.style.display = 'none'; labelAvalue.style.display = 'none'; break;
         default: divAval.style.display = ''; labelAvalue.style.display = ''; break;
      }
}
//}}}


// {{{ manageHttp()
// ROLE enable or disable https access
// IN  action: http or https
function manageHttp(action) {
    $("#confirm_restart_http").dialog({
        resizable: false,
        height:150,
        width: 500,
        modal: true,
        closeOnEscape: false,
        dialogClass: "dialog_cultibox",
        buttons: [{
            text: OK_button,
            click: function () {
                $( this ).dialog("close");
                $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        cache: false,
                        url: "main/modules/external/manage_https.php",
                        async: false,
                        data: {action:action},
                        success: function (data) {
                          $.unblockUI();
                          $("#success_restart_http").dialog({
                            resizable: false,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_message",
                            buttons: [{
                                text: OK_button,
                                click: function () {
                                    $( this ).dialog("close");
    
                                     if (window.location.protocol == "http:") {
                                        var restOfUrl = window.location.href.substr(5);
                                        window.location = "https:" + restOfUrl;
                                     } else {
                                        var restOfUrl = window.location.href.substr(6);
                                        window.location = "http:" + restOfUrl;
                                     }
                                     return false;
                                }
                            }]
                          });
                        },
                        error : function(data) {
                          $.unblockUI();
                          $("#error_restart_http").dialog({
                            resizable: false,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_error",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog("close");
                                    window.location = "/cultibox";
                                    return false;
                                }
                            }]
                          });
                        }
                    });
                } });
            }
         }, {
            text: CANCEL_button,
            click: function () {
                $( this ).dialog( "close" ); return false;
            }
         }]
    });
}


$(document).ready(function(){
     pop_up_remove("main_error");
     pop_up_remove("main_info");

    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });

    if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

    // Only for cultipi
    <?php if((isset($GLOBALS['MODE']))&&(strcmp($GLOBALS['MODE'],"cultipi")==0)) { ?>
        $.ajax({
              cache: false,
              async: true,
              url: "main/modules/external/get_soft_version.php"
        }).done(function (data) {
             var objJSON = jQuery.parseJSON(data);
            
            var version="<p class='p_center'><b><i><?php echo __('CULTIPI_SOFT_VERSION'); ?>:</i></b></p><br /><?php echo __('CULTIPI_SOFT'); ?>:  <b>"+objJSON[0]+"</b><br /><?php echo __('CULTIBOX_SOFT'); ?>:  <b>"+objJSON[1]+"</b><br /><?php echo __('CULTIRAZ_SOFT'); ?>:  <b>"+objJSON[2]+"</b><br /><?php echo __('CULTITIME_SOFT'); ?>:  <b>"+objJSON[3]+"</b><br /><?php echo __('CULTICONF_SOFT'); ?>:  <b>"+objJSON[4]+"</b><br /><?php echo __('CULTICAM_SOFT'); ?>:  <b>"+objJSON[5]+"</b><br /><?php echo __('CULTIDOC_SOFT'); ?>:  <b>"+objJSON[6]+"</b><br /><?php echo __('CULTIPI_IMAGE_VERSION'); ?>:  <b>"+objJSON[7]+"</b><br /><p class='p_center'><button id='manual_upgrade'><?php echo __('MANUAL_UPGRADE'); ?></button></p>";

            $('#version_soft').attr('title', version);
        });

        $.ajax({
             cache: false,
             async: true,
             url: "main/modules/external/scan_network.php"
        }).done(function (data) {
             $("#wifi_essid_list").empty();
             $("#wifi_essid_list").append("<p><?php echo __('WIFI_SCAN_SUBTITLE'); ?></p>");
             $.each($.parseJSON(data),function(index,value) {
                 var checked="";
                 if($("#wifi_ssid").val()==value) {
                     checked="checked";
                 }
                 $("#wifi_essid_list").append('<b>'+value+' : </b><input type="radio" name="wifi_essid" value="'+value+'" '+checked+' /><br />');
             });
        });
    <?php } ?>

    $("#manual_upgrade").live("click",function(e) {
        e.preventDefault();
        $('#upgradeupload').trigger('click');
    });


    // Call the fileupload widget and set some parameters
     $('#upgradeupload').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            data.submit();
        },
        progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress_bar_up').css(
                    'width',
                    progress + '%'
                );

                $('#progress_purcent_up').html(
                    progress + '%'
                );

                $("#progress_up").dialog({
                    width: 700,
                    modal: true,
                    resizable: false,
                    closeOnEscape: false,
                    dialogClass: "popup_message",
                    title: "<?php echo __('PROGRESS_CSV'); ?>"
                });

                if(progress==100) {
                    $('#progress_bar_up').css('width','0%');
                    $("#progress_up").dialog('close');
                    $('#progress_purcent_up').html();
                }
        },
        done: function (e, data) {
            e.preventDefault();

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });


            $.blockUI({
                message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/manual_upgrade.php",
                        data: {file:name}
                    }).done(function (data) {
                        var json = jQuery.parseJSON(data);
                        if(json==0) {
                            $.unblockUI();
                            $("#success_manual_upgrade").dialog({
                                resizable: false,
                                width: 500,
                                modal: true,
                                closeOnEscape: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" );
                                        var get_array = getUrlVars('submenu=admin_ui');
                                        get_content("configuration",get_array);
                                        return false;
                                    }
                                }]
                            });
                        } else {
                            $.unblockUI();
                            $("#error_manual_upgrade").dialog({
                                resizable: false,
                                width: 500,
                                closeOnEscape: true,
                                modal: true,
                                dialogClass: "popup_error",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        var get_array = getUrlVars('submenu=admin_ui');
                                        get_content("configuration",get_array);
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                            });
                        }
                    }); 
                } 
            });
        }});




    $('#reset_minmax').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showOn: 'both',
        buttonImage: "main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        buttonText: "<?php echo __('TIMEPICKER_BUTTON_TEXT') ;?>",
        timeFormat: 'hh:mm',
        timeText: "<?php echo __('TIMEPICKER_TIME') ;?>",
        hourText: "<?php echo __('TIMEPICKER_HOUR') ;?>",
        minuteText: "<?php echo __('TIMEPICKER_MINUTE') ;?>",
        secondText: "<?php echo __('TIMEPICKER_SECOND') ;?>",
        currentText: "<?php echo __('TIMEPICKER_ENDDAY') ;?>",
        closeText: "<?php echo __('TIMEPICKER_CLOSE') ;?>"
    });

 
    //Manage http or https procotol: 
    if(window.location.protocol=="http:") {
        $("#conf_https").show();
        $("#conf_http").css('display','none');
    } else {
        $("#conf_http").show();
        $("#conf_https").css('display','none');
    }

    $("#conf_http").click(function(e) {
        e.preventDefault();
        manageHttp("http");
    });


    $("#conf_https").click(function(e) {
        e.preventDefault();
        manageHttp("https");
    });


    $("#import_conf").click(function(e) {
        e.preventDefault();
        $('#confupload').trigger('click'); 
    });

    // Call the fileupload widget and set some parameters
     $('#confupload').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            data.submit();
        },
        progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress_bar_conf').css(
                    'width',
                    progress + '%'
                );
    
                $('#progress_purcent').html(
                    progress + '%'
                );

                $("#progress_conf").dialog({
                    width: 700,
                    modal: true,
                    resizable: false,
                    closeOnEscape: false,
                    dialogClass: "popup_message",
                    title: "<?php echo __('PROGRESS_CSV'); ?>"
                });

                if(progress==100) {
                    $('#progress_bar_conf').css('width','0%');
                    $("#progress_conf").dialog('close');
                    $('#progress_purcent').html();
                }
        },
        done: function (e, data) {
            e.preventDefault();

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });


            $.blockUI({
                message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/import_soft_config.php",
                        data: {filename:name}
                    }).done(function (data) {
                        var json = jQuery.parseJSON(data);
                        if(json==0) {
                            $.ajax({
                                type: "GET",
                                url: "main/modules/external/check_and_update_sd.php",
                                data: {
                                    sd_card:sd_card
                                },
                                async: false
                            });
                            $.unblockUI();

                            $("#success_import_conf").dialog({
                                resizable: false,
                                width: 500,
                                modal: true,
                                closeOnEscape: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CLOSE_button,
                                    id: 'btnCloseImport',
                                    click: function () {
                                        $( this ).dialog( "close" );
                                        $.ajax({
                                            cache: false,
                                            async: true,
                                            url: "main/modules/external/set_variable.php",
                                            data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                                        });

                                        var get_array = {};
                                        get_content("welcome",get_array);
                                        return false;
                                    }
                                }]
                            });
                        } else {
                            $.unblockUI();
                            $("#error_import_conf").dialog({
                                resizable: false,
                                width: 500,
                                closeOnEscape: true,
                                modal: true,
                                dialogClass: "popup_error",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        get_content("configuration");
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                            });
                        }
                    });
                }
            });
        }});




    //Export configuration:
     $('#export_conf').click(function(e) {
        e.preventDefault();

        $("#preparing-file-modal").dialog({ modal: true, resizable: false });
        $.ajax({
             cache: false,
             async: false,
             url: "main/modules/external/export_conf.php"
        }).done(function (data) {
            $("#preparing-file-modal").dialog('close');
            var json = jQuery.parseJSON(data);
            if(json==1) {
                $.fileDownload('tmp/export/backup_cultibox.sql');
            } else if(json==2) {
                $.fileDownload('tmp/export/backup_cultibox.sql.zip');
            }
        });
    });



    // Check errors for the configuration part:
    $("#submit_conf").click(function(e) {
      e.preventDefault();

      // block user interface during checking and saving
      $.blockUI({
        message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
      },
      onBlock: function() {

        var checked=true;

       
        if(typeof($("#reset_minmax").val())!="undefined") { 
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#reset_minmax").val(),type:'short_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_min_max").show(700);
                    checked=false;
                    expand('system_interface');
                } else {
                    $("#error_min_max").css("display","none");
                }
            });
        }

        if($("#alarm_activ option:selected").val()=="0001") {
            $("#alarm_value").val($("#alarm_value").val().replace(",","."));
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#alarm_value").val(),type:'alarm_value'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_alarm_value").show(700);
                    checked=false;
                    expand('alarm_interface');
                } else {
                    $("#error_alarm_value").css("display","none");
                }
            });
        }


        if(checked) {
            var check_update=true;

            
            $("#div_user_interface").find('select').each(function() {
                newValue    = $( this ).find(":selected").val();
                varToUpdate = $( this ).attr('name');

                if(varToUpdate.trim() != "" && typeof varToUpdate != "undefined") {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        try{
                            if($.parseJSON(data)!="") {  
                                check_update=false;
                            }
                        } catch(err) {
                                check_update=false;
                        }
                    });
                }
            });


            //If advanced regulation is disabled, we use default value:
            if($("#advanced_regul_options option:selected").val()=="False") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"False",
                        id:"all",
                        name:"PLUG_REGUL"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });


                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"M", 
                        id:"all",
                        name:"PLUG_COMPUTE_METHOD"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });


                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"1",     
                        id:"all",
                        name:"PLUG_REGUL_SENSOR"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });
            }



            //RTC OFFSET process:
            newValue    = $("#rtc_offset").val();
            varToUpdate = $("#rtc_offset").attr('name');

            if ($( "#rtc_offset" ).length && varToUpdate.trim() != "") {
                 $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_configuration.php",
                    data: {
                        value:newValue, 
                        variable:varToUpdate,
                        sd_card:sd_card
                    }
                }).done(function (data) {
                    try {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        } catch(err) {
                            check_update=false;
                        }
                });
            }

            //RESET MIN MAX process:
            newValue    = $("#reset_minmax").val();
            varToUpdate = $("#reset_minmax").attr('name');

            if ($( "#reset_minmax" ).length && varToUpdate.trim() != "") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_configuration.php",
                    data: {
                            value:newValue,
                            variable:varToUpdate,
                            sd_card:sd_card
                        }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });
            }


            //ALARM VALUE process:
            if($("#alarm_activ option:selected").val()=="0001") {
                newValue    = $("#alarm_value").val();
            } else {
                newValue    = 60;
            }

            varToUpdate = $("#alarm_value").attr('name');

            if ($( "#alarm_value" ).length && varToUpdate.trim() != "") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_configuration.php",
                    data: {
                            value:newValue,
                            variable:varToUpdate,
                            sd_card:sd_card
                        }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });
            }


            if(sd_card!="") {
                $.ajax({
                    type: "GET",
                    url: "main/modules/external/check_and_update_sd.php",
                    data: {
                        force_rtc_offset_value:1,
                        sd_card:sd_card
                    },
                    async: false
                }).done(function(data, textStatus, jqXHR) {
                    try {
                        // Parse result
                        var json = jQuery.parseJSON(data);

                        // For each information, show it
                        json.error.forEach(function(entry) {
                            check_update=false;
                        });
                    } catch(err) {
                        check_update=false;
                    }
                }); 
            }


             $.ajax({
                cache: false,
                url: "main/modules/external/get_variable.php",
                data: {name:"cost"}
            }).done(function (data) {
                try {
                    if(jQuery.parseJSON(data)=="1") {
                        $("#menu-cost").show();
                    } else {
                        $("#menu-cost").css('display','none');
                    }
                } catch(err) {

                }
            });

            if($("#advanced_regul_options option:selected").val()=="False") {
                var bet="False";
            } else {
                var bet="True";
            }

            $.ajax({
                cache: false,
                url: "main/modules/external/manage_dev_repo.php",
                async: false,
                data: {beta:bet}
            });

            if(check_update) {
                        $("#update_conf").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            hide: "fold",
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                id: "close-update-conf",
                                click: function () { 
                                    $( this ).dialog( "close" ); 

                                     $.ajax({
                                        cache: false,
                                        async: false,
                                        url: "main/modules/external/set_variable.php",
                                        data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                                    });

                                    var get_array = getUrlVars('submenu='+$("#submenu").val());
                                    get_content("configuration",get_array);
                                 }
                            }]
                        });
            } else  {
                        $("#error_update_conf").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            dialogClass: "popup_error",
                            hide: "fold",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () { 
                                    $( this ).dialog( "close" );  
                                    var get_array = getUrlVars('submenu='+$("#submenu").val());
                                    get_content("configuration",get_array);
                                }
                            }]
                        });
           }
        }
      } });
      $.unblockUI();
    }); 
   

    <?php if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { ?>
        $("#rtc_offset_slider").slider({
            max: 100,
            min: -100,
            slide: function( event, ui ) {
                // While sliding, update the value in the div element
                $("#rtc_offset").val(ui.value);
            },
            step: 1,
            value: rtc_offset_value
        });
    <?php } ?> 
    

    $("#reset_sd_card_submit").click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            url: "main/modules/external/check_sd.php",
            data: {path:$("#selected_hdd").val()}
         }).done(function (data) {
            if(data=="0") {
                $("#locked_sd_card").dialog({
                    width: 550,
                    resizable: false,
                    closeOnEscape: false,
                    buttons: [{ 
                        text: CLOSE_button,
                        click: function() {
                            $( this ).dialog( "close" );
                            get_content("configuration",getUrlVars("submenu=card_interface"));
                        }
                    }],
                    hide: "fold",
                    modal: true,
                    dialogClass: "popup_error"
                });
            } else {
                $("#format_dialog_sd").dialog({
                    resizable: false,
                    height:200,
                    width: 500,
                    closeOnEscape: false,
                    modal: true,
                    dialogClass: "dialog_cultibox",
                    buttons: [{
                        text: OK_button,
                        click: function () {
                            $( this ).dialog( "close" ); 
                            $("#progress").dialog({
                                resizable: false,
                                height:200,
                                width: 500,
                                closeOnEscape: false,
                                modal: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CANCEL_button,
                                    "id": "btnCancel",
                                    click: function () {
                                        var get_array = getUrlVars('submenu=card_interface');
                                        $(this).dialog('destroy').remove();
                                        get_content("configuration",get_array);
                                    }
                                }]
                            });
                            stop_format=false;
                            $("#progress_bar").progressbar({value:0});
                            $("#success_format").css("display","none");
                            $("#error_format").css("display","none");
                            formatCard($("#selected_hdd").val(),0);
                        }
                    }, {
                        text: CANCEL_button,
                        click: function () {
                            ajax_format.abort();
                            var get_array = getUrlVars('submenu=card_interface');
                            $(this).dialog('destroy').remove();
                            get_content("configuration",get_array);
                        }
                    }]
                });
            }
        });
    });
    

    
    //Reset network config:
    $("#reset_network_img").click(function(e) {
        e.preventDefault();
        $("#confirm_reset_network").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: false,
            dialogClass: "dialog_cultibox",
            buttons: [{
                text: OK_button,
                click: function () {
                    $( this ).dialog( "close" );
                    $.blockUI({
                        message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                        },
                        onBlock: function() {
                            $.ajax({
                                cache: false,
                                async: true,
                                timeout: 30000,
                                url: "main/modules/external/reset_network.php"
                            }).done(function (data) {
                                $.unblockUI();
                                $("#network_available").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,

                                    dialogClass: "popup_message",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                            }).fail(function (data) {
                                $.unblockUI();
                                $("#network_available").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_message",
                                    buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                            $( this ).dialog( "close" ); return false;
                                    }
                                    }]
                                });
                            });
                        }});
                }
            },{
                text: CLOSE_button,
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
   });



    //Change password:
    $("#change_password").click(function(e) {
        e.preventDefault();
        $("#dialog_change_password").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                click: function () {
                    $("#error_same_password").css("display","none");
                    $("#error_empty_password").css("display","none");
                    $("#new_password").val("");
                    $("#confirm_new_password").val("");
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });

    $("#save_password").click(function(e) {
          e.preventDefault();
          $("#error_same_password").css("display","none");
          $("#error_empty_password").css("display","none");

          if($("#new_password").val()=="") {
            $("#error_empty_password").show();
          } else {
            $.ajax({
              cache: false,
              async: false,
              url: "main/modules/external/check_value.php",
              data: {value:$("#new_password").val(),value2:$("#confirm_new_password").val(),type:"password"}
            }).done(function (data) {
              if(data!=1)  {
                  $("#error_same_password").show();
              } else {
                  $.blockUI({
                    message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                    },
                    onBlock: function() {
                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/reset_password.php",
                            data: {pwd:$("#new_password").val()},
                            success: function (data) {
                                $.unblockUI();
                                var objJSON = jQuery.parseJSON(data);

                                if(objJSON=="1") {
                                    $("#success_save_password").dialog({
                                        resizable: false,
                                        width: 800,
                                        modal: true,
                                        closeOnEscape: false,
                                        dialogClass: "popup_message",
                                        buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                                $("#error_same_password").css("display","none");
                                                $("#error_empty_password").css("display","none");
                                                $("#new_password").val("");
                                                $("#confirm_new_password").val("");
                                                $( this ).dialog( "close" ); return false;
                                            }
                                        }]
                                    });
                                } else {
                                    $("#error_save_password").dialog({
                                        resizable: false,
                                        width: 800,
                                        modal: true,
                                        closeOnEscape: false,
                                        dialogClass: "popup_error",
                                        buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                                $("#error_same_password").css("display","none");
                                                $("#error_empty_password").css("display","none");
                                                $("#new_password").val("");
                                                $("#confirm_new_password").val("");
                                                $( this ).dialog( "close" ); return false;
                                            }
                                        }]
                                    });
                                }
                            }, error: function(data) {
                                $("#error_save_password").dialog({
                                    resizable: false,
                                    width: 800,
                                    modal: true,
                                    closeOnEscape: false,
                                    dialogClass: "popup_error",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $("#error_same_password").css("display","none");
                                            $("#error_empty_password").css("display","none");
                                            $("#new_password").val("");
                                            $("#confirm_new_password").val("");
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                                $.unblockUI();
                            }
                        });
                    } });
              }
            });
          }
    });


// Display services logs:
    $("#view_logs").click(function(e) {
        e.preventDefault();
        $("#dialog_view_logs").dialog({
            modal: true,
            width: 600,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                 text: CLOSE_button,
                 click: function () {
                     $(this).dialog('close');
                     return false;
                 }
            }]
        });
    });


    $("a[name='cultipi_logs']").click(function(e) {
        e.preventDefault();
        $("#dialog_view_logs").dialog('close');
        var id=$(this).attr('id');
        $.blockUI({
        message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
        },
        onBlock: function() {
            $("#output_logs").empty();
            $("#error_logs").empty();
            $.ajax({
                cache: false,
                async: true,
                url: "main/modules/external/get_logs_cultipi.php",
                data: {
                    action:id
                },
                success: function (data) {
                    var objJSON = jQuery.parseJSON(data);

                    if(objJSON[0].length>0) {
                        $("#div_title_output").show();
                        $.each(objJSON[0], function(i, item) {
                            $("#output_logs").append(item+"<br />");
                        });
                    } else {
                        $("#div_title_output").css("display","none");
                    }

                    if(objJSON[1].length>0) {
                        $("#div_title_error").show();
                        $.each(objJSON[1], function(i, item) {
                            $("#error_logs").append(item+"<br />");
                        });
                    } else {
                        $("#div_title_error").css("display","none");
                    }

                    $.unblockUI(); 

                    $("#dialog_logs_cultipi").dialog({
                        modal: true,
                        width: 800,
                        height: $( window ).height(),
                        closeOnEscape: true,
                        dialogClass: "popup_message",
                        buttons: [{
                            text: CLOSE_button,
                            id: "btnCloseLogs",
                            click: function () {
                                $(this).dialog('close'); 
                                $("#dialog_view_logs").dialog('open');
                                return false;
                            }
                        }]
                    });
                },error: function (data) {
                    $("#dialog_view_logs").dialog('open');
                    $.unblockUI();
                }
            });
        }});
    });


    // Download logs services file:
    $("#dl_logs").click(function(e) {
        e.preventDefault();
        $("#dialog_dl_logs").dialog({
            modal: true,
            width: 600,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                 text: CLOSE_button,
                 click: function () {
                     $(this).dialog('close');
                     return false;
                 }
            }]
        });
    });


    $("a[name='dl_cultipi_logs']").click(function(e) {
        e.preventDefault();
        $("#dialog_dl_logs").dialog('close');
        var id=$(this).attr('id');
        $.blockUI({
        message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
        },
        onBlock: function() {
            $("#output_logs").empty();
            $("#error_logs").empty();
            $.ajax({
                cache: false,
                async: true,
                url: "main/modules/external/prepare_dl_logs.php",
                data: {action:id},
                success: function (data) {
                    var objJSON = jQuery.parseJSON(data);

                    if(objJSON!="") {
                        $.fileDownload('tmp/export/'+objJSON);
                    }

                    $.unblockUI();
                    $("#dialog_dl_logs").dialog('open');


                },error: function (data) {
                    $.unblockUI();
                    $("#dialog_dl_logs").dialog('open');
                }
            });
        }});
    });


    //Dl Bonjour setup
    $("#dl-bonjour").click(function(e) {
        e.preventDefault();
        $.fileDownload('../BonjourPSSetup.exe');
    });

    //Update RPI:
    $("#update_rpi").click(function(e) {
        e.preventDefault();
        if(advanced_regul=="True") {
            $("#confirm_upgrade").dialog({
                  resizable: false,
                  height:200,
                  width: 500,
                  modal: true,
                  closeOnEscape: false,
                  dialogClass: "popup_message",
                  buttons: [{
                      text: CLOSE_button,
                      click: function () {
                         $( this ).dialog("close");
                         if($('#beta_upgrade').is(':checked')) {
                            var bet="True";
                         } else {
                            var bet="False";
                         }

                         $.ajax({
                            cache: false,
                            url: "main/modules/external/manage_dev_repo.php",
                            async: false,
                            data: {beta:bet}
                         }).done(function(data) {
                            return false;
                         });
                      }
                  }, {
                    text: "<?php echo __('UPGRADE_BUTTON','dialog'); ?>",
                    click: function () {
                         if($('#beta_upgrade').is(':checked')) {
                            var bet="True";
                         } else {
                            var bet="False";
                         }

                         $( this ).dialog("close");
                         $.ajax({
                            cache: false,
                            url: "main/modules/external/manage_dev_repo.php",
                            async: false,
                            data: {beta:bet}
                         }).done(function (data) {   
                             check_rpi_update();                    
                         });
                      }
                  }]
             });
        } else {
            $.ajax({
               cache: false,
               url: "main/modules/external/manage_dev_repo.php",
               async: false,
               data: {beta:"False"}
            }).done(function (data) {
               check_rpi_update();
            });
        }
    });


    // Restart RPI:
    $("#restart_rpi").click(function(e) {
           e.preventDefault();
           $("#confirm_restart_rpi").dialog({
                resizable: false,
                height:150,
                width: 500,
                modal: true,
                closeOnEscape: false,
                dialogClass: "dialog_cultibox",
                buttons: [{
                    text: OK_button,
                    click: function () {
                        $( this ).dialog("close");
                        $.ajax({
                            cache: false,
                            url: "main/modules/external/services_status.php",
                            async: false,
                            data: {action:"restart_rpi"}
                        }).done(function (data) {
                            $.unblockUI();
                            $("#success_restart_rpi").dialog({
                                resizable: false,
                                height:200,
                                width: 500,
                                modal: true,
                                closeOnEscape: false,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: OK_button,
                                    click: function () {
                                        $( this ).dialog("close");
                                        return false;
                                    }
                                }]
                            });
                        });
                    }
                }, {
                    text: CANCEL_button,
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                }]
         });
    });
});


$(document).ready(function() {
    $("input:radio[name=wire_type]").click(function() {
        if($(this).val()=="static") {
            $("#wire_static").css("display","");
        } else {
            $("#wire_static").css("display","none");
        }
    });


     $("#eyes").mousedown(function() {
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="text" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });


    $("#eyes").mouseup(function(){
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="password" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });

    $("#eyes").mouseleave(function(){
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="password" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });

    $("#wifi_scan").click(function(e) {
         e.preventDefault();
         $("#wifi_essid_list").dialog({
            resizable: false,
            width: 500,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: RELOAD_button,
                click: function () {
                     $.blockUI({
                        message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                        },
                        onBlock: function() {
                            $("#wifi_essid_list").dialog('close');
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/scan_network.php"
                            }).done(function (data) {
                                $("#wifi_essid_list").empty();
                                $("#wifi_essid_list").append("<p><?php echo __('WIFI_SCAN_SUBTITLE'); ?></p>");
                                $.each($.parseJSON(data),function(index,value) {
                                    checked="";
                                    if($("#wifi_ssid").val()==value) {
                                        checked="checked";
                                    }
                                    $("#wifi_essid_list").append('<b>'+value+' : </b><input type="radio" name="wifi_essid" value="'+value+'" '+checked+' /><br />');
                                });
                                $("#wifi_essid_list").dialog('open');
                            });
                       }
                    }); 
                    $.unblockUI();
                }
             }, {
                text: CLOSE_button,
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            },{
                text: SELECT_button,
                click: function () {
                    $("#wifi_ssid").val($("input:radio[name=wifi_essid]:checked").val());
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });

    $("input:radio[name=wifi_type]").click(function() {
        if($(this).val()=="static") {
            $('#manual_ip_wifi').show();
        } else {
            $('#manual_ip_wifi').css('display', 'none');
        }
    });


    //Disable password for NONE key type:
    $('#wifi_key_type').change(function() {
        if($('#wifi_key_type').val()=="NONE") {
             $("#wifi_password").attr("disabled", "disabled");
             $("#wifi_password_confirm").attr("disabled", "disabled");
             $("#wifi_password").val("");
             $("#wifi_password_confirm").val("");
             $("#eyes").css("display","none");
        } else {
             $("#wifi_password").removeAttr("disabled");
             $("#wifi_password_confirm").removeAttr("disabled");
             $("#eyes").show();
        }


        if($('#wifi_key_type').val()=="WEP") {
            $("#hex_password").removeAttr("disabled");
        } else {
            $("#hex_password").attr("disabled", "disabled");
        }
    });


    $("#submit_conf_network").click(function(e) {
      e.preventDefault();


      $("#error_wire_ip").css("display","none");
      $("#error_wire_mask").css("display","none");
      $("#error_wifi_ssid").css("display","none");
      $("#error_wifi_password").css("display","none");
      $("#error_wifi_password_confirm").css("display","none");
      $("#error_password_wep").css("display","none");
      $("#error_password_hexa").css("display","none");
      $("#error_password_wpa").css("display","none");
      $("#error_wifi_ip").css("display","none");
      $("#error_wifi_mask").css("display","none");


      // block user interface during checking and saving
      $.blockUI({
          message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
          },
          onBlock: function() {

              var checked=true;

              if($('input[name=wire_type]:radio:checked').val()=="static") {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",

                        data: {value:$("#wire_address").val(),type:'ip'}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#error_wire_ip").show(700);
                            checked=false;
                        } else {
                            $("#error_wire_ip").css("display","none");
                        }
                    });

                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",

                        data: {value:$("#wire_mask").val(),type:'ip'}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#error_wire_mask").show(700);
                            checked=false;
                        } else {
                            $("#error_wire_mask").css("display","none");
                        }
                    });

                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",

                        data: {value:$("#wire_gw").val(),type:'ip'}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#wire_gw").val("0.0.0.0");
                        } 
                    });
                }


                var type_password="";
                //If password and SSID is not set, we use the adHoc connection:
                if(($("#wifi_ssid").val()!="")||($("#wifi_password").val()!="")||($("#wifi_password_confirm").val()!="")) {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {value:$("#wifi_ssid").val(),type:'ssid'}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#error_wifi_ssid").show(700);
                            checked=false;
                        } else {
                            $("#error_wifi_ssid").css("display","none");
                        }
                    });


                    switch ($("#wifi_key_type").val()) {
                        case 'NONE': type_password="password_none";
                            break;
                        case 'WEP': type_password="password_wep"
                            break;
                        case 'WPA AUTO': type_password="password_wpa";
                            break;
                        default: type_password="";
                    }

                    if($("#wifi_key_type").val()!="NONE") {
                        if($("#wifi_password").val()=="") {
                            $("#error_wifi_empty_password").css("display","");
                            checked=false;
                        } else if($("#wifi_password").val()!="") {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/check_value.php",
                                data: {value:$("#wifi_password").val(),value2:$("#wifi_password_confirm").val(),type:'password'}
                            }).done(function (data) {
                                $("#error_wifi_empty_password").css("display","none");
                                if(data!=1) {
                                    $("#error_wifi_password").show(700);
                                    $("#error_wifi_password_confirm").show(700);
                                    $("#error_password_wep").css("display","none");
                                    $("#error_password_wep_hexa").css("display","none");
                                    $("#error_password_wpa").css("display","none");
                                    checked=false;
                                } else {
                                    $("#error_wifi_password").css("display","none");
                                    $("#error_wifi_password_confirm").css("display","none");

                                    if($("#hex_password").attr('checked')) {
                                        var hex="1";
                                    } else {
                                        var hex="0";
                                    }

                                    $.ajax({
                                        cache: false,
                                        async: false,
                                        url: "main/modules/external/check_value.php",
                                        data: {value:$("#wifi_password").val(),type:type_password,hex:hex}
                                    }).done(function (data) {
                                        if(data!=1)  {
                                            checked=false;
                                            switch (type_password) {
                                                case 'password_wep': 
                                                    if($("#hex_password").attr('checked')) {
                                                        $("#error_password_wep_hexa").show(700);
                                                        $("#error_password_wpa").css("display","none");
                                                        $("#error_password_wep").css("display","none");
                                                    } else {
                                                        $("#error_password_wep").show(700);
                                                        $("#error_password_wpa").css("display","none");
                                                        $("#error_password_wep_hexa").css("display","none");   
                                                    }
                                                    break;
                                                case 'password_wpa': 
                                                    $("#error_password_wep").css("display","none");
                                                    $("#error_password_wpa").show(700);
                                                    $("#error_password_wep_hexa").css("display","none");
                                                    break;
                                                default: 
                                                    $("#error_password_wep").css("display","none")
                                                    $("#error_password_wpa").css("display","none");
                                                    $("#error_password_wep_hexa").css("display","none");
                                            }
                                        } else {
                                            $("#error_password_wep").css("display","none");
                                            $("#error_password_wpa").css("display","none");
                                            $("#error_password_wep_hexa").css("display","none");
                                        }
                                    });
                                }
                            });
                        }
                    } 
                

                    if($('input[name=wifi_type]:radio:checked').val()=="static") {
                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wifi_ip").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wifi_ip").show(700);
                                checked=false;
                            } else {
                                $("#error_wifi_ip").css("display","none");
                            }
                        });


                         $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wifi_mask").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wifi_mask").show(700);
                                checked=false;
                            } else {
                                $("#error_wifi_mask").css("display","none");
                            }
                        });

                         $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",
                
                            data: {value:$("#wifi_gw").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#wifi_gw").val("0.0.0.0");
                            } 
                        });
                    }
        
                }

                var check_update=false;
                if(checked) {
                    var dataForm=$("#configform_network").serialize();
                    if($("#hex_password").attr('checked')) {
                        var hex="1";
                    } else {
                        var hex="0";
                    }

                    dataForm=dataForm+"&wifi_type="+$('input[name=wifi_type]:radio:checked').val()+"&wire_type="+$('input[name=wire_type]:radio:checked').val()+"&hex_password="+hex;

                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/create_configuration.php",
                        data: dataForm
                    }).done(function (data) {
                        try{
                            if($.parseJSON(data)=="1") {  
                                check_update=true;
                            } else {
                                $("#error_network_file").dialog({
                                    resizable: false,
                                    width: 400,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_error",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                            }

                        } catch(err) {
                            check_update=false;
                            $("#error_network_file").dialog({
                                resizable: false,
                                width: 400,
                                modal: true,
                                closeOnEscape: true,
                                dialogClass: "popup_error",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                            });
                        }
                    });
                }

                if(check_update) {
                   $.ajax({
                       cache: false,
                       async: true,
                       timeout: 30000,
                       url: "main/modules/external/restart_service.php",
                       data: {type:type_password}
                   }).done(function (data) {
                        try{
                            if($.parseJSON(data)=="1") {
                                $.unblockUI();
                                $("#network_new_addr_set").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_message",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                                });
                            } else {
                                $.unblockUI();
                                $("#error_restore_conf").dialog({
                                    resizable: false,
                                    width: 400,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_error",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                            }
                        } catch(err) {
                            $.unblockUI();
                            $("#error_restore_conf").dialog({
                                    resizable: false,
                                    width: 400,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_error",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                            });
                        }
                   })
                  .fail(function() {
                        $.unblockUI();
                        //When restarting the network service, the Ajax call fails:
                        $("#network_new_addr_set").dialog({
                            resizable: false,
                            width: 500,
                            modal: true,
                            closeOnEscape: true,
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                         }]
                       });
                  });
                } else {
                    $.unblockUI();
                }
              }
        });
    });

    

    $('#wifi_wizard_link').click(function(e) {
       e.preventDefault();
       var count_wifi=1;
       open_dialog_wifi_wizard(count_wifi);
    });

    $('#dl_firm').click(function(e) {
       e.preventDefault();
       $("#dl_firm_div").dialog({
            resizable: false,
            width: 700,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
               text: CLOSE_button,
               id: "btnCloseFirm",
               click: function () {
                 $( this ).dialog( "close" );
                 return false;
               }
            }]
       });
    });

    $('#dl_cultibox_firm').click(function(e) {
       e.preventDefault();
       $.fileDownload('main/templates/data/cultibox_firmware/firm.hex');
    });

    $('#dl_wifi_firm').click(function(e) {
       e.preventDefault();
       $("#dl_firm_div").dialog('close');
       $("#wifi_upgrade").dialog({
            resizable: false,
            width: 700,
            modal: false,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
               text: CLOSE_button,
               click: function () {
                 $( this ).dialog( "close" );
                 $("#dl_firm_div").dialog('open');
                 return false;
               }
            }]
       });
       $.fileDownload('main/templates/data/cultibox_firmware_wifi/firm.hex');
    });

    
    // Send mail section
    
    //Initial HTML:
    var htmlMail = $("#mail_config_div").html();

    $('#mail-config').click(function(e) {
        e.preventDefault();
        $("#mail_config_div").dialog({
            width: 700,
            modal: true,
            resizable: false,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                text: TEST_button,
                click: function () {
                    $.blockUI({
                        message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                        },
                        onBlock: function() {
                            
                            // Create XML 
                            EMAIL_USE_SSL = "false";
                            if( $('#EMAIL_USE_SSL').is(':checked') ){
                                EMAIL_USE_SSL = "true";
                            }
                            
                            $.ajax({
                                async: true,
                                url: "main/modules/external/save_configuration.php",
                                data: {
                                    parttosave:"email",
                                    EMAIL_PROVIDER:$('#EMAIL_PROVIDER').val(),
                                    EMAIL_SMTP:$('#EMAIL_SMTP').val(),
                                    EMAIL_PORT:$('#EMAIL_PORT').val(),
                                    EMAIL_ADRESS:$('#EMAIL_ADRESS').val(),
                                    EMAIL_PASSWORD:$('#EMAIL_PASSWORD').val(),
                                    EMAIL_USE_SSL:EMAIL_USE_SSL
                                }
                            }).done(function (data) {
                                
                                // Test send eMail
                                $.ajax({
                                    async: true,
                                    url: "main/modules/external/save_configuration.php",
                                    data: {
                                        parttosave:"testemail",
                                        EMAIL_ADRESS:$('#EMAIL_ADRESS').val(),
                                    }
                                }).done(function (data) {
                                    
                                    var objJSON = jQuery.parseJSON(data)

                                    $.unblockUI();
                                    $("#test_send_mail").dialog({
                                        resizable: false,
                                        width: 500,
                                        modal: true,
                                        closeOnEscape: true,
                                        dialogClass: "popup_message",
                                        buttons: [{
                                             text: CLOSE_button,
                                             click: function () {
                                                $( this ).dialog( "close" );
                                                return false;
                                             }
                                         }]
                                     });
                                     
                                     // Erreur lorsque le smtp n'est pas le bon :
                                     // couldn't        open    socket: Le      nom     demandé est     valide, mais    aucune  donnée  du  type        requise n?a     été     trouvée.
                                     // Mauvais nom d'utilisateur ou Mot de passe :
                                     // 530:    5.5.1   Authentication  Required.       Learn   more    at 
                                     // L'utilisateur doit cocher la case SSL :
                                     // 530:    5.7.0   Must    issue   a       STARTTLS        command first.  mc18sm5886048wic.23     -   gsmtp
                                     // Autres tests a realiser : utilisation du mauvais port
                                     if (objJSON.status != "OK") 
                                     {
                                        $("#test_send_mail_return").html(objJSON.status);
                                     }
                                     
                                });
                            });
                        }
                    });
                }
            }, {
                text: SAVE_button,
                click: function () {
                    $(this).dialog("close");
                    $.blockUI({
                        message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                        },
                        onBlock: function() {
                            EMAIL_USE_SSL = "false";
                            if( $('#EMAIL_USE_SSL').is(':checked') ){
                                EMAIL_USE_SSL = "true";
                            }
                            
                            $.ajax({
                                async: true,
                                url: "main/modules/external/save_configuration.php",
                                data: {
                                    parttosave:"email",
                                    EMAIL_PROVIDER:$('#EMAIL_PROVIDER').val(),
                                    EMAIL_SMTP:$('#EMAIL_SMTP').val(),
                                    EMAIL_PORT:$('#EMAIL_PORT').val(),
                                    EMAIL_ADRESS:$('#EMAIL_ADRESS').val(),
                                    EMAIL_PASSWORD:$('#EMAIL_PASSWORD').val(),
                                    EMAIL_USE_SSL:EMAIL_USE_SSL
                                }
                            }).done(function (data) {
                                $.unblockUI();
                                $("#success_save_mail").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_message",
                                    buttons: [{
                                         text: CLOSE_button,
                                         click: function () {
                                            $( this ).dialog( "close" );
                                            return false;
                                         }
                                     }]
                                 });
                            });
                        }
                    });
                    return false;
                }
            }, {
                text: CLOSE_button,
                click: function () {
                    $("#mail_config_div").dialog("destroy");
                    $("#mail_config_div").html(htmlMail);
                    return false;
                }
            }]
        });
    });

    $( "#EMAIL_PROVIDER" ).change(function() {
        selectvalue = $(this).val();
        if (selectvalue == "other") {
            $('#EMAIL_SMTP').prop('disabled', false);
        } else {
            $('#EMAIL_SMTP').prop('disabled', true);
            $('#EMAIL_SMTP').val(selectvalue);
        }

    });
    
    
    // Supervision section
    
    //Initial HTML:
    var htmlSupervision = $("#supervision_config_div").html();

    $('#supervision_config').click(function(e) {
        e.preventDefault();
        $("#supervision_config_div").dialog({
            width: 500,
            modal: true,
            resizable: false,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                click: function () {
                    $(this).dialog("close");
                    $("#supervision_config_div").html(htmlSupervision);
                    return false;
                }
            }]
        });
    });
    
    // Variable to know index for process 
    $('#button_supervision_add').click(function(e) {
        e.preventDefault();
        
        idProcess = $("#button_supervision_add").data( "nextprocess" );
        
        switch ($('#button_supervision_add_type').val()) {
            case "checkPing" :
                dialogMsgToShow = "supervision_edit_checkPing";
                break;
            case "checkSensor" :
                dialogMsgToShow = "supervision_edit_checkSensor";
                break;
            case "report" :
                dialogMsgToShow = "supervision_edit_dailyReport";
                break;
        }
        
        $("#" + dialogMsgToShow).dialog({
            modal: true,
            resizable: false,
            minWidth: 500,
            dialogClass: "popup_message",
            buttons: [{
                text: SAVE_button,
                click: function () {
                    // Create XML and save it 
                    var XMLVal = new Array();

                    switch (dialogMsgToShow) {
                        case "supervision_edit_checkPing" :
                            nbIpToDeclare = 0;
                            for (i =0 ; i < 5 ; i++) 
                            {
                                if ($("#supervision_edit_checkPing_ip_" + i).val() != "") 
                                {
                                    nbIpToDeclare++;
                                } else {
                                    i = 6;                                                
                                }
                            }
                            XMLVal = {
                                action: "checkPing",
                                "IP,0": $("#supervision_edit_checkPing_ip_0").val(),
                                "IP,1": $("#supervision_edit_checkPing_ip_1").val(),
                                "IP,2": $("#supervision_edit_checkPing_ip_2").val(),
                                "IP,3": $("#supervision_edit_checkPing_ip_3").val(),
                                "IP,4": $("#supervision_edit_checkPing_ip_4").val(),
                                nbIP:nbIpToDeclare,
                                timeMax: $("#supervision_edit_checkPing_timemax").val(),
                                eMail:   $("#supervision_edit_checkPing_email").val(),
                                "error,action":   "sendMail " + $("#supervision_edit_checkPing_email").val()
                            }
                            actionVal = "checkPing";
                            actionName = "Vérification ping";
                            break;
                        case "supervision_edit_checkSensor" :
                            XMLVal = {
                                action: "checkSensor",
                                eMail: $("#supervision_edit_checkSensor_email").val(),
                                sensor: $("#supervision_edit_checkSensor_sensor").val(),
                                sensorName: $("#supervision_edit_checkSensor_sensorname").val(),
                                sensorOutput: $("#supervision_edit_checkSensor_sensoroutput").val(),
                                valueSeuil: $("#supervision_edit_checkSensor_valueseuil").val(),
                                timeSeuilInS: $("#supervision_edit_checkSensor_timeseuilinS").val(),
                                alertIf: $("#supervision_edit_checkSensor_alertif").val()
                            }
                            actionVal = "checkSensor";
                            actionName = "Vérification capteur";
                            break;
                        case "supervision_edit_report" :
                            XMLVal = {
                                action: "report",
                                frequency: $("#supervision_edit_report_frequency").val(),
                                hour: $("#supervision_edit_report_hour").val(),
                                eMail: $("#supervision_edit_dailyReport_email").val()
                            }
                            actionVal = "report";
                            actionName = "Rapport";
                            break;
                    }
                    $(this).dialog("close");
                    $.blockUI({
                        message: "<?php echo __('CULTIPI_PLUG_FORCE_WAIT'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                        },
                        onBlock: function() {
                            $.ajax({
                                async: true,
                                url: "main/modules/external/XML_set.php",
                                data: {
                                    server:"serverSupervision",
                                    filename:"process_" + idProcess + ".xml",
                                    xml:JSON.stringify(XMLVal)
                                }
                            }).done(function (data) {
                                
                                // Update name of the button
                                $("#button_supervision_add").data( "nextprocess" , idProcess + 1);
                                
                                $.unblockUI();
                                
                                // Add the new row 
                                var RowToAdd = '<tr id="process_' + idProcess + '.xml"><td><label>' + idProcess + ' : ' + actionName + ' :</label></td> ';
                                RowToAdd = RowToAdd + '<td><input type="image" id="button_supervision_configure_process_' + idProcess + '.xml" ';
                                RowToAdd = RowToAdd + 'data-filename="process_' + idProcess + '.xml" '
                                RowToAdd = RowToAdd + 'data-supervisiontype="' + actionVal + '" '
                                RowToAdd = RowToAdd + 'data-processid="' + idProcess + '" '
                                RowToAdd = RowToAdd + 'name="button_supervision_configure" '
                                RowToAdd = RowToAdd + 'title="Configurer" '
                                RowToAdd = RowToAdd + 'src="main/libs/img/advancedsettings.png" '
                                RowToAdd = RowToAdd + 'alt="Configurer" '
                                RowToAdd = RowToAdd + '/></td>'
                                RowToAdd = RowToAdd + '<td><input type="image" id="button_supervision_remove_$file" '
                                RowToAdd = RowToAdd + 'data-filename="process_' + idProcess + '.xml" '
                                RowToAdd = RowToAdd + 'name="button_supervision_remove" '
                                RowToAdd = RowToAdd + 'title="Supprimer" '
                                RowToAdd = RowToAdd + 'src="main/libs/img/button_cancel.png" '
                                RowToAdd = RowToAdd + 'alt="Supprimer" '
                                RowToAdd = RowToAdd + '/></td>'
                                RowToAdd = RowToAdd + '</tr>'

                                $('#button_supervision_row_add_type').before(RowToAdd);
                                
                                return false;
                            });
                        }
                    });
                }
            }, {
                text: CLOSE_button,
                click: function () {
                    $(this).dialog("close");
                    return false;
                }
            }]
        });
    });
    
    // To delete a process 
    $('#supervision_config_table').on("click", "input[name='button_supervision_remove']", function(e){   
        e.preventDefault();
        
        filename = $(this).data( "filename" );
        
        $.blockUI({
            message: "<?php echo __('CULTIPI_PLUG_FORCE_WAIT'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
            },
            onBlock: function() {
                $.ajax({
                    async: true,
                    url: "main/modules/external/XML_delete.php",
                    data: {
                        server:"serverSupervision",
                        filename:filename,
                    }
                }).done(function (data) {
                    
                    // Delete the row
                    $("#" + filename.replace(".", "\\.")).remove();
                    
                    $.unblockUI();

                    return false;
                });
            }
        });
    });
    
    // To configure an supervision element 
    $('#supervision_config_table').on("click", "input[name='button_supervision_configure']", function(e){  
        e.preventDefault();
        
        filename = $(this).data( "filename" );
        supervisiontype = $(this).data( "supervisiontype" );
        processid = $(this).data( "processid" );
        
        $.blockUI({
            message: "<?php echo __('CULTIPI_PLUG_FORCE_WAIT'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
            },
            onBlock: function() {
                $.ajax({
                    async: true,
                    url: "main/modules/external/XML_get.php",
                    data: {
                        server:"serverSupervision",
                        filename:filename,
                    }
                }).done(function (data) {
                    
                    var objJSON = jQuery.parseJSON(data)
                    
                    // load element 
                    switch (supervisiontype) {
                        case "checkPing" :
                            dialogMsgToShow = "supervision_edit_checkPing";
                            break;
                        case "checkSensor" :
                            dialogMsgToShow = "supervision_edit_checkSensor";
                            break;
                        case "report" :
                            dialogMsgToShow = "supervision_edit_dailyReport";
                            break;
                    }
                    
                    $("#" + dialogMsgToShow).dialog({
                        modal: true,
                        resizable: false,
                        dialogClass: "popup_message",
                        open: function( event, ui ) {
                            jQuery.each(objJSON, function(name, value) {
                                $("#supervision_edit_" + supervisiontype + "_" + name.toLowerCase().replace(",","_")).val(value);
                            });
                        },
                        buttons: [{
                            text: SAVE_button,
                            click: function () {
                                // Create XML and save it 
                                var XMLVal = new Array();

                                switch (dialogMsgToShow) {
                                    case "supervision_edit_checkPing" :
                                        nbIpToDeclare = 0;
                                        for (i =0 ; i < 5 ; i++) 
                                        {
                                            if ($("#supervision_edit_checkPing_ip_" + i).val() != "") 
                                            {
                                                nbIpToDeclare++;
                                            } else {
                                                i = 6;                                                
                                            }
                                        }
                                        XMLVal = {
                                            action: "checkPing",
                                            "IP,0": $("#supervision_edit_checkPing_ip_0").val(),
                                            "IP,1": $("#supervision_edit_checkPing_ip_1").val(),
                                            "IP,2": $("#supervision_edit_checkPing_ip_2").val(),
                                            "IP,3": $("#supervision_edit_checkPing_ip_3").val(),
                                            "IP,4": $("#supervision_edit_checkPing_ip_4").val(),
                                            nbIP:nbIpToDeclare,
                                            timeMax: $("#supervision_edit_checkPing_timemax").val(),
                                            eMail:   $("#supervision_edit_checkPing_email").val(),
                                            "error,action":   "sendMail " + $("#supervision_edit_checkPing_email").val()
                                        }
                                        break;
                                    case "supervision_edit_checkSensor" :
                                        XMLVal = {
                                            action: "checkSensor",
                                            eMail: $("#supervision_edit_checkSensor_email").val(),
                                            sensor: $("#supervision_edit_checkSensor_sensor").val(),
                                            sensorOutput: $("#supervision_edit_checkSensor_sensoroutput").val(),
                                            valueSeuil: $("#supervision_edit_checkSensor_valueseuil").val(),
                                            timeSeuilInS: $("#supervision_edit_checkSensor_timeseuilins").val(),
                                            alertIf: $("#supervision_edit_checkSensor_alertif").val()
                                        }
                                        break;
                                    case "supervision_edit_report" :
                                        XMLVal = {
                                            action: "report",
                                            frequency: $("#supervision_edit_report_frequency").val(),
                                            hour: $("#supervision_edit_report_hour").val(),
                                            eMail: $("#supervision_edit_dailyReport_email").val()
                                        }
                                        break;
                                }
                                $(this).dialog("close");
                                $.blockUI({
                                    message: "<?php echo __('CULTIPI_PLUG_FORCE_WAIT'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                                    },
                                    onBlock: function() {
                                        $.ajax({
                                            async: true,
                                            url: "main/modules/external/XML_set.php",
                                            data: {
                                                server:"serverSupervision",
                                                filename:"process_" + processid + ".xml",
                                                xml:JSON.stringify(XMLVal)
                                            }
                                        }).done(function (data) {
                                            $.unblockUI();
                                            return false;
                                        });
                                    }
                                });
                            }
                        }, {
                            text: CLOSE_button,
                            click: function () {
                                $(this).dialog("close");
                                return false;
                            }
                        }]
                    });
                    

                    $.unblockUI();

                    return false;
                });
            }
        });
    });
    
    
});



function open_dialog_wifi_wizard(step) {
    $("#error_sd_wizard").css('display','none');

    $("#wifi_wizard_step"+step).dialog({
        resizable: false,
        width: 700,
        modal: true,
        dialogClass: "popup_message",
        closeOnEscape: false,
        buttons: [{
           text: CLOSE_button,
           style:"margin-right:90px;",
           click: function () {
             sd_wizard="";
             $( this ).dialog( "close" );
             return false;
           }
        },{
           text: NEXT_button,
           id: "btnNEXT",
           style:"margin-right:90px;",
           click: function () {
             if(step==1) {
                $( this ).dialog("destroy");
                open_dialog_wifi_wizard(step+1)
                return false;
             }

             if(step==2) {
                 $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/get_sd.php"
                 }).done(function(data) {
                    sd_wizard = jQuery.parseJSON(data);
                 });


                 if(sd_wizard!="") {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/copy_firm_sd.php",
                        data: {path:sd_wizard,reverse:0}
                    }).done(function(data) {
                        var ret=jQuery.parseJSON(data);

                        if(ret=="false") {
                             $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/copy_firm_sd.php",
                                data: {path:sd_wizard,reverse:1}
                            });
                        }
                    });

                    $( this ).dialog( "destroy" );
                    open_dialog_wifi_wizard(step+1)
                    return false; 
                 } else {
                    $("#error_sd_wizard").show();
                 }
             }


             if(step==3) {
                $("#preparing-file-modal").dialog({ modal: true, resizable: false });
                $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/export_conf.php"
                }).done(function (data) {
                    $("#preparing-file-modal").dialog('close');
                    var json = jQuery.parseJSON(data);
                    if(json==1) {
                        $.fileDownload('tmp/export/backup_cultibox.sql');
                    } else if(json==2) {
                        $.fileDownload('tmp/export/backup_cultibox.sql.zip');
                    }
                });

                $( this ).dialog( "destroy" );
                open_dialog_wifi_wizard(step+1)
                return false;
             }

             if(step==4) {
                $( this ).dialog( "destroy" );
                open_dialog_wifi_wizard(step+1)
                return false;
             }
           }
        }],
        open: function(event, ui) { 
            if(step==4) {
                $('#btnNEXT').css("display", 'none');
            } else {
                 $("#btnNEXT").show();
            }
        }
    });
}


</script>
