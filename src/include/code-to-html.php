<?php
define('KWIKI_SHOW_SOURCE', 'show source');
define('KWIKI_HIDE_SOURCE', 'hide source');
// TODO: in future, support 'collapse source' as well; perhaps turn into bit mask

function code_to_html($doc_rest_id, $show_source=KWIKI_SHOW_SOURCE) {
    // want file path, start with rest ID
    $file_path = $doc_rest_id;
    $file_path = preg_replace('/^\/documentation\//', '', $file_path);
    $baseDir = '/home/user/playground';
    $file_path = $baseDir . '/' . $file_path;

    echo '<div class="grid_12">'."\n";
    /**
       <div class="p">
       It's now time to process the file itself. The basic idea is to iterate over
       the lines, looking for the documentation opening and closing
       markers. Documentation is extracted the rest is treated as code and placed
       into 'prettyprint' classes to be processed by
       <a href="https://code.google.com/p/google-code-prettify/">Google's pretty print JS</a>.
       </div>
       <div class="p">
       There are basically two states: 'inDoc' and 'not inDoc', aka: in code. In
       order to keep the line numbers in the code blocks correct, we count the lines
       as we process them.
       </div>
    */
    $minExpandSize = 5;
    $inDoc = false; // track state
    $doc_style = 'unknown'; // can be 'unknown', 'plain', or 'html'
    $i = 0; // count lines
    $codeCount = 0;
    $currCodeId = null;
    $contents = file_get_contents($file_path);
    $lines = preg_split('/((\r?\n)|(\r\n?))/', $contents);
    /**
       <div class="p">
       The code blocks are analyzed for length and those longer that
       <code>$minExpandSize</code> are annotated with an expandable control allowing
       viewers to expand the code block size. The <code>codeClose()</code> function
       makes the determination.
       </div>
    */
    function codeClose($codeCount, $currCodeId, $minExpandSize) {
	echo '</pre><div class="ui-resizable-handle ui-resizable-s resizeControl"></div></div>'."\n";
	// if the $codeCount is greater than 6, then apply the 'long' modifier,
	// which sets the initial height
	if ($codeCount > $minExpandSize)
	    echo "<script>$('#".$currCodeId."').addClass('long');</script>";
    }
    foreach ($lines as $line) {
	// first, process the state changes
	if (preg_match('=^\s*(<\?php\s+)?#?/\*\*\s*$=', $line)) {
	    $inDoc = true;
	    $doc_style = 'unknown';
	    if ($i > 0 && $show_source == KWIKI_SHOW_SOURCE) {
		codeClose($codeCount, $currCodeId, $minExpandSize);
		$codeCount = 0;
	    }
	}
	else if ($inDoc && preg_match('=^#?\s*\*{1,}/(\s+\?>)?($|.+)=', $line)) { // TODO: this will swallow up anything else on the line
	    // we only want to open a code block if there's any code left... in
	    // other words, check for last line or last line blank (this isn't
	    // foolproof but allows us to work around the issue for now)
	    if ($i + 1 < count($lines) && ($i + 2 < count($lines) || strlen(trim($lines[$i + 1])) > 0) &&
		$show_source == KWIKI_SHOW_SOURCE) {
		$currCodeId = 'codeBlock'.$i;
		echo '<div class="prettyprintBox"><pre id="'.$currCodeId.'" class="prettyprint linenums:'.($i + 2).'">'."\n";
	    }
	    $inDoc = false;
	    $codeCount = -1; // start at -1 because we don't want to count this line, but '$codeCount' will be incremented
	}
	else if ($i == 0 && // if we don't start with the special <?php /**, then the first line is treated as code
		 $show_source == KWIKI_SHOW_SOURCE) { 
	    $currCodeId = 'codeBlock'.$i;
	    echo '<div class="prettyprintBox"><pre id="'.$currCodeId.'" class="prettyprint linenums:'.($i + 1).'">'."\n";
	    $codeCount = -1; // start at -1 because we don't want to count this line, but '$codeCount' will be incremented
	    echo htmlspecialchars($line)."\n";
	}
	// Otherwise, process the line according to the state. In doc-mode, remove
	// leading stars and spaces to make compatibla with Java-style docs set-off.
	else if ($inDoc) {
	    /**
	     * <div class="p">
	     * We try to guess whether the documentatino is embedded HTML or
	     * 'plain' text. If HTML, we expect proper escaping. If Plain, we do
	     * the escraping.
	     * </div>
	     */
	    if ($doc_style == 'unknown' && preg_match('/\s*\*?\s*</', $line))
		$doc_style = 'html';
	    else if ($doc_style == 'unknown' && preg_match('/\s*\*?\s*[^<]/', $line)) {
		$doc_style = 'plain';
	    }
	    
	    // strip off leading '*' to make compatible with JavaDoc style
	    $real_line = preg_replace('/^#?\s*\*?\s*/', '', $line);
	    
	    if ($doc_style == 'plain')
		echo htmlspecialchars($real_line)."\n";
	    else echo $real_line."\n";
	}
	else {// in code
	    if ($show_source == KWIKI_SHOW_SOURCE)
		echo htmlspecialchars($line)."\n";
	}
	$i += 1;
	if (!$inDoc) $codeCount += 1;
    }
    /**
       <div class="p">
       Finally, we check whether we need to close the final code block and spit out
       the page closing stuff.
       </div>
    */
    if (!$inDoc && $show_source == KWIKI_SHOW_SOURCE) {
	codeClose($codeCount, $currCodeId, $minExpandSize);
	$codeCount = 0;
    }
    echo '
</div><!-- .blurbSummary .grid_12 -->
';
  }
?>