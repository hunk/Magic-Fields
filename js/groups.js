(function($) { // closure and $ portability


  $.stripTags = function(str) { return $.trim(str.replace(/<\/?[^>]+>/gi, '')); };

  $.fn.mf_group_summary = function(options) {
    
    var count = 0;
    
    return this.each( function() {
      count++;
      
      var el = $(this);
      
      if (!el.data("mf_summarised")) {
        
        var d = el.data("mf_group_summary") || {};
        el.data("mf_group_summary", d);
        el.data("mf_summarised", true);
        // record the field containers
        d.fields = el.find("div.mf-field");
        d.fc = el.find(".mf-fields");

        el.find(".collapse_button").hide();
      
        // create summary container
      
        if (d.container) {
          d.container.remove(); // remove any existing summaries
        }
      
        d.container = $('<div title="click to edit field data" class="mf-group-summary"></div>');
      
        if (el.hasClass("mf_duplicate_group")) {
          d.container.addClass("sortable_mf"); 
        }

        d.table = $('<table cellspacing="0"><thead><tr></tr></thead><tbody><tr></tr></tbody></table>');
      
        d.thr = d.table.find("thead tr");
        d.tbr = d.table.find("tbody tr");
      
        el.addClass("empty");

        // convert the fields 
      
        d.fields.each( function() {
        
          var f = $(this);
          var cn = [];
        
          var lb = $(this).find("label span.name").eq(0);
          var content = "&nbsp;";
        
          var td = $('<td>&nbsp;</td>');
          var th = $('<th></th>');
        
          var t;
        
          // derive the "type" class
          var matches = f.attr("class").match(/mf-t-[a-z0-9\-]+/);
        
          if (matches.length) {
            t = matches[0];
            cn.push(t);
          }
        
          td.data("rid", f.attr("id"));
          th.data("rid", f.attr("id"));
        
          var tc = t.replace("mf-t-", "");
        
          var def = f.find(".mf_custom_field").hasClass("mf-default");
        
          // derive the content display
          switch (tc) {
          
            case "textbox" : {
              var orig = $.trim(f.find("input[type=text]").val());
              content = $.stripTags(orig).substring(0, 70);
            
              if (content == "") {
                content = "( empty )";
                td.addClass("none");
                th.addClass("none");
              } else {
                if (orig != content) {
                  content = content + "&hellip;";
                }
                if (!def) { el.removeClass("empty"); }
              }
            
              break;
            }
            case "checkbox" : {
              var checked = f.find("input[type=checkbox]:checked").length;
            
              if (!checked) {
                content = "( not checked )";
                td.addClass("none");
                th.addClass("none");
              } else {
                th.addClass("mf-t-checkbox-checked");
                content = "( checked )";
                if (!def) { el.removeClass("empty"); }
              }
            
              break;
            }
            case "checkbox-list" :
            case "radiobutton-list" : {
              val = f.find("input:checked");
            
              var v = [];
            
              val.each( function() {
                v.push($(this).attr("value"));
              });
            
              if (v.length) {
                content = v.join(", ");
                if (!def) { el.removeClass("empty"); }
              } else {
                content = "( none selected )";
                td.addClass("none");
                th.addClass("none");
              }
              break;
            }
            case "color-picker" : {
              var color = $.trim(f.find("input[type=text]").val());
            
              if (color) {
                content = $('<div class="mf-color-swatch"><span style="background-color: ' + color + '"></span><strong>' + color + '</strong></div>');
                if (!def) { el.removeClass("empty"); }
              } else {
                content = "( none )";
                td.addClass("none");
                th.addClass("none");
              }

              break;
            }
            case "date" : {
              content = $.trim(f.find("input[type=text]").val());

              if (content == "") {
                content = "( none )";
                td.addClass("none");
                th.addClass("none");
              } else {
                th.addClass("mf-t-date-selected");
                if (!def) { el.removeClass("empty"); }
              }
            
              break;
            }
            case "file" : {
              content = f.find("a.mf-file-view").attr("href");
            
              if (!content || content == "") {
                content = "( none )";
                td.addClass("none");
                th.addClass("none");
              } else {
                content = '<a href="' + content + '" target="_blank" class="mf-s-file-view">View File</a>';
                if (!def) { el.removeClass("empty"); }
              }
              break;
            }
            case "image" : 
            case "image-" : {
              var img = f.find("img");
              content = img.clone().attr("height", 60).attr("id", "s_" + img.attr("id"));

              var src = img.attr("src");
            
              if (src && src.search) {
                
                if (src.search("noimage.jpg") == -1) {
                  if (!def) { el.removeClass("empty"); }
                } else {
                  th.addClass("none");
                  td.addClass("none");
                }
              }
              break;
            }
            case "listbox" : {
              val = f.find("select").val();
            
              if (val) {
                content = val.join(", ");
                if (!def) { el.removeClass("empty"); }
              } else {
                content = "";
              }
            
              if (content == "") {
                content = "( none selected )";
                td.addClass("none");
                th.addClass("none");
              }
              break;
            }
            case "multiline-textbox" :
            case "markdown-textbox" : {
              var ta = f.find("textarea");
              var editor_text = ta.attr('id');
            
              if (tinyMCE) {
                var editor = tinyMCE.get(editor_text);
              
                if (editor) {
                  ta.attr('value', editor.getContent());
                }
        		  }
      		
      		    var orig = $.stripTags(ta.val());
              content = orig.substring(0, 150);

              if (content == "") {
                content = "( empty )";
                td.addClass("none");
                th.addClass("none");
              } else {
                if (!def) { el.removeClass("empty"); }
                
                if (orig != content) {
                  content += "&hellip;";
                }
              }
            
              break;
            }
            case "related-type" :
            case "dropdown-list" : {
              var sel = f.find("select");
              var val = sel.val();
             
              content = $.trim(sel.find("option:selected").text());
            
              if (val == "" || def) {
                content = "( not selected )";
                td.addClass("none");
                th.addClass("none");
              } else {
                if (!def) { el.removeClass("empty"); } 
              }
            
              break;
            }
            case "audio" : {
              var content = f.find('input[type=hidden]').val();
            
              if (content != "") {
                if (!def) { el.removeClass("empty"); } 
              } else {
                content = "( none )";
                td.addClass("none");
                th.addClass("none");
              }
            
              break;
            
            }
            case "slider" : {
              var content = f.find('input[type=hidden]').val();

              if (content != "") {
                content = "( " + content + " )";
              }
            
              if (content != "0") {
                if (!def) { el.removeClass("empty"); } 
              }
              break;
            }

          }

          if (def) {
            th.addClass("none");
            td.addClass("none");
          }
        
          td.addClass(cn.join(" "));
          th.addClass(cn.join(" "));
        
          // set the label (based on the label inside the field)
        
          var origLabel = $.trim($.stripTags(lb.html()));
          var exLabel = origLabel.substring(0, 28);
        
          if (origLabel != exLabel) {
            exLabel = exLabel + "&hellip;";
          }
        
          th.html(exLabel);
          td.html(content);
        
          d.thr.append(th);
          d.tbr.append(td);
        });
      
        if (el.hasClass("empty")) {
          // if no data has been provided yet, hide the "add" button, since it's likely people will click this to try to add the initial record
          // and this is not what the button does. We will show the button as soon as they expand the initial field.
          el.find(".duplicate_button").hide();
        }
        
        var lth = d.table.find("thead th:last");

        d.container.removeClass("last-none");
        
        if (lth.hasClass("none")) {
          d.container.addClass("last-none");
        }
        
        el.find(".mf-group-loading").hide();

        // hide the field container
        d.fc.hide();
        d.container.append(d.table).insertAfter(d.fc.eq(0));
        d.container.find(".mf-s-file-view").windowopen({ width: 'aw', height: 'ah'});

        el.mf_group_update_count();
      
        d.container.jScrollPane({ selectorStrut: 'table', novscroll: true });
    
      }
    
    
    });
    
    
  };

  $.fn.mf_group_update_header_buttons = function() {
    return this.each( function() {
      var wrapper = $(this).closest(".write_panel_wrapper");

      var btca = wrapper.find('.mf-collapse-all-button');
      var btea = wrapper.find('.mf-expand-all-button');
      
      var buttons = btca.add(btea).removeClass("disabled");
      
      if (wrapper.find(".mf-fields:visible").length == 0) {
        btca.addClass("disabled");
      }

      if (!wrapper.find(".mf-group-summary").length) {
        btea.addClass("disabled");
      }
    });
  };
  
  $.fn.mf_group_update_count = function() {
    return this.each( function() {
      
      var wrapper = $(this).closest(".write_panel_wrapper");
      var status = wrapper.find(".mf-group-count");
      
      var gc = wrapper.find(".mf-group-controls");
      
      var toolbox = wrapper.find(".mf_toolbox");
      
      var btca = wrapper.find('.mf-collapse-all-button');
      var btea = wrapper.find('.mf-expand-all-button');
      
      var buttons = btca.add(btea).removeClass("disabled");
      
      toolbox.show();
      buttons.show();
      
      if (status) {
        
        gc.removeClass("hl");
        var groups = wrapper.find(".magicfield_group");
        var count = groups.length;
        
        if (count == 1) {
          // check that the first element isn't empty
          
          if (groups.eq(0).hasClass("empty")) {
            count = 0;
            status.html("No items. Click summary below to create a new item.");
            toolbox.hide();
            gc.addClass("hl");
          } else {
            status.html("1 item");
          }
          
          buttons.hide();
          
        } else {
          status.html(count + " items");
        }
      }
      
      $(this).mf_group_update_header_buttons();
      
    });
  };
  
  $.fn.mf_group_show_save_warning = function() {
    return this.each( function() {
      var warning = $(this).closest(".write_panel_wrapper").find(".mf-group-save-warning");
      
      if (warning.not(":visible")) {
        warning.fadeIn("normal");
      }
      
    });
  };
  
  $.fn.mf_group_expand = function() {
    return this.each( function() {

      var el = $(this);
      
      el.data("mf_summarised", false);
    
      el.find(".collapse_button,.duplicate_button").fadeIn();
      var fc = el.find(".mf-fields");
      var fields = $(this).find(".mf-field");
    
      // set the editor in textarea
  		add_editor_text($(this));
  		add_color_picker($(this));

      // load any internal iframes - this speeds up the intial load time by a whole lot if there are a lot of file upload controls, since the browser doesn't load them all initially!
      fields.find('.iframeload').each( function() {
        var el = $(this);
      
        var iframe = el.find("iframe");
      
        if (!iframe.length) {
          var md = el.metadata({ type: 'class' });
        
          if (md.iframe) {
            iframe = $($.tmpl('<iframe id="#{id}" src="#{src}" frameborder="" scrolling="no" style="border-width: 0px; height: #{height}px; width: #{width}px;vertical-align:top;"></iframe>', md.iframe));
            el.append(iframe);
          }
        }
      });
      
      fc.show();

      fc.find("input,textrea,select").eq(0).focus();
      
      if (el.data("mf_group_summary")) {
        // remove the group summary
        el.find(".mf-group-summary").remove();
      }

      el.mf_group_update_header_buttons();

    });
  };
 
  
  
  jQuery(window).load( function() {
    $(window).resize();
  });
  
  jQuery(document).ready(function(){
    
    var tt_template = 
   '<div class="tt"> \
    <div class="tthl"><div class="tthr"><div class="tth"></div></div></div> \
    <div class="ttbl"><div class="ttbr"><div class="ttb"><div class="ttbc">#{content}</div></div></div></div> \
    <div class="ttfl"><div class="ttfr"><div class="ttf"></div></div></div> \
    </div>';

    $('small.tip').live("mouseenter", function(event) {
      var el = $(this);

      if (!el.data("tt")) {
        // create a tooltip
        var fh = $.trim(el.find(".field_help").html());

        if (fh && fh != "") {
          var tt = $($.tmpl(tt_template, { content: fh }));
          
          tt.hide().appendTo("body");

          // setup the reveal
          el.revealTooltip({el: tt, affix: { to: 'nw', offset: [-12, 0] }});
          el.data("tt", tt);

          // show the tooltip
          setTimeout( function() { el.reveal('show') }, 100 );
        }
      } 
    });
    
    var wrappers = $('.write_panel_wrapper')
      
      wrappers.find(".mf-expand-all-button").live("click", function() {

        if (!$(this).hasClass("disabled")) {
          $(this).closest(".write_panel_wrapper").find(".magicfield_group").mf_group_expand();
        }
      
        return false;
      });

      wrappers.find(".mf-collapse-all-button").live("click", function() {
        
        if (!$(this).hasClass("disabled")) {
          $(this).closest(".write_panel_wrapper").find(".magicfield_group").mf_group_summary();
        }
      
        return false;
      });
      
      
      var mf_groups = $('.magicfield_group');
      
      // make the save warning appear when fields are clicked
      
      mf_groups.find("input,select,textarea").live("change", function() {
        $(this).closest(".mf_custom_field").removeClass("mf-default");
        $(this).mf_group_show_save_warning();
        $('#mf-publish-errors').hide();
      });
      
      
      $('.mf_message_error .error_magicfields').hide();
    
      mf_groups.mf_group_summary({ init: true });
      
      wrappers.mf_group_update_count();

      mf_groups.live( "dblclick", function(event) {
        
        if (!$(event.target).closest("input,textarea,button,a").length) {
          // don't collapse if we double click on a summayr, form field, or link!
          $(this).closest(".magicfield_group").mf_group_summary();
        }
        
      });

      $('.mf-group-summary').live( "click", function(event) {
        
        if (!$(event.target).closest(".jspTrack,a").length) {
          var group = $(this).closest(".magicfield_group");
          
          if (group.hasClass("empty")) {
            group.removeClass("empty");
            group.mf_group_update_count();
          }
          
          group.mf_group_expand();
          
          var cells = $(event.target).closest("td,th");
          
          var rid = cells.data("rid");
        }
      });
      
      mf_groups.find('.collapse_button').live( "click", function() {
        $(this).closest(".magicfield_group").mf_group_summary();
        
        return false;
      });
      
      
      $(window).resize( function() {
        $('.mf-group-summary').each( function() {
          $(this).data("jsp").reinitialise();
        });

      });
        
      moveAddToLast();

      
      //sorteable
      jQuery(".write_panel_wrapper").sortable({ 
          handle: ".sortable_mf",
  		// function fix the problem of block of the editor visual textareas
  		start: function() { 
  			id =  jQuery(this).attr("id");
  			jQuery("#"+id+" :input[type='textarea'].mf_editor").each( function(inputField){
  				var editor_text = jQuery(this).attr('id');
  				if(tinyMCE.get(editor_text)){
  					tinyMCE.execCommand('mceRemoveControl', false, editor_text);
  					jQuery('#'+editor_text).addClass('temp_remove_editor');
  				}
  			});
  		},
          stop : function(){
              id =  jQuery(this).attr("id").split("_")[3];
              kids =  jQuery("#write_panel_wrap_"+id).children().filter(".magicfield_group");
              for(i=0;i < kids.length; i++){
                  groupCounter =  kids[i].id.split("_")[2];
                  ids = kids[i].id.split("_")[3];
                  jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                  jQuery("#counter_"+groupCounter+"_"+ids).text((i+1));
              }
  			//add the editor visual in textareas
  			jQuery("#"+jQuery(this).attr("id")+" :input[type='textarea'].temp_remove_editor").each( function(inputField){
  				var editor_text = jQuery(this).attr('id');
  				tinyMCE.execCommand('mceAddControl', false, editor_text);
  				jQuery('#'+editor_text).removeClass('temp_remove_editor');
  			});
			
  			  moveAddToLast();
			  
          }
      });

      //duplicate  group
      jQuery(".duplicate_button").live("click", function(){
          id = jQuery(this).attr("id"); 
          id = id.split("_"); 
          group = id[2];
          customGroupID =  id[3];
          order = id[4];
          order =  parseInt(order) + 1;
        
          jQuery(this).data("originalText", jQuery(this).html()).html("Adding - Please Wait...");
          GetGroupDuplicate(group,customGroupID,order);
        
      });

      //delete duplicate field
      jQuery(".delete_duplicate_field").live("click",function(event){
          id = jQuery(this).attr("id");
  		pattern =  /delete\_field\_repeat\-(([0-9]+)\_([0-9]+)\_([0-9]+)\_([0-9]+)\_([a-z0-9\_\-]+))/i;
  		items =  pattern.exec(id);

  		div =  items[1];
          div = "row_"+div;
          deleteGroupDuplicate(div);

  		inputName = items[6];
  		groupCounter = items[3];	
        
          counter_field = inputName +"_"+ groupCounter;
          fixcounter("counter_"+counter_field);
      });


      //delete  duplicate group
      jQuery(".delete_duplicate_button").live("click",function(event){
          id = jQuery(this).attr("id");
          div = id.split("-")[1];
          deleteGroupDuplicate(div);

          recount =  div.split("_")[2];
        
          kids =  jQuery("#write_panel_wrap_"+recount).children().filter(".postbox1");
          for(i=0;i < kids.length; i++){
              groupCounter =  kids[i].id.split("_")[2];
              ids = kids[i].id.split("_")[3];
              jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
          }
      }); 

      //duplicate field
      jQuery(".typeHandler").live("click",function(event){
  		pattern =  /type_handler\-(([0-9]+)\_([0-9]+)\_([0-9]+)\_([0-9]+)\_([a-z0-9\_\-]+))/i;
		
  		id =  jQuery(this).attr("id");
  		items = pattern.exec(id); 

  		inputNameId =  items[1];
  		inputName = items[6];
  		customFieldId = items[2];
  		groupCounter = items[3];	
          groupId = items[5];

          oldval = jQuery("#c"+inputNameId+"Counter").val();
          newval = parseInt(oldval) + 1; 
          jQuery("#c"+inputNameId+"Counter").val(newval);

          counter = jQuery("#c"+inputNameId+"Counter").val();
          div  = "c"+inputNameId+"Duplicate";
          counter_field = inputName +"_"+ groupCounter;
       	
  		getDuplicate(customFieldId,counter,div,groupCounter,groupId,counter_field);
      });
    

  });

  moveAddToLast = function(context, bt) {
      if (bt && context) {
        bt.prependTo(context.find(".mf_toolbox:last .mf_toolbox_controls"));

        if (bt.data("originalText")) {
          bt.html(bt.data("originalText"));
        }

      } else {

        jQuery('.duplicate_button', context).each( function() {
          var el = jQuery(this);
          el.prependTo(el.closest(".write_panel_wrapper").find(".mf_toolbox:last .mf_toolbox_controls"));
        
          if (el.data("originalText")) {
            el.html(el.data("originalText"));
          }
        
        });

      }
  };

  /**
   * field duplicate 
   */
  getDuplicate = function(fId,fcounter,div,gcounter,groupId,counter_field){
      jQuery.ajax({
          type : "POST",
          url  : mf_path+'RCCWP_GetDuplicate.php',
          data : "customFieldId="+fId+"&fieldCounter="+fcounter+"&groupCounter="+gcounter+"&groupId="+groupId,
          success: function(msg){
            
              var newel = $(msg);
              
              jQuery("#"+div).before(newel);
        			// set the editor in textarea
        			add_editor_text(newel); 
        			add_color_picker(newel);
			        
              newel.find('.mf_message_error .error_magicfields').hide();
			        newel.fadeIn();
              newel.mf_group_show_save_warning();
			        
  			//fixing the order in the indexes of the custom fields
  		    fixcounter("counter_"+counter_field);
		    
          }
      });
  }


  fixcounter = function(fields){
      init = 1;
      jQuery.each(jQuery('.'+fields),function(key,value){
          counter = init+key + 1;
          jQuery(this).text(counter);
      });
  }

  /**
   * Add a new duplicate group
   *
   */
  GetGroupDuplicate = function(div,customGroupID,order){
      customGroupCounter =  jQuery('#g'+customGroupID+'counter').val();
      customGroupCounter++;
      jQuery("#g"+customGroupID+"counter").val(customGroupCounter);
    
      //order = jQuery("order_"+customGroupID);

    
      jQuery.ajax({
          type    : "POST",
          url     : mf_path+'RCCWP_GetDuplicate.php',
          data    : "flag=group&groupId="+customGroupID+"&groupCounter="+customGroupCounter+"&order="+order,
          success : function(msg){
              var newel = jQuery(msg);
              jQuery("#write_panel_wrap_"+customGroupID).append(newel);
              
              kids =  jQuery("#write_panel_wrap_"+customGroupID).children().filter(".magicfield_group");
                  for(i=0;i < kids.length; i++){
                      groupCounter =  kids[i].id.split("_")[2];
                      ids = kids[i].id.split("_")[3];
                      jQuery("#order_"+groupCounter+"_"+ids).val(i+1);
                      value =  i + 1;
                      jQuery("#counter_"+groupCounter+"_"+ids).text(value);

                      newel.find('.mf_message_error .error_magicfields').hide();
                      newel.mf_group_expand();
                      
                      newel.find(".mf-group-loading").hide();

              		    // move the add button to the last panel
              		    moveAddToLast(jQuery("#write_panel_wrap_"+customGroupID));
                      newel.find("input,textarea").eq(0).focus();
                      newel.mf_group_update_count();
                      newel.mf_group_show_save_warning();
                      
                      //jQuery.scrollTo(newel, 500);
                  }
          }
      });
  }

    
})(jQuery);


/**
 * Delete a Duplicate Group
 *
 */
deleteGroupDuplicate = function(div){
    var parent = jQuery("#"+div);
    var db = parent.find(".duplicate_button").clone();
    var context = parent.closest(".write_panel_wrapper");
    parent.fadeOut({ duration: "normal", complete: function() { parent.remove(); context.mf_group_update_count(); context.mf_group_show_save_warning(); moveAddToLast(context, db); } });
}

/**
 * Add the editor in new textarea
 *
 */
add_editor_text = function(context){
  var $ = jQuery;
  
  var options = jQuery.extend(true, {}, tinyMCEPreInit.mceInit, { 
    setup : function(ed) {
      ed.onClick.add( function(ed, l) {
        var el = ed.getElement();
        if (el) {
          jQuery(el).mf_group_show_save_warning().mf_group_update_count();
        }
      })
    }
  });
  
  
  var doInit = true;
  
  if (context && context.length) {
    // find textareas inside the context element (much faster than ALL available textareas)
    
    var editors = context.find('textarea.pre_editor');
    
    var ids = [];
    
    if (editors.length) {
      // gather the ids of the editors
      editors.each( function() {
        ids.push($(this).attr("id"));
      });
      
      options.elements = ids.join(",");
      options.mode = "exact";
      
    } else {
      options.editor_selector = "pre_editor";
      doInit = false; // there are no editors, so don't initialise
    }
  
  } else {
    options.editor_selector = "pre_editor";
  }
  
  if (doInit) {
    tinyMCE.init(options);
  
  	jQuery(".Multiline_Textbox :input[type='textarea'].pre_editor", context).each( function(inputField){
      var editor_text = jQuery(this).attr('id');
  		tinyMCE.execCommand('mceAddControl', true, editor_text); 
  		jQuery('#'+editor_text, context).removeClass('pre_editor');
  	});
  }
  
	jQuery(".markdowntextboxinterface:not(.markItUpEditor)").markItUp(mySettings);
}

/**
 * Add the color picker, only inputs with class mf_color_picker
 */
add_color_picker = function(context){
  
  jQuery(".mf-cp", context).each( function() {
    var f = jQuery(this).closest('.mf-field');
    var input = f.find('input.mf_color_picker');
    var bt = f.find("button.mf-color-clear");
    
    var cp = jQuery(this);
    
    input.attr('readonly', 'readonly');
    
    var val = jQuery.trim(input.val());
    
    
    if (!jQuery(this).data("ColorPicker")) {
      jQuery(this).ColorPicker({
        flat: true,
        color: input.val(),
        
        onChange: function (hsb, hex, rgb) {
          input.val('#' + hex);
      	}

      }).data("ColorPicker", true);
    }
    
    
    if (val == "") {
      jQuery(this).find(".colorpicker_color div div").hide();
    } 
    
    jQuery(this).mousedown( function() {
      jQuery(this).find(".colorpicker_color div div").show();
    });
    
    // setup button
    
    bt.click( function() {
      cp.find(".colorpicker_color div div").hide();
      input.val('');
      cp.ColorPickerSetColor("#FFFFFF");
      return false;
    });
    
    
  });
    
}
