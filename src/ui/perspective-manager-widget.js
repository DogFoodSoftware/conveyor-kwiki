// start with a closure to bind '$' to jQuery and scope further variables
(function($) {
    // define templates first; this is a Kibbles thing
    ich.addTemplate('perspective_manager','<div class="perspective-manager"></div>');
    ich.addTemplate('perspective_manager_dropdown','<select data-placeholder="Set Perspective" style="width: 100%" multiple="multiple"><option value=""></option></select>');
    ich.addTemplate('perspective_manager_option','<option>{{option}}</option>');
    
    // defnie the plugin methods; 'init' and 'destroy' are part of the 
    // jQuery lifecycle and should always be defined
    var methods = {
    init : function(options) {
	var defaults = {"style":"dropdown",
			"perspectives": $.ui_state('get_properties', 'perspective')};
      // unless returning an intrinsic value, always 'return this' to
      // support jQuery chanability; '.each()' results in this
      return this.each(function() {
        // here 'this' is an HTML element, so let's jQuery-ify it
          var $this = $(this);
          var data = $.extend({},
			      defaults,
			      ($this.data('perspective-manager') || {}),
			      options);
	  ;
          $this.data('perspective-manager', data);
	  
	  $this.append(ich.perspective_manager());

	  $this.perspective_manager('render_perspectives');
      });
    },
    destroy : function() {
      return this.each(function() {
        var $this = $(this);
        // clean up the data
        $this.removeData('perspective-manager');
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
		  $canvas.append(ich.perspective_manager_dropdown());
		  var $select = $canvas.find('select');
		  var perspectives = perspective_data.data;

		  for (var i = 0; i < perspectives.length; i += 1)
		      $select.append(ich.perspective_manager_option({option: perspectives[i]}));

		  $select.val(data['perspectives']);

		  $select.chosen().on('change', (function($select, $this) {
		      return function(event) {
			  $this.data('perspective-manager')['perspectives'] = $select.val(); // that's an array
			  methods.render_perspectives.call($this);
			  $.ui_state('set_property', 'perspective', $select.val());
		      };
		  })($select, $this));
	      }
	  });
      },
	render_perspectives: function() {
	  return this.each(function() {
	      var $this = $(this);
	      var data = $this.data('perspective-manager');
	      
	      var selected_perspectives = data['perspectives'];
	      $('[data-perspective]').each(function(i, el) {
		  var $this = $(el);
		  var perspective_string = $this.data('perspective');
		  if (perspective_string != 'all') { // we leave 'all' alone
		      // then we test to see if it's shown or not
		      var element_perspectives = perspective_string.split(/\s+/);
		      var matched = false;
		      for (var i = 0; i < element_perspectives.length; i += 1) {
			  var perspective = element_perspectives[i];
			  if ($.inArray(perspective, selected_perspectives) != -1) {
			      matched = true;
			      $(el).fadeIn((function($el) {
				  // see note below for the else case
				  return function() {
				      $el.show();
				  };
			      })($(el)));
			  }
		      }
		      if (!matched) {
			  // this is necessary to deal with the case where a
			  // containing element is NOT displayed initially,
			  // which would normally cause 'fadeOut' to take no
			  // action, but we still want our element to be
			  // hidden when it is shown
			  $(el).fadeOut((function($el) {
			      return function() {
				  $el.hide();
			      };
			  })($(el)));
		      }
		  }
	      });
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

