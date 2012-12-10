<?php

/**
   A Kwiki document can be either an HTML snippet or a directory containing an
   HTML snippet of the same name and zero or more document resources. A
   document resource is any image, audio file, video, etc. which is
   essentially part of the document. This is oppossed to a file referenced or
   included by the document but which has (conceptual) indepndent existence
   outside the ducemnt. Functionally, it really doesn't matter. The
   organization of files as part of a document set is a matter of practical
   design.
*/

else if (is_dir($abs_document_path)) { // it's an index request
    $folder_path = $project.(strlen($file_path) > 0 ? "/$file_path" : '');
    $contents = '<div class="document-index-widget" data-folder-path="'.$folder_path.'"></div>';
}
else if (file_exists($abs_document_path)) { // it's a file, but what kind?

}
else // it's a link to a file which doesn't yet exist
    $contents = 
/**
   Note, there is no need to handle other document resources (as found in
   a document directory) because these are caught by Apache and routed
   directly without involving PHP.
*/


?>
