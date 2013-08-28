
function smartTrim(string, maxLength) {
  if (!string) return string;
  if (maxLength < 1) return string;
  if (string.length <= maxLength) return string;
  if (maxLength == 1) return string.substring(0,1) + ' &hellip; ';

  var midpoint = Math.ceil(string.length / 2);
  var toremove = string.length - maxLength;
  var lstrip = Math.ceil(toremove/2);
  var rstrip = toremove - lstrip;
  return string.substring(0, midpoint-lstrip) + ' &hellip; ' + string.substring(midpoint+rstrip);
}

    
(function($) { // closure and $ portability


  
  // ajax file uploader customisation
  
  qq.FileUploader.prototype._formatSize = function(bytes) {
    var i = -1;                                    

    do {
        bytes = bytes / 1024;
        i++;  
    } while (bytes > 99);
        
    return "( " + Math.max(bytes, 0.1).toFixed(1) + " " + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i] + " )";          
  }

  qq.FileUploader.prototype._addToList = function(id, fileName) {

      var item = qq.toElement(this._options.fileTemplate);                
      item.qqFileId = id;
      
      var fileElement = this._find(item, 'file');        
      qq.setText(fileElement, this._formatFileName(fileName));
      
      this._find(item, 'size').style.display = 'none';        
      $(this._listElement).html(item);
   };
  
   qq.UploadDropZone.prototype.onDrop = function(e){
     $('.ajax-upload-drop-area').hide().removeClass(self._classes.dropActive);
     self._uploadFileList(e.dataTransfer.files);    
   }




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
              //content = $.stripTags(orig).substring(0, 70);
            
              content = smartTrim($.stripTags(orig), 70);
              
              if (content == "") {
                content = "( empty )";
                td.addClass("none");
                th.addClass("none");
              } else {
                //if (orig != content) {
                //  content = content + "&hellip;";
                //}
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
              //content = orig.substring(0, 150);
              content = smartTrim(orig, 150);


              if (content == "") {
                content = "( empty )";
                td.addClass("none");
                th.addClass("none");
              } else {
                if (!def) { el.removeClass("empty"); }
                
                /*if (orig != content) {
                  content += "&hellip;";
                }*/
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

                if (tc == "related-type") {
                  var href = document.location.href.split("?")[0];
                  content += ' (&nbsp;<a href="' + href + '?action=edit&post=' + val + '" target="_blank" title="Edit related page/post in a new window">Edit Post</a>&nbsp;)';
                }

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
          
          if (el.closest(".write_panel_wrapper").find(".magicfield_group").length == 1) {
            // if no data has been provided yet and this is the ONLY group, hide the "add" button, since it's likely people will click this to try to add the initial record
            // and this is not what the button does. We will show the button as soon as they expand the initial field.
            el.find(".duplicate_button").hide();
          }
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

    
  $.fn.mf_field_init_uploader = function() {
    
    return this.each( function() {
      
      // load any internal iframes - this speeds up the intial load time by a whole lot if there are a lot of file upload controls, since the browser doesn't load them all initially!
        
      $(this).find('.iframeload').each( function() {
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
    
      
      // load (alternate) ajax file uploaders instead, if the AJAX uploader is activated
      
      $(this).find(".ajaxupload").each( function() {
        
        var f = $(this).closest(".mf-field");
        
        var t = '';
        
        // derive the "type" class
        var matches = f.attr("class").match(/mf-t-[a-z0-9\-]+/);
    
        if (matches.length) {
          t = matches[0];
        }
        
        var tc = t.replace("mf-t-", "");
      
      
        var md = $(this).metadata({ type: 'class' });
        
        var el = $(this);
        
        if (!el.data("uploader")) {
          
          
          var allowedExtensions = ["pdf", "doc", "xls", "ppt", "txt", "jpeg", "psd", "jpg", "gif", "png", "docx", "pptx", "xslx", "pps", "zip", "gz", "gzip", "mp3", "aac", "mp4", "wav", "wma", "aif", "aiff", "ogg", "flv", "f4v", "mov", "avi", "mkv", "xvid", "divx"];
          
          // compile the allowed extensions
          if (tc) {
            if (tc == "image") {
              allowedExtensions = ["jpeg", "jpg", "gif", "png"];
            } else if (tc == "audio") {
              allowedExtensions = ["mp3", "aac", "mp4", "wav", "wma", "aif", "aiff", "ogg"];
            }
          }
          
          
          //allowedExtensions: [],
          
          var input_el = f.find(".mf_custom_field input[type=hidden]");
          var ival = input_el.val();
          var nonce_file = nonce_ajax_upload;

          var uploader = new qq.FileUploader({
              element: this,
              multiple: false,
              action: mf_path + "/RCCWP_upload_ajax.php?nonce_ajax=" + nonce_file,
              allowedExtensions: allowedExtensions,
              
              onComplete: function(id, fileName, result) {
                
                // hide all drop targets
                $('.ajax-upload-drop-area').hide()

                var field = el.closest(".mf-field");
                
                // get the upload message element
                var um = field.find(".upload-msg");
                um.removeClass("mf-upload-success mf-upload-error");
                
                if (result.success) {
                  
                  field.find(".ajax-upload-button span").html(md.lang.replace);

                  um.addClass("mf-upload-success");
                  um.html(md.lang.upload_success); // the success string is in metadata

                  field.mf_group_show_save_warning();


                  if (input_el.length) {
                    input_el.val(result.file);
                  }

                
                  if (tc == "image") {
                    // find the image element
                
                    var img_el = field.find(".image_photo img");
                  
                    if (img_el.length) {
                      // set the thumbnail preview
                      img_el.attr("src", $("<div/>").html(result.thumb).text() );
                    }

                  } else if (tc == "audio") { 
                    
                    // add or update the audio player 
                    
                    // find the value container (new)
                    var av = field.find(".mf-audio-value");
                    
                    // set html to the flash audio player
                    av.empty().html(
                      $.tmpl(
                        '<div id="obj-#{id}" style="width:260px; padding-top: 3px;">\
                         <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="99%" height="20" wmode="transparent">\
                         <param name="movie" value="#{mf_path}js/singlemp3player.swf?file=#{uri}" wmode="transparent" />\
                         <param name="quality" value="high" wmode="transparent" />\
                         <embed src="#{mf_path}js/singlemp3player.swf?file=#{uri}" width="99%" height="20" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent" />\
                         </embed>\
                         </object>\
                         </div>',
                         { id: input_el.attr("id"), mf_path: mf_path, uri: result.uri }
                      )
                    );
                    
                    // show the actions
                    field.find(".actions-audio").show(); 
                  
                  } else { // other file
                    // update the view link
                    field.find("a.mf-file-view").attr("href", result.uri);
                  }

                
                } else {
                  
                  um.addClass("mf-upload-error");
                  um.html(md.lang.upload_error); // the error string is in metadata

                }
                
                um.show();
                
              },
            
              template: '<div class="ajax-uploader">' + 
                  '<ul class="ajax-upload-list"><li>' + smartTrim(ival, 50) + '</li></ul>' + 
                  '<div class="ajax-upload-drop-area"><span>' + md.lang.drop + '</span></div>' +
                  '<div class="ajax-upload-button button"><span>' + (ival && ival != "" ? md.lang.replace : md.lang.upload) + '</span></div>' +
               '</div>',
             
              fileTemplate: '<li>' +
                  '<span class="ajax-upload-file"></span>' +
                  '<span class="ajax-upload-spinner"></span>' +
                  '<span class="ajax-upload-size"></span>' +
                  '<a class="ajax-upload-cancel" href="#">Cancel</a>' +
                  '<span class="ajax-upload-failed-text">Failed</span>' +
              '</li>',        
            
              classes: {
                // used to get elements from templates
                button: 'ajax-upload-button',
                drop: 'ajax-upload-drop-area',
                dropActive: 'ajax-upload-drop-area-active',
                list: 'ajax-upload-list',
                        
                file: 'ajax-upload-file',
                spinner: 'ajax-upload-spinner',
                size: 'ajax-upload-size',
                cancel: 'ajax-upload-cancel',

                // added to list item when upload completes
                // used in css to hide progress spinner
                success: 'ajax-upload-success',
                fail: 'ajax-upload-fail'
              }
        
          });
        
          // store the uploader against this element, so it doesn't get created again
          el.data("uploader", uploader);

        }  
      });
            
            
    
    
    });
    
    
    
  };
  

  $.fn.mf_group_expand = function(init) {
    return this.each( function() {

      var el = $(this);
      
      el.data("mf_summarised", false);
    
      el.find(".collapse_button,.duplicate_button").fadeIn();
      var fc = el.find(".mf-fields");
      var fields = $(this).find(".mf-field");
    
      // set the editor in textarea
  		add_editor_text($(this));
  		add_color_picker($(this));

      fields.mf_field_init_uploader();
      
      fc.show();

      if (!init) {
        fc.find("input,textrea,select").eq(0).focus();
      }
    
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
  
  jQuery(window).load( function() {
    // this can't be done in document ready for some reason
    $('.mf-group-expanded').mf_group_expand(true);
  });
  
  jQuery(document).ready(function(){
    
    var tt_template = 
   '<div class="tt"> \
    <div class="tthl"><div class="tthr"><div class="tth"></div></div></div> \
    <div class="ttbl"><div class="ttbr"><div class="ttb"><div class="ttbc">#{content}</div></div></div></div> \
    <div class="ttfl"><div class="ttfr"><div class="ttf"></div></div></div> \
    </div>';

    $(document).on("mouseenter",'small.tip', function(event) {
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
          $(this).closest(".write_panel_wrapper").find(".magicfield_group")
            .mf_group_expand(true)
            .eq(0).find("input[type=text],textarea,select").eq(0).focus();
          
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
      
      var fieldchange = function() {
        $(this).closest(".magicfield_group").find(".mf-default").removeClass("mf-default");
        $(this).closest(".mf_custom_field").removeClass("mf-default");
        $(this).mf_group_show_save_warning();
        $('#mf-publish-errors').hide();
      };
         
      mf_groups.find("input[type=text],textarea").live("keydown", fieldchange);
      mf_groups.find("input[type=checkbox],input[type=radio]").live("click", fieldchange);
      mf_groups.find("select").live("change", fieldchange);

      $('.ajax-upload-drop-area').live( "mouseleave", function() {
        $(this).hide();
      });
      
      
      
      $('.mf_message_error .error_magicfields').hide();
    
      mf_groups.filter(":not(.mf-group-expanded)").mf_group_summary({ init: true });
      
      mf_groups.filter(".mf-group-expanded")
        .find(".collapse_button").show().end()
        .find(".mf-group-loading").hide().end()
      
      $('.tab_multi_mf a.edButtonHTML_mf').click( function() {
        $(this).closest(".tab_multi_mf").find(".edButtonHTML_mf").removeClass("current");
        $(this).addClass("current");
      });

      wrappers.mf_group_update_count();


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

      jQuery(".mf-field").find("input,textarea,select")
        .live("focus", function(event) {
          $(this).closest(".mf-fields").find(".mf-field").removeClass("focused");
          $(this).closest(".mf-field").addClass("focused");
        })
        .live("blur", function(event) {
          $(this).closest(".mf-field").removeClass("focused");
        });
        
      
      //duplicate  group
      jQuery(".duplicate_button").live("click", function(event){
          id = jQuery(this).attr("id"); 
          id = id.split("_"); 
          group = id[2];
          customGroupID =  id[3];
          order = id[4];
          order =  parseInt(order) + 1;

          var group = $(this).closest(".magicfield_group");
          
          if (event.shiftKey) {
            // collapse the current group as this one is added
            group.mf_group_summary();
          }
          
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

        var doIt = true;
      
        if (!$(this).closest(".mf_duplicate_group.empty").length) {
          // this data set is not empty, so we should double-check the removal
          // get the language for confirm message
          var md = $(this).metadata({ type: 'class' });
          var msg = "Are you sure?";
          
          if (md && md.lang) {
            msg = md.lang.confirm || msg;
          }
          
          doIt = confirm(msg);
        }
        
        if (doIt) {

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

	//validate maxlength
	jQuery('.mf-field.maxlength input, .mf-field.maxlength textarea').live( 'keyup', function(){
		var maximal = parseInt(jQuery(this).attr('maxlength'));
		var actual = parseInt(jQuery(this).val().length);
		//alert( maximal + ' - ' + actual );
		if(actual > maximal){
			jQuery(this).val(jQuery(this).val().substr(0, maximal));
		}
		
		if(maximal - actual < 10) {
			jQuery(this).parents(".mf-field").find('.charsRemaining').addClass('extreme');
		}else {
			jQuery(this).parents(".mf-field").find('.charsRemaining').removeClass('extreme');
		}
		jQuery(this).parents(".mf-field").find('.charsRemaining').html(maximal - actual);
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
              
              newel
                .fadeIn()
                .mf_field_init_uploader()
                .mf_group_show_save_warning();
			        
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
          jQuery(el).closest(".mf-field").addClass("focused");
        }
      });
      
      ed.onActivate.add( function(ed, l) {
        var el = ed.getElement();
        
        if (el) {
          jQuery(el).closest(".mf-field").addClass("focused");
        }
      });

      ed.onDeactivate.add( function(ed, l) {
        var el = ed.getElement();
        if (el) {
          jQuery(el).closest(".mf-field").removeClass("focused");
        }
      });
      
    }
  });

  options.theme_advanced_buttons1 = "add_image,add_video,add_audio,add_media,|," + options.theme_advanced_buttons1;
  
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
      options.init_instance_callback = 'mf_resizeEditorBox';
      
    } else {
      options.editor_selector = "pre_editor";
      doInit = false; // there are no editors, so don't initialise
    }
  
  } else {
    options.editor_selector = "pre_editor";
  }
  
  if (doInit) {
    //tinyMCE.init(options);
  	jQuery(".pre_editor", context).each( function(inputField){
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

/** 
 * Set a Default width/height to the multiline text fields
 * 
 */
mf_resizeEditorBox = function (editor) {
    // Have this function executed via TinyMCE's init_instance_callback option!
    // requires TinyMCE3.x
    var container = editor.contentAreaContainer; /* new in TinyMCE3.x - */

    // Traversal note: This code causes big problems when resizing multiline text fields, and in the fullscreen mode (at least on the Mac it does). Commented out for now.
    // extend container by the difference between available width/height and used width/height
    //docFrame = container.children [0] // doesn't seem right : was .style.height;
    //docFrame.style.width = container.style.width =  "100%";
    //docFrame.style.height = container.style.height = "200px";
}

