<?php
require('/home/user/playground/kibbles-core/conf/minify.php');

$documentationIndexJs = array('/home/user/playground/kwiki/runnable/lib/documentationindexwidget.js');
$documentationIndexJs = array_merge($kibblesCoreJs, $documentationIndexJs);

$documentationIndexCss = $kibblesCoreCss;
return array('documentationIndexJs' => $documentationIndexJs,
             'documentationIndexCss' => $documentationIndexCss);
?>
