// start with a closure to bind '$' to jQuery and scope further variables
(function($) {
    // define templates first; this is a Kibbles thing
    ich.addTemplate('perspective_manager','<div class="perspective-manager"></div>');
    ich.addTemplate('perspective_manager_dropdown','<select data-placeholder="Set Perspective" style="width: 100%" multiple="multiple"><option value=""></option><option value="all">all</option></select>');
    ich.addTemplate('perspective_manager_optgroup','<optgroup label={{group name}}></optgroup>');
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
		  var perspective_groups = perspective_data.data;

		  for (var i = 0; i < perspective_groups.length; i += 1) {
		      var $optgroup = $select.append(ich.perspective_manager_optgroup(perspective_groups[i])).
			  // 'append()' returns the jQuery object upon which
			  // it's invoked, following the standard chaining
			  // rules for jQuery so we have to then access the
			  // thing we just added to get the optgroup
			  children().last();
		      var options = perspective_groups[i]['group options'];
		      for (var j = 0; j < options.length; j += 1)
			  $optgroup.append(ich.perspective_manager_option({option: options[j]}));
		  }


		  $select.val(data['perspectives']);

		  $select.chosen().on('change', (function($select, $this) {
		      return function(event) {
			  var selected_perspectives = $select.val(); // that's an array
			  // for some reason, I believe it has to do with
			  // support for empty value lead in, once the user
			  // selects any value, there's always a single empty
			  // value at the head of the array; we don't really
			  // want that
			  if (selected_perspectives != null && 
			      selected_perspectives.length > 0 &&
			      selected_perspectives[0] == '')
			      selected_perspectives = selected_perspectives.slice(1);
			  $this.data('perspective-manager')['perspectives'] = selected_perspectives;
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
		  var element_perspectives = perspective_string.split(/\s+/);
		  // The perspective selection supports:
		  // - the special string 'all', which is always true
		  // - the special string 'empty', which is true only when
		  //   selection is empty
		  // - implied 'or' test when items are space separated
		  // - 'and' test if items are separated by '&'
		  // - ! the set of terms
		  //
		  // In the future, it would be great to support full boolean
		  // algebra:
		  // http://my.safaribooksonline.com/book/databases/sql/9781449319724/a-simplified-bnf-grammar/id2809359
		  // but at the moment we have bigger fish to fry to so we
		  // keep it simple and cover 80% of the use cases
		  var matched = false; // default
		  // check whether the expression is inverted
		  var inverted = false;
		  if (perspective_string.indexOf('!') == 0) {
		      inverted = true;
		      perspective_string = perspective_string.substring(1);
		  }

		  if (perspective_string == 'all' || $.inArray('all', selected_perspectives) > -1)
		      matched = true;
		  else if (perspective_string == 'any')
		      matched = selected_perspectives != null && selected_perspectives.length > 0;
		  else if (perspective_string == 'empty')
		      matched = selected_perspectives == null || selected_perspectives.length == 0;
		  else if (selected_perspectives != null && selected_perspectives.length > 0) { 
		      // process for 'and', 'or' or trivial matches
		      var element_perspectives = perspective_string.split(/ /);
		      // first, we'll process 'or' matches and trivial
		      // matches; this will still run a test for an '&' match,
		      // but it will always be falso so that's okay
		      for (var i = 0; i < element_perspectives.length; i += 1) {
			  var perspective = element_perspectives[i];
			  if ($.inArray(perspective, selected_perspectives) != -1) {
			      matched = true;
			      break;
			  }
		      }
		      // now we process 'and' matches
		      if (!matched) {
			  var element_perspectives = perspective_string.split(/&/);
			  var match_count = 0;
			  for (var i = 0; i < element_perspectives.length; i += 1) {
			      var perspective = element_perspectives[i];
			      if ($.inArray(perspective, selected_perspectives) == -1)
				  break;
			      else match_count += 1;
			  }
			  matched = match_count == element_perspectives.length;
		      }
		  }
		  
		  if ((matched && !inverted) || (!matched && inverted) || 
		      // the user selected 'all' always shows everything
		      $.inArray('all', selected_perspectives) > -1) {
		      if ($(el).prop('tagName') == 'A')
			  $(el).attr('href', $(el).data('href'));
		      else
			  $(el).fadeIn((function($el) {
			      // see note below for the else case for we have
			      // to show after fading in
			      return function() {
				  $el.show();
			      };
			  })($(el)));
		  }
		  else if ((!matched && !inverted) || (matched && inverted)) {
		      if ($(el).prop('tagName') == 'A')
			  $(el).removeAttr('href');
		      else
			  // it is necessary to hide after fading out to deal
			  // with the case where a containing element is NOT
			  // displayed initially, which would normally cause
			  // 'fadeOut' to take no action, but we still want
			  // our element to be hidden when it is shown
			  $(el).fadeOut((function($el) {
			      return function() {
				  $el.hide();
			      };
			  })($(el)));
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

