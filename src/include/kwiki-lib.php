<?php
function convert_file_to_url($file_path) {
    // first, strip the base
    $file_path = substr("$file_path", strlen('/home/user/playground'));
    $project = preg_replace('/^\/([^\/]+).*/', '$1', $file_path); // take first segment as project
    $file_path = preg_replace('/^\/[^\/]+\//', '', $file_path); // remove first segment from file_path
    // can we process the file?
    /**
       <todo>This section has to coordinate with the processing logic. Share the logic.</todo>
    */
    if (preg_match('|kdata/documentation/[^\.]+$|', $file_path)) {
	$web_path = preg_replace('|kdata/documentation/|', '', $file_path);
	$web_path = "/documentation/$project/$web_path";
	return $web_path;
    }
    else if (preg_match('/(\.php|\.js)$/', $file_path))
	return "/documentation/$project/$file_path";
}
?>
