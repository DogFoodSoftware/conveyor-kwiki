<?php 
/**
<div class="p">
  Script to process <code>GET</code> requests for specific a single documentation
  resource. Documentation resources are (currently) always a file, but may be
  of a one of two types. Specifically, we handle source code files and
  'standard' wiki pages.
</div>
<div class="p">
  Source code is recognized by the file extension. Standard wiki pages have no
  extension.
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
$file_path = $_SERVER['REQUEST_URI'];
/**
<todo>We are using regexp here for brevity, but it's probably more efficient
 to use substr_compare: http://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string</todo>
 */
if (preg_match('/(.php|.js)$/', $file_path))
    require '/home/user/playground/kwiki/runnable/lib/code_processor.php';
else // it's a 'standard wiki page'
    require '/home/user/playground/kwiki/runnable/lib/wiki_page_processor.php';
?>
