<?php 
/**
<div class="p">
  Script to process <code>GET</code> requests for a single documentation
  item. We may distinghuish two primary types of documentation items: files
  and folders. A request for a folder results in an index of the files and
  sub-folders within the requested folder. A request for a file results in the
  file contents. The current implementation supports JSON indexs and HTML
  files (embedded within the selected site template).
</div>
<div class="p">
  Files recongized as source code&mdash;by location and extension&mdash;are
  formatted and optimized for HTML presentation. Wiki files and file
  sets&mdash;under the <code>&lt;project&gt;/kdata/documentation</code>
  directory are encoded as HTML fragments and returned as is.
</div>
<div class="p" data-todo="Link to an example.">
  To process a source code page, embedded HTML elements are extracted and
  copied directly to the output. The remainder of the source file (more or
  less non-comment source) is embedded in HTML elements suitable for
  processing by the 'prettify' JS library. This results in nicely formatted
  HTML output that interleaves the embedded HTML with 'prettified' source
  code. <span data-todo="Link to template docs or something.">The output is
  embedded in the standard header / footer template.</span>
</div>
<div class="p">
  Standard wiki pages are simply read as is and embedded the standard header /
  footer template.
</div>
 */
$rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
/**
<todo>We are using regexp here for brevity, but it's probably more efficient
 to use substr_compare: http://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string</todo>
 */
if (preg_match('/(.php|.js|.sh)$/', $rest_id))
    require '/home/user/playground/kwiki/runnable/lib/code_processor.php';
else // it's a 'standard wiki page'
    require '/home/user/playground/kwiki/runnable/lib/wiki_page_processor.php';
?>
