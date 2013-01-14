<?php

require_once ('RCCWP_Application.php');
require_once ('RCCWP_ManagementPage.php');
require_once ('RCCWP_CreateCustomWritePanelPage.php');
require_once ('RCCWP_CreateCustomGroupPage.php');
require_once ('RCCWP_CreateCustomFieldPage.php');
require_once ('RCCWP_CustomFieldPage.php');

class RCCWP_Menu
{
	function PrepareModulesPanelsMenuItems()
	{
		$sub_menu_is_modules = false;
		
		if(empty($_REQUEST['mf_action'])){
			$currentAction = "";
		}else{
			$currentAction = $_REQUEST['mf_action'];
		}
		
		switch ($currentAction){
			
			// ------------ Custom Fields
			case 'create-custom-field':
				$page_group = 'RCCWP_CreateCustomFieldPage';
				$page_type = 'Main';
				break;

			case 'continue-create-custom-field':		
				if(isset($_REQUEST['custom-group-id']) && !empty($_REQUEST['custom-group-id']) )
				$customGroupId = (int)$_REQUEST['custom-group-id'];
				$customGroup = RCCWP_CustomGroup::Get($customGroupId);
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_REQUEST['custom-field-type']);
				if ($current_field->has_options == "true" || $current_field->has_properties == "true")
				{
					$page_group = 'RCCWP_CreateCustomFieldPage';
					$page_type = 'SetOptions';
				}
				else if ($current_field->has_options == "false")
				{
				  $default = array(
  				  'custom-group-id' => '',
  				  'custom-field-name' => '',
  				  'custom-field-description' => '',
  				  'custom-field-order' => '',
  				  'custom-field-required' => '',
  				  'custom-field-type' => '',
  				  'custom-field-options' => '',
  				  'custom-field-duplicate' => '',
  				  'custom-field-helptext' => ''
  				);
  				$save = array_merge($default,$_POST);
  				
					RCCWP_CustomField::Create(
						$save['custom-group-id'],
						$save['custom-field-name'],
						$save['custom-field-description'],
						$save['custom-field-order'],
						$save['custom-field-required'],
						$save['custom-field-type'],
						$save['custom-field-options'],
						null,null,
						$save['custom-field-duplicate'],
						$save['custom-field-helptext']);
	
					$page_group = 'RCCWP_CustomWritePanelPage';
					$page_type = 'View';
				}
				break;
		
			case 'delete-custom-field':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
					
			case 'finish-create-custom-field':		
			case 'cancel-edit-custom-field':
			case 'cancel-create-custom-field':
			case 'submit-edit-custom-field':
			case 'copy-custom-field':


      /*
       * Adding JS for sorting the order of the fields
       * using a drag and drop feature
       */
				wp_enqueue_script(	'magic_set_categories',
					MF_URI.'js/sorting_fields.js',
					array('jquery','jquery-ui-sortable')
        );

				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-field':
				$page_group = 'RCCWP_CustomFieldPage';
				$page_type = 'Edit';
				break;
		
			// ------------ Groups
			
			case 'create-custom-group':
				$page_group = 'RCCWP_CreateCustomGroupPage';
				$page_type = 'Main';
				break;
					
			case 'view-custom-group':
				$page_group = 'RCCWP_CustomGroupPage';
				$page_type = 'View';
				break;

			case 'cancel-edit-custom-group':
			case 'cancel-create-custom-group':
			case 'delete-custom-group':
			case 'submit-edit-custom-group':
			case 'finish-create-custom-group':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-group':
				$page_group = 'RCCWP_CustomGroupPage';
				$page_type = 'Edit';
				break;
				

			// ------------ Custom Write Panels

			case 'view-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';

      /*
       * Adding JS for sorting the order of the fields
       * using a drag and drop feature
       */
				wp_enqueue_script(	'magic_set_categories',
					MF_URI.'js/sorting_fields.js',
					array('jquery','jquery-ui-sortable')
        );
				break;
				
			case 'create-custom-write-panel':
				$page_group = 'RCCWP_CreateCustomWritePanelPage';
				$page_type = 'Main';
				break;

			case 'finish-create-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'Edit';
				break;
				
			case 'cancel-edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'submit-edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'import-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'Import';
				break;
												
			default:
				
				
				
				if (isset($_REQUEST['assign-custom-write-panel']))
				{
					$page_group = 'RCCWP_ManagementPage';
					$page_type = 'AssignCustomWritePanel';
					$sub_menu_is_modules = false;
				}
				// ------- Groups
				
				else if (isset($_REQUEST['cancel-edit-custom-group']))
				{
					$page_group = 'RCCWP_CustomGroupPage';
					$page_type = 'View';
				}
				
				else if (isset($_REQUEST['view-groups']))
				{
					$page_group = 'RCCWP_ManagementPage';
					$page_type = 'ViewGroups';
				}
				
				// ------- Default behavior
				else{
					$page_group = 'RCCWP_CustomWritePanelPage';
					$page_type = 'ViewWritePanels';
					$sub_menu_is_modules = false;
				}
				
		}
		
		if( !is_object( $result ) ) { $result = new StdClass; }
		
		if ($sub_menu_is_modules){
			$result->panelsMenuFunction = array('RCCWP_CustomWritePanelPage', 'ViewWritePanels');
			$result->modulesMenuFunction = array($page_group, $page_type);
		}
		else{
			$result->panelsMenuFunction = array($page_group, $page_type);
			$result->modulesMenuFunction = array('RCCWP_ManagementPage', 'ViewModules');
		}

		return $result;


	}

	/**
	 * Adding menus  
	 *
	 *
	 */
	function AttachMagicFieldsMenus()
	{
		global $mf_domain;
		require_once ('RCCWP_OptionsPage.php');

		$panelsAndModulesFunctions = RCCWP_Menu::PrepareModulesPanelsMenuItems();

		// Add top menu

		add_menu_page(__('Magic Fields > Manage',$mf_domain), __('Magic Fields',$mf_domain), 'edit_pages', 'MagicFieldsMenu', $panelsAndModulesFunctions->panelsMenuFunction, plugins_url(MF_PLUGIN_DIR.'/images/wand-hat.png'));
		// Add Magic Fields submenus
		add_submenu_page('MagicFieldsMenu', __('Write Panels',$mf_domain), __('Write Panels',$mf_domain), 'edit_pages','MagicFieldsMenu', $panelsAndModulesFunctions->panelsMenuFunction);		
		
	}

	function AttachOptionsMenuItem()
	{
		global $mf_domain;

		require_once ('RCCWP_OptionsPage.php');
		add_options_page(__('Magic Fields Options',$mf_domain), __('Magic Fields',$mf_domain), 'manage_options', 'RCCWP_OptionsPage.php', array('RCCWP_OptionsPage', 'Main'));
	}
	
	function AttachCustomWritePanelMenuItems() {
		global $submenu,$menu,$wp_version;
		global $mf_domain,$wpdb;
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		
			$new_indicator_text = __('New',$mf_domain);
			$edit_indicator_text = __('Manage',$mf_domain);
		
		
			$new_menu = array();
			ksort($menu); 
			foreach ($menu as $k => $v) {
				if($k > 5) break;
				$new_menu[$k]=$v;
			}
		
			$base=5;
			$offset=0;
			$add_post =  false;
			
			// fix for WP 3.0
			if(substr($wp_version, 0, 3) < 3.0){
				
			  // WP <= 2.9
    		$page_new    = "page-new.php?";
    		$page_edit   = "page.php?";
    		$page_manage = "edit-pages.php?";
    	}else{
    	  // WP > 3.0
    	  $page_new    = "post-new.php?post_type=page&";
    		$page_edit   = "post.php?";
    		$page_manage = "edit.php?post_type=page&";
    	}
			// end fix
			
			foreach ($customWritePanels as $panel){
			  if ($panel->name != '_Global') { // traversal: fix to ignore the global group
				//exists a single write panel? and if exists  this write panel have posts?
				if($panel->single == 1){
					$has_posts = $wpdb->get_var('SELECT post_id FROM '.$wpdb->prefix.'postmeta  where meta_key = "_mf_write_panel_id" and  meta_value = '.$panel->id);
					if(empty($has_posts)){
						$add_post = true;
					}else{
						$add_post = false;
					}
				}

				$offset++;


				if ($panel->type == "post"){
					$type_write_panel="edit-posts";
				}else{
					$type_write_panel="edit-pages";	
				}
				
				if ($assignToRole == 1){
					$requiredPostsCap = $panel->capability_name;
					$requiredPagesCap = $panel->capability_name;
				}   

				require_once ('RCCWP_Options.php');
				$condence = RCCWP_Options::Get('condense-menu');

				//IF we has unactivated the condenced menu
				if(!$condence){
					//adding the top parent menus
					$new_menu[$base+$offset] = array( __($panel->name), $type_write_panel, $base+$offset.'.php', '', 'mf-menu-'.sanitize_title_with_dashes($panel->name). ' menu-top mf-menu-'.$type_write_panel, 'mf-menu-'.$panel->id, 'div');
					
					//adding submenu options (add new and manage for each write panel)
					if ($panel->type == "post"){
						if($panel->single == 1){  //if the post is single
							if($add_post){ //if the post is single and don't have any related post
								add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
							}else{ //if have one related post we just can  edit the post 
								add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPostsCap,'post.php?action=edit&post='.$has_posts);
							}
						}else{
						  add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPostsCap, 'edit.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
							
						}
					}else{
						if($panel->single == 1){ //if the page is single
							if($add_post){ //if the page is single and don't have any related post
								add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, $page_new.'custom-write-panel-id=' . $panel->id);
							}else{
								add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPagesCap,$page_edit.'action=edit&post='.$has_posts);
							}
						}else{
						  add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPagesCap, $page_manage.'filter-posts=1&custom-write-panel-id=' . $panel->id);
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, $page_new.'custom-write-panel-id=' . $panel->id);
						}
					}
				}else{//if condenced is activated
					if ($panel->type == "post"){
			 			if($panel->single == 1){ //if the post is single
			 				if($add_post){ //if the post is single and don't have any related post
			 					add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
			 				}
			 			}else{
			 				add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
			 			}
					}else {
			 			if($panel->single == 1){ //if the page is single
			 				if($add_post){ //if the page is single and don't have any related post
			 					add_submenu_page('page-new.php', __($panel->name), __($panel->name), $requiredPagesCap, $page_new.'custom-write-panel-id=' . $panel->id);
			 				}else{
  							add_submenu_page('page-new.php',__($panel->name),__($panel->name)." (Edit)",$requiredPagesCap,$page_edit.'action=edit&post='.$has_posts);
  						}
						}else{
			 				add_submenu_page('page-new.php', __($panel->name), __($panel->name), $requiredPagesCap, $page_new.'custom-write-panel-id=' . $panel->id);
			 			}
					}
				}
				
			} // traversal: endif '$panel->name == '_Global'
		}
		foreach ($menu as $k => $v) {
			if($k > 5) $new_menu[$k+$offset]=$v;
		}

                global $_wp_last_utility_menu;
                $_wp_last_utility_menu += $offset;

		$menu = $new_menu;
		RCCWP_Menu::SetCurrentCustomWritePanelMenuItem();
		
	}
	

	function AttachCustomWritePanelFavoriteActions()
	{
		global $mf_domain;
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		$actions = array(
		'post-new.php' => array(__('New Post',$mf_domain), 'edit_posts'),
		'edit.php?post_status=draft' => array(__('Drafts',$mf_domain), 'edit_posts'),	
		'page-new.php' => array(__('New Page',$mf_domain), 'edit_pages'),
		'media-new.php' => array(__('Upload',$mf_domain), 'upload_files'),
		'edit-comments.php' => array(__('Comments',$mf_domain), 'moderate_comments')
		); 


		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();

		foreach ($customWritePanels as $panel) {
			if ($assignToRole == 1){
				$requiredPostsCap = $panel->capability_name;
				$requiredPagesCap = $panel->capability_name;
			}

		
				if ($panel->type == "post"){
					$actions['post-new.php?custom-write-panel-id=' . 	$panel->id] = array('New '.__($panel->name), 'edit_posts');
				} else {
				$actions['page-new.php?custom-write-panel-id=' . $panel->id] = array('New '.__($panel->name), 'edit_pages');
			}
		}
		return $actions;
	}
	
	function HighlightCustomPanel(){
		global $wpdb, $submenu_file, $post; 

		if(empty($post)){
			return True;
		}
		
		$result = $wpdb->get_results( " SELECT meta_value
						FROM $wpdb->postmeta
						WHERE post_id = '".$post->ID."' and meta_key = '_mf_write_panel_id'", ARRAY_A );
		$currPage = basename($_SERVER['SCRIPT_NAME']);

		if(is_wp30()){
      if (count($result) > 0 && $currPage =="edit.php" ){
        $id = $result[0]['meta_value'];
        $base = 'edit.php?';
        if($_GET['post_type'] == 'page') $base = 'edit.php?post_type=page&';
  			$submenu_file = $base."filter-posts=1&custom-write-panel-id=$id";
      }elseif(@$_GET['custom-write-panel-id'] ){
        //$id = $result[0]['meta_value'];
        $base = 'post-new.php?';
        if(isset($_GET['post_type']) && $_GET['post_type'] == 'page') $base = 'post-new.php?post_type=page&';
    		$submenu_file = $base."custom-write-panel-id=".$_GET['custom-write-panel-id'];
      }elseif (count($result) > 0 && $currPage =="post.php" ){
        $id = $result[0]['meta_value'];
        $base = 'edit.php?';
        if($post->post_type == 'page') $base = 'edit.php?post_type=page&';
  			$submenu_file = $base."filter-posts=1&custom-write-panel-id=$id";
      }
		}else{
		  if (count($result) > 0 && $currPage =="post.php" ){
    	  $id = $result[0]['meta_value'];
    		$submenu_file = "edit.php?filter-posts=1&custom-write-panel-id=$id";
    	}elseif (count($result) > 0 && $currPage == "page.php" ){
    		$id = $result[0]['meta_value'];
    		$submenu_file = "edit-pages.php?filter-posts=1&custom-write-panel-id=$id";
    	}
		}
		
	}

	function FilterPostsPagesList($where){
		global $wpdb;
		if (isset($_GET['filter-posts'])) {
			$panel_id = $_GET['custom-write-panel-id'];
				$where .= " and $wpdb->postmeta.meta_key = '_mf_write_panel_id' and $wpdb->postmeta.meta_value = '$panel_id' ";
		}
		return $where;
	}
	
	function FilterPostsPagesListJoin($join){
		global $wpdb;
    
		if (isset($_GET['filter-posts'])) {
		  $join .= " JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID ";
	  }
		return $join;
	}
	
	function DetachWpWritePanelMenuItems()
	{
		global $menu;
		global $submenu;

		require_once ('RCCWP_Options.php');
		
		$options = RCCWP_Options::Get();
		
			if(!empty($options['hide-write-post']) == '1'){
				unset($submenu['edit.php'][5]);
				unset($submenu['edit.php'][10]);
			}
	
	
			if (!empty($options['hide-write-page']) && $options['hide-write-page'] == '1'){
				foreach ($menu as $k => $v){ 
					if ($v[2] == "edit-pages.php"){
						unset($menu[$k]);
					}
				}
			}
		
	}
	
	function SetCurrentCustomWritePanelMenuItem() {
		global $submenu_file;
		global $menu;
		
		require_once ('RCCWP_Options.php');
		$options = RCCWP_Options::Get();
		
		if (!empty($options['default-custom-write-panel'])){
			require_once ('RCCWP_CustomWritePanel.php');
			
			$customWritePanel = RCCWP_CustomWritePanel::Get((int)$options['default-custom-write-panel']);
			
			if ($customWritePanel->type == "post")
				$menu[5][2] = 'post-new.php?custom-write-panel-id=' . (int)$options['default-custom-write-panel'];
			else
				$menu[5][2] = 'page-new.php?custom-write-panel-id=' . (int)$options['default-custom-write-panel'];
			
		}
		
		if(empty($_REQUEST['custom-write-panel-id'])){
			$_REQUEST['custom-write-panel-id'] = "";
		}

		if ($_REQUEST['custom-write-panel-id'])
		{
			$customWritePanel = RCCWP_CustomWritePanel::Get((int)$_REQUEST['custom-write-panel-id']);
			if ( isset($_REQUEST['filter-posts']) ){
				if ($customWritePanel->type == "post")
					$submenu_file = 'edit.php?filter-posts=1&custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
				else
					$submenu_file = 'edit-pages.php?filter-posts=1&custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
			}
			else{
				if ($customWritePanel->type == "post")
					$submenu_file = 'post-new.php?custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
				else
					$submenu_file = 'page-new.php?custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
			}
		}

	}
	
	function ShowPanel($panel){
		return true;
		require_once ('RCCWP_CustomWritePanel.php');
		global $wpdb, $canvas;

		if ($panel->always_show) return true;


		if ( 0 < $wpdb->get_var("SELECT count($wpdb->postmeta.meta_value)
			FROM $wpdb->postmeta
			WHERE $wpdb->postmeta.meta_key = '_mf_write_panel_id' and $wpdb->postmeta.meta_value = '$panel->id'")){
				return true;
		}

		return false;
	}
}
