<?php
/**
<div class="p">
  We want to return a list of all documentation known to the system. At the
  moment, we only handle JSON requests.
</div>
*/
require('/home/user/playground/kibbles/runnable/include/accept-processing-lib.php');
setup_for_get();
// it stops here with a 406 if the client ain't buying what we're selling
process_accept_header();

extract($_GET, EXTR_SKIP);
if (respond_in_html()) {
    global $minifyBundle;
    $minifyBundle = 'fileIndex';
    require('/home/user/playground/kibbles/runnable/include/interface-response-lib.php');
    echo_interface("<div class=\"document-index-widget loading-spinner-widget grid_12\" data-folder-path=\"$folder_path\"></div>");
}
else {
  require('/home/user/playground/kibbles/runnable/include/data-response-lib.php');
  // check parameters if any
  // extract($_GET, EXTR_SKIP);
  // handle_errors();
  // determine REST ID and retrieve data
  // $rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
  // check authorizaiton if necessary
  // and bail if there are problems
  // handle_errors();

  $base_dir = '/home/user/playground';
  $dir_path = "$base_dir/$folder_path";

  function listDir($dir) {
      $dir_title = preg_replace('|/home/user/playground/|', '', $dir);
      $dir_title = explode('/', $dir_title);
      if (count($dir_title) == 1) $dir_title = $dir_title[0];
      else $dir_title = '<span class="folder-parents">'.implode('/', array_slice($dir_title, 0, -1)).'</span>/'.$dir_title[count($dir_title) - 1];
      $result = array('folder' => $dir_title);
      $files = array();
      $dirs = array();
      $dh = opendir($dir);
      while (false !== ($file = readdir($dh))) {
	  if (!preg_match('/^\./', $file) && !preg_match('/~$/', $file)) {
	      if (is_dir("$dir/$file") && !is_file("$dir/$file/$file"))
		  $dirs[] = "$dir/$file";
	      else $files[] = $file;
	  }
      }
      sort($files);
      sort($dirs);
      $result['files'] = $files;

      if (count($files) > 0) {
	  $i = 1;
	  foreach($files as $file) {
	      // listFile($file, str_replace("/home/user/playground/$project/", '', $dir), $i);
	      $i += 1;
	  }
      }
      $result['folders'] = array();
      foreach ($dirs as $subDir) { // remember, $subDir is full path
	  if (!preg_match(":/home/user/playground/[^/]+/runnable(\$|/):", "$subDir") && !preg_match(":/home/user/playground/[^/]+/data(\$|/):", "$subDir")) {
	      $sub_result = listDir("$subDir");
	      array_push($result['folders'], $sub_result);
	  }
      }

      return $result;
  }
  $result = listDir($dir_path);
  final_result_ok("Item retrieved.", $result);
}
?>
