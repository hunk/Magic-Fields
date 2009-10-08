/**
 * Custom Callback for upload  files
 * Actually this function as used by  the next types of custom fieds:
 *  - Image
 *  - Audio
 *  - File
 */

uploadurl = function(input_name,file_type){
    var url     = jQuery('#upload_url_'+input_name).val();
    var progr   = jQuery('#upload_progress_'+input_name);
    var h;
    
    progr.css('visibility','visible');
    progr.css('height','auto');
    progr.html("<img src="+mf_path+"images/spinner.gif /> Downlading File ...");
    
    jQuery.ajax({
      type: "POST",
      data: "upload_url="+url+"&input_name="+input_name+"&type="+file_type,
      url: mf_path+'RCCWP_GetFile.php',
      success: function(msg){
          h = msg.split("*");
          
          progr.html(h[0]);
          
          if(h[1] == "None"){
              return false;
          }
          
          jQuery('#'+input_name).val(h[1]);
          jQuery('#'+input_name+'_delete').val(0);
          
          if(jQuery('#img_thumb_'+input_name)){
             jQuery('#img_thumb_'+input_name).attr('src',phpthumb+"?&w=150&h=120&src="+JS_MF_FILES_PATH+h[1]);
             var b = "&nbsp;<strong><a href='#remove' class='remove' id='remove-"+input_name+"'>Delete</a></strong>";
             jQuery('#photo_edit_link'+input_name ).innerHTML = b;
          }
      } 
    });
}