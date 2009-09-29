var swfu = new Array();

/**
 * TODO: move this function to /js/custom_fields/image.js
 */
function uploadurl(input_name,file_type){
	var url = document.getElementById( "upload_url_"+input_name).value ;
	var progr = document.getElementById("upload_progress_"+input_name) ;
	var h ;
	progr.style.visibility = "visible" ;
	progr.style.height = "auto" ;
	progr.innerHTML = "<img src="+mf_path+"images/spinner.gif /> Downlading File ..." ;

	new Ajax.Request(mf_path+'RCCWP_GetFile.php',
		{
			method:'post',
			onSuccess: function(transport){
			    h = transport.responseText.split("*") ;
			    document.getElementById(input_name).value = h[1] ;
			    document.getElementById(input_name+"_deleted").value = 0;
			    progr.innerHTML = h[0] ;
			    if( document.getElementById( "img_thumb_"+input_name ) ) {
				    document.getElementById("img_thumb_"+input_name).src = phpthumb+"?&w=150&h=120&src="+JS_MF_FILES_PATH+h[1];
				    var b = "&nbsp;<strong><a href='#remove' class='remove' id='remove-"+input_name+"'>Delete</a></strong>";
 				    document.getElementById( "photo_edit_link_"+input_name ).innerHTML = b;
			    }
		    },
			parameters: "upload_url="+url+"&input_name="+input_name+"&type="+file_type
			});
}