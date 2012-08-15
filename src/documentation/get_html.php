<?php
/**
<div class="p">

</div>
*/
?>
<?php
$requestUri = $_SERVER['REQUEST_URI'];

// extract the project
$project = preg_replace('/^\/documentation\/([^\/]+).*/', '$1', $requestUri);
// extract the file path
$filePath = preg_replace("/^\/documentation\/$project\//", '', $requestUri);
$baseDir = '/home/user/playground';
// now put it all together
$absFilePath = "$baseDir/$project/kdata/kwiki/$filePath";

/**
Open with the standard Dog Food Software page header and opening. This will be
parameterized at some point before the release of 1.0.
*/
$pageTitle = "Dog Food Software || $project/$filePath";
$headerTitle = "$project/$filePath";
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
$minifyBundle = 'kibblesCore';
$isaTrail = array('<a href="/projects/">projects</a>', "<a href=\"/projects/$project\">$project</a>");
require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');

echo file_get_contents($absFilePath);

require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
?>
