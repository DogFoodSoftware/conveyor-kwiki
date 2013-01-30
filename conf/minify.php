<?php
require('/home/user/playground/kibbles/conf/minify.php');

$documentationIndexJs = array('/home/user/playground/kwiki/runnable/include/document-index-widget.js');
$documentationIndexJs = array_merge($kibblesCoreJs, $documentationIndexJs);

$documentationIndexCss = $kibblesCoreCss;

// jquery-ui is used for the '.resize()' allowing us to resize code blocks
$fileDocJs = array();
$fileDocJs = array_merge($kibblesCoreJs, $fileDocJs);

$fileDocCss = array();
$fileDocCss = array_merge($kibblesCoreCss, $fileDocCss);

$fileIndexJs = array('/home/user/playground/kwiki/runnable/include/document-index-widget.js');
$fileIndexJs = array_merge($kibblesCoreJs, $fileIndexJs);

$fileIndexCss = array('/home/user/playground/kwiki/runnable/include/document-index-widget.css',
		      '/home/user/playground/kibbles/runnable/ui/chase-layout-widget.css');
$fileIndexCss = array_merge($kibblesCoreCss, $fileIndexCss);

return array('documentationIndexJs' => $documentationIndexJs,
             'documentationIndexCss' => $documentationIndexCss,
	     'fileDocJs' => $fileDocJs,
	     'fileDocCss' => $fileDocCss,
	     'fileIndexJs' => $fileIndexJs,
	     'fileIndexCss' => $fileIndexCss);
?>
