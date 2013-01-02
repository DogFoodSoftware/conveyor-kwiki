#!/home/user/playground/php5/runnable/bin/php
<?php
require_once('/home/user/playground/kibbles/runnable/lib/pest/PestJSON.php');
require_once '/home/user/playground/kibbles/runnable/lib/SimpleDOM.php';
$client = new PestJSON('http://127.0.0.1:42069');
/**
<div data-todo="revisit this once we support path filtering" class="p">
  In the future, we may support path filtering. If we do, then we would filter
  on '<code>&#42/src/rest/*</code>' here. Should make a decission on this one
  way or the, document, and remove this note.
</div>
 */
$web_paths = $client->get('/documentation/?format=flat');
$client = new Pest('http://127.0.0.1:42069');
    $files_tested = 0;
foreach ($web_paths['data'] as $web_path) {
    if (preg_match('|src/rest/[^/]+/[^\./]*\.php$|', $web_path)) {
	$files_tested += 1;
	$document = SimpleDOM::loadHTML($client->get($web_path));
	$implementation_sections = $document->xpath("//div[@id='implementation']");
	if ($implementation_sections == null || count($implementation_sections) == 0)
	    fwrite(STDOUT, "ERROR: '$web_path' appears to be a REST script with no 'implementation' section.\n");	
	else if (count($implementation_sections) > 1)
	    fwrite(STDOUT, "ERROR: '$web_path' appears have multiple 'implementation' sections.\n");	
    }
    // else, not a rest script
}
    if ($files_tested < 10)
	fwrite(STDOUT, "WARNING: Suspiciously small number of files--{$files_tested}--tested for REST documnetation standards compliance.'\n");	
?>
