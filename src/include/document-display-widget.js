/**
 * <div class="p">
 * Displays a single document. The document is receieved as an HTML fragment
 * in a <a href="/documentation/kibbles/ref/Headless_Exchange">data exchange</a>.
 * </div>
 */
(function($) {
    /**
     * Basic <a
     * href="/documentation/kibbles/ref/Widget_Reference#canvas-container"> canvas
     * container</a>.
     */
    ich.addTemplate('document_display_widget', '<div class="document-display"></div>');
    
    methods = {
	init : function(options) {
	    return this.each(function() {
		var $this = $(this);
		var settings = $.extend({}, options);

		return $this.each(function() {
		    var $this = $(this);
		    $this.data('document-display', {});
		    $this.append(ich.document_display_widget());
		});
	    });
	},
	destroy : function() {
	    return this.each(function() {
		return this.each(function() {
		    var $this = $(this);
		    $this.removeData('document-display');
		});
	    });
	},
	/**
	 * <div class="p">

	 * Called to render the document after a widget has been
	 * initialized. There are three ways in which this method may be
	 * invoked:
	 * <ul>
	 *   <li>With no arguments, the method requests a
	 *   <code>/documentation</code> item using the path encoded in: 1)
	 *   <code>data-document-path</code> attribute attached to the
	 *   element, 2) <code>document-path</code> field associated to the
	 *   widget data or 3) the path portion of the request URL.</li>
	 *   <li>A string argument is treated as a path specifying the
	 *   document path, which is then requested from the
	 *   <code>/documentation</code> resource.</li>
	 *   <li>An object argument is immediately procsesed to retrieve and
	 *   display the embedded HTML fragment.</li>
	 * </ul>
	 * </div>
	 */
	render_document : function(path_or_data) {
	    return this.each(function() {
		var $this = $(this);
		var data = $this.data('document-display');

		if (path_or_index_data == null) {
		    // then we determine the path and then retrieve the
		    // document
		    var path = data['folder path'] || $this.data('folder-path') || location.pathname;
		    $this.document_index('render_index', path)
		}
		else if (typeof path_or_index_data === 'string') {
		    // then we request document as indicated by the path
		    var path = path_or_index_data;
		    $.getJSON(path,
			      function(result) {
				  $this.loading_spinner('destroy').document_display('render_index', result);
			      }).
			error(function() {
			    $this.loading_spinner('destroy').find('.project-summary').html('Error processing groups request.');
			});
		}
		else {
		    var index_data = path_or_index_data;
		    var $canvas = $this.find('document-index');
		    /**
		     * Here's what we do: list immediate files directly, then
		     * do a depth first search of the folders; first level
		     * generates a full subsection, blurbSummary with
		     * blurbTitle. After that, it's subheaders and no further
		     * nesting.
		     */
		    var display_files = function(index_data, $canvas) {
			$file_container = ich.document_index_widget_file_container();
			$canvas.append($file_container);
			for (var j = 0; j < index_data.files.length; j += 0) {
			    var file = index_data.files[j];
			    ich.document_index_widget_file({link: 'foo', title: file});
			}
		    };
		    var display_subsection = function() {
		    };

		    display_files(index_data, $canvas);
		    for (var i = 0; i < index_data.folders.length; i += 1)
			display_subsection(index_data.folders[i], $canvas);
		}
	    });
	}
    };

    $.fn.document_display = function(method) {
	if (methods[method])
	    return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	else if ( typeof method === 'object' || ! method )
	    return methods.init.apply( this, arguments );
	else $.error( 'Method ' +  method + ' does not exist on jQuery.document_index');
    };

    $(document).ready(function() {
	$('.document-index-widget').document_index().document_index('render_index');
    });
})(jQuery);
