<?php 
/**
<div class="p">
  Script to process <code>GET</code> requests for a single documentation
  item. We may distinghuish two primary types of documentation items: pages
  and folders. A request for a file results in the file contents. A request
  for a folder results in an index of the files and sub-folders within the
  requested folder. The current implementation supports JSON indexs and HTML
  files (embedded within the selected site template).
</div>
<div class="p">
  Pages recongized as source code&mdash;by location and extension&mdash;are
  formatted and optimized for HTML presentation. Wiki pages &mdash;under the
  <code>&lt;project&gt;/kdata/documentation</code> directory are encoded as
  HTML fragments and returned as is.
</div>
<div class="p">
  Note the use of 'page' and 'folder' rather than 'file' and 'directory'. Page
  and folder refer to the documentation items, as viewed from the perspective
  of the web service API. Files and directories are how pages and folders are
  stored. A page in particular may be stored as either a single file, or a
  file set within a directory.<span class="note">This is used when the page
  embeds images or other elements not encoded directly into the HTML which are
  particular to the page itself. E.g., a diagram image.</span>
</div>
<div class="p">
  See <code><a
  href="/kwiki/documentation/src/code-to-html.php">code-to-html.php</a></code>
  and <code><a
  href="/kwiki/documenqtation/src/wiki-page-processor.php">wiki-page-processor.php</a></code>
  for details on the processing of each particular type.
</div>
 */
$rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
$file = basename($rest_id); // used as the page title
    /**
       <div class="p">
         Setup the page template variables common to all documentation item types.
       </div>
    */
$pageTitle = 'Dog Food Software || '.$file;
$headerTitle = $file;
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
$project = preg_replace('/^\/documentation\/([^\/]+).*/', '$1', $rest_id);
/**
 <span data-todo="process the documentatino $rest_id to determine more
 complete isa trail">The isa trail is currently incomplete, just points back
 to projects.</span>
*/
$isaTrail = array('<a href="/projects/">projects</a>');
/**
  <span data-todo>We are using regexp here for brevity, but it's probably more efficient
   to use substr_compare:
 http://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string</span>
 */
if (preg_match('/(.php|.js|.sh)$/', $rest_id)) {
    require('/home/user/playground/kwiki/runnable/include/kwiki-lib.php');
    $minifyBundle = 'fileDoc';
    require '/home/user/playground/dogfoodsoftware.com/runnable/page_open.php';
    require '/home/user/playground/kwiki/runnable/include/code-to-html.php';
    code_to_html($rest_id);
    require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
}
else // it's a 'standard wiki page'
    require '/home/user/playground/kwiki/runnable/inclide/wiki-page-processor.php';
?>
