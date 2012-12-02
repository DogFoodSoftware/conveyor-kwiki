<?php
/**
<div class="p">
Retrieves a single document.
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
$isaTrail = array('<a href="/projects/">projects</a>', "<a href=\"/documentation/$project\">$project</a>");
$baseDir = '/home/user/playground';

// TODO: SECURITY we're allowing the user to pull up files; need to make sure it's limited to the kdata directory

// TODO: are we stepping on the 'get_all' convention? Pick a way and go with it or document why not
if ($doc_rest_id == "/documentation/$project") {
    $documentationGenerator = "/home/user/playground/$project/kdata/documentation/index";
    if (file_exists($documentationGenerator))
	require $documentationGenerator;
}
else {
    // we will try and retrieve the specific file
    // extract the file path
    $filePath = preg_replace("/^\/documentation\/$project\//", '', $doc_rest_id);
    $page_title = preg_replace('/_/', ' ', $filePath);
    // now put it all together
    $absDocumentPath = "$baseDir/$project/kdata/documentation/$filePath";
    /**
       Open with the standard Dog Food Software page header and opening. This will be
       parameterized at some point before the release of 1.0.
    */
    $pageTitle = "Dog Food Software || $project/$page_title";
    $headerTitle = "$project/$page_title";
    $minifyBundle = 'kibblesCore';
    /**
       A Kwiki document can be either an HTML snippet or a directory
       containing an HTML snippet of the same name and zero or more document
       resources. A document resource is any image, audio file, video,
       etc. which is essentially part of the document. This is oppossed to a
       file referenced or included by the document but which has (conceptual)
       indepndent existence outside th. ducemnt. Functionally, it really
       doesn't matter. The organization of files as part of a document set is
       a matter of practical design.
     */
    if (is_dir($absDocumentPath)) { // it's a document set
	$snippet = $absDocumentPath.'/'.basename($absDocumentPath);
	$contents = file_get_contents($snippet);
    }
    else if (file_exists($absDocumentPath)) // it's a file and therefore a simple snippet
	$contents = file_get_contents($absDocumentPath);
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
}
?>
