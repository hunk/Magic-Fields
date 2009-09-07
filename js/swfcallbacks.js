var swfu = new Array();

jQuery(document).ready(function(){

/**    jQuery(".upload_file").each(function(i){
        file =  this.id.split("-")[1];
          
	    swfu[i]  = new SWFUpload({
                                    //button settings
			                        button_text: '<span class="button">Browse</span>',
                                    button_text_style: '.button { text-align: center; font-weight: bold; font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }',
			                        button_height: "24",
                        			button_width: "132",
                		        	button_image_url: wp_root+'/wp-includes/images/upload.png',
                		        	file_post_name: "async-upload",
       			                    
                   
                                    
                                    //requeriments settings
                                    upload_url  : mf_path + "/RCCWP_GetFile.php",
           		                	flash_url :  wp_root+"/wp-includes/js/swfupload/swfupload.swf",
                                    file_size_limit : "20 MB",
                                    button_placeholder_id : "upload-"+file,
                                    debug: false,

                                    //custom settings
                                    custom_settings :{
                                        'file_id' : file
                                    },
                                        
                                    //handlers
                                    file_queued_handler : adjust,
                                    upload_success_handler :  completed,
        
                                    post_params : {
		                                auth_cookie : swf_authentication,
                                    	_wpnonce : swf_nonce
        		                	}
                        		});
    });*/
});

function adjust(file)
{
	var progr = document.getElementById("upload_progress_"+this.customSettings.file_id); 
	progr.style.visibility = "visible" ;
	progr.style.height = "auto" ;
	progr.innerHTML = "<img src="+mf_path+"images/spinner.gif /> uploading ... <img src='"+mf_path+"images/bar.jpg' height=10 width=0 />" ;
    this.startUpload();
}

function completed(file,server_data)
{

    //hidden the upload  progress icon
	var progr = document.getElementById("upload_progress_"+ this.customSettings.file_id);
	progr.style.visibility = "visible";
	progr.style.height = "auto";


    var hold = new Array() ;
    hold = server_data.split("*") ;


    progr.innerHTML = hold[0] ;  // <--- Message "Successful upload!"
    
    file =  this.customSettings.file_id;

    document.getElementById(file).value = hold[1] ;
	if (document.getElementById( "img_thumb_"+file)){
	
		document.getElementById( "img_thumb_"+file ).src =phpthumb + "?&w=150&h=120&src="+JS_MF_FILES_PATH+hold[1] ;
		var s = "<a href='#impossible_location' onclick=call_thickbox('"+hold[2]+"')>" ;
		var e = "<strong onclick=prepareUpdatePhoto('"+file+"')>Edit</strong> </a>" ;
 		document.getElementById( "photo_edit_link_"+file ).innerHTML = s + e ;
	}
}

function call_thickbox(url)
{
	tb_show("Magic_Fields",url,false) ;
}


function uploadurl(input_name,file_type)
{
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
			progr.innerHTML = h[0] ;
			if( document.getElementById( "img_thumb_"+input_name ) )
			{
				document.getElementById("img_thumb_"+input_name).src = phpthumb+"?&w=150&h=120&src="+JS_MF_FILES_PATH+h[1];
				var b = "&nbsp;<strong><a href='#remove' class='remove' id='remove-"+input_name+"'>Delete</a></strong>";
 				document.getElementById( "photo_edit_link_"+input_name ).innerHTML = b;
			}
			},
			parameters: "upload_url="+url+"&input_name="+input_name+"&type="+file_type
			});
}
