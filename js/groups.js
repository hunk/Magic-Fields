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
        
        counter_field = id.split("_")[6] +"_"+ div.split("_")[2];   
        fixcounter("counter_"+counter_field);
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
        counter_field = inputName.split("_")[4] +"_"+ inputName.split("_")[1];
        getDuplicate(customFieldId,counter,div,groupCounter,groupId,counter_field);
    });
});


/**
 * field duplicate 
 */
getDuplicate = function(fId,fcounter,div,gcounter,groupId,counter_field){
    jQuery.ajax({
        type : "POST",
        url  : mf_path+'RCCWP_GetDuplicate.php',
        data : "customFieldId="+fId+"&fieldCounter="+fcounter+"&groupCounter="+gcounter+"&groupId="+groupId,
        success: function(msg){
            jQuery("#"+div).before(msg);
			// set the editor in textarea
			add_editor_text();
			add_color_picker();
			
			//fixing the order in the indexes of the custom fields
		    fixcounter("counter_"+counter_field);
        }
    });
}


fixcounter = function(fields){
    init = 1;
    jQuery.each(jQuery('.'+fields),function(key,value){
        counter = init+key + 1;
        jQuery(this).text(counter);
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
  tinyMCE.init(
    jQuery.extend(true, {}, tinyMCEPreInit.mceInit, { editor_selector: "pre_editor" })
  );
	jQuery(".Multiline_Textbox :input[type='textarea'].pre_editor").each( function(inputField){
		var editor_text = jQuery(this).attr('id');
		tinyMCE.execCommand('mceAddControl', true, editor_text); 
		jQuery('#'+editor_text).removeClass('pre_editor');
	});
	jQuery(".markdowntextboxinterface:not(.markItUpEditor)").markItUp(mySettings);
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
