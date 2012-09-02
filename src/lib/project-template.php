<?php /**
<div class="p">
Template script to generate Dog Food Software project pages. The project pages
are the hub of contextual webs. The script should be included at the top of
the project documentation index file, a number of config globals (defined
below) defined, and then at the end of the file, the <code>output()</code> is
called.
</div>
<ul>
  <li><code>$projectTitle</code> : string</li>
  <li><code>$projectStatement</code> : string</li>
</ul>
 */
function output($project) {
    // config strings set by user
    global $projectTitle;
    global $projectStatement;
    global $isaTrail;
    // section data set by script
    global $primaryContent;
    global $secondaryContent;
/**
Open with the standard Dog Food Software page header and opening. This will be
parameterized at some point before the release of 1.0.
*/
$pageTitle = 'Dog Food Software || '.$projectTitle;
$headerTitle = $projectTitle;
$pageDescription = ''; // TODO
$pageAuthor = 'Liquid Labs, LLC';
$minifyBundle = 'kibblesCore';
require('/home/user/playground/dogfoodsoftware.com/runnable/page_open.php');
/**
   <div class="p">
     We then get into the body proper. There are a number of "fixed" elements,
     which are populated by the variables mentioned earlier. Specifically, we
     open with a project statement. The statement spans the body. After that,
     the body is split into a 'mainContent', comprising 2/3'rds of the screen
     (on the left hand side) and 'secondaryContent' (established later on in
     the flow), which takes up the remaining 1/3 on the right hand side.
   </div>
*/
// context from the opener
//<body>
//<div id="nonFooter">
//  <div id="content">
//    <div id="page" class="container_12">
//      <div id="body">
echo <<<EOT
	<div class="grid_12 blurbSummary">
	  <div class="blurbTitle">
		Project Statement
	  </div>
	  <div class="p">
	        $projectStatement
	  </div>
	</div><!-- .blurbSummary for 'project statement' -->
	<div class="clear"></div>
	<div class="grid_8 mainContent">
EOT;
/**
<div class="p">
Process the primary sections. The sections are built up by
calling <code>addPrimaryContent()</code> which takes a section name and
section content. These are then spit back out in the same order her.
</div>
*/
foreach (getPrimarySections() as $sectionTitle) {
    $content = getPrimaryContent($sectionTitle);
echo<<<EOT
	  <div class="grid_8 alpha omega blurbSummary">
	    <div class="blurbTitle">
	      $sectionTitle
	    </div>
	    <div class="description">
	      $content
	    </div>
	  </div>
	  <div class="clear"></div>
EOT;
} // close primary section foreach
/**
   <div class="p">
     Now we generate the the project file index.
   </div>
*/
echo <<<EOT

            <div id="project_files" class="grid_8 blurbSummary alpha omega">
	      <div class="blurbTitle">
		Project Files
	      </div>
	      <div class="description">
EOT;
/**
  <div class="p">
    The project files section lists all file artifacts associated with the
    static project definition.<!-- note: This means dynamic files, which may
    be generated during runtime, are not listed. --> In effect, this means
    everything but the <code>runnable</code> and <code>data</code> directory
    is listed.
  </div>
  <div class="p">
    The files are annotated with CSS style c lasses 'second', 'third',
    'fourth', and 'eight' (TODO: should be 'eighth') according to a modulo
    test.<!-- note: Notice that any given item may have multiple
    designations. The eighth item, for instance, is also a second and fourth
    item. --> This is used in the adaptive layout to determine where to insert
    breaks. E.g., in a relatively narrow screen, allowing for two colomns, we
    would break after every 'second' item. In a very wide screen, we might
    break after every eighth. This is laid out as an 8 column sub-section of a
    grid 12 layout, and so 8 is as fine as it gets.
  </div>
  <div class="p">
    The <code>listFile()</code> determines the CSS classes as described above
    and sets up file links.
  </div>
*/
function listFile($file, $relPath, $i) {
  global $project;
  $style = ($i % 8) == 0 ? 'eight ' : '';
  if ($i % 4 == 0) $style .= 'fourth ';
  if ($i % 3 == 0) $style .= 'third ';
  if ($i % 2 == 0) $style .= 'second';
  echo "<li".(strlen($style) > 0 ? ' class="'.$style.'"' : '').">";
  if (preg_match('/\.php$|\.js$/', $file))
      echo "<a href=\"/documentation/$project/$relPath/$file\">$file</a>";
  else if (preg_match('|^kdata/documentation|', $relPath)) {
      $extracted_path = preg_replace('|^kdata/documentation|','',$relPath);
      if (strlen($extracted_path) > 0)
	  echo "<a href=\"/documentation/$project/$extractedPath/$file\">$file</a>";
      else echo "<a href=\"/documentation/$project/$file\">$file</a>";
  }
  else echo "$file";
  echo '</li>';
}
/**
  <div class="p">
    We will kick things off by calling the <code>listDir()</code> function on
    the project root. The method will then alphabetize and sort the directory
    contents into files and directories. The files will be listed under the
    directory heading and the directories will be processed with a recursive
    call to <code>listDir()</code>, resulting in a depth first traversal of
    the project directories. The <code>runnable</code> and <code>data</code>
    directories will be ignored as these are exclusively part of the project
    runtime.
  </div>
 */
function listDir($title, $dir) {
  global $project;
  $files = array();
  $dirs = array();
  $dh = opendir($dir);
  while (false !== ($file = readdir($dh))) {
    if (!preg_match('/^\./', $file) && !preg_match('/~$/', $file)) {
      if (is_dir("$dir/$file"))
        $dirs[] = "$dir/$file";
      else $files[] = $file;
    }
  }
  sort($files);
  sort($dirs);

  if (count($files) > 0) {
      echo "<div class=\"floatList\">";
      echo "<div class=\"subHeader\">$title</div>\n";
      echo "<ul>\n";
      $i = 1;
      foreach($files as $file) {
	  listFile($file, str_replace("/home/user/playground/$project/", '', $dir), $i);
	  $i += 1;
      }
      echo "</ul>\n";
      echo "</div>\n";
  }
  foreach ($dirs as $subDir) { // remember, $subDir is full path
      if (!preg_match('/^runnable$/', "$subDir") && !preg_match('/^data$/', "subDir"))
	  listDir(($title == '/' ? '' : $title).'/'.basename($subDir), "$subDir");
  }
}

listDir('/', "/home/user/playground/$project");
echo <<<EOT
	  </div><!-- .description for Project Files -->
	</div><!-- .blurbSummary for Project Files -->
EOT;
echo '	</div><!-- .mainContent -->';
/**
   <div class="p">
     We are now done with the 'mainContent' section and open the
     'secondaryContent'. To be clear, this section will visuall display
     alongside and even with the main content vertically in a second
     column. 
   </div>
*/
echo '	<div class="grid_4 secondaryContent">';
foreach (getSecondarySections() as $sectionTitle) {
    $content = getSecondaryContent($sectionTitle);
    // TODO: turn title into snake case and add as class
echo<<<EOT
          <div class="grid_4 alpha omega blurbSummary">
	    <div class="blurbTitle">
	      $sectionTitle
	    </div>
	    <div class="description">
	      $content
	    </div>
	  </div><!-- .status -->
EOT;
} // loop for secondary sections
echo <<<EOT
	</div><!-- .secondaryContent -->
	<div class="clear"></div>
EOT;
/**
   <div class="p">
     Finally, we append the standard footer.
   </div>
*/
require '/home/user/playground/dogfoodsoftware.com/runnable/page_close.php';
}

$primarySections = array();
$primaryContent = array();
function addPrimaryContent($sectionTitle, $content) {
    global $primarySections;
    global $primaryContent;

    array_push($primarySections, $sectionTitle);
    $primaryContent[$sectionTitle] = $content;
}

function getPrimarySections() {
    global $primarySections;
    return $primarySections;
}
function getPrimaryContent($sectionTitle) { 
    global $primaryContent;
    return $primaryContent[$sectionTitle];
}

$secondarySections = array();
$secondaryContent = array();
function addSecondaryContent($sectionTitle, $content) {
    global $secondarySections;
    global $secondaryContent;

    array_push($secondarySections, $sectionTitle);
    $secondaryContent[$sectionTitle] = $content;
}

function getSecondarySections() {
    global $secondarySections;
    return $secondarySections;
}
function getSecondaryContent($sectionTitle) { 
    global $secondaryContent;
    return $secondaryContent[$sectionTitle];
}
?>
