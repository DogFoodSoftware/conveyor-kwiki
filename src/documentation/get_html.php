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
// common config elements
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
$isaTrail = array('<a href="/projects/">projects</a>', "<a href=\"/projects/$project\">$project</a>");
$baseDir = '/home/user/playground';

if ($requestUri == "/documentation/$project") {
    // then we want to index the project concepts
    $pageTitle = "Dog Food Software || $project documentation ";
    $headerTitle = "$project documentation";
    $minifyBundle = 'documentationIndex';

    $projectPath = "$baseDir/$project/kdata/kwiki";
    $contents =<<<EOT
<div class="grid_12 blurbSummary">
  <div class="blurbTitle">Index</div>
  <div class="documentationIndex" data-project="$project"></div>
</div>
EOT;
}
else {
    // we will try and retrieve the specific file
    // extract the file path
    $filePath = preg_replace("/^\/documentation\/$project\//", '', $requestUri);
    // now put it all together
    $absFilePath = "$baseDir/$project/kdata/kwiki/$filePath";
    /**
       Open with the standard Dog Food Software page header and opening. This will be
       parameterized at some point before the release of 1.0.
    */
    $pageTitle = "Dog Food Software || $project/$filePath";
    $headerTitle = "$project/$filePath";
    $minifyBundle = 'kibblesCore';
    
    $contents = file_get_contents($absFilePath);
}
require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
echo $contents;
require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
?>
