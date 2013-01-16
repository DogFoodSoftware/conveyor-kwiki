// start with a closure to bind '$' to jQuery and scope further variables
(function($) {
  // define templates first; this is a Kibbles thing
  ich.addTemplate('perspective_manager_widget','<div class="perspective-manager"></div>');

  // defnie the plugin methods; 'init' and 'destroy' are part of the 
  // jQuery lifecycle and should always be defined
  var methods = {
    init : function(options) {
      var settings = $.extend({/*default options go here*/},
        options);
      // unless returning an intrinsic value, always 'return this' to
      // support jQuery chanability; '.each()' results in this
      return this.each(function() {
        // here 'this' is an HTML element, so let's jQuery-ify it
        var $this = $(this);
        // qualify events with namespace
        // uses '$this.data' to maintain instance variables; don't
        // overwrite if already present
        var data = $this.data('perspective-manager');
        if (!data)
          $this.data('perspective-manager', {
                                      /*defaults go here*/
                                    });
	  $this.append(ich.perspective_manager_widget());
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
    show : function() { /* ... */ },
    hide : function() { /* ... */ },
    reposition : function() {  /* ... */ }
  };

  // expose the plugin
  $.fn.perspective_manager = function(method) {
    if (methods[method])
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    else if (typeof method === 'object' || !method)
      return methods.init.apply(this, arguments);
    else $.error("Method '" + method + "' does not exist on jQuery.tooltip_example.");
  };

  // most widgets have a standard binding
  $(document).ready(function() {
    	if (typeof(suppress_default_kibbles_widget_bindings) == 'undefined' ||
	    !suppress_default_kibbles_widget_bindings) {
	  $('.perspective-manager-widget').perspective_manager();
        }
  });
})(jQuery);

alert('hi are you working?');
