<?php
/**
<div class="p">
  Set up the code blocks. Add the resize handler, allowing the user to expand
  the code blocks, along with code to stop the resize when the maximum extent
  of the code has been reached.
</div>
*/
global $extraHeader;
$extraHeader =
"   <script>
     $(document).ready(function() {
       $('.prettyprintBox').each(function(i, el) {
         \$prettyprint = $(el).find('.prettyprint');
	 // TODO: the '- 4' is for padding, totally style dependent and should be made dynamic
         if (\$prettyprint.get(0).scrollHeight - 4 > \$prettyprint.height()) {
           $(el).resizable({
	     alsoResize: $(el).find('pre'), /* resize the pre block within the div contanier */
	     handles: {'s': $(el).find('.resizeControl')}, /* limit to vertical / south */
	     maxHeight: $(el).find('pre').prop('scrollHeight') +
                        ($(el).prop('clientHeight') - $(el).find('pre').prop('clientHeight'))
	   });
         }
	 else $(el).find('.resizeControl').remove();
       });
     });
   </script>\n";

function convert_file_to_url($file_path) {
    // first, strip the base
    $file_path = substr("$file_path", strlen('/home/user/playground'));
    $project = preg_replace('/^\/([^\/]+).*/', '$1', $file_path); // take first segment as project
    $file_path = preg_replace('/^\/[^\/]+\//', '', $file_path); // remove first segment from file_path
    // can we process the file?
    /**
       <todo>This section has to coordinate with the processing logic. Share the logic.</todo>
    */
    if (preg_match('|kdata/documentation/[^\.]+$|', $file_path)) {
	$web_path = preg_replace('|kdata/documentation/|', '', $file_path);
	$web_path = "/documentation/$project/$web_path";
	return $web_path;
    }
    else if (preg_match('/(\.php|\.js)$/', $file_path))
	return "/documentation/$project/$file_path";
}
?>
