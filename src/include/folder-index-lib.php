<?php
function index_folder($results, $directory_path) {
    function load_contents($results, $directory_path) {
	$files = array();
	$dirs = array();
	$dh = opendir($directory_path);
	while (false !== ($file = readdir($dh))) {
	    if (!preg_match('/^\./', $file) && !preg_match('/~$/', $file)) {
		// is a file, but is not a file set
		if (is_dir("$directory_path/$file") && !is_file("$directory_path/$file/$file")) {
		    $dir_path = "$directory_path/$file";
		    if (!preg_match(":/playground/[a-zA-Z_-]+/(runnable|data)(\$|/):", $directory_path)) {
			$subresults = array('folder title' => preg_replace('/_/', ' ', $file),
					    'folders' => array(),
					    'files' => array());
			$subresults = load_contents($subresults, "$directory_path/$file");
			$results['folders'][] = $subresults;
		    }
		}
		else {
		    // echo $file;
		    $results['files'][] = $file;
		}
	    }
	}
	sort($results['folders']);
	sort($results['files']);

	error_log('foo');
	return $results;
    }
    
    $results = array('folder title' => preg_replace('/_/', ' ', basename($directory_path)),
		     'folders' => array(),
		     'files' => array());
    return load_contents($results, $directory_path);
}
?>
