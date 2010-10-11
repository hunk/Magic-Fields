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
	function TopMenu(){
		global $mf_domain;
		// Add top menu
		/**
		 * For now this menu is only displayed for the admin user, i think to this will change soon 
		 */ 
		$post_type_screen = add_submenu_page('MagicFieldsMenu', __('Post Types',$mf_domain), __('Post types',$mf_domain),10,'mf_posttypes',array('MF_PostTypePages','Dispacher'));

		add_contextual_help($post_type_screen,
			'<p>' . __('Hi you can create and manage Post Types here'). '</p>'
		);
	}

	/** 
	 * Determine which action will be executed
	 *
	 */
	function Dispacher(){
		if(empty($_GET['action'])){
			$action = "manage";
		}else{
			$action = $_GET['action'];
		}
		
		$action = esc_attr($action);
		
		switch($action){
			case "manage":
				MF_PostTypePages::ManagePostType();
			break;	
			case "add":
				MF_PostTypePages::AddPostType();
			break;	
			case "save":
				MF_PostTypePages::SavePostType();
		}
	}

	/**
	 * Magic Fields manage Page
	 * @todo Which others columns will be  displayed on this table?
	 *
	 */
	function ManagePosttype(){
		global $mf_domain,$wpdb;

		//Getting the  Custom types
		$items = $wpdb->get_results('SELECT id,name FROM '.MF_TABLE_POSTTYPES_TAXONOMIES);
	
		$customtypes =  "";
		foreach($items as $key => $value){
			$customtypes .=  "<tr>".
								"<td>".$value->id."</td>".
								"<td>".$value->name."</td>".
								"<td>Post</td>".
								"<td>".__('Edit')."|".__('Delete')."</td>".
							"</tr>";
		}

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
							"<th>".__("Actions",$mf_domain)."</th>".
						"</tr>".	
					"</thead>".
					"<tbody>".
					$customtypes.
					"</tbody>".
					"<tfoot>".
						"<tr>".
							"<th>".__('ID',$mf_domain)."</th>".
							"<th>".__('Post Type Name',$mf_domain)."</th>".
							"<th>".__('Capability Type',$mf_domain)."</th>".
							"<th>".__('Actions',__($mf_domain))."</th>".
						"</tr>".	
					"</tfoot>".
				"</table>".
				"<p class='submit'>".
					"<a href='admin.php?page=mf_posttypes&action=add' class='button tagadd'>".__('Add a new Custom Type',$mf_domain)."</a>".
				"</p>".
			"</div>";
	}

	/**
	 * Display the form for add a new type of custom type
	 * 
	 * @todo Add a section for the capabilities
	 * @todo Add section of REWRITE
	 * @todo Improve the help text
	 */
	function AddPosttype(){
		global $mf_domain;

		print
			"<div class='wrap'>".
				"<div id='icon-edit-pages' class='icon32'></div>".
				"<h2>".__('Add New Post Type',$mf_domain)."</h2>".
				"<form method='POST' action='?page=mf_posttypes&action=save'>".
					"<div class='mf_form'>".
						"<div class='form-field'>".
							"<label for='post_type_name'>".__('Name',$mf_domain)."<span>*</span>:</label>".
							"<input type='text' id='post_type_name' maxlength='20' size='23' name='post_type_name' value=''/>".
							"<p>".__('Put the name of your Post Type')."</p>".
						"</div>".	
						"<div class='form-field'>".
							"<label for='description'>".__('Description',$mf_domain).":<span>*</span></label>".
							"<textarea name='description'></textarea>".
							"<p>".__('description about for what is your custom post type',$mf_domain)."</p>".
						"</div>".	
						"<div class='form-field'>".
							"<input type='checkbox' name='is_public' /><label for='is_public' class='label_checkbox'>".__('Is Public',$mf_domain)."</label>".
							"<p>".__('This post type is public?',$mf_domain)."</p>".
							"<div class='sub-options is_public_options'>".
								"<input type='checkbox' name='display_ui' /><label for='display_ui' class='label_checkbox'>".__('Show UI',$mf_domain)."</label>".
								"<p>".__('This type will be displayed into the Administration menu?',$mf_domain)."</p>".
								"<input type='checkbox' name='exclude_search' /><label for='exclude_search' class='label_checkbox'>".__('Exclude Search',$mf_domain)."</label>".
								"<p>".__('This element will be exclude in the search page',$mf_domain)."</p>".
							"</div>".
						"</div>".
						"<div class='form-field'>".
							"<input type='checkbox' name='supports' /><label class='label_checkbox' for='supports'>".__('Customize which Fields will be displayed in this post type',$mf_domain)."</label>".
							"<p></p>".
							"<div class='sub-options supports_options'>".
								"<input type='checkbox' name='title'><label class='label_checkbox' for='title'>".__('Title',$mf_domain)."</label>".
								"<p>".__('Title',$mf_domain)."</p>".
								"<input type='checkbox' name='editor'><label class='label_checkbox' for='editor'>".__('Editor',$mf_domain)."</label>".
								"<p>".__('The user put here the content of the post',$mf_domain)."</p>".
								"<input type='checkbox' name='comments'><label class='label_checkbox' for='comments'>".__('Comments',$mf_domain)."</label>".
								"<p>".__('This post type have comments',$mf_domain)."</p>".
								"<input type='checkbox' name='trackbacks'><label class='label_checkbox' for='trackbacks'>".__('Trackbacks',$mf_domain)."</label>".
								"<p>".__('This post type have trackbacks',$mf_domain)."</p>".
								"<input type='checkbox' name='revisions'><label class='label_checkbox' for='revisions'>".__('Revisions',$mf_domain)."</label>".
								"<p>".__('This post type have revisions',$mf_domain)."</p>".
								"<input type='checkbox' name='author'><label class='label_checkbox' for='author'>".__('Author',$mf_domain)."</label>".
								"<p>".__('Author',$mf_domain)."</p>".
								"<input type='checkbox' name='excerpt'><label class='label_checkbox' for='excerpt'>".__('Excerpt',$mf_domain)."</label>".
								"<p>".__('This post type have Excerpt',$mf_domain)."</p>".
								"<input type='checkbox' name='thumbnail'><label class='label_checkbox' for='thumbnail'>".__('Thumbnail',$mf_domain)."</label>".
								"<p>".__('This post type have Thumbnail',$mf_domain)."</p>".
								"<input type='checkbox' name='page_attributes'><label class='label_checkbox' for='thumbnail'>".__('Page Attributes',$mf_domain)."</label>".
								"<p>".__('Page Attributes',$mf_domain)."</p>".
							"</div>".
						"</div>".
						"<p class='submit'>".
						"<input name='save_post_type' type='submit' value='Create post Type'/>".
						"</p>".
					"</div>".	
				"</form>".
			"</div>";
	}

	/** 
	 * Save a New Post type
	 */
	function SavePostType(){
		global $wpdb;
		if(!empty($_POST)){

			//Sanitize data
			$data = array();
			foreach($_POST as $key => $value){
				$key = esc_html($key);
				$value = esc_html($value); 
				$data[$key] = $value;
			}

			$name = esc_html($data['post_type_name']);
			$desc = esc_html($data['description']);
			
			unset($data['post_type_name']);
			unset($data['description']);

			$settings = json_encode($data);
 
			//Saving the new post type
			$wpdb->insert(MF_TABLE_POSTTYPES_TAXONOMIES,array('type' => 'posttype','name' => $name,'description' => $desc,'settings' => $settings),array('%s','%s','%s','%s'));
			print_r($_POST);	
		}
	}

	/**	
	 * Install Function, add the post types tables into 
	 * the wordpress instalation
	 */
	function CreatePostTypesTables(){
		global $wpdb;
		
		//this table is already installed?
		if($wpdb->get_var("SHOW TABLES LIKE '".MF_TABLE_POSTTYPES_TAXONOMIES."'") != MF_TABLE_POSTTYPES_TAXONOMIES) {
			$sql =	"CREATE TABLE ".MF_TABLE_POSTTYPES_TAXONOMIES. " (
						id mediumint(9) NOT NULL AUTO_INCREMENT ,
						type varchar(10) NOT NULL DEFAULT 'posttype',
						name tinytext NOT NULL,
						description text NOT NULL,
						settings text,
						UNIQUE KEY id (id)
			)";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
}
?>
