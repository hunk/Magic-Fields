(function($) { // closure and $ portability

  $.metadata.setType("attr", "validate");

  $(document).ready(function() {
  	$("#post").validate({
  			errorClass: "error_magicfields",
			  
			  
  			submitHandler: function(form) {
  			  $('#mf-publish-errors').remove();
          form.submit();
  		  },
		  
  			invalidHandler: function(form, validator) {
          
          var errors = validator.numberOfInvalids();

          if (errors) {
            
            $('#mf-publish-errors').remove();
            
            $('#publishing-action #ajax-loading').hide();
            $('#publishing-action #publish').removeClass("button-primary-disabled");
          
            $('#major-publishing-actions').append( $('<div id="mf-publish-errors">Sorry, some required fields are missing. Please provide values for any highlighted fields and try again.</div>') ); 
          }
        
        },
        
        showErrors: function(errorMap, errorList) {
      		
      		// expand the group summary for each field, so that the error is visible (could be very confusing for the user otherwise!)

      		$.each(errorList, function() {
      		  if (this.element) {
      		    $(this.element).closest(".magicfield_group").mf_group_expand();
      		  }
    		  });
    		  
      		this.defaultShowErrors();
      	}

  		});
  });

  
    
})(jQuery);

