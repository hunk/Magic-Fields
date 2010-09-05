jQuery(document).ready(function(){
    
    moveAddToLast();

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
                jQuery("#counter_"+groupCounter+"_"+ids).text("(" + (i+1) + ")");
            }
			//add the editor visual in textareas
			jQuery("#"+jQuery(this).attr("id")+" :input[type='textarea'].temp_remove_editor").each( function(inputField){
				var editor_text = jQuery(this).attr('id');
				tinyMCE.execCommand('mceAddControl', false, editor_text);
				jQuery('#'+editor_text).removeClass('temp_remove_editor');
			});
			
			  moveAddToLast();
			  
        }
    });

    //duplicate  group
    jQuery(".duplicate_button").live("click", function(){
        id = jQuery(this).attr("id"); 
        id = id.split("_"); 
        group = id[2];
        customGroupID =  id[3];
        order = id[4];
        order =  parseInt(order) + 1;
        
        jQuery(this).data("originalText", jQuery(this).html()).html("Adding - Please Wait...");
        GetGroupDuplicate(group,customGroupID,order);
        
    });

    //delete duplicate field
    jQuery(".delete_duplicate_field").live("click",function(event){
        id = jQuery(this).attr("id");
		pattern =  /delete\_field\_repeat\-(([0-9]+)\_([0-9]+)\_([0-9]+)\_([0-9]+)\_([a-z0-9\_\-]+))/i;
		items =  pattern.exec(id);

		div =  items[1];
        div = "row_"+div;
        deleteGroupDuplicate(div);

		inputName = items[6];
		groupCounter = items[3];	
        
        counter_field = inputName +"_"+ groupCounter;
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
		pattern =  /type_handler\-(([0-9]+)\_([0-9]+)\_([0-9]+)\_([0-9]+)\_([a-z0-9\_\-]+))/i;
		
		id =  jQuery(this).attr("id");
		items = pattern.exec(id); 

		inputNameId =  items[1];
		inputName = items[6];
		customFieldId = items[2];
		groupCounter = items[3];	
        groupId = items[5];

        oldval = jQuery("#c"+inputNameId+"Counter").val();
        newval = parseInt(oldval) + 1; 
        jQuery("#c"+inputNameId+"Counter").val(newval);

        counter = jQuery("#c"+inputNameId+"Counter").val();
        div  = "c"+inputNameId+"Duplicate";
        counter_field = inputName +"_"+ groupCounter;
       	
		getDuplicate(customFieldId,counter,div,groupCounter,groupId,counter_field);
    });
    

});

moveAddToLast = function(context, bt) {
    if (bt && context) {
      bt.prependTo(context.find(".mf_toolbox:last .add_mf"));

      if (bt.data("originalText")) {
        bt.html(bt.data("originalText"));
      }

    } else {

      jQuery('.duplicate_button', context).each( function() {
        var el = jQuery(this);
        el.prependTo(el.closest(".write_panel_wrapper").find(".mf_toolbox:last .add_mf"));
        
        if (el.data("originalText")) {
          el.html(el.data("originalText"));
        }
        
      });

    }
};

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
            var newel = jQuery(msg);
            jQuery("#write_panel_wrap_"+customGroupID).append(newel);
            kids =  jQuery("#write_panel_wrap_"+customGroupID).children().filter(".magicfield_group");
                for(i=0;i < kids.length; i++){
                    groupCounter =  kids[i].id.split("_")[2];
                    ids = kids[i].id.split("_")[3];
                    jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                    value =  i + 1;
                    jQuery("#counter_"+groupCounter+"_"+ids).text("(" + value + ")");

            		    // move the add button to the last panel
            		    moveAddToLast(jQuery("#write_panel_wrap_"+customGroupID));
                    newel.find("input,textarea").eq(0).focus();
                    //jQuery.scrollTo(newel, 500);
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
    var parent = jQuery("#"+div);
    var db = parent.find(".duplicate_button").clone();
    var context = parent.closest(".write_panel_wrapper");
    parent.fadeOut({ duration: "normal", complete: function() { parent.remove(); moveAddToLast(context, db); } });
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
