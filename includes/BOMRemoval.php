<?php
// Tell me the root folder path.
// You can also try this one
// $HOME = $_SERVER["DOCUMENT_ROOT"];
// Or this
// dirname(__FILE__)
$HOME = dirname(__FILE__);

// Is this a Windows host ? If it is, change this line to $WIN = 1;
$WIN = 0;

// That's all I need
$BOMBED = array();
RecursiveFolder($HOME);
//echo '<h2>These files had UTF8 BOM, but i cleaned them:</h2><p class="FOUND">';
foreach ($BOMBED as $utf) { echo $utf ."<br />\n"; }
//echo '</p>';

// Recursive finder
function RecursiveFolder($sHOME) {
  global $BOMBED, $WIN;
  
  $win32 = ($WIN == 1) ? "\\" : "/";
  
  $folder = dir($sHOME);
  
  $foundfolders = array();
  while ($file = $folder->read()) {
    if($file != "." and $file != "..") {
      if(filetype($sHOME . $win32 . $file) == "dir"){
        $foundfolders[count($foundfolders)] = $sHOME . $win32 . $file;
      } else {
        $content = file_get_contents($sHOME . $win32 . $file);
        $BOM = SearchBOM($content);
        if ($BOM) {
          $BOMBED[count($BOMBED)] = $sHOME . $win32 . $file;
          
          // Remove first three chars from the file
          $content = substr($content,3);
          // Write to file 
          file_put_contents($sHOME . $win32 . $file, $content);
        }
      }
    }
  }
  $folder->close();
  
  if(count($foundfolders) > 0) {
    foreach ($foundfolders as $folder) {
      RecursiveFolder($folder, $win32);
    }
  }
}

// Searching for BOM in files
function SearchBOM($string) { 
    if(substr($string,0,3) == pack("CCC",0xef,0xbb,0xbf)) return true;
    return false; 
}
?>