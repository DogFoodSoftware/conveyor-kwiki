<?php
/**
<div class="p">
  Retrieves a single wiki page. The referenced page is mapped to a file or
  directory ('file set') and echoed as is with no further processing.
</div>
*/
?>
<?php
$doc_rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
// extract the project
$project = preg_replace('/^\/documentation\/([^\/]+).*/', '$1', $doc_rest_id);
// common config elements
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
$isaTrail = array('<a href="/projects/">projects</a>');
// TODO: build up further isas from path
$base_dir = '/home/user/playground';

// TODO: SECURITY we're allowing the user to pull up files; need to make sure it's limited to the kdata directory
// we will try and retrieve the specific file
// extract the file path
$file_path = preg_replace("/^\/documentation\/$project\/?/", '', $doc_rest_id);
$page_title = preg_replace('/_/', ' ', $file_path);
// now put it all together
$abs_document_path = "$base_dir/$project/kdata/documentation/$file_path";
/**
   Open with the standard Dog Food Software page header and opening. This will be
   parameterized at some point before the release of 1.0.
*/
$pageTitle = "Dog Food Software || $project/$page_title";
$headerTitle = "$project".(strlen($page_title) > 0 ? "/$page_title" : '');
$minifyBundle = 'kibblesCore';
/**
   A Kwiki document can be either an HTML snippet or a directory containing an
   HTML snippet of the same name and zero or more document resources. A
   document resource is any image, audio file, video, etc. which is
   essentially part of the document. This is oppossed to a file referenced or
   included by the document but which has (conceptual) indepndent existence
   outside th. ducemnt. Functionally, it really doesn't matter. The
   organization of files as part of a document set is a matter of practical
   design.
*/
if (is_dir($abs_document_path) && strlen($file_path) > 0 && 
    file_exists("$abs_document_path/".basename($file_path))) { // it's a document set
    $snippet = $abs_document_path.'/'.basename($abs_document_path);
    $contents = file_get_contents($snippet);
}
else if (is_dir($abs_document_path)) { // it's an index request
    $folder_path = $project.(strlen($file_path) > 0 ? "/$file_path" : '');
    $contents = '<div class="document-index-widget" data-folder-path="'.$folder_path.'"></div>';
}
else if (file_exists($abs_document_path)) { // it's a file, but what kind?
    // if it's starts with '<?php', treat it as a script
    $contents = file_get_contents($abs_document_path);
    if (preg_match('/^<\?php/', $contents)) {
	require $abs_document_path;
	return; // and then exit
    }
    // else fall through to default handling of contents
}
else // it's a link to a file which doesn't yet exist
    $contents = '<div style="text-align: center">PAGE NOT YET CREATED.</div>';
/**
   Note, there is no need to handle other document resources (as found in
   a document directory) because these are caught by Apache and routed
   directly without involving PHP.
*/

require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
echo $contents;
require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
?>
