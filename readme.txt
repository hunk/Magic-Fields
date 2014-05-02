=== Magic Fields ===
Contributors: hunk, Gnuget
Tags: custom write panel, custom, write panel, cms, magic fields
Tested up to: Wordpress 3.9
Requires at least: 2.9
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=edgar%40programador%2ecom&lc=GB&item_name=Donation%20Magic%20Fields&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Stable tag: 1.6.2
Description: Magic Fields  is a feature rich WordPress CMS plugin.

== Description ==
Magic Fields is a Wordpress CMS plugin, focuses in simplifies content management for the admin creating custom write panels also with Magic Fields you will be able to create (in a very easy way) custom fields for your write panels.

You can start to use Magic Fields following our <a href="http://magicfields.org/getting-started/">getting starter tutorial</a> or visiting  [the wiki](http://wiki.magicfields.org).

== Installation ==
Follow the following steps to install this plugin.

1.	Download plugin to the **/wp-content/plugins/** folder.
2.	Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==
1. Dashboard of Write Panels
2. Settings of Magic Fields

== Frequently Asked Questions ==
[Magic Fields Home](http://magicfields.org/)
[Magic Fields Wiki](http://wiki.magicfields.org/)

== Changelog ==

= 1.6.2 =
	* fix issue visual editor (WP 3.9)
	* fix issue drag and drop multiline

= 1.6.1.1 =
	* fix issue image media (WP 3.6)

= 1.6.1 =
	* Update ui.datepicker.js
	* fix Warning in php 5.4 (Empty Object Issue)
	* update markdownPReview, clean javascript data

= 1.6 =
	* Update the Datepicker Plugin
	* fixes for Wordpress 3.5 new media uploader conflict
	* Corrected the removal of data from the mf_post_meta table on deletion (@doublesharp)
	* use variable for postmeta table in case it doesn't have the standard  (@doublesharp)
	* fixed: split deprecated in php 5.3
	* And much more bugfixes

= 1.5.8.3 =
	* Add jquery.stringToSlug.min.js file
	
	
= 1.5.8.2 =
	* Fixed problems with WP 3.3.x
	* Fixed problems with multiline field
	* more fixes

= 1.5.8.1 =
 	* Fix in aux_image function
 
= 1.5.8 =
 	* problem with svn version
 
= 1.5.7 =
 	* Fixed problems with WP 3.2
 	* add action for mf_before_delete_file, mf_after_upload_file, mf_before_generate_thumb, mf_save_thumb_file
 	* Fixed problems with menus write panels
 	* Little fix for the WPML compatibility 
 	* Fix menu (add_utility_page) 

= 1.5.6 =
 * Security bug fixed related with the uploader

= 1.5.5 =
 * Added more file formats as requested by mrhughes (thanks!) for the file uploader
 * Fixed the label for fields in the group view (was saying Group Name / Field Label) whereas it is now just Label
 * Fix for AjaxUploader PHP4 issue
 * Added a option for switch between the traditional file uploader and the ajax uploader
 * resolved bug with the internal links in wp 3.1 
 * added new slider.js for wp 3.1
 * Rewrote query for display the lsit of posts/pages in the Manage page (this improve a lot the performace) 
 * Added options to create image using native wordpress 'size' (Thanks to bigfive) bigfive@e05275905c26ab4ba096fce3e0639877ffa78f8c
 * Fixed many warnings and notices in the whole plugin 
 * Fixed a few bugs related with the multiline field
 * Added media buttons at the multiline fields
 * Added option for not remove the br and p tags in the change between visual/html in the multiline field
 * Fix in how are saved the categories, for fix issue in wpmu
 * Changed the engine for export write panels thanks to  Jarl (http://github.com/jarltotland) for this
 * Removed deprecated functions (like esc_attr) 
 * Added Categories option for the Related Type Field thanks Clément Bongibault

= 1.5.4 =
 * add slider script, sorry

= 1.5.3 =
 * Fixed a bug with internal links for WP 3.1 (update jquery validate).
 * Fixed a bug with slider in WP 3.1
 * add jpeg format in phpthumb.php
 * add option for non-ajax upload and bugfixes in uploader

= 1.5.2 =
 * Reinstated the insert media buttons in the Multiline text field type. This problem was due to the fact that the init call to TinyMCE was changed so that Magic Fields Visual Editors can now honour any other plug-ins installed. Since the media buttons aren't rendered within the visual editor area for the content block (they sit separately above the TinyMCE control), Magic Fields was now missing them also.
 * Upload limit for the Ajax uploader has been increased to 10GB to get around problems some people are having.
 * Fixed some spacing issues with duplicate fields. 
 * Enhanced the focused field highlight to make the current field stand out better. It's now a subtle shade of blue, with the input having a stronger blue border
 * Fixed the focus state slightly, to use the :focus pseudo selector, as some field types have multiple controls.
 * Fixed an issue where the lowest auto-expanded field would receive focus
 * Removed the Exception throw statement to hopefully address issues with Upload failures with the new uploader.
 * Restored the delete link for field groups
 * Made the entire toolbox (footer) for each field group item draggable, rather than just the grip nub.
 * Fixed a bug with resizing the TinyMCE control in the multiline editor. Fullscreen mode should also now work correctly.
 * Fixed an issue with focusing when using "Expand All" links.

= 1.5.1 =
 * Fixed critical bug in Multisite version and a few more fixes

= 1.5 = 
 * Related Field Types: Extra selections are now available ‘All Posts AND Pages’ and ‘All Posts AND Pages with Write Panel’.
 * Added extra CSS class to Write Panel main menus, based on the sanitized write panel name. E.g. the Menu item for panel named ‘Home Page’ will get the extra class ‘mf-menu-home-page’. Ths allows alternative menu images to be attached to the write panel. Currently this is only possible by having an admin CSS file added through a WordPress ‘admin_head’ filter function in your functions.php
 * Added caching feature to support mostly used functions like get() and get_group();
 * Count down feature for inputs and textareas showing characters left in twitter style.
 * Now displaying Categories in hierarchy in the Write Pannel.
 * Issue 34 fixed
 * Issue 53 Fixed
 * Issue 50 Fixed
 * Issue 57 Fixed
 * Issue 65 Fixed
 * New way to sort the order of the fields in the write panel
 * New Template API Functions: gen_image_for, get_group_with_options,get_group_with_prefix,get_flat_group,get_flat_group_with_prefix
 * gen_image_for: Gets a generated image for a field value that’s already known, as is the case for values from the “get_group” function.
 * get_group_with_options: a function that allows certain options to be passed in to make front end code a bit cleaner. Refer to code comments for more details.
 * Added 'Name (order)' column to the grid, which displays the code-friendly name of the field followed by the field order number in brackets
 * revamped the entire ui for add posts in the write panels
 * When CREATING a field, Magic Fields now suggests an appropriate name for the field after the label is entered or changes. This is based on removing all special characters, converting to lowercase, and converting spaces to underscores. Also, if the field is part of a group, the (singularized) group name is appended to the beginning, which ensures it is unique across the set of fields. For example, if we have a group named ”Image Assets” and label a new field “File”, Magic Fields will suggest the field name should be “image_asset_file”.
 * When CHANGING a field, magic fields can suggest a field name based on the rules above by clicking the "suggest" button.
 * Tidied up the user interface for (expanded) magic fields groups, which now includes nicer bevels for field groups, better spacing, and nicer icons from the Fugue collection by Yusuke Kamiyamane ( http://p.yusukekamiyamane.com/ )
 * The terminology for “Duplicating” magic fields groups has been changed to ”Add Another [Item]“, or “Remove [Item]“.
 * Collapsible fields feature, where magic fields groups are collapsed down into a compact read-only group summary. Clicking group summaries will expand the group for editing. By default, any field groups and data that already exist will be loaded as a summary, meaning that pages and posts edit screens are generally MUCH shorter than before. This also makes it far easier to re-order items within each group, since the group summaries are always only about 150 pixels high.
 * Group summaries make use of the jScrollPane plug-in from Kevin Luck ( http://jscrollpane.kelvinluck.com/ ) to provide a much neater horizontal scrollbar for long group summaries. These are much smaller and tidier than the native OS widgets.
 * A new “Magic Fields” attributes panel is now available that allows you to change the write panel for a given page/post.
 * Added an alternative AJAX file uploader that supports drag and drop, adds an ajax progress spinner, and provides a more consistent file upload UI across all browsers based on Valum’s AJAX uploader ( http://valums.com/ajax-upload/ ). This uploader also improves performance DRAMATICALLY for large numbers of fields, since it does not use an iframe for every file-based field.
 * AJAX Uploader no longer adds a timestamp prefix to uploaded files, instead saving the file to the server as a lowercase sanitized version of the original file name.
 * Enhanced the layout of file upload controls, audio controls, image controls for the new uploader.
 * TinyMCE initilisation has been deferred until the user expands a group summary, to improve load performance.
 * Enhanced the form validation routines to be more robust, and work correctly with the group summaries. Any fields with errors will have their group summary expanded automatically so that the user can see the errors. Also added a little warning box inside the “Publish” panel when there are validation errors so that it’s easier to see that fields are missing (since they might be scrolled out of view at the bottom).
 * Added a much improved color picker by Stefan Petre ( http://www.eyecon.ro/colorpicker/ ) which uses a Photoshop-style color picker allowing you to select many more colors than before. This has been slightly customised to work better in the context of magic fields.
 * Added a “loading data” spinner for the initial load of a group summary.

= 1.4.5 = 
* Issue  17  fixed. http://bit.ly/b8AMUQ
* Issue 6  fixed. http://bit.ly/cFV9bi
* Issue 10 fixed. http://bit.ly/alvcS6
* Issue 12 fixed. http://bit.ly/atiK2v
* Issue 29 fixed. http://bit.ly/9RKJeW
* Issue 31 fixed. http://bit.ly/bjYrWn
* Issue 32 fixed. http://bit.ly/cIpJno
* Issue 35 fixed. http://bit.ly/9gifQ1
* Fixed some issues related with permissions
* New custom field  for images (this custom field use the media library of wordpress)

= 1.4.1 =
* fixed </div> issue with Multiline text fields when the visual editor is turned off.
* Adding a little fix in the "condense" menu mode 
* Fixed issue in the generate image.

= 1.4 =
* Magic Fields works fine with  the next release of Wordpress (v3.0)
* Was removed phpthumb, and was added a specific functions for make the same things to phpthumb would  do, this means to magic fields is now 7000 lines more lightweight
* Was added a new type of field (markdown text field)
* Prototype framework is not used anymore, now magic fields only use  jquery
* Implemented new shortcodes for use the content of the magic fields inside of a post
* Magic Fields now is avalaible in spanish and was added the .po and .mo files for translate MF in more languages.
* Was integrated the changes made by the "store lives plugin" for magic fields works well with  "living stories"
* was added a new option for delete the cache of the images  (for avoid overweight in the cache folder)
* Was added a new function called get_clean this function doesn't apply any filter of "the content" onto  the multiline field.
* Was removed Edit in place feature,  this feature cause more troubles than benefits
* Adding a new boton "html" in the multiline custom field.
* new engine for remove  the physical files when a file is removed in a write panel
* magic fields don't send anymore trash to the wordpress multimedia content http://bit.ly/av88h5
* Now the order in the  groups works well, http://bit.ly/9tqH59
* new way to assign categories onto the write panels (for wordpress 3.0 compatibility)  http://bit.ly/blTAZB
* now magic fields works well when the wp-config.php file is located outside (up one level) from the wordpress root directory http://bit.ly/9NAxdI
* Little fixes in the  in the export write panel function
* Now the button "add new" inside of the manage page into a write panel point into the correct place (before the reference of the write panel was lost)

= 1.3.2 =
* Was applied the changes made by  ericzhang for make Magic Field being compatible with the Living-Stories Wordpress Plugin 

= 1.3.1 =
* Fix critical bug in write panels type page

= 1.3 = 

* New field Type: Related Type (thanks to Wouter de Winter for this)
* Fix in the Image Custom Field 
* Now the empty groups don't be displayed in the  post page
* Fix in the multiline box.
* Removing a lot of obsolete and unused code
* Adding a dropdown with years in the datepicker's calendar for choice a year more easily
* Adding a option for choice the parent page in the write panels type page
* Removing   jquery1.2 and the jquery-ui, now  Magic Fields use only the wordpress version of jquery
* Adding multimedia  buttons in the  multiline editor, now is easy add images and files in this field
* Fix a little bug in the date field
* fix in get\_field\_duplicate and get\_group (not return more empty fields)
* Adding support i18n 
* new system of validation for fields

= 1.2.1 =

* Fix in the GetDataField function, this function is used by all front-end functions

= 1.2 =

* Now the cache of phpthumb and the mf files get stored in a single place (Jeff Minard)
* Adding get\_panel\_name function 
* adding one param in get_image for allows template writers to override the default phpthumb params to be set 
on the fly
* New design in the image input area for be more space efficient (Jeff Minard)
* Fix bug in the admin, before the info was stored twice 
* Added a  new functions for get the data in the frontend, big improvement of performance with this new functions
* Added  the option "hide non-standart content in Post Panel" (more info 
here: http://bit.ly/2KJwh3)
* Added a real validations for the image and audio custom field, now is checked the mime type for avoid any dangerous file
* Adding "Condense Menu" Option (thanks to doc4  http://bit.ly/8Gy9q)
* Removing the inline CSS in the EIP feature  (Carlos Mendoza)
* add new tooltip feature for the custom fields (Carlos Mendoza)
* Fix in the getGroupOrder function
* adding get\_field\_duplicate function for get all the values of one duplicate custom field
* now all the project has  hardtabs  with the size of  4 spaces
* gen\_image function generate a new image with new params of the phpthumbs and attr of the tag image the first time to is executed
* fix in the "editing prompt" option
* add css for menus of magic fields (write panels) (Jeff Minard)
* Tested up to Wordpress 2.8.5
* Change the max value for the "order display" value for the custom fields and write panels

= 1.1 =
* Remove a bunch of obsolete files and Code. 
* Fix bug #172 of flutter's tracker (http://bit.ly/4iQf95) thanks to Pixelate.
* Fix issue related with the Listbox field type.
* 30% less queries in the functions of front-end [get, get_image, get_audio]
* Fix bug #185 of flutter's tracker (http://bit.ly/kcOPb)
* Fix bug #201 of flutter's tracker (http://bit.ly/UAeEz)
* Fix of some paths for works fine at windows server.
* Adding a new function called get_image ( more info about how use it, soon)
* Removing all the short-tags of php.
* Now is used the  jquery ui datepicker for the Date custom field.
* Now you can use  get_image, get_audio, and gen_image outside of the loop. ( more info soon )
* Fix some issues related with the import/export  of writepanels.
* Fix the uninstall proccess.
* Little fix in the Edit In Place editor.
* Fix in Assing Custom Write panel.

== Upgrade Notice ==

= 1.5 =
Revamped all the UI of Magic fields
