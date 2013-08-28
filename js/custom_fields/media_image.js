jQuery(document).ready(function(){

	jQuery('.del-link').each(function(){
	  id = jQuery(this).next().attr('id');
	  check = parent.window.mf_field_id;
	  if(check){
      jQuery(this).before('<a href="#"  class="mf_media_upload button" onclick="mf_set_image_field(\''+id+'\'); return false;">Set image in field</a>');
    }
  });
	
	jQuery('body').on("click", ".update_field_media_upload",function(){
	   window.mf_field_id = jQuery(this).attr('id');
	});
	
	jQuery('#set-post-thumbnail , #add_image').click( function(){
    window.mf_field_id = '';
	});
	
	jQuery(".mce_add_image , .mce_add_video , .mce_add_audio , .mce_add_media").live('click',function(){
	  window.mf_field_id = '';
	  var a = this;
	  // When a mce button is clicked, we have to hotswap the activeEditor instance, else the image will be inserted into the wrong tinyMCE box (current editor)
	  setTimeout( function() {
		  tinyMCE.activeEditor = tinyMCE.EditorManager.getInstanceById( a.id.replace('_add_media', '') );
		  wpActiveEditor = a.id.replace('_add_media', '');
		}, 500 );
	});
	
});


function mf_set_image_field(id){
  id_element = parent.window.mf_field_id;
  jQuery.post(parent.window.mf_path+"MF_ImageMedia.php", { "image_id": id, 'field_id': id_element },
     function(data){
       jQuery('#img_thumb_'+data.field_id, top.document).attr('src',data.image);
       jQuery('#'+data.field_id, top.document).attr('value',data.image_value);
       jQuery('#photo_edit_link_'+data.field_id, top.document).html("&nbsp;<strong><a href='#remove_media' class='remove_media' id='remove-"+data.field_id+"'>Remove Image</a></strong>");
       parent.window.mf_field_id = '';
       parent.window.tb_remove();
     }, "json");
}

function load_link_in_media_upload(){
  jQuery('.del-link').each(function(){
	  id = jQuery(this).next().attr('id');
    check_repet = jQuery(this).prev().attr('class');
    if(check_repet == "mf_media_upload"){
    }else{
      check = parent.window.mf_field_id;
      if(check == "" || check == undefined ){}else{
        jQuery(this).before('<a href="#" class="mf_media_upload button" onclick="mf_set_image_field(\''+id+'\'); return false;">Set image in field</a>');
      }
    }
  });
}