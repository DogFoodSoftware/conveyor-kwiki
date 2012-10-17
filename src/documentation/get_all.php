<?php
/**
<div class="p">
  We want to return a list of all documentation known to the system. At the
  moment, we only handle JSON requests.
</div>
*/
$requested_format = $_SERVER['HTTP_ACCEPT'];
if (preg_match('/application\/json/', $requested_format)) { // good to go
    $urls = array(); // where we collect answer
    $frontier = array();
    array_push($frontier, '/home/user/playground');

    while (count($frontier) > 0) {
	$dir_to_explore = array_shift($frontier);
	$dh = opendir($dir_to_explore);
	// we start out processing the projects, after the first dir, we're in
	// the projects and our frontier filter logic changes
	$in_projects = false;
	while (false !== ($file = readdir($dh))) {
	    if (is_dir("$dir_to_explore/$file")) {// add dirs to frontier
		if ($file != '.' && $file != '..' && // skip special directories
		    $file != '.git' && // skip git repos
		    ($in_projects || 
		     ($file != 'data' && // skip data dirs
		      $file != 'build' && // skip build dirs
		      $file != 'runnable'))) // and runnable
		    array_unshift($frontier,"$dir_to_explore/$file");
	    }
	    else {// add URI path that corresponds to file to result
		// first, strip the base
		$file_path = substr("$dir_to_explore/$file", strlen('/home/user/playground'));
		$project = preg_replace('/^\/([^\/]+).*/', '$1', $file_path); // take first segment as project
		$file_path = preg_replace('/^\/[^\/]+\//', '', $file_path); // remove first segment from file_path
		// can we process the file?
		/**
		   <todo>This section has to coordinate with the processing logic. Share the logic.</todo>
		*/
		if (!(preg_match('/~$/', $file_path) || preg_match('/^#/', $file_path))) { // not emacs save file
		    if (preg_match('|kdata/documentation/[^\.]+$|', $file_path)) {
			$web_path = preg_replace('|kdata/documentation/|', '', $file_path);
			$web_path = "/documentation/$project/$web_path";
			array_push($urls, $web_path);
		    }
		    else if (preg_match('/(\.php|\.js)$/', $file_path))
			array_push($urls, "/documentation/$project/$file_path");
		}
		// otherwise it's a document we can't yet process, so it gets dropped
		/**
		   <todo>Support filter for 'undocumented non-build, non-data files'.</todo>
		 */
	    }
	}

	// after the first iteration, we are in projects
	$in_projects = true;
    }

    echo json_encode($urls, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
else header("HTTP/1.0 406 Cannot satisfy requested response format.");
?>
