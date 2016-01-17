<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;
var nb_webcam = <?php echo json_encode($GLOBALS['MAX_WEBCAM']); ?>;
var webcam_conf = <?php echo json_encode($webcam_conf); ?>;
var upload_dir="../../libs/img/";
var jqXHR;


// GLobal var for slidder
var syno_configure_element_object = {
    scaleImageId:"",
    scale:1,
    zindexImageId:"",
    z:1,
    element:"",
    rotation:"0",
    image:""
};
var syno_configure_element_object_old = {
    scaleImageId:"",
    scale:1,
    zindexImageId:"",
    z:1,
    element:"",
    rotation:"0",
    image:""
};

var idOfElem = "";
var typeOfElem = "";

var absolut_X_position = "";
var absolut_Y_position = "";

function show_webcam() {
    $("#show_webcam").dialog({
         resizable: true,
         modal: true,
         width: Math.round($( window ).width()*80/100),
         closeOnEscape: false,
         dialogClass: "popup_message",
         buttons: [{
             text: CLOSE_button,
             "id": "btnClose",
              click: function () {
                     $.ajax({
                      cache: false,
                      async: true,
                      url: "main/modules/external/enable_webcam.php",
                      data: {action:"disable"}
                     });

                     for(i=0;i<nb_webcam;i++) {
                        $("#screen_webcam"+i).attr("src", "");
                        if(i==0) {
                            $("#screen_webcam"+i).show();
                            $("#webcam"+i).show();
                        } else {
                            $("#screen_webcam"+i).css("display","none");
                            $("#webcam"+i).css("display","none");
                        }
                        $("#error_webcam"+i).css("display","none");
                        $("#div_link_webcam"+i).css("display","none");
                     }
                     $("#webcam_id0").prop("checked", true);
                     $(this).dialog('close');
               }
         }]
    });
}

function get_webcam(first) {
      var present=false;
      $.ajax({
        cache: false,
        async: true,
        url: "main/modules/external/get_webcam.php"
      }).done(function (data) {
            try {
                var objJSON = jQuery.parseJSON(data);
                $.each(objJSON, function(idx, obj) {
                    if(obj!="0") {
                        d = new Date();
                        if((obj==1)&&($('#webcam_id'+idx).is(':checked'))) {
                            var src="tmp/webcam"+idx+".jpg?v="+d.getTime();
                            $("#screen_webcam"+idx).load( function(){
                                $.unblockUI();
                                show_webcam();
                            }).error(function(){
								$.unblockUI();
                                show_webcam();
							}).attr('src', src);
                            present=true;

                            $("#screen_webcam"+idx).show();
                            $("#last_webcam"+idx).show();
                        } else {
                            var src="";
                            $("#screen_webcam"+idx).attr("src", src);
                            $("#screen_webcam"+idx).hide();
                            $("#last_webcam"+idx).hide();
                        }
                        $("#error_webcam"+idx).css("display","none");
                        $("#div_link_webcam"+idx).show();
                    } else {
                        d = new Date();
                        $("#screen_webcam"+idx).attr("src", "");
                        $("#screen_webcam"+idx).css("display","none");
                        $("#error_webcam"+idx).show();
                        $("#div_link_webcam"+idx).css("display","none");
                        $("#last_webcam"+idx).hide();
                    }
                });

                if(first=="1") {
                    $("#webcam0").show();
                }
            } catch(err) {
                $.unblockUI();
                show_webcam();
            }

            if(!present) {
                $.unblockUI();
                show_webcam();
            }
        });
}

$(document).ready(function(){
     pop_up_remove("main_error");
     pop_up_remove("main_info");

     $("#syno_configure_element_force_plug_time").slider({
        max: 3599,
        min: 1,
        slide: function( event, ui) {
            $("#equi_time").html(secondsToTime(ui.value));
            $("#duration_plug_label").val(ui.value);
        },
        step: 1,
        value: 1
     });

     for(i=0;i<nb_webcam;i++) {
        var val=i;
        if(webcam_conf[i]['auto_brightness']=="-1") {
            var auto=true;
        } else {
            var auto=false;
        }
        $("#brightness_slider"+i).slider({
            max: 100,
            min: 0,
            slide: function( event, ui) {
                $("#brightness"+$(this).attr('id').substr($(this).attr('id').length - 1)).val(ui.value);
            },
            step: 1,
            value: webcam_conf[i]['brightness'],
        });

        if(auto) {
            $("#brightness_slider"+i).slider("option","disabled",true);
        }

        if(webcam_conf[i]['auto_contrast']=="-1") {
            var auto=true;
        } else {
            var auto=false;
        }
        $("#contrast_slider"+i).slider({
            max: 100,
            min: 0,
            slide: function( event, ui ) {
                $("#contrast"+$(this).attr('id').substr($(this).attr('id').length - 1)).val(ui.value);
            },
            step: 1,
            value: webcam_conf[i]['contrast'],
            disabled: auto
        });

        if(auto) {
            $("#contrast_slider"+i).slider("option","disabled",true);
        }
    }

    $("#import_image_plug").click(function(e) {
           e.preventDefault()
           $("#add_images_syno").dialog('close');
           $("#upload_image_plug").dialog({
                resizable: false,
                width: 700,
                modal: true,
                closeOnEscape: false,
                dialogClass: "popup_message",
                open: function( event, ui ) {
                    $('#btnUpImg').attr("disabled", true);
                    $("#btnUpImg").css("color", "white");
                },
                buttons: [{
                    text: CLOSE_button,
                    click: function () {
                        $( this ).dialog("close");
                        $("#add_images_syno").dialog('open');
                        $("#upload_image_plug").dialog('close');
                        $("#label_on_img").html("");
                        $("#label_off_img").html("");
                        return false;
                    }
                }, {
                    text: SAVE_button,
                    id: "btnUpImg",
                    click: function () {
                        $(this).dialog("close");
                        $("#upload_image_plug").dialog('close');
                        upload_dir=upload_dir+"images-synoptic-plug";
                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/move_uploaded_file.php",
                            data: {filename:$("#label_on_img").html()+"===="+$("#label_off_img").html(),upload_dir:upload_dir,type:"plug"}
                        }).done(function(data) {
                            get_content("cultipi");
                        });
                        return false;
                    }
                }]
           });
    });

    var cheBu;
    $('#upload_on_file, #upload_off_file').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {            
            $("#upload_image_plug").dialog('close');
            cheBu=$(this);
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
            var uploadErrors = [];
            if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                uploadErrors.push("<?php echo __('ERROR_IMAGE_TYPE'); ?>");
            }

            if(data.originalFiles[0]['size'] > 500000) {
                uploadErrors.push("<?php echo __('ERROR_IMAGE_SIZE'); ?>");
            }

            if(uploadErrors.length > 0) {
                $("#error_upload_image").html(uploadErrors.join("<br /><br />"));
                    $("#error_upload_image").dialog({
                        width: 700,
                        modal: true,
                        resizable: false,
                        closeOnEscape: false,
                        dialogClass: "popup_error",
                        title: "<?php echo __('ERROR_UPLOAD_IMAGE'); ?>",
                        buttons : [{
                        text: CLOSE_button,
                        click: function(){
                            $(this).dialog("close");
                            $("#upload_image_plug").dialog('open');
                            $("#error_upload_image").html("");
                        }
                    }]
                 });
            } else {
                $('#progress_bar_upload_img').css('width', '0%');
                $('#progress_purcent_img').html("0%");
                $("#error_upload_image").html("");
                jqXHR = data.submit();
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress_bar_upload_img').css(
                'width',
                progress + '%'
            );

            $('#progress_purcent_img').html(
                progress + '%'
            );

            $("#progress_upload_img").dialog({
                width: 700,
                modal: true,
                resizable: false,
                closeOnEscape: false,
                dialogClass: "popup_message",
                title: "<?php echo __('PROGRESS_CSV'); ?>"
            });



            if(progress==100) {
                $("#progress_upload_img").dialog('close');
            }
        },
        done: function (e, data) {
            e.preventDefault();
            $("#upload_image_plug").dialog('open');

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });

            if(cheBu.attr('id')=="upload_on_file") {
                $("#label_on_img").html(name);
            } else {
                $("#label_off_img").html(name);
            }

            if(($("#label_on_img").html()!="") && ($("#label_off_img").html()!="")) {
                $('#btnUpImg').attr("disabled", false);
                $("#btnUpImg").css("color", "black");
            } 
        }
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





    $('a[id^="link_webcam"]').click(function(e) {
        e.preventDefault();
        var selected=$(this).attr('name');
        $("#configure_webcam"+$(this).attr('name')).dialog({
             resizable: false,
             width: 700,
             modal: false,
             closeOnEscape: false,
             dialogClass: "popup_message",
             buttons: [{
                 text: SAVE_button,
                 click: function () {
                         $(this).dialog('close');
                         $.ajax({
                            cache: false,
                            async: true,
                            url: "main/modules/external/update_webcam.php",
                            data: {id:selected,brightness:$("#brightness"+selected).val() , contrast:$("#contrast"+selected).val(), resolution:$("#resolution_value"+selected+" option:selected").val(), title: $("#webcam_name"+selected).val(), auto_contrast: $("#auto_contrast"+selected).prop('checked'),auto_brightness:$("#auto_brightness"+selected).prop('checked')}
                         }).done(function (data) {
                             $("#subtitle_webcam"+selected).html($("#webcam_name"+selected).val());
                         });
                 }
             },{
                 text: CANCEL_button,
                 "id": "btnClose",
                  click: function () {
                         $(this).dialog('close');
                   }
             }]
        });
    });

    $('a[id^="link_snapshot"]').click(function(e) {
        e.preventDefault();
        var selected=$(this).attr('name');
        $("#show_webcam").dialog('close');

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
                    url: "main/modules/external/enable_webcam.php",
                    data: {action:"enable",webcam:selected}
                }).done(function(data) {
                    $.ajax({
                        cache: false,
                        async: true,
                        url: "main/modules/external/snapshot_webcam.php",
                        data: {webcam:selected}
                    }).done(function(data) {
                         $.ajax({
                            cache: false,
                            async: true,
                            url: "main/modules/external/enable_webcam.php",
                            data: {action:"disable",webcam:selected}
                        }).done(function(data) {
                            $.unblockUI();
                            $.ajax({
                                cache: false,
                                async: true,
                                url: "main/modules/external/get_mtime.php",
                                data: {file:"/var/www/cultibox/tmp/webcam"+selected+".jpg"}
                            }).done(function(data) {
                                var objJSON = jQuery.parseJSON(data);
                                $("#last_webcam"+selected).html(objJSON);
                                get_webcam(selected);
                            });
                        });
                    });
                });
            }
        });
    });


    $('a[id^="link_stream"]').click(function(e) {
        e.preventDefault();
        var selected=$(this).attr('name');
        $("#show_webcam").dialog('close');

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
                    url: "main/modules/external/enable_webcam.php",
                    data: {action:"enable",webcam:selected}
                }).done(function(data) {
                    $.unblockUI();
                    d = new Date();
                    $("#stream_src").attr("src", "http://<?php echo $_SERVER['SERVER_ADDR']; ?>:8081/?action=stream&v="+d.getTime());
                    $("#show_stream").dialog({
                        resizable: true,
                        modal: true,
                        width: Math.round($( window ).width()*80/100),
                        closeOnEscape: false,
                        dialogClass: "popup_message",
                        buttons: [{
                            text: CLOSE_button,
                            click: function () {
                                $(this).dialog('close');
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
                                            url: "main/modules/external/enable_webcam.php",
                                            data: {action:"disable",webcam:selected}
                                        }).done(function(data) {
                                            $.unblockUI();
                                            get_webcam(selected);
                                            return false;
                                        });
                                    }
                                });
                            }
                        }]
                    });
                });
            }
        });
    });



    $("input[name=webcam]:radio").change(function () {
        for(i=0;i<nb_webcam;i++) {
            if(i==$("input[type='radio'][name='webcam']:checked").val()) {
                $("#webcam"+i).show();
            } else {
                $("#webcam"+i).css("display","none");
            }
        }
    });

    $('input[id^="auto_contrast"]').click(function(e) {
        var selected=$(this).attr('name');
        if($("#auto_contrast"+selected).is(':checked')) {
            $("#contrast_slider"+selected).slider("option","disabled",true);
            $("#label_contrast"+selected).css('display','none');
        } else {
            $("#contrast_slider"+selected).slider("option","disabled",false);
            $("#label_contrast"+selected).show();
        }
    });

    $('input[id^="auto_brightness"]').click(function(e) {
        var selected=$(this).attr('name');
        if($("#auto_brightness"+selected).is(':checked')) {
            $("#brightness_slider"+selected).slider( "option", "disabled", true );
            $("#label_brightness"+selected).css('display','none');
        } else {
            $("#brightness_slider"+selected).slider( "option", "disabled", false );
            $("#label_brightness"+selected).show();
        }
    });

    
    $('#syno_webcam').click(function(e) {
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
            get_webcam("1");
        }
        });
    });

    
    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });



     // Gestion of drag and drop
    $( "#set div" ).draggable({
        distance: 10,
        grid: [ 10, 10 ],
        containment : '#set',
        cursor: "move",
        stop:function(event, ui) {
            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    elem:$(this).attr('id').split("_")[2],
                    x:(parseInt($(this).position().left) / 10) * 10,
                    y:(parseInt($(this).position().top) / 10) * 10,
                    action:"updatePosition"
                },
                url: "main/modules/external/synoptic.php",
                success: function (data) {
                }, error: function(data) {
                }
            });
            absolut_X_position = "";
            absolut_Y_position = "";
        }
    });
    
    // Drag and drop from extern modal UI
    $( "#syno_add_element_ui div" ).draggable({
        revert: false,
        helper: 'clone',
        appendTo: '#set',
        cursor: 'move',
        zIndex: 9999,
        stop:function(event, ui) {
        
            if (absolut_X_position != "" && absolut_Y_position != "")
            {

                $.ajax({
                    cache: false,
                    type: "POST",
                    data: {
                        image:$(this).attr('id'),
                        x:(parseInt(absolut_X_position) / 10) * 10,
                        y:(parseInt(absolut_Y_position) / 10) * 10,
                        action:"addElementOther"
                    },
                    url: "main/modules/external/synoptic.php",
                    success: function (data) {

                    // Add element from database
                    if(data != "") {

                        var objJSON = jQuery.parseJSON(data);
                        
                        // Create the div
                        $("#set").append('<div id="syno_elem_' + objJSON.id + '" class="" style="position:absolute; top:' + objJSON.y + 'px ; left:' + objJSON.x + 'px ;z-index:' + objJSON.z + '" ></div>');
                        
                        var inTable = '<table>';
                        inTable = inTable +  '    <tr>';
                        inTable = inTable +  '    <td id="syno_elem_title" ></td>';
                        inTable = inTable +  '      <td>';
                        inTable = inTable +  '          <input type="image" id="syno_elemConfigur_' + objJSON.id + '" name="syno_elemConfigur_' + objJSON.id + '"';
                        inTable = inTable +  '                 title=""';
                        inTable = inTable +  '                 src="main/libs/img/advancedsettings.png"';
                        inTable = inTable +  '                 alt="configure"';
                        inTable = inTable +  '                 class="syno_conf_elem_button"';
                        inTable = inTable +  '          />';
                        inTable = inTable +  '      </td>';
                        inTable = inTable +  '    </tr>';
                        inTable = inTable +  '    <tr>';
                        inTable = inTable +  '    </tr>';
                        inTable = inTable +  '  </table>';
                
                        $("#syno_elem_" + objJSON.id).append(inTable);
                        $("#syno_elem_" + objJSON.id).append('<img id="syno_elemImage_' + objJSON.id + '" src="main/libs/img/images-synoptic-other/' + objJSON.image + '" alt="capteur" style="width:' + objJSON.scale + 'px;cursor: move" class="rotate' + objJSON.rotation + '" >');
                    
                    
                        $( "#syno_elem_" + objJSON.id ).draggable({
                            distance: 10,
                            grid: [ 10, 10 ],
                            containment : '#set',
                            cursor: "move",
                            stop:function(event, ui) {
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    data: {
                                        elem:$(this).attr('id').split("_")[2],
                                        x:(parseInt($(this).position().left) / 10) * 10,
                                        y:(parseInt($(this).position().top) / 10) * 10,
                                        action:"updatePosition"
                                    },
                                    url: "main/modules/external/synoptic.php",
                                    success: function (data) {
                                    }, error: function(data) {
                                    }
                                });

                                absolut_X_position = "";
                                absolut_Y_position = "";
                            }
                        });
                    
                    
                    }
                    }, error: function(data) {
                    }
                });
                absolut_X_position = "";
                absolut_Y_position = "";
            }
        
        }
    });
    
    
    $('#set').droppable({
        drop : function(event, ui){
            // Save X and T position of drop
            absolut_X_position = ui.position.left;
            absolut_Y_position = ui.position.top;
        }
    });
    
    // Display and control user form to add an element
    $("#syno_add_element").click(function(e) {
        e.preventDefault();
        $("#syno_add_element_ui").dialog({
            resizable: false,
            width: 300,
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


    // Add images for the synoptic:
    $("#manage_images").click(function(e) {
        e.preventDefault();
        $("#add_images_syno").dialog({
            resizable: false,
            width: 600,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                click: function () {
                    $("#add_images_syno").dialog('destroy'); return false;
                }
            }]
        });
    });

     
     $('#import_image_other, #import_image_sensor').fileupload({
            dataType: 'json',
            url: 'main/modules/external/files.php',
            add: function (e, data) {
                $("#add_images_syno").dialog('close');
                if($(this).attr('id')=='import_image_other') {
                    upload_dir=upload_dir+"images-synoptic-other";
                } else {
                    upload_dir=upload_dir+"images-synoptic-sensor";
                }

                var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
                var uploadErrors = [];
                if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                    uploadErrors.push("<?php echo __('ERROR_IMAGE_TYPE'); ?>");
                }
      
                if(data.originalFiles[0]['size'] > 500000) {
                    uploadErrors.push("<?php echo __('ERROR_IMAGE_SIZE'); ?>");
                }

                if(uploadErrors.length > 0) {
                    $("#error_upload_image").html(uploadErrors.join("<br /><br />"));
                    $("#error_upload_image").dialog({
                        width: 700,
                        modal: true,
                        resizable: false,
                        closeOnEscape: false,
                        dialogClass: "popup_error",
                        title: "<?php echo __('ERROR_UPLOAD_IMAGE'); ?>",
                        buttons : [{
                        text: CLOSE_button,
                        click: function(){
                            $(this).dialog("close");
                            $("#add_images_syno").dialog('open');
                            $("#error_upload_image").html("");
                        }
                    }]
                    });

                } else {
                    $("#error_upload_image").html("");
                    jqXHR = data.submit();
                }
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress_bar_upload').css(
                    'width',
                    progress + '%'
                );

                $('#progress_purcent').html(
                    progress + '%'
                );

                $("#progress_upload").dialog({
                    width: 700,
                    modal: true,
                    resizable: false,
                    closeOnEscape: false,
                    dialogClass: "popup_message",
                    title: "<?php echo __('PROGRESS_CSV'); ?>",
                    buttons : [{
                        text: CANCEL_button,
                        id: "cancelbtnid",
                        click: function(){
                            jqXHR.abort();
                            $(this).dialog("close");
                        }   
                    }]
                });

                
                
                if(progress==100) {
                    $("#cancelbtnid").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                }
            },
            done: function (e, data) {
                e.preventDefault();

                var name="";
                $.each(data.result.files, function (index, file) {
                    name=file.name;
                });


                $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/move_uploaded_file.php",
                    data: {filename:name,upload_dir:upload_dir}
                 }).done(function(data) {
                    get_content("cultipi");
                 });
            }
        });



    // Slider for zoom
    $("#syno_configure_element_scale").slider({
        max: 1000,
        min: 10,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_scale_val").val(ui.value);
            $('#' + syno_configure_element_object.scaleImageId).width(ui.value);
        },
        step: 1,
        value: syno_configure_element_object.scale
    });
    
    // Slider for zindex
    $("#syno_configure_element_zindex").slider({
        max: 200,
        min: 1,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_zindex_val").val(ui.value);
            $('#' + syno_configure_element_object.zindexImageId).zIndex(ui.value);
        },
        step: 1,
        value: syno_configure_element_object.z
    });
    
    // Rotation
    $( 'input[name="syno_configure_element_rotate"]:radio' ).change(
        function(){
            // retrieve the class
            var className = $('#' + syno_configure_element_object.scaleImageId).attr('class');
            $('#' + syno_configure_element_object.scaleImageId).removeClass(className);
            var newClass = $('input[name=syno_configure_element_rotate]:checked').val();
            $('#' + syno_configure_element_object.scaleImageId).addClass("rotate" + newClass);
            
            // Change td heigth for 90 and 270
            if (newClass == 0 || newClass == 180) {
                $('#' + syno_configure_element_object.scaleImageId + "_td").css("height",$('#' + syno_configure_element_object.scaleImageId).height());
            } else {
                $('#' + syno_configure_element_object.scaleImageId + "_td").css("height",$('#' + syno_configure_element_object.scaleImageId).width());
        }
        }
    );
    
    // Image
    $('#syno_configure_element_image_other, #syno_configure_element_image_plug, #syno_configure_element_image_sensor').on('change', function() {
        try {
            $('#syno_elemImage_' + idOfElem).attr('src', 'main/libs/img/images-synoptic-' + syno_configure_element_object.element + '/' + this.value);
        } catch (e) {
            alert(e.message);
        }
    });
    
    // Display and control user form for configuring item
    $('body').on('click', '.syno_conf_elem_button', function(e) {
        e.preventDefault();

        idOfElem = $(this).attr('id').split("_")[2];
        idButton = $(this).attr('id');
        
        // retriev name of this element
        elementTitle = $("#syno_elem_title_" + idOfElem).html();
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                id:idOfElem,
                action:"getParam"
            },
            position: { 
                my: "left top", 
                at: "center", 
                of: "#" + idButton
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {
            
                if(data != "") {
                    syno_configure_element_object = jQuery.parseJSON(data);

                    // Parse if needed
                    syno_configure_element_object.scale = parseInt(syno_configure_element_object.scale)
                    syno_configure_element_object.z     = parseInt(syno_configure_element_object.z)
                    
                    // Add some elements to the object
                    syno_configure_element_object.scaleImageId  = "syno_elemImage_" + idOfElem ;
                    syno_configure_element_object.zindexImageId = "syno_elem_" + idOfElem ;
                    
                    // Save it
                    syno_configure_element_object_old = syno_configure_element_object;
                    
                    // Update style of each configure element
                    $("#syno_configure_element_rotate_0" ).prop("checked", false);
                    $("#syno_configure_element_rotate_90" ).prop("checked", false);
                    $("#syno_configure_element_rotate_180" ).prop("checked", false);
                    $("#syno_configure_element_rotate_270" ).prop("checked", false);
                    $("#syno_configure_element_rotate_" + syno_configure_element_object.rotation ).prop("checked", true);
                    
                    $("#syno_configure_element_scale_val").val(syno_configure_element_object.scale);
                    $("#syno_configure_element_scale").slider("value",syno_configure_element_object.scale);
                    
                    $("#syno_configure_element_zindex_val").val(syno_configure_element_object.z);
                    $("#syno_configure_element_zindex").slider("value",syno_configure_element_object.z);

                    $('#syno_configure_element_image_' + syno_configure_element_object.element + ' option[value="' + syno_configure_element_object.image + '"]').prop('selected', true);

                    // Select correct image option
                    $("#syno_configure_element_image_other").hide();
                    $("#syno_configure_element_image_plug").hide();
                    $("#syno_configure_element_image_sensor").hide();
                    $("#syno_configure_element_image_" + syno_configure_element_object.element).show();
                    


                    $("#syno_configure_element").dialog({
                        resizable: false,
                        width: 400,
                        modal: true,
                        closeOnEscape: true,
                        dialogClass: "popup_message",
                        title:"Configurer " + elementTitle,
                        open: function( event, ui ) {
                            // remove delete button for plugs and sensor
                            if (syno_configure_element_object.element == "other") {
                                $("#DELETE_button").show();
                            } else {
                                $("#DELETE_button").hide();
                            }
                        },
                        buttons: [{
                            id: "DELETE_button",
                            text: DELETE_button,
                            click: function () {
                            
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    data: {
                                        id:idOfElem,
                                        action:"deleteElement"
                                    },
                                    url: "main/modules/external/synoptic.php"
                                }).done(function (data) {
                                });
                                
                                // Delete it
                                $( "#syno_elem_" + idOfElem ).remove();
                                
                                $( this ).dialog( "close" );
                                $.unblockUI();
                                return false;
                            }
                        } , {
                            text: CANCEL_button,
                            click: function () {
                            
                                // Roll back object value
                                
                                // Image
                                $('#syno_elemImage_' + idOfElem).attr('src', 'main/libs/img/images-synoptic-' + syno_configure_element_object_old.element + '/' + syno_configure_element_object_old.image);
                            
                                //scale
                                $('#' + syno_configure_element_object.scaleImageId).width(syno_configure_element_object_old.scale);
                                
                                //z
                                $('#' + syno_configure_element_object_old.zindexImageId).zIndex(syno_configure_element_object_old.z);
                                
                                //rotation
                                // retrieve the class
                                var className = $('#' + syno_configure_element_object_old.scaleImageId).attr('class');
                                $('#' + syno_configure_element_object_old.scaleImageId).removeClass(className);
                                var newClass = syno_configure_element_object_old.rotation;
                                $('#' + syno_configure_element_object_old.scaleImageId).addClass("rotate" + newClass);
                            
                                // Update height of the row
                                // Change td heigth for 90 and 270
                                if (newClass == 0 || newClass == 180) {
                                    $('#' + syno_configure_element_object.scaleImageId + "_td").css("height",$('#' + syno_configure_element_object.scaleImageId).height());
                                } else {
                                    $('#' + syno_configure_element_object.scaleImageId + "_td").css("height",$('#' + syno_configure_element_object.scaleImageId).width());
                                }
                            
                                $( this ).dialog( "close" );
                                $.unblockUI();
                                return false;
                            }
                        }, {
                            text: SAVE_button,
                            click: function () {
                            
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    data: {
                                        id:idOfElem,
                                        z:$("#syno_configure_element_zindex_val").val(),
                                        scale:$("#syno_configure_element_scale_val").val(),
                                        image:$( "#syno_configure_element_image_" + syno_configure_element_object.element + " option:selected" ).val(),
                                        rotation:$('input[name=syno_configure_element_rotate]:checked').val(),
                                        action:"updateZScaleImageRotation"
                                    },
                                    url: "main/modules/external/synoptic.php"
                                }).done(function (data) {
                                
                                });
                                $( this ).dialog( "close" );
                                $.unblockUI();
                                return false;
                            }
                        }]
                    });
                }
            }, error: function(data) {
            }
        });
    });
    
    
    //////////////////////////////////////////////////////////////////
    // Control plug section

    // Slider for xmax_1
    syno_configure_element_force_plug_xmax_1_slider_val = 0;
    $("#syno_configure_element_force_plug_xmax_1_slider_val").val(syno_configure_element_force_plug_xmax_1_slider_val);
    $("#syno_configure_element_force_plug_xmax_1_slider").slider({
        max: 9,
        min: 0,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_force_plug_xmax_1_slider_val").val(ui.value);
        },
        step: 1,
        value: syno_configure_element_force_plug_xmax_1_slider_val
    });
    $("#syno_configure_element_force_plug_xmax_1_slider_val").change(function() {
       syno_configure_element_force_plug_xmax_1_slider_val = $("#syno_configure_element_force_plug_xmax_1_slider_val").val();
       $( "#syno_configure_element_force_plug_xmax_1_slider" ).slider( "option", "value", syno_configure_element_force_plug_xmax_1_slider_val );
    });
    
    // Slider for xmax_2
    syno_configure_element_force_plug_xmax_2_slider_val = 0;
    $("#syno_configure_element_force_plug_xmax_2_slider_val").val(syno_configure_element_force_plug_xmax_2_slider_val);
    $("#syno_configure_element_force_plug_xmax_2_slider").slider({
        max: 9,
        min: 0,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_force_plug_xmax_2_slider_val").val(ui.value);
        },
        step: 1,
        value: syno_configure_element_force_plug_xmax_2_slider_val
    });
    $("#syno_configure_element_force_plug_xmax_2_slider_val").change(function() {
       syno_configure_element_force_plug_xmax_2_slider_val = $("#syno_configure_element_force_plug_xmax_2_slider_val").val();
       $( "#syno_configure_element_force_plug_xmax_2_slider" ).slider( "option", "value", syno_configure_element_force_plug_xmax_2_slider_val );
    });
    
    // Slider for xmax_3
    syno_configure_element_force_plug_xmax_3_slider_val = 0;
    $("#syno_configure_element_force_plug_xmax_3_slider_val").val(syno_configure_element_force_plug_xmax_3_slider_val);
    $("#syno_configure_element_force_plug_xmax_3_slider").slider({
        max: 9,
        min: 0,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_force_plug_xmax_3_slider_val").val(ui.value);
        },
        step: 1,
        value: syno_configure_element_force_plug_xmax_3_slider_val
    });
    $("#syno_configure_element_force_plug_xmax_3_slider_val").change(function() {
       syno_configure_element_force_plug_xmax_3_slider_val = $("#syno_configure_element_force_plug_xmax_3_slider_val").val();
       $( "#syno_configure_element_force_plug_xmax_3_slider" ).slider( "option", "value", syno_configure_element_force_plug_xmax_3_slider_val );
    });
    
    // Slider for dimmer
    syno_configure_element_force_plug_dimmer_slider_val = 0;
    $("#syno_configure_element_force_plug_dimmer_slider_val").val(syno_configure_element_force_plug_dimmer_slider_val);
    $("#syno_configure_element_force_plug_dimmer_slider").slider({
        max: 100,
        min: 0,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_force_plug_dimmer_slider_val").val(ui.value);
        },
        step: 1,
        value: syno_configure_element_force_plug_dimmer_slider_val
    });

    // Slider for dimmer
    syno_configure_element_force_plug_pwm_slider_val = 0;
    $("#syno_configure_element_force_plug_pwm_slider_val").val(syno_configure_element_force_plug_pwm_slider_val);
    $("#syno_configure_element_force_plug_pwm_slider").slider({
        max: 100,
        min: 0,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_force_plug_pwm_slider_val").val(ui.value);
        },
        step: 1,
        value: syno_configure_element_force_plug_pwm_slider_val
    });

    $("#syno_configure_element_force_plug_dimmer_slider_val").change(function() {
       syno_configure_element_force_plug_dimmer_slider_val = $("#syno_configure_element_force_plug_dimmer_slider_val").val();
       $( "#syno_configure_element_force_plug_dimmer_slider" ).slider( "option", "value", syno_configure_element_force_plug_dimmer_slider_val );
    });

    // Display and control user form for pilot plug
    $('body').on('click', '.syno_pilot_plug_elem_button', function(e) {
        e.preventDefault();

        idOfElem = $(this).attr('id').split("_")[2];
        idButton = $(this).attr('id');

        // Retrieve name of this element
        elementTitle = $("#syno_elem_title_" + idOfElem).html();
        
        // Clear old status value
        $('#syno_configure_element_force_plug_status').html("");
        
        // Retrieve type of the element
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                id:idOfElem,
                action:"getPlugInformation"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {
            
                if(data != "") {
                    console.log(data);
                    syno_pilot_element_object = jQuery.parseJSON(data);
                    
                    typeOfElem = syno_pilot_element_object.PLUG_MODULE;
                    
                    // Select correct options
                    switch (typeOfElem)
                    {
                        case "xmax" :
                            $("#syno_configure_element_force_plug_xmax_1").show();
                            $("#syno_configure_element_force_plug_xmax_2").show();
                            $("#syno_configure_element_force_plug_xmax_3").show();
                            $("#syno_configure_element_force_plug_dimmer").hide();
                            $("#syno_configure_element_force_plug_on_off").hide();
                            $("#syno_configure_element_force_plug_pwm").hide();
                            break;
                        case "dimmer" :
                        case "pwm" :
                            $("#syno_configure_element_force_plug_xmax_1").hide();
                            $("#syno_configure_element_force_plug_xmax_2").hide();
                            $("#syno_configure_element_force_plug_xmax_3").hide();
                            $("#syno_configure_element_force_plug_dimmer").show();
                            $("#syno_configure_element_force_plug_on_off").hide();
                            $("#syno_configure_element_force_plug_pwm").hide();
                            break;
                        case 'pwm' :
                            $("#syno_configure_element_force_plug_xmax_1").hide();
                            $("#syno_configure_element_force_plug_xmax_2").hide();
                            $("#syno_configure_element_force_plug_xmax_3").hide();
                            $("#syno_configure_element_force_plug_dimmer").hide();
                            $("#syno_configure_element_force_plug_on_off").hide();
                            $("#syno_configure_element_force_plug_pwm").show();
                            break;
                        default :
                            $("#syno_configure_element_force_plug_xmax_1").hide();
                            $("#syno_configure_element_force_plug_xmax_2").hide();
                            $("#syno_configure_element_force_plug_xmax_3").hide();
                            $("#syno_configure_element_force_plug_dimmer").hide();
                            $("#syno_configure_element_force_plug_on_off").show();
                            $("#syno_configure_element_force_plug_pwm").hide();
                            break;
                    }

                    $("#syno_pilotPlug_element").dialog({
                        resizable: true,
                        width: 600,
                        closeOnEscape: true,
                        position: { 
                            my: "left top", 
                            at: "center", 
                            of: "#" + idButton
                        },
                        dialogClass: "popup_message",
                        title:"<?php echo __('CULTIPI_SYNO_PILOTER'); ?> " + elementTitle,
                        buttons: [{
                            id: "syno_configure_element_force_plug_pilot",
                            text: "<?php echo __('CULTIPI_SYNO_PILOTER'); ?>",
                            click: function () {
                                $( this ).dialog( "close" );

                                // Retrieve the good value to send
                                switch (typeOfElem)
                                {
                                    case "xmax" :
                                        valToSend = $("#syno_configure_element_force_plug_xmax_1_slider_val").val().toString()
                                                  + $("#syno_configure_element_force_plug_xmax_2_slider_val").val().toString()
                                                  + "."
                                                  + $("#syno_configure_element_force_plug_xmax_3_slider_val").val().toString();
                                        break;
                                    case "dimmer" :
                                    case "pwm" :
                                        valToSend = $("#syno_configure_element_force_plug_dimmer_slider_val").val();
                                        if(valToSend == 0 ) {
                                                valToSend="off";
                                        }

                                        if(valToSend == 100 ) {
                                                valToSend="on";
                                        }
            
                                        break;
                                    default :
                                        valToSend = $( "#syno_configure_element_force_plug_value option:selected" ).val()
                                        break;
                                }

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
                                            cache: false,
                                            type: "POST",
                                            data: {
                                                action:"forcePlug",
                                                id:idOfElem,
                                                value:valToSend,
                                                time:$("#duration_plug_label").val()
                                            },
                                            url: "main/modules/external/synoptic.php",
                                            success: function (data) {

                                                var objJSON = jQuery.parseJSON(data);

                                                switch(objJSON.status.toUpperCase()) {
                                                    case 'TIMEOUT' :
                                                        // Update text 
                                                        $('#syno_configure_element_force_plug_status').html("<div class='error_field'><?php echo __('CULTIPI_PLUG_FORCE_NOK'); ?></div>");
                                                        break;
                                                    case 'ERROR' :
                                                        // Update text 
                                                        $('#syno_configure_element_force_plug_status').html("<div class='error_field'><?php echo __('CULTIPI_PLUG_FORCE_NOK'); ?></div>");
                                                        break;
                                                    case 'DONE' :
                                                        // Change text and image
                                                        if (valToSend == "off" || valToSend == "00.0" || valToSend == 0) {
                                                            $('#syno_elemImage_' + idOfElem).attr('title',"<?php echo __('VALUE_OFF'); ?>");
                                                            $('#syno_elemImage_' + idOfElem ).attr('src',$('#syno_elemImage_' + idOfElem ).attr('src').replace("_ON", "_OFF"));
                                                            $('#syno_pilotPlug_' + idOfElem ).attr('src',"main/libs/img/plug_on.png");
                                                        } else {
                                                            $('#syno_elemImage_' + idOfElem).attr('title',"<?php echo __('VALUE_ON'); ?>");
                                                            $('#syno_elemImage_' + idOfElem ).attr('src',$('#syno_elemImage_' + idOfElem ).attr('src').replace("_OFF", "_ON"));
                                                            $('#syno_pilotPlug_' + idOfElem ).attr('src',"main/libs/img/plug_off.png");
                                                             }

                                                        // Change opacity
                                                        $('#syno_elemImage_' + idOfElem ).css("opacity", "1");

                                                        // Update text 
                                                        $('#syno_configure_element_force_plug_status').html("<div class='info_field'><?php echo __('CULTIPI_PLUG_FORCE_OK'); ?></div>");
                                                        break;
                                                }

                                                $.unblockUI();
                                                $("#syno_pilotPlug_element").dialog( "open" );

                                            }, error: function(data) {

                                                $.unblockUI();
                                                $("#syno_pilotPlug_element").dialog( "open" );
                                            }
                                        });
                                    }
                                });
                                return false;
                            }
                        },{
                            text: CLOSE_button,
                            click: function () {
                                $( this ).dialog( "close" );
                                return false;
                            }
                        }]
                    });
                }
            }, error: function(data) {
            }
        });
    });
    

    function baseName(str)
    {
        if(typeof(str)!="undefined") {
            var base = new String(str).substring(str.lastIndexOf('/') + 1); 
        } else {
            var base="";
        }
        return base;
    }
    

    // Loop for updating sensors
    function updateSensors() {
    
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
            type: "POST",
            data: {
                action:"getAllSensorLiveValue"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {

                var objJSON = jQuery.parseJSON(data);

                if (objJSON.error == "") {
                
                    $.each( objJSON, function( key, value ) {
                        if (key != "error") {
                        
                            // Change text and opacity
                            if (value != "DEFCOM" && value != "TIMEOUT" ) {
                                newBaseName = baseName($('img[name="syno_elemSensorImage_' + key + '"]').attr('src'));
                                var valueSplitted = value.split(" "); 
                                switch(newBaseName) {
                                    case 'T_RH_sensor.png' :
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "°C " + valueSplitted[1] + "RH");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "°C " + valueSplitted[1] + "RH");
                                        break;
                                    case 'water_T_sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "°C");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "°C");
                                        break;
                                    case 'symbole_cuve.png': 
                                    case 'level_sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "cm");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "cm");
                                        break;
                                    case 'pH-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ph");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ph");
                                        break;
                                    case 'conductivity-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ec");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ec");
                                        break;
                                    case 'dissolved-oxygen-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "OD");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "OD");
                                        break;
                                    case 'ORP-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ORP");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ORP");
                                        break;
                                    case 'co2-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ppm");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ppm");
                                        break;
                                    case 'manometre-sensor.png': 
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "bar");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "bar");
                                        break;                                        
                                    default :
                                        $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "???");
                                        $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "???");
                                        break;
                                }
                                $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "1");
                            } else if (value == "TIMEOUT") {
                                $('#syno_elemValueSensor_val1_' + key).html("");
                                $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',"<?php echo __('TIMEOUT'); ?>");
                                $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "0.4");
                            } else {
                                $('#syno_elemValueSensor_val1_' + key).html("");
                                $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',"<?php echo __('DEFCOM'); ?>");
                                $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "0.4");
                            }

                        }
                    });
                    
                    var ladate=new Date();
                    $('#synoptic_updateSensorHour').html("<b>"+addZ(ladate.getHours())+":"+addZ(ladate.getMinutes())+":"+addZ(ladate.getSeconds())+"</b>");
                    
                }

                // Call again !
                updatePlugs();
                
            }, error: function(data) {
                // Call again !
                updatePlugs();
            }
        });
    }

    // Call the function the first time
    $.timeout.push(
        setTimeout(updateSensors, 3000)
    );

    // Loop for updating plugs
    function updatePlugs() {
        
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
            type: "POST",
            data: {
                action:"getAllPlugLiveValue"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {

                var objJSON = jQuery.parseJSON(data);

                if (objJSON.error == "") {
                
                    $.each( objJSON, function( key, value ) {
                        // Check if element exists
                        if ($('img[name="syno_elemPlugImage_' + key + '"]').length != 0 ) {
                        
                            // Change text and opacity
                            if (value == "DEFCOM") {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('DEFCOM'); ?>");
                                $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "0.4");
                                $('input[name="syno_pilotPlug_' + key + '"]').css("opacity", "0.4");
                            } else if (value == "off" || value == "00.0") {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('VALUE_OFF'); ?>");
                                $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "1");
                                $('input[name="syno_pilotPlug_' + key + '"]').css("opacity", "1");
                            } else if (value == "TIMEOUT") {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('TIMEOUT'); ?>");
                                $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "0.4");
                                $('input[name="syno_pilotPlug_' + key + '"]').css("opacity", "0.4");
                            } else if (value == "NA") {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('NA'); ?>");
                                $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "0.4");
                                $('input[name="syno_pilotPlug_' + key + '"]').css("opacity", "0.4");                                
                            } else {
                                // On case
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('VALUE_ON'); ?>");
                                $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "1");
                                $('input[name="syno_pilotPlug_' + key + '"]').css("opacity", "1");
                            }
                            
                            // Update image
                            if (value != "DEFCOM" && value != "off" && value != "TIMEOUT" && value != "00.0"  && value != "NA") {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('src',$('img[name="syno_elemPlugImage_' + key + '"]').attr('src').replace("_OFF", "_ON"));
                                $('input[name="syno_pilotPlug_' + key + '"]').attr('src',"main/libs/img/plug_off.png");
                            } else  {
                                $('img[name="syno_elemPlugImage_' + key + '"]').attr('src',$('img[name="syno_elemPlugImage_' + key + '"]').attr('src').replace("_ON", "_OFF"));
                                $('input[name="syno_pilotPlug_' + key + '"]').attr('src',"main/libs/img/plug_on.png");
                            }
                        }
                    });
                    
                    var ladate=new Date();
                    $('#synoptic_updatePlugHour').html("<b>"+addZ(ladate.getHours())+":"+addZ(ladate.getMinutes())+":"+addZ(ladate.getSeconds())+"</b>");
                    
                }

                // Call again !
                updateCultipiStatus();
            }, error: function(data) {
                
                // Call again !
                updateCultipiStatus();
            }
        });
    }

    // Loop for updating plugs
    function updateCultipiStatus() {
        
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
            type: "POST",
            data: {
                action:"getCultiPiStatus"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {

                var objJSON = jQuery.parseJSON(data);

                if (objJSON.error == "") {
                
                    switch (objJSON.status) {
                        case "starting" :
                        case "loading_serverLog" :
                        case "init_log" :
                        case "wait_20s" :
                        case "checking_date" :
                        case "loading_serverAcqSensor" :
                        case "loading_serverPlugUpdate" :
                        case "loading_serverHisto" :
                            $('#synoptic_updateCultipiStatus').attr('src','main/libs/img/service_restart.png');
                            $('#synoptic_updateCultipiStatus').attr('title','<?php echo __('SYNO_UPDATE_CULTIPI_STATUS_START'); ?>' + "<br />Heure locale : " + objJSON.cultihour);
                            break;
                        case "initialized" :
                            $('#synoptic_updateCultipiStatus').attr('src','main/libs/img/service_on.png');
                            $('#synoptic_updateCultipiStatus').attr('title','<?php echo __('SYNO_UPDATE_CULTIPI_STATUS_STARTED'); ?>' + "<br />Heure locale : " + objJSON.cultihour);
                            break;
                        case "TIMEOUT" :
                        case "DEFCOM" :
                        case "NA" :
                        default :
                            $('#synoptic_updateCultipiStatus').attr('src','main/libs/img/button_cancel.png');
                            $('#synoptic_updateCultipiStatus').attr('title','<?php echo __('SYNO_UPDATE_CULTIPI_STATUS_TIMEOUT'); ?>' + "<br />Heure locale : " + objJSON.cultihour);
                            break;
                    }
                }
                
                // Call again !
                $.timeout.push(
                    setTimeout(updateSensors, 2000)
                );
                
            }, error: function(data) {
                
                // Call again !
                $.timeout.push(
                    setTimeout(updateSensors, 2000)
                );
                
            }
        });
    }
});
</script>

