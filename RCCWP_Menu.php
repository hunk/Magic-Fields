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
					RCCWP_CustomField::Create(
						$_POST['custom-group-id'],
						$_POST['custom-field-name'],
						$_POST['custom-field-description'],
						$_POST['custom-field-order'],
						$_POST['custom-field-required'],
						$_POST['custom-field-type'],
						$_POST['custom-field-options'],
						null,null,
						$_POST['custom-field-duplicate'],
						$_POST['custom-field-helptext']);
	
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
		add_menu_page(__('Magic Fields > Manage',$mf_domain), __('Magic Fields',$mf_domain), 10, __FILE__, $panelsAndModulesFunctions->panelsMenuFunction);

		// Add Magic Fields submenus
		add_submenu_page(__FILE__, __('Write Panels',$mf_domain), __('Write Panels',$mf_domain), 10, __FILE__, $panelsAndModulesFunctions->panelsMenuFunction);		
		
	}

	function AttachOptionsMenuItem()
	{
		global $mf_domain;

		require_once ('RCCWP_OptionsPage.php');
		add_options_page(__('Magic Fields Options',$mf_domain), __('Magic Fields',$mf_domain), 'manage_options', 'RCCWP_OptionsPage.php', array('RCCWP_OptionsPage', 'Main'));
	}
	
	function AttachCustomWritePanelMenuItems() {
		global $submenu,$menu;
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
			
			foreach ($customWritePanels as $panel){
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
					$new_menu[$base+$offset] = array( __($panel->name), $type_write_panel, $base+$offset.'.php', '', 'wp-menu-open menu-top mf-menu-'.$type_write_panel, 'mf-menu-'.($base+$offset), 'div' );
					
					//adding submenu options (add new and manage for each write panel)
					if ($panel->type == "post"){
						if($panel->single == 1){  //if the post is single
							if($add_post){ //if the post is single and don't have any related post
								add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
							}else{ //if have one related post we just can  edit the post 
								add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPostsCap,'post.php?action=edit&post='.$has_posts);
							}
						}else{
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
							add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPostsCap, 'edit.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
						}
					}else{
						if($panel->single == 1){ //if the page is single
							if($add_post){ //if the page is single and don't have any related post
								add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
							}else{
								add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPagesCap,'page.php?action=edit&post='.$has_posts);
							}
						}else{
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
							add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPagesCap, 'edit-pages.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
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
			 					add_submenu_page('page-new.php', __($panel->name), __($panel->name), $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
			 				}
						}else{
			 				add_submenu_page('page-new.php', __($panel->name), __($panel->name), $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
			 			}
					}
				}
		}
		foreach ($menu as $k => $v) {
			if($k > 5) $new_menu[$k+$offset]=$v;
		}
			
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
		if (count($result) > 0 && $currPage =="post.php" ){
			$id = $result[0]['meta_value'];
			$submenu_file = "edit.php?filter-posts=1&custom-write-panel-id=$id";
		}
		elseif (count($result) > 0 && $currPage == "page.php" ){
			$id = $result[0]['meta_value'];
			$submenu_file = "edit-pages.php?filter-posts=1&custom-write-panel-id=$id";
		}
		
		
	}

	function FilterPostsPagesList($where){
		global $wpdb;
		if (isset($_GET['filter-posts'])) {
			$panel_id = $_GET['custom-write-panel-id'];
			$where = $where . " AND 0 < (SELECT count($wpdb->postmeta.meta_value)
					FROM $wpdb->postmeta
					WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key = '_mf_write_panel_id' and $wpdb->postmeta.meta_value = '$panel_id') ";
		}
		return $where;
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
			if ($_REQUEST['filter-posts']){
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
