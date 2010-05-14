jQuery(document).ready(function(){
    jQuery(".remove").live('click',remove_photo);
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
         jQuery("#img_thumb_"+id).attr("src",mf_path+"images/noimage.jpg");
         jQuery("#photo_edit_link_"+id).empty();
    }
}