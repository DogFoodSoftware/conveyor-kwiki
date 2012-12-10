<?php 
/**
<div class="p">
  Script to process <code>GET</code> requests for a single documentation
  item. We may distinghuish two primary types of documentation items: pages
  and folders. A request for a file results in the file contents. A request
  for a folder results in an index of the files and sub-folders within the
  requested folder. The current implementation supports JSON indexs and HTML
  files (embedded within the selected site template).
</div>
<div class="p">
  Pages recongized as source code&mdash;by location and extension&mdash;are
  formatted and optimized for HTML presentation. Wiki pages &mdash;under the
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
    require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
    echo '<div style="text-align: center">PAGE .</div>';
    require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
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
    $abs_file_path = "$base_dir/$project/$file_path";
    if (file_exists($abs_file_path)) {
	require('/home/user/playground/kwiki/runnable/include/kwiki-lib.php');
	$minifyBundle = 'fileDoc';
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	require '/home/user/playground/kwiki/runnable/include/code-to-html.php';
	code_to_html($abs_file_path);
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
    }
    else no_page_result();
}
else { // it's a 'standard wiki page' or index
    $abs_document_path = "$base_dir/$project/kdata/documentation/$file_path";
    $minifyBundle = 'kibblesCore';
    /**
       <div class="p">
         Four possibilities remain. The mapped request points directly to a
         file or file set, which indicates a page request, or the mapped
         request points to a non-file set directory, indicating a folder index
         request. In both cases, the mapped request points to something. If
         the mapped requset cannot be resolved to a valid file or directory,
         then we output the 'no such page' result.
       </div>
     */
    if (!file_exists($abs_document_path)) no_page_result();
    else if (is_file($abs_document_path)) {
	// TODO: small effeciency gain: read first 6 characters of file only, then output file_get_contents() in non-script case
	// if it's starts with '<?php', treat it as a script
	$contents = file_get_contents($abs_document_path);
	if (preg_match('/^<\?php/', $contents))
	    require $abs_document_path;
	else { // echo contents of file
	    require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	    echo $contents;
	    require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
	}
    }
    else if (is_dir($abs_document_path) && strlen($file_path) > 0 && 
	     file_exists("$abs_document_path/".basename($file_path))) { // it's a document set
	$snippet = $abs_document_path.'/'.basename($abs_document_path);
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	echo file_get_contents($snippet);
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
    }
    else { // must be an index request
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
	$folder_path = $project.(strlen($file_path) > 0 ? "/$file_path" : '');
	echo '<div class="document-index-widget" data-folder-path="'.$folder_path.'"></div>';
	require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';	
    }
}
?>
