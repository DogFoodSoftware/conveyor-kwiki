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
$filePath = preg_replace("/^\/documentation\/$project/", '', $requestUri);
$baseDir = '/home/user/playground';
// now put it all together
$filePath = "$baseDir/$project/kdata/documentation/$filePath";

echo file_get_contents($filePath);
?>
