jQuery(document).ready(function(){
    jQuery(".remove").live('click',remove_photo);
    jQuery(".remove_media").live('click',remove_photo_media);
});

remove_photo = function(){
     if(confirm("Are you sure?")){
         //get the  name to the image
         pattern =  /remove\-(.+)/i;
         id = jQuery(this).attr('id');
         id = pattern.exec(id);
         id = id[1];

         image = jQuery('#'+id).val();
         //add file for delete
         delete_field = jQuery('#magicfields_remove_files').val();
         if(delete_field != ''){
            jQuery('#magicfields_remove_files').val(delete_field+"|||"+image);
         }else{
            jQuery('#magicfields_remove_files').val(image);
         }
         
         jQuery('#'+id).val('');
         var field = jQuery('#'+id).closest(".mf-field");
         
         field.find(".ajax-upload-list").html('');
         var um = field.find(".upload-msg");
         um.removeClass("mf-upload-success mf-upload-error").html('');
                

         jQuery("#img_thumb_"+id).attr("src",mf_path+"images/noimage.jpg");
         jQuery("#photo_edit_link_"+id).empty();
    }
}

remove_photo_media = function(){
     if(confirm("Are you sure?")){
         //get the  name to the image
         pattern =  /remove\-(.+)/i;
         id = jQuery(this).attr('id');
         id = pattern.exec(id);
         id = id[1];

         image = jQuery('#'+id).val();
         
         jQuery('#'+id).val('');
         jQuery('#'+id).closest(".mf-field").find(".ajax-upload-list").html('');
         jQuery("#img_thumb_"+id).attr("src",mf_path+"images/noimage.jpg");
         jQuery("#photo_edit_link_"+id).empty();
    }
}