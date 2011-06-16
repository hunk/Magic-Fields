/**
 * Custom Callback for upload  files
 * Actually this function as used by  the next types of custom fieds:
 *  - Image
 *  - Audio
 *  - File
 */

uploadurl = function(input_name,file_type,nonce){
    var url     = jQuery('#upload_url_'+input_name).val();
    var progr   = jQuery('#upload_progress_'+input_name);
    var h;
    
    progr.css('visibility','visible');
    progr.css('height','auto');
    progr.html("<img src="+mf_path+"images/spinner.gif /> Downlading File ...");
    
    jQuery.ajax({
      type: "POST",
      data: "upload_url="+url+"&input_name="+input_name+"&type="+file_type+"&nonce="+nonce,
      url: mf_path+'RCCWP_GetFile.php',
      success: function(msg){
          h = msg.split("*");
          
          progr.html(h[0]);
          
          if(h[1] == "None"){
              return false;
          }
          
          old_file = jQuery('#'+input_name).val();
          if(old_file != '' ){
            delete_field = jQuery('#magicfields_remove_files').val();
            if(delete_field != ''){
                jQuery('#magicfields_remove_files').val(delete_field+"|||"+old_file);
            }else{
                jQuery('#magicfields_remove_files').val(old_file);
            }
          }
          
          jQuery('#'+input_name).val(h[1]);
          
          if(jQuery('#img_thumb_'+input_name)){
             jQuery('#img_thumb_'+input_name).attr('src',phpthumb+"?&w=150&h=120&src="+JS_MF_FILES_PATH+h[1]);
             var b = '<a href="' + h[1] + '" target="_blank">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;<strong><a href="#remove" class="remove" id="remove-'+input_name+'">Delete</a></strong>';
             jQuery('#photo_edit_link'+input_name ).innerHTML = b;
          }
      } 
    });
}