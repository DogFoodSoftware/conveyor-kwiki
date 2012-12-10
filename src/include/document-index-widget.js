/**

 * <div class="p">
 * By default, the widget will <span data-todo="define and link to basic
 * request protocol">attempt to request</span>
 * <code>GET:*:/documentation/[folder path]</code> and generate a rendering of
 * the index from the results. It is also possible to pass in a well formed
 * index data set directly to the widget.
 * </div>
 * <div class="subHeader">Inputs</div>
 * <div class="p">
 * The folder path may be specified as part of the element data in a
 * <code>data-folder-path</code> attribute, or it may be specified as an
 * option, <code>folder path</code> in instantiating the widget. <span
 * class="define and link">Standard override rules apply.</span>
 * </div>
 * <div class="p">

 * If index data is provided directly to the widget, the default behavior will be suppressed.<span class="note">This does not in-and-of-itself supress the <a href="/documentation/kibbles/ref/Widget_Reference#default-element-binding-and-auto-loading">default binding and auto-loading</a> as the two processes are essentially unrelated; the auto-loading originates from the inclusion of the file itself and is unaware of 'manual' usages of the widget.</span>

 * </div>

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
