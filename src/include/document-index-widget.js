/**
 * <div class="p">
 * Generates an index for a given <span data-todo="define and
 * referenece">documentation path</span>. The widget <em>must</em> attempt to contact the <code>/documentation</code> resource 

If the documentation path does not
 * point to a directory, the widget <em>must</em> add style class
 * <code>error-loading-widget</code> to all target elements and invoke the
 * error loading widget to process the elements as well as send an appropriate
 * global error messasge to the site messsages widget.
 * </div>
 * <div class="p">
 * 
 * </div>
 */
(function($) {
  /**
   * The <a href="/documentation/kibbles/Widget_Reference#canvas-container">
   * canvas container</a> and primary template. Brief description.
   */
  ich.loadTemplate('foo_bar_widget', '<div class="foo-bar"></div>');

  methods = {
    init : function() {
      return this.each(function() {
        var $this = $(this);
        // initialize data if necessary; see http://docs.jquery.com/Plugins/Authoring#Data
        // initialize event handlers if necessary;
        // don't forget to namespace events: http://docs.jquery.com/Namespaced_Events
        // load templates into targets if appropriate for the widget
      };
    },
    destroy : function() {
      return this.each(function() {
        // clean up data as necessary
        // clean up event bindings as necessary
      };
    }//,
    // other widget specific functions as necessary; specifically, any
    // asynchronous communication necessary to perfect the data should be
    // handled through plugin methods, allowing a user to manually load data
    // themselves using the same infrastructure
  };

  // load the plugin into jQuery
  // this (more or less) always looks the same, just change the plugin name
  $.fn.foo_bar = function(method) {
    if (methods[method])
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    else if ( typeof method === 'object' || ! method )
      return methods.init.apply( this, arguments );
    else $.error( 'Method ' +  method + ' does not exist on jQuery.foo_bar');
  };

  // as Kibbles widgets, we will generally want to set up default bindings
  // here 
  $(document).ready(function() {
    $('.foo-bar-widget').foo_bar();
    // and any other initialization logic
  });
})(jQuery);
