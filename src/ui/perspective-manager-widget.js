// start with a closure to bind '$' to jQuery and scope further variables
(function($) {
    // define templates first; this is a Kibbles thing
    ich.addTemplate('perspective_manager','<div class="perspective-manager"></div>');
    ich.addTemplate('perspective_manager_dropdown','<form class="perspective-manager"><textarea rows="1"></textarea></form>');
    
    // defnie the plugin methods; 'init' and 'destroy' are part of the 
    // jQuery lifecycle and should always be defined
    var methods = {
    init : function(options) {
	var defaults = {"style":"dropdown"};
      // unless returning an intrinsic value, always 'return this' to
      // support jQuery chanability; '.each()' results in this
      return this.each(function() {
        // here 'this' is an HTML element, so let's jQuery-ify it
          var $this = $(this);
          var data = $.extend({}, 
			      defaults,
			      ($this.data('perspective-manager') || {}),
			      options);
          $this.data('perspective-manager', data);
	  
	  $this.append(ich.perspective_manager());
      });
    },
    destroy : function() {
      // cleanup global events
      $(window).off('.tooltip-example');
      return this.each(function() {
        var $this = $(this);
        // cleanup local events
        $this.off('.tooltip-example');
        // clean up the data
        $this.removeData('hello-world');
      });
    },
    // plugin specific methods
      set_perspectives : function(perspective_data) {
	  return this.each(function() {
	      var $this = $(this);
	      var data = $this.data('perspective-manager');
	      // if (typeof perspective_data == 'undefined') { // get the data
	      if (perspective_data == null) { // get the data
		  $.kibbles_data({url: '/documentation-perspectives/',
				  success: function(perspective_data) {
				      $this.loading_spinner('destroy').
					  perspective_manager('set_perspectives', perspective_data);
				  },
				  error: function() {
				      /**
				       * <todo>Make this a template.</todo>
				       */
				      $this.loading_spinner('destroy').find('.project-summary').html('Error processing groups request.</div>');
				  }
				 });
	      }
	      else { // render the data
		  var $canvas = $this.find('.perspective-manager');
		  (function($this, $canvas, data) {
		      if (data.style == "chase") {
		      }
		      else { // default to dropdown
			  $canvas.append(ich.perspective_manager_dropdown());
			  $canvas.find('textarea').textext({
			      plugins: 'tags prompt filter autocomplete arrow',
			      prompt: 'Set perspective...'
			  }).bind('getSuggestions', function(e, data) {
			      var textext = $(e.target).textext()[0];
			      query = (data ? data.query : '') || '';
			      $(this).trigger('setSuggestions',
					      { result : textext.itemManager().filter(perspective_data.data, query) }
					     );
			  }).bind('setFormData', function(e, data, isEmpty) {
			      var textext = $(e.target).textext()[0];
			      var val = eval(textext.hiddenInput().val());
			      if ($(val).not($(data['selected perspectives'])).length != 0 ||
				  $(data['selected perspectives']).not($(val)).length != 0) {
				  data['selected perspectives'] = val;
				  alert(data['selected perspectives']);
			      }
			  });
		      }
		  })($this, $canvas, data);
	      }
	  });
      }
  };

  // expose the plugin
  $.fn.perspective_manager = function(method) {
    if (methods[method])
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    else if (typeof method === 'object' || !method)
      return methods.init.apply(this, arguments);
    else $.error("Method '" + method + "' does not exist on jQuery.perspective_manager.");
  };

  // most widgets have a standard binding
  $(document).ready(function() {
    	if (typeof(suppress_default_kibbles_widget_bindings) == 'undefined' ||
	    !suppress_default_kibbles_widget_bindings) {
	    $('.perspective-manager-widget').
		perspective_manager().
		perspective_manager('set_perspectives');
        }
  });
})(jQuery);

