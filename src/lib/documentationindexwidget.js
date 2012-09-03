/**
<div class="p">
Widget to display documentation index.
</div>
*/
      var panel = null;
      var sizeSlugs = function(i, width) {
	delete panel.conf.containerColNum;
	delete panel.conf.colPx;
	delete panel.conf.strideAdjustments;
        if (width <= 760) {
	  delete panel.conf.colPx;
          panel.bindTo($('.documentationIndex'), {containerColNum: 1, internalGutterPx: 20});
        }
        else if (width <= 980) {
          panel.bindTo($('.documentationIndex'), {containerColNum: 2, colPx: 340, internalGutterPx: 20});
        }
        else if (width <= 1280) {
          panel.bindTo($('.documentationIndex'), {containerColNum: 3, colPx: 300, internalGutterPx: 20});
        }
        else if (width <= 1600) {
          panel.bindTo($('.documentationIndex'), {containerColNum: 3, colPx: 380, internalGutterPx: 20});
        }
        else if (width <= 1920) {
          panel.bindTo($('.documentationIndex'), {containerColNum: 4, colPx: 370, internalGutterPx: 20});
        }
        else if (width <= 2540) {
          panel.bindTo($('.documentationIndex'), {containerColNum: 6, colPx: 300, internalGutterPx: 20});
        }
        else {
          panel.bindTo($('.documentationIndex'), {containerColNum: 6, colPx: 400, internalGutterPx: 20});
        }
      };

      var ADAPT_CONFIG = {
      path: '/kibbles/include/adapt/',
      dynamic: true,
      callback: function(i, width) { setGlobal(i, width); if (panel != null) sizeSlugs(i, width); },
      range: [
      '0px    to 760px  = mobile.css',
      '760px  to 980px  = 720.css',
      '980px  to 1280px = 960.css',
      '1280px to 1600px = 1200.css',
      '1600px to 1920px = 1560.css',
      '1940px to 2540px = 1920.css',
      '2540px           = 2520.css'
      ]
      };

$(document).ready(function() {
    // TODO: this trie and supports multiple indexes per page, but the
    // sizeSlugs method is assuming a single panel / container
    $('.documentationIndex').each(function(i, el) {
	var project = $(el).attr('data-project');
	// TODO: add support for project-less fulol index
	$.get('/documentation/' + project, 'format=json',
	      function(data) {
		  panel = new k.core.OmniPanel();
		  var maxColCount = Math.ceil(data.entries.length / _adaptI);
		  var currentBlock;
		  for (var i = 0; i < data.entries.length; i += 1) {
		      if ((i % maxColCount) == 0) {
			  currentBlock  = $('<ul></ul>');
			  panel.loadBlock(currentBlock, {colSpan: 1});
		      }
		      $(currentBlock).append('<li><a href="/documentation/' + project + '/' + data.entries[i] + '">' + data.entries[i] + '</a></li>');
		  };
		  sizeSlugs(_adaptI, _adaptWidth);
	      });
    });
});// end document ready function
