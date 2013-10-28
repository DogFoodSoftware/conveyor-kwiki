<?php /**
<div class="p">
  Script to process <code>GET</code> requests for a single documentation
  item. Documentation items are generally of two types: content pages and
  doc-ified source code artifacts. Unlike most services, this service embeds
  the documentation data directly in the HTML result (rather than relying on a
  widget to retrieve the information). This is necessary to expose
  documentation to search engines.
</div>
<div class="p">
  Pages recongized as source code&mdash;by location and extension&mdash;are <a
  href="/documentation/kwiki/src/include/code-to-html.php">formatted for presentation
  as HTML pages</a>. Wiki pages&mdash;under the
  <code>&lt;project&gt;/kdata/documentation</code> directories&mdash;are
  processed locally. These pages are analyzed and recognized as HTML fragments
  and encoded as is or recognized as PHP scripts and evaluated.
</div>
<div id="Implementation" data-perspective="coding" class="blurbSummary grid_12">
<div class="blurbTitle">Implementation</div>
 */
require('/home/user/playground/kibbles/runnable/lib/accept-processing-lib.php');
// it stops here with a 406 if the client ain't buying what we're selling
process_accept_header();

$rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
// Kwiki's get is used by other resources.
if (empty($document_url_path))
   $document_url_path = 'documentation';
/**
 * Decompose the REST ID to map to a file.
 */
$project = preg_replace("/^\/$document_url_path\/([^\/]+).*/", '$1', $rest_id);
$file = basename($rest_id); // used as the page title
$file_title = preg_replace('/_/', ' ', $file);
$base_dir = '/home/user/playground';
$file_path = preg_replace("/^\/$document_url_path\/$project\/?/", '', $rest_id);
$page_title = preg_replace('/_/', ' ', $file_path);

$content = null;
$no_page_content = '<div style="text-align: center">NO SUCH PAGE.</div>';

$code_path = "$base_dir/$project/$file_path";
$doc_path = "$base_dir/$project/kdata/$document_url_path/$file_path";
if (file_exists($code_path) ||
    file_exists($doc_path) && preg_match('/\.[a-zA-Z]+$/', $doc_path)) { // it's a code page
    require('/home/user/playground/kwiki/runnable/include/kwiki-lib.php');
    require '/home/user/playground/kwiki/runnable/include/code-to-html.php';
    ob_start();
    code_to_html($code_path);
    $contents = ob_get_clean();
}
else if (file_exists($doc_path)) { // it's a 'standard wiki page'
    // TODO: small effeciency gain: read first 6 characters of file only, then output file_get_contents() in non-script case
    if (is_dir($doc_path)) $doc_path = "$doc_path/".basename($doc_path);
    $contents = file_get_contents($doc_path);
    // if it's starts with '<?php', treat it as a script and be done
    if (preg_match('/^<\?php/', $contents)) {
	ob_start();
	require $doc_path;
	$contents = ob_get_clean();
    }
    // else, the $contents are just the file, which is already read
}
else {
    // remember, this script is a little idiomatic, we set up a proper
    // response for HTML with $contents and JSON with the message update
    if (respond_in_html()) {
	require_once('/home/user/playground/kibbles/runnable/lib/interface-response-lib.php');
	$pageTitle = $file_title;
	$headerTitle = $file_title;
	final_result_bad_request($no_page_content);
    }
    else {
	require_once('/home/user/playground/kibbles/runnable/lib/data-response-lib.php');
	add_global_message("No such document: $rest_id.");
    }
}
if (respond_in_html()) {
    global $pageTitle;
    require_once('/home/user/playground/kibbles/runnable/lib/interface-response-lib.php');
    $pageTitle = $file_title;
    $headerTitle = $file_title;
    // We expect $minifyBundle determined in echo_interface().
    $contents = '<div class="prefix_4 grid_4 suffix_4 perspective-manager-widget loading-spinner-widget" data-perspective-manager=\'{"style":"dropdown"}\'></div><div class="clear"></div>'.
	$contents;
    echo_interface ($contents);
}
else {
    require_once('/home/user/playground/kibbles/runnable/lib/data-response-lib.php');
    handle_errors();
    final_result_ok("Document retrieved.", $content);
}
?>
<?php /**
</div><!-- .blurbSummary#Implementation -->
*/ ?>
