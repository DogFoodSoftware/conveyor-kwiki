/**
 * <div class="p">
 * By default, the widget will <span data-todo="define and link to basic
 * request protocol">attempt to request</span>
 * <code>GET:JSON:/documentation/[folder path]</code> and generate a rendering of
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
 * If index data is provided directly to the widget, the default behavior will
 * be suppressed.<span class="note">This does not in-and-of-itself supress the
 * <a
 * href="/documentation/kibbles/ref/Widget_Reference#default-element-binding-and-auto-loading">default
 * binding and auto-loading</a> as the two processes are essentially
 * unrelated; the auto-loading originates from the inclusion of the file
 * itself and is unaware of 'manual' usages of the widget.</span>
 * </div>
 * <div class="subHeader">Results Processing</div>
 * <div class="p">
 * In the normal flow, the widget will receive a JSON index of the contents of
 * the folder which will be rendered according to the widget settings. <span
 * data-todo="define and link">Standard error processing rules apply.</span>
 * If the documentation path does not point to a directory, the widget
 * <em>must</em> add style class <code>error-loading-widget</code> to all
 * target elements and invoke the error loading widget to process the elements
 * as well as send an appropriate global error messasge to the site messsages
 * widget.
 * </div>
 */
(function($) {
    /**
     * Basic <a
     * href="/documentation/kibbles/Widget_Reference#canvas-container"> canvas
     * container</a>.
     */
    ich.addTemplate('document_index_widget', '<div class="document-index"></div>');
    ich.addTemplate('document_index_widget_file_container',
		    '<div class="blurbSummary"><div class="clear"></div><div class="blurbTitle">{{{folder}}}</div><div class="chase-layout medium-slug"></div></div>');
    ich.addTemplate('document_index_widget_file',
		    '<div class="slug"><a href="{{link}}">{{title}}</a></div><div class="slug-spacer"></div>');
    
    methods = {
	init : function(options) {
	    return this.each(function() {
		var $this = $(this);
		var settings = $.extend({}, options);

		return $this.each(function() {
		    var $this = $(this);
		    $this.data('document-index', {});
		    $this.append(ich.document_index_widget());
		});
	    });
	},
	destroy : function() {
	    return this.each(function() {
		return this.each(function() {
		    var $this = $(this);
		    $this.removeData('document-index');
		});
	    });
	},
	render_index : function(path_or_index_data) {
	    return this.each(function() {
		var $this = $(this);
		var data = $this.data('document-index');

		if (path_or_index_data == null) { // then we get the path from our own data
		    var path = data['folder path'] || $this.data('folder-path');
		    if (path == null || path.length == 0)
			$this.error_loading_widget();
		    else $this.document_index('render_index', path)
		}
		else if (typeof path_or_index_data === 'string') {
		    var path = path_or_index_data;
		    $.kibbles_data({url: '/documentation/',
				    data: 'folder_path='+path,
				    success: function(index_data) {
					$this.loading_spinner('stop', function() {
					    $this.document_index('render_index', index_data);
					});
				    },
				    error: function() {
					$this.loading_spinner('stop', function() {
					    /**
					     * <todo>Make this a template.</todo>
					     */
					    $this.find('.project-summary').html('Error processing groups request.</div>');
					});
				    }
				   });
		}
		else {
		    var index_data = path_or_index_data;
		    var $canvas = $this.find('.document-index');
		    /**
		     * Here's what we do: list immediate files directly, then
		     * do a depth first search of the folders; first level
		     * generates a full subsection, blurbSummary with
		     * blurbTitle. After that, it's subheaders and no further
		     * nesting.
		     */
		    var display_files = function(index_data, $canvas) {
			$file_container = ich.document_index_widget_file_container(index_data);
			$canvas.append($file_container);
			$chase_layout = $file_container.find('.chase-layout');
			for (var j = 0; j < index_data.files.length; j += 1)
			    $chase_layout.append(ich.document_index_widget_file(index_data.files[j]));
			for (var j = 0; j < index_data.folders.length; j += 1)
			    display_files(index_data.folders[j], $file_container);
		    };

		    display_files(index_data.data, $canvas);
		}
	    });
	}
    };

    $.fn.document_index = function(method) {
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
