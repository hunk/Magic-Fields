# Magic Fields
* Authors: [Edgar Garcia](http://hunk.com.mx "Hunk"), [David Valdez](http://gnuget.org "Gnuget")
* Contributors: [Freshout](http://freshout.us "Freshout"), [Jeff Minard](http://jrm.cc/ "Jeff Minard"),[Carlos Mendoza](http://http://github.com/kaziel/Magic-Fields "Carlos Mendoza"),Michael Bayard,[Doc4](doc4design.com/ "Doc4")
* Tested up to: Wordpress 2.8.6
* Stable tag: 1.3
* Description: Magic Fields  is a feature rich WordPress CMS plugin.

## Description

Magic Fields is a feature rich WordPress CMS plugin that focuses on easy templating for the developer and simplifies content management for the admin by creating custom write panels that can be fully customized (radio buttons, file uploads, image uploads, checkboxes, etc). 

## Installation 

Follow the following steps to install this plugin.

1.	Download plugin to the **/wp-content/plugins/** folder.
2.	Activate the plugin through the 'Plugins' menu in WordPress.

## Changelog ##

###1.3###

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


###1.2.1###

* Fix in the GetDataField function, this function is used by all front-end functions

###1.2###

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
* Tested up to: Wordpress 2.8.5
* Change the max value for the "order display" value for the custom fields and write panels


###1.1 ###

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
* Fix the uninstall process.
* Little fix in the Edit In Place editor.
* Fix in Assign Custom Write panel.
