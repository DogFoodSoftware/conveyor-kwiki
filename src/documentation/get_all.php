<?php
/**
<div class="p">
  We want to return a list of all documentation known to the system. At the
  moment, we only handle JSON requests.
</div>
*/
$requested_format = $_SERVER['HTTP_ACCEPT'];
if (preg_match('/application\/json/', $requested_format)) { // good to go
    $files = array(); // where we collect answer
    $frontier = array();
    array_push($frontier, '/home/user/playground');

    while (count($frontier) > 0) {
	$dir_to_explore = array_shift($frontier);
	$dh = opendir($dir_to_explore);
	while (false !== ($file = readdir($dh))) {
	    if (is_dir("$dir_to_explore/$file")) {// add dirs to frontier
		if ($file != '.' && $file != '..' && // skip special directories
		    $file != '.git' && // skip git repos
		    $file != 'data') // skip data dirs
		    array_unshift($frontier,"$dir_to_explore/$file");
	    }
	    else {// add URI path that corresponds to file to result
		// first, strip the base
		$file_path = substr("$dir_to_explore/$file", strlen('/home/user/playground'));
		$project = preg_replace('/^\/([^\/]+).*/', '$1', $file_path);
		$file_path = preg_replace('/^\/[^\/]+/', '', $file_path);
		// There are three kinds of files we need to deal with:
		// 1) Code files we know how to process:
		if (preg_match('/(\.php|\.js)$/', $file_path))
		    array_push($files, "/documentation/$project/$file_path");
		// 2) Wiki pages
		else if (preg_match('/\/[^\/\.]+$/', $file_path))
		     array_push($files, "/documentation/$project/$file_path");
		// otherwise it's a document we can't yet process, so it gets dropped
		/**
		   <todo>We should log a warning for dropped files.</todo>
		 */
	    }
	}
    }

    /**
       <todo>Use the JSON_PRETTY_PRINT and JSON_UNESCAPED_SLASHES once we upgrade to PHP 5.4+.</tode>
     */
    echo json_encode($files); //, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
else header("HTTP/1.0 406 Cannot satisfy requested response format.");
?>
