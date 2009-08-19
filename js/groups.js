jQuery(document).ready(function(){
    
    //sorteable
    jQuery(".write_panel_wrapper").sortable({
        handle: ".sortable_mf",
        stop : function(){
            id =  jQuery(this).attr("id").split("_")[3];
            kids =  jQuery("#write_panel_wrap_"+id).children().filter(".magicfield_group");
            for(i=0;i < kids.length; i++){
                groupCounter =  kids[i].id.split("_")[2];
                ids = kids[i].id.split("_")[3];
                jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                jQuery("#counter_"+groupCounter+"_"+ids).text(i+1);
            }
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
                    jQuery("#counter_"+groupCounter+"_"+ids).text(i+1);
                }
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

