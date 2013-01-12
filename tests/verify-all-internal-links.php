#!/home/user/playground/php5/runnable/bin/php
<?php
require_once('/home/user/playground/kibbles/runnable/lib/pest/PestJSON.php');
require_once '/home/user/playground/kibbles/runnable/lib/SimpleDOM.php';
$client = new PestJSON('http://127.0.0.1:42069');
$web_paths = $client->get('/documentation/?format=flat');
$client = new Pest('http://127.0.0.1:42069');
$client->throw_exceptions = false;
$files_tested = 0;
$links_tested = 0;
$tested_hrefs = array();
foreach ($web_paths['data'] as $web_path) {
    // test every page
    $files_tested += 1;
    $document = SimpleDOM::loadHTML($client->get($web_path));
    $links = $document->xpath("//a[@href]");
    if ($links != null)
	foreach ($links as $link) {
	    $links_tested += 1;
	    $href = $link->getAttribute('href');
	    if (preg_match('|^/|', $href)) {
		if (array_key_exists($href, $tested_hrefs) && !$tested_hrefs[$href])
		    // we've tested this href before
		    // TODO: this optimization means that we're not checking all
		    // relative links correctly, some may be skipped; at the
		    // moment, the recomendation is to always use root-relative
		    // links, so this is not high priority, just something to be
		    // aware of.
		    fwrite(STDOUT, "ERROR: Another instance of bad '$href' on page '$web_path'.\n");
		else {
		    $client->get($href);
		    if ($client->lastStatus() != '200') {
			fwrite(STDOUT, "ERROR: Non-200 error for HREF '$href' on page '$web_path': ".$client->lastStatus().".\n");
			$tested_hrefs[$href] = false;
		    }
		    else $tested_hrefs[$href] = true;
		}
	    }
	    else if (!preg_match('/^([a-z]+:|#)/', $href))
		fwrite(STDOUT, "WARNING: found context-relative href '$href' on page '$web_path'. Root relative web paths preferred.\n");
	}
}
if ($files_tested < 10)
    fwrite(STDOUT, "WARNING: Suspiciously small number of files--{$files_tested}--tested for valid link references.'\n");	
if ($links_tested < 10)
    fwrite(STDOUT, "WARNING: Suspiciously small number of links--{$linkes_tested}--tested for valid references.'\n");	
?>
