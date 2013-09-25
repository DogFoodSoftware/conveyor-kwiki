/**
<div class="p">
  Widget to display code with syntax highlighting. At the moment, the widget
  only highlights code already present in the element. Future versions will
  support code retrieval as well.
</div>
*/
(function($) {
    $(document).ready(function() {
    	if (typeof(suppress_default_kibbles_widget_bindings) == 'undefined' ||
	    !suppress_default_kibbles_widget_bindings) {
	    prettyPrint();
	    $('.prettyPrintBox').resizable_block();
        }
    });
})(jQuery);
