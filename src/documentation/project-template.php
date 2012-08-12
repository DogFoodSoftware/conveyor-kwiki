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
     As part of the 'mainContent' section, there is support for old style API
     documentation. It is not clear whether or not this section will be
     retained as we regularize on a more "pure" style of REST.
   </div>
*/
echo <<<EOT

            <div class="grid_8 blurbSummary alpha omega projectFiles">
	      <div class="blurbTitle">
		Project Files
	      </div>
	      <div class="description">
EOT;
/**
  <div class="p">
    We sort out the files in the 'src' directory into files and
    directories. Generally, the directories represent resource categories or
    code libraries. Files directly in the project src should be the
    exception.
  </div>
*/
function listFile($file, $relPath) {
  global $project;
  if (preg_match('/\.php$|\.js$/', $file))
    echo "<li><a href=\"/documentation/$project/$relPath/$file\">$file</a></li>";
  else echo "<li>$file</li>";
}

$col = FALSE; // need $col as global to track
function listDir($title, $dir, $descendInPlace) {
  global $col;
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
  // might not use the $style, but that's okay
  $style = "grid_4";
  if ($col) $style .= ' alpha';
  else $style .= ' omega';
  if (!$descendInPlace) {  
    if (count($files) > 0) {
      echo "<div class=\"$style\">\n";
      echo "<div class=\"filesTitle\">$title</div>\n";
      echo "<ul>\n";
      foreach($files as $file) listFile($file, str_replace("/home/user/playground/$project/", '', $dir));
      echo "</ul>\n";
      echo "</div>\n";
      if ($col) echo "<div class=\"clear\"></div>\n";
      $col = !$col;
    }
    foreach ($dirs as $subDir) { // remember, $subDir is full path
      listDir('/'.basename($subDir), "$subDir", TRUE);
    }
  }
  else {
    echo "<div class=\"$style\">\n";
    echo "<div class=\"filesTitle\">$title</div>\n";
    echo "<ul>\n";
    foreach($files as $file) listFile($file, str_replace("/home/user/playground/$project/", '', $dir));
    // at this point, the $dirs becomes the search frontier; process depth first
    while (count($dirs) > 0) {
      $subFiles = array();
      $nextDir = array_shift($dirs);
      $dh = opendir($nextDir);
      // TODO: we can't sort the dirs, so we need them to come in sorted... (or push to separate array and merge... ugly)
      while (false !== ($file = readdir($dh))) {
        if (!preg_match('/^\./', $file) && !preg_match('/~$/', $file)) {
          if (is_dir("$dir/$file"))
            array_unshift($dirs, "$nextDir/$file");
          else $subFiles[] = $file;
        }
      }
      sort($subFiles);
      $relDir = str_replace($dir, '', $nextDir);
      foreach($subFiles as $file) {
        echo "<li>$relDir/$file</li>";
      }
    }
    echo "</ul>\n";
    echo "</div>\n";
    if ($col) echo "<div class=\"clear\"></div>\n";
    $col = !$col;
  }
}

listDir('Files', "/home/user/playground/$project/src", FALSE);
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
echo<<<EOT
          <div class="status grid_4 alpha omega blurbSummary">
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
