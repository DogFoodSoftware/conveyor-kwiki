<?php
require('/home/user/playground/kibbles/conf/minify.php');

$documentationIndexJs = array('/home/user/playground/kwiki/runnable/ui/document-index-widget.js');
$documentationIndexJs = array_merge($kibblesCoreJs, $documentationIndexJs);

$documentationIndexCss = $kibblesCoreCss;

$fileIndexJs = array('/home/user/playground/kwiki/runnable/ui/document-index-widget.js');
$fileIndexJs = array_merge($kibblesCoreJs, $fileIndexJs);

$fileIndexCss = array('/home/user/playground/kwiki/runnable/ui/document-index-widget.css',
		      '/home/user/playground/kibbles/runnable/ui/chase-layout-widget.css');
$fileIndexCss = array_merge($kibblesCoreCss, $fileIndexCss);

return array('documentationIndexJs' => $documentationIndexJs,
             'documentationIndexCss' => $documentationIndexCss,
	     'fileIndexJs' => $fileIndexJs,
	     'fileIndexCss' => $fileIndexCss);
?>
