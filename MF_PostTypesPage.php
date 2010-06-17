<?php 
/**	
 * Magic Fields PostTypes Class
 */
Class MF_PostTypePages{

	/**
	 * Adding the menu in the admin
	 *
	 * @todo Add a more descriptive help
	 */
	function top_menu(){
		global $mf_domain;
		// Add top menu
		/**
		 * For now this menu is only displayed for the admin user, i think to this will change soon 
		 */ 
		$post_type_screen = add_menu_page(__('Magic Fields Post Types',$mf_domain), __('Post Types',$mf_domain),10,'mf_posttypes',array('MF_PostTypePages','ManagePosttype'));
		add_submenu_page('mf_posttypes',NULL,NULL,10,"mf_posttype_add",array('MF_PostTypePages','AddPosttype')); 
	
		add_contextual_help($post_type_screen,
			'<p>' . __('Hi you can create and manage custom pages here'). '</p>'
		);
	}

	/**
	 * Magic Fields manage Page
	 * @todo Which others columns will be  displayed on this table?
	 *
	 */
	function ManagePosttype(){
		global $mf_domain,$_wp_contextual_help;
		$title = __('Manage Custom Types');


		print 
			"<div class ='wrap'>".
				"<div id='icon-options-general' class='icon32'></div>".
				"<h2>".__('Manage Custom Types',$mf_domain)."</h2>".
				"<table class='widefat'>".
					"<thead>".
						"<tr>".
							"<th>".__('ID',$mf_domain)."</th>".
							"<th>".__('Post Type Name',$mf_domain)."</th>".
							"<th>".__("Capability Type",$mf_domain)."</th>".
						"</tr>".	
					"</thead>".
					"<tbody>".
						"<tr>".
							"<td>1</td>".
							"<td>Custom Type Demo 1</td>".
							"<td>Post</td>".
						"</tr>".
					"</tbody>".
					"<tfoot>".
						"<tr>".
							"<th>".__('ID',$mf_domain)."</th>".
							"<th>".__('Post Type Name',$mf_domain)."</th>".
							"<th>".__("Capability Type",$mf_domain)."</th>".
						"</tr>".	
					"</tfoot>".
				"</table>".
				"<p class='submit'>".
					"<a href='#' class='button tagadd'>".__('Add a new Custom Type',$mf_domain)."</a>".
				"</p>".
			"</div>";
	}

	/**
	 *
	 *
	 */
	function AddPosttype(){
		echo "Hola";
	}
}
?>
