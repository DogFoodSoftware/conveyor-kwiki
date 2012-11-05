<?php
/**
<div class="p">
  We want to return a list of all documentation known to the system. At the
  moment, we only handle JSON requests.
</div>
*/
require_once('/home/user/playground/kibbles/runnable/include/kibbles-file-lib.php');
require_once('/home/user/playground/kwiki/runnable/include/kwiki-lib.php');

$urls = array();
foreach (get_kibbles_static_files() as $file_path)
    array_push($urls, convert_file_to_url($file_path));
// otherwise it's a document we can't yet process, so it gets dropped
/**
   <todo>Support filter for 'undocumented non-build, non-data files'.</todo>
*/


$requested_format = $_SERVER['HTTP_ACCEPT'];
if (preg_match('/application\/json/', $requested_format)) // good to go
    echo json_encode($urls, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
else header("HTTP/1.0 406 Cannot satisfy requested response format.");
?>
