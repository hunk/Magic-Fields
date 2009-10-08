jQuery(document).ready(function(){
    jQuery(".remove").live('click',remove_photo);
});

remove_photo = function(){
     if(confirm("Are you sure?")){
         //get the  name to the image
         id = jQuery(this).attr('id').split("-")[1];
         image = jQuery('#'+id).val();
         jQuery("#"+id+"_deleted").val(1);
         jQuery("#img_thumb_"+id).attr("src",mf_path+"images/noimage.jpg");
         jQuery("#photo_edit_link_"+id).empty();
    }
}