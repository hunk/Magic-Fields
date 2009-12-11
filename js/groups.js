jQuery(document).ready(function(){
    
    //sorteable
    jQuery(".write_panel_wrapper").sortable({ 
        handle: ".sortable_mf",
		// function fix the problem of block of the editor visual textareas
		start: function() { 
			id =  jQuery(this).attr("id");
			jQuery("#"+id+" :input[type='textarea'].mf_editor").each( function(inputField){
				var editor_text = jQuery(this).attr('id');
				if(tinyMCE.get(editor_text)){
					tinyMCE.execCommand('mceRemoveControl', false, editor_text);
					jQuery('#'+editor_text).addClass('temp_remove_editor');
				}
			});
		},
        stop : function(){
            id =  jQuery(this).attr("id").split("_")[3];
            kids =  jQuery("#write_panel_wrap_"+id).children().filter(".magicfield_group");
            for(i=0;i < kids.length; i++){
                groupCounter =  kids[i].id.split("_")[2];
                ids = kids[i].id.split("_")[3];
                jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                jQuery("#counter_"+groupCounter+"_"+ids).text(i+1);
            }
			//add the editor visual in textareas
			jQuery("#"+jQuery(this).attr("id")+" :input[type='textarea'].temp_remove_editor").each( function(inputField){
				var editor_text = jQuery(this).attr('id');
				tinyMCE.execCommand('mceAddControl', false, editor_text);
				jQuery('#'+editor_text).removeClass('temp_remove_editor');
			});
        }
    });

    //duplicate  group
    jQuery(".duplicate_button").click(function(){
        id = jQuery(this).attr("id");        
        id = id.split("_");
        group = id[2];
        customGroupID =  id[3];
        order = id[4];
        order =  parseInt(order) + 1;
        GetGroupDuplicate(group,customGroupID,order);

    });

    //delete duplicate field
    jQuery(".delete_duplicate_field").live("click",function(event){
        id = jQuery(this).attr("id");
        div = id.split("-")[1]; 
        div = "row_"+div;
        deleteGroupDuplicate(div);
    });


    //delete  duplicate group
    jQuery(".delete_duplicate_button").live("click",function(event){
        id = jQuery(this).attr("id");
        div = id.split("-")[1];
        deleteGroupDuplicate(div);

        recount =  div.split("_")[2];
        
        kids =  jQuery("#write_panel_wrap_"+recount).children().filter(".postbox1");
        for(i=0;i < kids.length; i++){
            groupCounter =  kids[i].id.split("_")[2];
            ids = kids[i].id.split("_")[3];
            jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
        }
    }); 

    //duplicate field
    jQuery(".typeHandler").live("click",function(event){
        inputName = jQuery(this).attr("id").split("-")[1];
        customFieldId =  inputName.split("_")[0];
        groupCounter = inputName.split("_")[1];

        groupId = inputName.split("_")[3];

        oldval = jQuery("#c"+inputName+"Counter").val();    
        newval = parseInt(oldval) + 1; 
        jQuery("#c"+inputName+"Counter").val(newval); 


        counter = jQuery("#c"+inputName+"Counter").val();
        div  = "c"+inputName+"Duplicate";

        getDuplicate(customFieldId,counter,div,groupCounter,groupId);


    });
});


/**
 * field duplicate 
 */
getDuplicate = function(fId,fcounter,div,gcounter,groupId){
    jQuery.ajax({
        type : "POST",
        url  : mf_path+'RCCWP_GetDuplicate.php',
        data : "customFieldId="+fId+"&fieldCounter="+fcounter+"&groupCounter="+gcounter+"&groupId="+groupId,
        success: function(msg){
            jQuery("#"+div).after(msg);
			// set the editor in textarea
			add_editor_text();
			add_color_picker();
        }
    });
}

/**
 * Add a new duplicate group
 *
 */
GetGroupDuplicate = function(div,customGroupID,order){
    customGroupCounter =  jQuery('#g'+customGroupID+'counter').val();
    customGroupCounter++;
    jQuery("#g"+customGroupID+"counter").val(customGroupCounter);
    
    //order = jQuery("order_"+customGroupID);

    
    jQuery.ajax({
        type    : "POST",
        url     : mf_path+'RCCWP_GetDuplicate.php',
        data    : "flag=group&groupId="+customGroupID+"&groupCounter="+customGroupCounter+"&order="+order,
        success : function(msg){
            jQuery("#write_panel_wrap_"+customGroupID).append(msg);  
            kids =  jQuery("#write_panel_wrap_"+customGroupID).children().filter(".magicfield_group");
                for(i=0;i < kids.length; i++){
                    groupCounter =  kids[i].id.split("_")[2];
                    ids = kids[i].id.split("_")[3];
                    jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                    value =  i + 1;
                    jQuery("#counter_"+groupCounter+"_"+ids).text("(" + value + ")");
                }
				// set the editor in textarea
				add_editor_text();
				add_color_picker();
        }
    });
}


/**
 * Delete a Duplicate Group
 *
 */
deleteGroupDuplicate = function(div){
    jQuery("#"+div).remove();
}

/**
 * Add the editor in new textarea
 *
 */
add_editor_text = function(){
	tinyMCE.init({
		mode:"specific_textareas", editor_selector:"pre_editor", width:"100%", theme:"advanced", skin:"wp_theme", theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv,|,add_image,add_video,add_audio,add_media", theme_advanced_buttons2:"formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,wp_help", theme_advanced_buttons3:"", theme_advanced_buttons4:"", language: lan_editor, spellchecker_languages:"+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv", theme_advanced_toolbar_location:"top", theme_advanced_toolbar_align:"left", theme_advanced_statusbar_location:"bottom", theme_advanced_resizing:"1", theme_advanced_resize_horizontal:"", dialog_type:"modal", relative_urls:"", remove_script_host:"", convert_urls:"", apply_source_formatting:"", remove_linebreaks:"1", gecko_spellcheck:"1", entities:"38,amp,60,lt,62,gt", accessibility_focus:"1", tabfocus_elements:"major-publishing-actions", media_strict:"", wpeditimage_disable_captions:"", plugins:"safari,inlinepopups,spellchecker,paste,wordpress,media,fullscreen,wpeditimage,wpgallery,tabfocus"
	});
	jQuery(".Multiline_Textbox :input[type='textarea'].pre_editor").each( function(inputField){
		var editor_text = jQuery(this).attr('id');
		tinyMCE.execCommand('mceAddControl', true, editor_text); 
		jQuery('#'+editor_text).removeClass('pre_editor');
	});
}

/**
 * Add the color picker, only inputs with class mf_color_picker
 */
add_color_picker = function(){
	jQuery(":input.mf_color_picker").each( function(inputField){
		var editor_text = jQuery(this).attr('id');
		jQuery('#'+editor_text).SevenColorPicker();
	});
}
