<?php
/**
 * <div class="p">
 * Multi_Perspective Service. Returns a list off the perspectives known in the
 Kibbles instance. 
 * </div>
 * <div id="implementation" class="blurbSummary">
 *  <div class="blurbTitle">Implementation</div>
 *  <div class="description">
 */
require_once('/home/user/playground/kibbles/runnable/lib/accept-processing-lib.php');
setup_for_get();
// it stops here with a 406 if the client ain't buying what we're selling
process_accept_header();

if (respond_in_html()) {
  require('/home/user/playground/kibbles/runnable/lib/interface-response-lib.php');
  // echo_interface('<div class="widget-class"></div>');
}
else {
    //   require_once '/home/user/playground/kibbles/runnable/lib/pest/PestJSON.php';
    // using '$client = PestXML(...); $document = $client->get($webpath)', many
    // todos missing; SimpleDOM works better; don't know why
    //   require_once '/home/user/playground/kibbles/runnable/lib/SimpleDOM.php';
    //    $client = new PestJSON('http://127.0.0.1:42069');
    //    $documentation = $client->get('/documentation/?format=flat');
    // swap out JSON client for raw client for next step
    //    $client = new Pest('http://127.0.0.1:42069');
    //    $results = array();
    //    foreach ($documentation['data'] as $web_path) {
    //	try {
    //	    $document = SimpleDOM::loadHTML($client->get($web_path));
    //	    foreach ($document->xpath('//*[@data-perspective]') as $i => $el) {
		//		array_push($results, (string) $el->attributes()['data-perspective']);
    //	    }
    //	}
    //	catch (PestXML_Exception $e) {
    //	    /**
//	     * <todo>This should generate a warning. It seems unavoidable on some pages (without rewriting of the third party code); in practice, we want to except all 3rd party code from emebbed TODO processing anyway.</todo>
//	     */
//	}
//    }
    require('/home/user/playground/kibbles/runnable/lib/data-response-lib.php');
$results = array(array('group name' => 'role',
		       'group options' => array("vision","finance", "development", "operations", "management", "sales")),
		 array('group name' => 'additional',
		       'group options' => array("detailed","historical","future")));
    final_result_ok("Item retrieved.", $results);
}
?>
<?php /**
</div><!-- .descirption -->
</div><!-- .blurbSummary #implementation -->
*/ ?>
