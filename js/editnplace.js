var currentItemInEdit;


Event.observe(window, 'load', CreateEditnPlaceObjects);


var EIPObject = Class.create({

	 // -- Constructor
	 initialize: function(element, valueType, showPanel) {
	 
	 	var originalContent = "";
	 
	 	// Initialize object variables
		this.postID  = '';
		this.metaID = '';
		this.elementID = '';
		this.elementInnerID = '';
		this.myNicEditor1 = null;
		this.editActive = false;
		this.oldVal = '';
		this.valueType = valueType;
		this.showPanel = showPanel;
		this.panelID = '';
		this.highlightColor = '#FFFFCC';
		
		
		// Get post ID
		var tmpPostID = '';
		var tmpMetaID = '';
		$w(element.className).each(function(currClassName){
			if (currClassName.substr(0, 10) == 'EIP_postid')
				tmpPostID = currClassName.substr(10);
				
			if (currClassName.substr(0, 8) == 'EIP_mid_')
				tmpMetaID = currClassName.substr(8);
		});
		this.postID = tmpPostID;
		this.metaID = tmpMetaID;
		
		// Get/create element ID
		if (element.id == ''){
			// Create random ID
			var d = new Date();
			randomnumber= Math.floor(Math.random()*1000);
			element.id = "id" + d.getMilliseconds() + "_" + randomnumber;
		}
		this.elementID = element.id;
		this.elementInnerID = element.id + '_inner';
		originalContent = $(this.elementID).innerHTML;
		elementBG = GetFontColor(this.elementID); 
		
		// Create nicEditor object
		if (showPanel){
			this.myNicEditor1 = new nicEditor({iconsPath : JS_MF_URI + 'js/nicEditorIcons.gif',buttonList : ['bold','italic','underline','ol','ul','link','unlink']});
		}
		else{
			this.myNicEditor1 = new nicEditor({buttonList : []});
		}
		
		// Creat nicEditor panel
		this.panelID = "panel_" + this.elementID; 
		myNicPanel = new Element('div', {'id': this.panelID, 'class': 'EIPnicPanelDiv','style': "display:none"});
		$(document.body).insert({top: myNicPanel});				
		this.myNicEditor1.setPanel(this.panelID);
		
			
		// Wrap the field in a div		
		element_inner = new Element('div', {'id': this.elementInnerID, 'style':'overflow:hidden'}).update(element.innerHTML);
		element.innerHTML = "";
		element.insert({top: element_inner});
		
		// Inherit styles
		stylesList = ["fontSize", "fontFamily", "fontWeight", "letterSpacing", "color", "textTransform", "lineHeight"];
		applyStyles(this.elementID, this.elementInnerID, stylesList);
		//applyStyles(this.elementID, this.elementID, ['height']);
		
		if (showPanel){
			recursiveApplyStyles($(this.elementInnerID));			
		}
		
				
		// Attach niceditor		
		this.myNicEditor1.addInstance(this.elementID);
		if (isset(element.firstChild.contentDocument)){
			this.elementDivDocument =  element.firstChild.contentDocument;
			Event.observe(this.elementDivDocument.firstChild, "mousedown", this.startEdit.bindAsEventListener(this));
		}
		else{
			this.elementInnerID = this.elementID;
			$(this.elementID).innerHTML = originalContent;
			this.elementDivDocument = document; // $(this.elementInnerID)
			Event.observe($(this.elementInnerID), "mousedown", this.startEdit.bindAsEventListener(this));
		}
		
		// Adjust highlight color to be suitable for the theme
		
		if (elementBG != ''){
			newcolor = new Color(elementBG);
	 		this.highlightColor = newcolor.invert().getHex();  
		}
		
		$$('EIP_title:hover, .EIP_content:hover').each(function(element) {
				element.setStyle({backgroundColor: this.highlightColor});
				});
					
	 },
	 
	 startEdit: function (){
		if (!this.editActive){
			
			// set current field
			cancelSaveField(); 
			currentItemInEdit = this; 
			
			// Adjust pane/save positions
			objOffset = $(this.elementID).cumulativeOffset();
			elementTop = objOffset['top'] ;
			if(Prototype.Browser.IE) elementTop = elementTop-20; 
			elementLeft = objOffset['left'];
			if (this.showPanel){
				panel_top = elementTop - $(this.panelID).getHeight();
				$(this.panelID).setStyle({display: "", top:panel_top+"px",left:elementLeft+"px"});
			}
			
			currWidth = $(this.elementID).getWidth(); 
			if ( currWidth < $('save_cancel_field').getWidth()) 
				currWidth = $('save_cancel_field').getWidth();
					
			save_cancel_field_left = elementLeft + currWidth - $('save_cancel_field').getWidth();
			save_cancel_field_top = elementTop - $('save_cancel_field').getHeight();
			$('save_cancel_field').setStyle({display: "", top:save_cancel_field_top+"px",left:save_cancel_field_left+"px"});

			// Save old value to restore it on cancel						
			this.oldVal = this.elementDivDocument.getElementById(this.elementInnerID).innerHTML;
			this.elementDivDocument.getElementById(this.elementInnerID).style.backgroundColor = this.highlightColor;
			
			this.editActive = true;
		}
	}
	
	
	 
});


//------------------------------------------------------------
// Create common objects (save button and status messages)
// and initialize editnplace objects
//------------------------------------------------------------

function CreateEditnPlaceObjects() {
	
	// Create save/cancel buttons
	saveCancel = new Element('div', {'id': 'save_cancel_field', 'class':'EIPSaveCancel', 'style': "display:none;"});
	saveCancel.innerHTML = "<div id='savingDiv' style='display:none'>saving ...</div><div id='saveButton'><input type='button' value='Save' onclick='saveField()' /> Or <input type='button' value='Cancel' onclick=' cancelSaveField()' /></div>";
	$(document.body).insert({top: saveCancel});
	
	/*// Create status message div
	savingDiv = new Element('div', {'id': 'savingDiv', 'class':'EIPSaveStatus',  'style': "display:none;"});
	savingDiv.innerHTML = "Saving ...";
	$(document.body).insert({top: savingDiv});*/	
		
	// Loop through all post titles
	$$('.EIP_title').each(function(element){
		new EIPObject(element, 'EIP_title', false);
	});
	
	// Loop through all post contents 
	$$('.EIP_content').each(function(element){
		new EIPObject(element, 'EIP_content', true);
	});
	
	// Loop through text fields
	$$('.EIP_textbox').each(function(element){
		new EIPObject(element, 'EIP_textbox', false);
	});
	
	// Loop through all post contents 
	$$('.EIP_mulittextbox').each(function(element){
		new EIPObject(element, 'EIP_mulittextbox', true);
	});
	
	
						
}

//------------------------------------------------------------
// Saving a field
//------------------------------------------------------------

function saveField(){
	var postParameters;	
	
	$('savingDiv').style.display = "";
	$('saveButton').style.display = "none";
	//$('save_cancel_field').style.display = "";
	//$(currentItemInEdit.elementID).style.display = "none";
	if (currentItemInEdit.showPanel) $(currentItemInEdit.panelID).style.display = "none";
	
	fieldVal = currentItemInEdit.elementDivDocument.getElementById(currentItemInEdit.elementInnerID).innerHTML;
	
	postParameters = 
		"post_id=" + escape(encodeURI(currentItemInEdit.postID)) +
		"&meta_id=" + escape(encodeURI(currentItemInEdit.metaID)) +
		"&field_value=" + escape(encodeURI(fieldVal )) + 
		"&field_type=" + escape(encodeURI(currentItemInEdit.valueType));
	
	new Ajax.Request(JS_MF_URI + 'RCCWP_EditnPlaceResponse.php',
		{
			method:'post',
			onSuccess: function(transport){
				currentItemInEdit.oldVal = fieldVal;
				cancelSaveField();	
			},
			parameters: postParameters
		});
}

//------------------------------------------------------------
// Cancel Saving
//------------------------------------------------------------

function cancelSaveField(){
	if (!isset(currentItemInEdit)) return;
	currentItemInEdit.elementDivDocument.getElementById(currentItemInEdit.elementInnerID).innerHTML = currentItemInEdit.oldVal;

	$('savingDiv').style.display = "none";
	$('saveButton').style.display = "";
	$('save_cancel_field').style.display = "none";
	if (currentItemInEdit.showPanel) $(currentItemInEdit.panelID).style.display = "none";
	//$(currentItemInEdit.elementID).style.display = "";
	
	currentItemInEdit.elementDivDocument.getElementById(currentItemInEdit.elementInnerID).style.backgroundColor = "";
	currentItemInEdit.editActive = false;
}

//------------------------------------------------------------
// Copy styles from an object to another
//------------------------------------------------------------

function applyStyles(from, to, stylesList){
	to_styles = {};
	stylesList.each( function (elmnt){
		to_styles[elmnt] = $(from).getStyle(elmnt);
	});
	$(to).setStyle(to_styles);
}

//------------------------------------------------------------
// Recursivly apply all styles of an object as an inline
// styles to that object.
//------------------------------------------------------------

function recursiveApplyStyles(elmnt){
	stylesList = [	"backgroundAttachment",	"backgroundColor","backgroundImage", "backgroundPosition","backgroundRepeat",
					"fontSize", "fontFamily", "fontWeight", "fontStyle", 
					"color", "direction", "lineHeight", "letterSpacing", "textAlign", "textDecoration", "textIndent", "textTransform", "whiteSpace", "wordSpacing", 
					"borderBottomColor", "borderBottomStyle", "borderBottomWidth",
					"borderLeftColor", "borderLeftStyle", "borderLeftWidth",
					"borderTopColor", "borderTopStyle", "borderTopWidth",
					"borderRightColor", "borderRightStyle", "borderRightWidth",
					"listStyleImage", "listStylePosition", "listStyleType",
					"paddingLeft", "paddingRight", "paddingTop", "paddingBottom", 
					"marginLeft", "marginRight", "marginTop", "marginBottom",
					"left", "right", "top", "bottom", "width",
					"clear", "cursor", "display", "float", "position", "visibility"
					
					];
					
	applyStyles(elmnt, elmnt, stylesList);

	childElements = elmnt.childElements();
	if (childElements)
		childElements.each(recursiveApplyStyles);
}


function isset(  ) {
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: FremyCompany
	// *     example 1: isset( undefined, true);
	// *     returns 1: false
	// *     example 2: isset( 'Kevin van Zonneveld' );
	// *     returns 2: true
	
	var a=arguments; var l=a.length; var i=0;
	
	while ( i!=l ) {
		if (typeof(a[i])=='undefined') { 
		return false; 
		} else { 
		i++; 
		}
	}
	
	return true;
}



//------------------------------------------------------------------------------------
// Colors library
// Based on code from : http://www.ozzu.com/programming-forum/javascript-color-object-t66915.html
//------------------------------------------------------------------------------------

// Try to get the hex of the color 
function GetColorHex(colorName){

	if (!colorName || colorName == "" || colorName == "transparent") return "";
	if (colorName.charAt(0) == "#") return colorName;
	
	tmpColor = Color.getFilteredObject(colorName);
	if (tmpColor == false){
		for (i=0;i<147;i=i+2){
			if (colorName.toLowerCase == COLOR_NAMES[i].toLowerCase)
				return COLOR_NAMES[i+1];
		}
	}
	return colorName;
}

// Recursive function to get the font color
function GetFontColor(element){
	var elementBGColor = GetColorHex($(element).getStyle('color'));
		
	tmpColor = Color.getFilteredObject(elementBGColor);
	if (tmpColor == false){
		if (isset($(element).parentNode))
			return GetFontColor($(element).parentNode);
		else
			return '';
	}
	else
		return elementBGColor;
}  


/*
   Converts INT to HEX
   If Prototype library is loaded, use theirs, else use ours.
*/
if(!Number.toColorPart){Number.prototype.toColorPart = function(){return ((this < 16 ? '0' : '') + this.toString(16));}}

/*
   Constructor
   @String c : hexadecimal, shorthand hex, or rgb()
   #returns : Object reference to instance or false
*/
Color = function(c){
   if(!c || !(c = Color.getFilteredObject(c))){return false;}
   this.original = c;
   this.r=c.r;this.g=c.g;this.b=c.b;
   this.check();
   this.gray = Math.round(.3*this.r + .59*this.g + .11*this.b);
   this.hex = this.getHex();
   this.rgb = this.getRGB();
   return this;
}

/*
   Screens color strings.
   @String str : hexadecimal, shorthand hex, or rgb()
   #returns : Object {r: XXX, g: XXX, b: XXX} or false
*/
Color.getFilteredObject = function(str){
   if(/^#?([\da-f]{3}|[\da-f]{6})$/i.test(str)){
      function _(s,i){return parseInt(s.substr(i,2), 16);}
      str = str.replace(/^#/, '').replace(/^([\da-f])([\da-f])([\da-f])$/i, "$1$1$2$2$3$3");
      return {r:_(str,0), g:_(str,2), b:_(str,4)}
   }else if(/^rgb *\( *\d{0,3} *, *\d{0,3} *, *\d{0,3} *\)$/i.test(str)){
      str = str.match(/^rgb *\( *(\d{0,3}) *, *(\d{0,3}) *, *(\d{0,3}) *\)$/i);
      return {r:parseInt(str[1]), g:parseInt(str[2]), b:parseInt(str[3])};
   }
   return false;
}

/*
   Checks the internal RGB registers for out of range values.
   Resets out of range values.
   #returns : Object reference to instance
*/
Color.prototype.check = function(){
   if(this.r>255){this.r=255;}else if(this.r<0){this.r=0;}
   if(this.g>255){this.g=255;}else if(this.g<0){this.g=0;}
   if(this.b>255){this.b=255;}else if(this.b<0){this.b=0;}
   return this;
}

/*
   Resets color to the original color passed to the constructor.
   #returns : Object reference to instance
*/
Color.prototype.revert = function(){
   this.r=this.original.r;this.g=this.original.g;this.b=this.original.b;
   return this;
}

/*
   Inverts the color.
   Black to White, vice versa
   #returns : Object reference to instance
*/
Color.prototype.invert = function(){
   this.check();
   this.r = 255-this.r;
   this.g = 255-this.g;
   this.b = 255-this.b;
   return this;
}

/*
   Lightens the color.
   @Int amount : 1-254 -- RGB amount to lighten the color
   #returns : Object reference to instance
*/
Color.prototype.lighten = function(amount){
   this.r += parseInt(amount);
   this.g += parseInt(amount);
   this.b += parseInt(amount);
   return this;
}

/*
   Darkens the color.
   @Int amount : 1-254 -- RGB amount to darken the color
   #returns : Object reference to instance
*/
Color.prototype.darken = function(amount){
   return this.lighten(parseInt('-'+amount));
}

/*
   Converts the color to Grayscale
   #returns : Object reference to instance
*/
Color.prototype.grayscale = function(){
   this.check();
   this.gray = Math.round(.3*this.r + .59*this.g + .11*this.b);
   this.r=this.gray;this.g=this.gray;this.b=this.gray;
   return this;
}

/*
   Convenience function for lightening color.
   @Int amount : amount to lighten color
   @Bool returnRGB : true uses RGB return string, false uses HEX return string.
   #returns : String color
*/
Color.prototype.getLighter = function(amount, returnRGB){
   return this.lighten(amount).check()[returnRGB ? 'getRGB' : 'getHex']();
}

/*
   Convenience function for darkening color.
   @Int amount : amount to darken color
   @Bool returnRGB : true uses RGB return string, false uses HEX return string.
   #returns : String color
*/
Color.prototype.getDarker = function(amount, returnRGB){
   return this.darken(amount).check()[returnRGB ? 'getRGB' : 'getHex']();
}

/*
   Convenience function for grayscaling color.
   @Bool returnRGB : true uses RGB return string, false uses HEX return string.
   #returns : String color
*/
Color.prototype.getGrayscale = function(returnRGB){
   this.grayscale();
   return (returnRGB ? ('rgb('+this.gray+','+this.gray+','+this.gray+')') : this.gray.toColorPart().replace(/^([\da-f]{2})$/i, "#$1$1$1"));
}

/*
   Convenience function for inverting color.
   @Bool returnRGB : true uses RGB return string, false uses HEX return string.
   #returns : String color
*/
Color.prototype.getInverted = function(returnRGB){
   return this.invert()[returnRGB ? 'getRGB' : 'getHex']();
}

/*
   Gets the rgb(x,x,x) value of the color
   #returns : String rgb color
*/
Color.prototype.getRGB = function(){
   this.check();
   this.rgb = 'rgb('+this.r+','+this.g+','+this.b+')';
   return this.rgb;
}

/*
   Gets the hex value of the color
   @Bool shorthandReturnAcceptable : true will return #333 instead of #333333
   #returns : String hex color
*/
Color.prototype.getHex = function(shorthandReturnAcceptable){
   this.check();
   this.hex = '#' + this.r.toColorPart() + this.g.toColorPart() + this.b.toColorPart();
   if(shorthandReturnAcceptable){return this.hex.replace(/^#([\da-f])\1([\da-f])\2([\da-f])\3$/i, "#$1$2$3");}
   return this.hex;
}


var COLOR_NAMES = new Array(
	"AliceBlue" , "#F0F8FF",
	"AntiqueWhite" , "#FAEBD7",
	"Aqua" , "#00FFFF",
	"Aquamarine" , "#7FFFD4",
	"Azure" , "#F0FFFF",
	"Beige" , "#F5F5DC",
	"Bisque" , "#FFE4C4",
	"Black" , "#000000",
	"BlanchedAlmond" , "#FFEBCD",
	"Blue" , "#0000FF",
	"BlueViolet" , "#8A2BE2",
	"Brown" , "#A52A2A",
	"BurlyWood" , "#DEB887",
	"CadetBlue" , "#5F9EA0",
	"Chartreuse" , "#7FFF00",
	"Chocolate" , "#D2691E",
	"Coral" , "#FF7F50",
	"CornflowerBlue" , "#6495ED",
	"Cornsilk" , "#FFF8DC",
	"Crimson" , "#DC143C",
	"Cyan" , "#00FFFF",
	"DarkBlue" , "#00008B",
	"DarkCyan" , "#008B8B",
	"DarkGoldenRod" , "#B8860B",
	"DarkGray" , "#A9A9A9",
	"DarkGrey" , "#A9A9A9",
	"DarkGreen" , "#006400",
	"DarkKhaki" , "#BDB76B",
	"DarkMagenta" , "#8B008B",
	"DarkOliveGreen" , "#556B2F",
	"Darkorange" , "#FF8C00",
	"DarkOrchid" , "#9932CC",
	"DarkRed" , "#8B0000",
	"DarkSalmon" , "#E9967A",
	"DarkSeaGreen" , "#8FBC8F",
	"DarkSlateBlue" , "#483D8B",
	"DarkSlateGray" , "#2F4F4F",
	"DarkSlateGrey" , "#2F4F4F",
	"DarkTurquoise" , "#00CED1",
	"DarkViolet" , "#9400D3",
	"DeepPink" , "#FF1493",
	"DeepSkyBlue" , "#00BFFF",
	"DimGray" , "#696969",
	"DimGrey" , "#696969",
	"DodgerBlue" , "#1E90FF",
	"FireBrick" , "#B22222",
	"FloralWhite" , "#FFFAF0",
	"ForestGreen" , "#228B22",
	"Fuchsia" , "#FF00FF",
	"Gainsboro" , "#DCDCDC",
	"GhostWhite" , "#F8F8FF",
	"Gold" , "#FFD700",
	"GoldenRod" , "#DAA520",
	"Gray" , "#808080",
	"Grey" , "#808080",
	"Green" , "#008000",
	"GreenYellow" , "#ADFF2F",
	"HoneyDew" , "#F0FFF0",
	"HotPink" , "#FF69B4",
	"IndianRed " , "#CD5C5C",
	"Indigo " , "#4B0082",
	"Ivory" , "#FFFFF0",
	"Khaki" , "#F0E68C",
	"Lavender" , "#E6E6FA",
	"LavenderBlush" , "#FFF0F5",
	"LawnGreen" , "#7CFC00",
	"LemonChiffon" , "#FFFACD",
	"LightBlue" , "#ADD8E6",
	"LightCoral" , "#F08080",
	"LightCyan" , "#E0FFFF",
	"LightGoldenRodYellow" , "#FAFAD2",
	"LightGray" , "#D3D3D3",
	"LightGrey" , "#D3D3D3",
	"LightGreen" , "#90EE90",
	"LightPink" , "#FFB6C1",
	"LightSalmon" , "#FFA07A",
	"LightSeaGreen" , "#20B2AA",
	"LightSkyBlue" , "#87CEFA",
	"LightSlateGray" , "#778899",
	"LightSlateGrey" , "#778899",
	"LightSteelBlue" , "#B0C4DE",
	"LightYellow" , "#FFFFE0",
	"Lime" , "#00FF00",
	"LimeGreen" , "#32CD32",
	"Linen" , "#FAF0E6",
	"Magenta" , "#FF00FF",
	"Maroon" , "#800000",
	"MediumAquaMarine" , "#66CDAA",
	"MediumBlue" , "#0000CD",
	"MediumOrchid" , "#BA55D3",
	"MediumPurple" , "#9370D8",
	"MediumSeaGreen" , "#3CB371",
	"MediumSlateBlue" , "#7B68EE",
	"MediumSpringGreen" , "#00FA9A",
	"MediumTurquoise" , "#48D1CC",
	"MediumVioletRed" , "#C71585",
	"MidnightBlue" , "#191970",
	"MintCream" , "#F5FFFA",
	"MistyRose" , "#FFE4E1",
	"Moccasin" , "#FFE4B5",
	"NavajoWhite" , "#FFDEAD",
	"Navy" , "#000080",
	"OldLace" , "#FDF5E6",
	"Olive" , "#808000",
	"OliveDrab" , "#6B8E23",
	"Orange" , "#FFA500",
	"OrangeRed" , "#FF4500",
	"Orchid" , "#DA70D6",
	"PaleGoldenRod" , "#EEE8AA",
	"PaleGreen" , "#98FB98",
	"PaleTurquoise" , "#AFEEEE",
	"PaleVioletRed" , "#D87093",
	"PapayaWhip" , "#FFEFD5",
	"PeachPuff" , "#FFDAB9",
	"Peru" , "#CD853F",
	"Pink" , "#FFC0CB",
	"Plum" , "#DDA0DD",
	"PowderBlue" , "#B0E0E6",
	"Purple" , "#800080",
	"Red" , "#FF0000",
	"RosyBrown" , "#BC8F8F",
	"RoyalBlue" , "#4169E1",
	"SaddleBrown" , "#8B4513",
	"Salmon" , "#FA8072",
	"SandyBrown" , "#F4A460",
	"SeaGreen" , "#2E8B57",
	"SeaShell" , "#FFF5EE",
	"Sienna" , "#A0522D",
	"Silver" , "#C0C0C0",
	"SkyBlue" , "#87CEEB",
	"SlateBlue" , "#6A5ACD",
	"SlateGray" , "#708090",
	"SlateGrey" , "#708090",
	"Snow" , "#FFFAFA",
	"SpringGreen" , "#00FF7F",
	"SteelBlue" , "#4682B4",
	"Tan" , "#D2B48C",
	"Teal" , "#008080",
	"Thistle" , "#D8BFD8",
	"Tomato" , "#FF6347",
	"Turquoise" , "#40E0D0",
	"Violet" , "#EE82EE",
	"Wheat" , "#F5DEB3",
	"White" , "#FFFFFF",
	"WhiteSmoke" , "#F5F5F5",
	"Yellow" , "#FFFF00",
	"YellowGreen" , "#9ACD32");
