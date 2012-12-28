<?php
/**
<div class="p">
  Provides index of documentation. 
</div>
*/
require('/home/user/playground/kibbles/runnable/lib/improved-error-response-lib.php');
require('/home/user/playground/kibbles/runnable/lib/accept-processing-lib.php');
setup_for_get();
// it stops here with a 406 if the client ain't buying what we're selling
process_accept_header();

extract($_GET, EXTR_SKIP);
if (!isset($folder_path)) $folder_path='/';
if (!isset($format)) $format = 'nested';
if (respond_in_html()) {
    global $minifyBundle;
    $minifyBundle = 'fileIndex';
    require('/home/user/playground/kibbles/runnable/lib/interface-response-lib.php');
    echo_interface("<div class=\"document-index-widget loading-spinner-widget grid_12\" data-folder-path=\"$folder_path\"></div>");
}
else {
  require('/home/user/playground/kibbles/runnable/lib/data-response-lib.php');
  $base_dir = '/home/user/playground';
  if ($folder_path == '/') $dir_path = $base_dir;
  else $dir_path = "$base_dir/$folder_path";

  function make_doc_link($proj_path) {
      return '/documentation'.preg_replace('|^(/[^/]+)/kdata/documentation|', '$1', $proj_path);
  }

  function map_file_data($el) {
      return array('link' => make_doc_link($el), 'title' => basename($el));
  }

  function listDir($dir) {
      global $format;

      $folder_path = preg_replace('|/home/user/playground|', '', $dir);
      $dir_title = $folder_path;
      $dir_title = explode('/', $dir_title);
      if (count($dir_title) == 1) $dir_title = $dir_title[0];
      else $dir_title = '<span class="folder-parents">'.implode('/', array_slice($dir_title, 0, -1)).'</span>/'.$dir_title[count($dir_title) - 1];
      $files = array();
      $dirs = array();
      $dh = opendir($dir);
      while (false !== ($file = readdir($dh))) {
	  if (!preg_match('/^\./', $file) && !preg_match('/(\.png|\.jpg|\.jpeg|\.gif|\.svg|~)$/', $file)) {
	      if (is_dir("$dir/$file") && !is_file("$dir/$file/$file")) {
		  if (!preg_match("@/home/user/playground/[^/]+/(runnable|data)@", "$dir/$file"))
		      $dirs[] = "$dir/$file";
		  // else, skip it; don't include 'runnable' or 'data' in the index
	      }
	      else $files[] = "$folder_path/$file";
	  }
      }
      sort($files);
      sort($dirs);
      // title and link
      if ($format == 'flat') {
	  $result = array_map('make_doc_link', $files);
	  foreach ($dirs as $subDir) // remember, $subDir is full path
	      $result = array_merge($result, listDir($subDir));
	  return $result;
      }
      else { // nested
	  $result = array('folder' => $dir_title);
	  $result['files'] = array_map('map_file_data', $files);

	  if (count($files) > 0) {
	      $i = 1;
	      foreach($files as $file) {
		  // listFile($file, str_replace("/home/user/playground/$project/", '', $dir), $i);
		  $i += 1;
	      }
	  }
	  $result['folders'] = array();
	  foreach ($dirs as $subDir) { // remember, $subDir is full path
	      $sub_result = listDir("$subDir");
	      array_push($result['folders'], $sub_result);
	  }

	  return $result;
      }
  }
  $result = listDir($dir_path);
  final_result_ok("Item retrieved.", $result);
}
?>
