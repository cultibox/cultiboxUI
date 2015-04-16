<script>


<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;
var ajax_format;


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


    <?php if((isset($GLOBALS['MODE']))&&(strcmp($GLOBALS['MODE'],"cultipi")==0)) { ?>
    $.ajax({
          cache: false,
          async: true,
          url: "main/modules/external/get_soft_version.php"
    }).done(function (data) {
         var objJSON = jQuery.parseJSON(data);
        
        var version="<p class='p_center'><b><i><?php echo __('CULTIPI_SOFT_VERSION'); ?>:</i></b></p><br /><?php echo __('CULTIPI_SOFT'); ?>:  <b>"+objJSON[0]+"</b><br /><?php echo __('CULTIBOX_SOFT'); ?>:  <b>"+objJSON[1]+"</b><br /><?php echo __('CULTIRAZ_SOFT'); ?>:  <b>"+objJSON[2]+"</b><br /><?php echo __('CULTITIME_SOFT'); ?>:  <b>"+objJSON[3]+"</b><br /><?php echo __('CULTICONF_SOFT'); ?>:  <b>"+objJSON[4]+"</b><br /><?php echo __('CULTICAM_SOFT'); ?>:  <b>"+objJSON[5]+"</b><br /><?php echo __('CULTIDOC_SOFT'); ?>:  <b>"+objJSON[6]+"</b><br /><?php echo __('CULTIPI_IMAGE_VERSION'); ?>:  <b>"+objJSON[7]+"</b>";

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

    
    // Call the fileupload widget and set some parameters
     $('#confupload').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            $('#import_conf_name').text(name);
            $('#import_conf').val("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_conf').text("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_conf').removeClass("inputDisable");
            $('#import_conf').attr('disabled', false);


            data.context = $('#import_conf').click(function (e) {
                e.preventDefault();
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
                    data.submit();
                } });
            });
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
                                    click: function () {
                                        $( this ).dialog( "close" );
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
            $("select").each(function() {
                newValue    = $( this ).find(":selected").val();
                varToUpdate = $( this ).attr('name');


                if(varToUpdate.trim() != "") {
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
                $("#locked_sd_card").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); get_content("configuration",getUrlVars("submenu=card_interface")); } }], hide: "fold", modal: true,  dialogClass: "popup_error"  });
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
    $("a[name='cultipi_logs']").click(function(e) {
        e.preventDefault();
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
                data: {action:id},
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
                            click: function () {
                                $(this).scrollTop(0);
                                $(this).dialog('close'); 
                                return false;
                            }
                        }]
                    });
                },error: function (data) {
                    $.unblockUI();
                }
            });
        }});
    });


     // Download logs services file:
    $("a[name='dl_cultipi_logs']").click(function(e) {
        e.preventDefault();
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

                },error: function (data) {
                    $.unblockUI();
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
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     }
               });
            }
        });
        


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



    $("#restart_cultipi").click(function(e) {
           e.preventDefault();
           $("#confirm_restart_cultipi").dialog({
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
                                    async: true,
                                    url: "main/modules/external/services_status.php",
                                    data: {action:"restart_cultipi"},
                                    success: function (data) {
                                        var objJSON = jQuery.parseJSON(data);
                                        if(objJSON=="0") {
                                            $.ajax({
                                                cache: false,
                                                async: true,
                                                url: "main/modules/external/services_status.php",
                                                data: {action:"status_cultipi"}
                                            }).done(function (data) {
                                                var objJSON = jQuery.parseJSON(data);
                                                if(objJSON=="0") {
                                                    $("#cultipi_on").show();
                                                    $("#cultipi_off").css('display','none');
                                                } else {
                                                    $("#cultipi_off").show();
                                                    $("#cultipi_on").css('display','none');
                                                }
                                                $.unblockUI();

                                                $("#success_restart_service").dialog({
                                                    resizable: false,
                                                    width: 400,
                                                    closeOnEscape: true,
                                                    modal: true,
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
                                            $("#error_restart_service").dialog({
                                                resizable: false,
                                                width: 400,
                                                closeOnEscape: true,
                                                modal: true,
                                                dialogClass: "popup_error",
                                                buttons: [{
                                                    text: CLOSE_button,
                                                    click: function () {
                                                        $( this ).dialog( "close" ); return false;
                                                    }
                                                }]
                                            });
                                            $.unblockUI();
                                        }
                                    }, error: function (data) {
                                        $("#error_restart_service").dialog({
                                            resizable: false,
                                            width: 400,
                                            closeOnEscape: true,
                                            modal: true,
                                            dialogClass: "popup_error",
                                            buttons: [{
                                                text: CLOSE_button,
                                                click: function () {
                                                    $( this ).dialog( "close" ); return false;
                                            } }]
                                        });
                                        $.unblockUI();
                                    }
                                });
                            }
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

    $("#activ_wire").change(function() {
        if($("#activ_wire").val()=="True") {
            $("#wire_interface").show();
        } else {
            $("#wire_interface").css("display","none");
        }
    });


    $("#activ_wifi").change(function() {
        if($("#activ_wifi").val()=="True") {
            $("#wifi_interface").show();
        } else {
            $("#wifi_interface").css("display","none");
        }
    })

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


      if(($("#activ_wire option:selected").val()=="False")&&($("#activ_wifi option:selected").val()=="False")) {
         $("#empty_network_conf").dialog({
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
      } else {
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

                if($("#activ_wire option:selected").val()=="True") {            
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
                }

                var type_password="";
                if($("#activ_wifi option:selected").val()=="True") {
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
                    var dataForm=$("#configform").serialize();
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
      }
    });
});

</script>
