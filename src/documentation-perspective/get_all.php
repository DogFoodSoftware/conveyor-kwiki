<?php
/**
 * <div class="p">
 * Multi_Perspective Service. Returns a list off the perspectives known in the
 Kibbles instance. 
 * </div>
 */
require_once('/home/user/playground/kibbles/runnable/include/accept-processing-lib.php');
setup_for_get();
// it stops here with a 406 if the client ain't buying what we're selling
process_accept_header();

if (respond_in_html()) {
  require('/home/user/playground/kibbles/runnable/include/interface-response-lib.php');
  // echo_interface('<div class="widget-class"></div>');
}
else {
    require_once '/home/user/playground/kibbles/runnable/include/PestJSON.php';
    // using '$client = PestXML(...); $document = $client->get($webpath)', many
    // todos missing; SimpleDOM works better; don't know why
    require_once '/home/user/playground/kibbles/runnable/lib/SimpleDOM.php';
    $client = new PestJSON('http://127.0.0.1:42069');
    $documentation = $client->get('/documentation/');
    // swap out JSON client for raw client for next step
    $client = new Pest('http://127.0.0.1:42069');
    $results = array();
    foreach ($documentation as $web_path) {
    try {
	$document = SimpleDOM::loadHTML($client->get($web_path));
	foreach ($document->xpath('//*[@data-perspective]') as $i => $perspective) {
	    /*$json_element = array();
	    process_common($json_element, $perspective);
	    if ($perspective->getName() == 'perspective')
		$json_element['description'] = (string) $perspective;
	    else process_attribute($json_element, $perspective, 'data-perspective', SINGLE, 'description');
	    array_push($results, $json_element);*/
	}
    }
    catch (PestXML_Exception $e) {
	/**
	 * <todo>This should generate a warning. It seems unavoidable on some pages (without rewriting of the third party code); in practice, we want to except all 3rd party code from emebbed TODO processing anyway.</todo>
	 */
    }
}






  require('/home/user/playground/kibbles/runnable/include/data-response-lib.php');
  // check parameters if any
  // extract($_GET, EXTR_SKIP);
  // handle_errors();
  // determine REST ID and retrieve data
  // $rest_id = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
  // check authorizaiton if necessary
  // and bail if there are problems
  // handle_errors();

  // build up result
  $result = array();
  final_result_ok("Item retrieved.", $result);
}
?>




<?php

function process_attribute(&$hash, $el, $source_attr, $is_multi=SINGLE, $target_attr=null) {
    if ($target_attr == null) $target_attr = $source_attr;
    $val = $el->attributes()[$source_attr];
    if ($val != null && strlen(trim($val)) > 0) {
	if ($is_multi === MULTI) $val = preg_split('/\s+/', $val);
	$hash[$target_attr] = preg_replace('/\s{2,}/', ' ', (string) $val);
    }
}
function process_common(&$json_element, $perspective, $prefix='') {
    foreach (array('id' => SINGLE, 'project' => SINGLE, 'classification' => MULTI,
		   'provider' => MULTI, 'dependencies' => MULTI, 'status' => SINGLE,
		   'completed-branch' => SINGLE) as $source_attr => $mult)
	process_attribute($json_element, $perspective, $prefix.$source_attr, $mult);
}
// for readability
define('MULTI', true);
define('SINGLE', false);



$requested_format = $_SERVER['HTTP_ACCEPT'];
if (preg_match('/application\/json/', $requested_format)) {
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
else { // default HTML; PERSPECTIVE: actually, should only give HTML if they accept HTML or */* or no accept headers provides; otherwise result in 406
    $pageTitle = 'Dog Food Software : Kibbles : Perspective';
    $headerTitle = 'Perspective';
    require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
    echo '<div class="grid_12"><ul>';
    foreach ($results as $result)
	echo '<li>'.$result['description'].'</li>';
    echo '</ul></div>';
    require('/home/user/playground/dogfoodsoftware.com/runnable/page_close.php');
} // close format if-else block
?>
