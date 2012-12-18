<?php 
/**
<div class="p">
  Script to process <code>GET</code> requests for a single documentation
  item. Documentation items are generally of two types: content pages and
  doc-ified source code artifacts.
</div>
<div class="blurbSummary grid_12">
<div class="p">
  Pages recongized as source code&mdash;by location and extension&mdash;are
  formatted and optimized for HTML presentation. 

Wiki pages &mdash;under the
  <code>&lt;project&gt;/kdata/documentation</code> directory are encoded as
  HTML fragments and returned as is.
</div>
<div class="p">
  Note the use of 'page' and 'folder' rather than 'file' and 'directory'. Page
  and folder refer to the documentation items, as viewed from the perspective
  of the web service API. Files and directories are how pages and folders are
  stored. A page in particular may be stored as either a single file, or a
  file set within a directory.<span class="note">This is used when the page
  embeds images or other elements not encoded directly into the HTML which are
  particular to the page itself. E.g., a diagram image.</span>
</div>
<div class="p">
  See <code><a
  href="/kwiki/documentation/src/code-to-html.php">code-to-html.php</a></code>
  and <code><a
  href="/kwiki/documenqtation/src/wiki-page-processor.php">wiki-page-processor.php</a></code>
  for details on the processing of each particular type.
</div>
 */
$rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
/**
   Decompose the REST ID to map to a file.
 */
$project = preg_replace('/^\/documentation\/([^\/]+).*/', '$1', $rest_id);
$file = basename($rest_id); // used as the page title
$base_dir = '/home/user/playground';
// TODO: SECURITY we're allowing the user to pull up files; need to make sure it's limited to the kdata directory
// we will try and retrieve the specific file
// extract the file path
$file_path = preg_replace("/^\/documentation\/$project\/?/", '', $rest_id);
$page_title = preg_replace('/_/', ' ', $file_path);
/**
   <div class="p">
   Setup the page template variables common to all documentation item types.
   </div>
*/
$pageTitle = 'Dog Food Software || '.$file;
$headerTitle = $file;
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
/**
 <span data-todo="process the documentatino $rest_id to determine more
 complete isa trail">The isa trail is currently incomplete, just points back
 to projects.</span>
*/
$isaTrail = array('<a href="/projects/">projects</a>');

function no_page_result() {
    global $pageTitle, $headerTitle, $isaTrail, $minifyBundle, $html;
    $minifyBundle = 'kibblesCore';
    if ($html) require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
    echo '<div style="text-align: center">NO SUCH PAGE.</div>';
    if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
}

/**
   <div class="p">
   There are three different formats we potentially support: HTML, partial
   HTML, and JSON. We go ahead and figure out what's what here, respond with
   406 if none. <span data-todo="Define and implement">The current accept
   header processing is pretty half-assed. Long term, the processing will be
   abstracted and worked into the request processors.</span>
   </div>
 */
$requested_format = $_SERVER['HTTP_ACCEPT'];
$html_fragment = false;
$html = false;
$json = false;
if (preg_match('|text/html;type=ajax|', $requested_format)) $html_fragment = true;
else if (preg_match('|text/html|', $requested_format)) $html = true;
else if (preg_match('|application/json|', $requested_format)) $json = true;
else if (preg_match(':(\*|text)/(\*|html):', $requested_format) ||
	 $requested_format == null || strlen($requested_format) == 0)
    $html = true;
else {
    header("HTTP/1.0 406 Cannot satisfy requested response format.");
    return true;
}
/**
<div class="p">
  Now we're ready to determine what kind of request we're dealing with and
  spit out a response. There are a total of five different cases. We end up
  with a couple more conditional statements because the file mapping varies in
  some cases so we cannot do the 'exists' test until we know the sub case, and
  the exists test gets duplicated. We also end up duplicating the template
  output (<code>page_open.php</code> and <code>page_close.php</code>) in order
  to avoid reading the contents to a variable, preferring to duplicate the
  code and echo outputs directly to the results buffer.
</div>
 */
if (preg_match('/(.php|.js|.sh)$/', $rest_id)) { // it's a code page
    if ($json) {
	header("HTTP/1.0 406 Cannot satisfy requested response format.");
	return true;
    }
    $abs_file_path = "$base_dir/$project/$file_path";
    if (file_exists($abs_file_path)) {
	require('/home/user/playground/kwiki/runnable/include/kwiki-lib.php');
	$minifyBundle = 'fileDoc';
	if ($html)
	    require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	if ($html) require '/home/user/playground/kwiki/runnable/include/code-to-html.php';
	code_to_html($abs_file_path);
	if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
    }
    else no_page_result();
}
else { // it's a 'standard wiki page' or index
    // this is a little wonky; for pages, it's always the $abs_document_path,
    // but for indexes, it could be either
    $abs_document_path = "$base_dir/$project/kdata/documentation/$file_path";
    $abs_dir_path = "$base_dir/$project/$file_path";
    $minifyBundle = 'kibblesCore';
    /**
       <div class="p">
         Four possibilities remain. The mapped request points directly to a
         file or file set, which indicates a page request, or the mapped
         request points to a non-file set directory, indicating a folder index
         request. In both cases, the mapped request points to something. If
         the mapped requset cannot be resolved to a valid file or directory,
         then we output the 'no such page' result. <span data-todo="refactor
         the logic">The requested formats are, specifically the JSON test, is
         repeated within the block to deal with each type. In 3 cases, the
         response is the same (HTTP 406). This should be fixed; probably best
         to deal with it after the accept header support has been improved at
         the high level first.</span>
       </div>
     */
    if (!file_exists($abs_document_path) && !is_dir($abs_dir_path)) {
	if ($json) {
	    header("HTTP/1.0 406 Cannot satisfy requested response format.");
	    return true;
	}
	no_page_result();
    }
    else if (is_file($abs_document_path)) {
	if ($json) {
	    header("HTTP/1.0 406 Cannot satisfy requested response format.");
	    return true;
	}
	// TODO: small effeciency gain: read first 6 characters of file only, then output file_get_contents() in non-script case
	// if it's starts with '<?php', treat it as a script
	$contents = file_get_contents($abs_document_path);
	if (preg_match('/^<\?php/', $contents))
	    require $abs_document_path;
	else { // echo contents of file
	    if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	    echo $contents;
	    if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
	}
    }
    else if (is_dir($abs_document_path) && strlen($file_path) > 0 && 
	     file_exists("$abs_document_path/".basename($file_path))) { // it's a document set
	if ($json) {
	    header("HTTP/1.0 406 Cannot satisfy requested response format.");
	    return true;
	}
	$snippet = $abs_document_path.'/'.basename($abs_document_path);
	if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	echo file_get_contents($snippet);
	if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
    }
    else { // must be an index request
	if ($json) {
	    require '/home/user/playground/kwiki/runnable/include/folder-index-lib.php';
	    $results = index_folder(preg_replace('/_/', ' ', $file), 
				    is_dir($abs_document_path) ? $abs_document_path : $abs_dir_path);
	    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	    header('Content-type: application/json');    
	    // $file is the 'folder name'
	    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}
	else {
	    $minifyBundle = 'documentationIndex';
	    if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	    $folder_path = $project.(strlen($file_path) > 0 ? "/$file_path" : '');
	    echo '<div class="loading-spinner-widget document-index-widget" data-folder-path="'.$folder_path.'"></div>';
	    if ($html) require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
	}
    }
}
?>
