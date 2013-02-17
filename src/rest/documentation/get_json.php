<?php
/**
 * Retrieves all currently active projects and returns a JSON representation.
 */
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
// TODO: this code is essentially the same as with get_html.php
$baseDir = '/home/user/playground';
$requestUri = $_SERVER['REQUEST_URI'];
$pathInfo = preg_replace('/\?.*/', '', $requestUri);
$project = preg_replace('/^\/documentation\/([^\/?]+).*/', '$1', $pathInfo);

if ($pathInfo == "/documentation/$project") {
    $searchPath = "$baseDir/$project/kdata/kwiki";
    if (is_dir($searchPath)) {
	$dh = @opendir($searchPath);
	$results = array();
	while (false !== ($file = readdir($dh))) {
	    if (is_file("$searchPath/$file") && !preg_match('/^#|~$/', $file))
		array_push($results, $file);
	}
    }
}

sort($results);
$firstEcho = true;
echo "{ \"entries\": [";
foreach ($results as $file) {
    if (!$firstEcho) echo ",";
    echo "\"$file\"";
    $firstEcho = false;
}
echo "]}";
?>
