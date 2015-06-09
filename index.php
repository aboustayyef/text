<?php

require_once('vendor/autoload.php');

// Convenience function
function extractIt($url){
  return ( new \Aboustayyef\Extract($url))->getText();
}

$string1 = extractIt('http://ginosblog.com/2015/06/09/thoughts-on-the-issam-maalouf-issue/');
$string2 = extractIt('http://blogbaladi.com/justice-for-issam-maalouf-but-dont-close-down-hospitals/');

$analyzer = new \Aboustayyef\Text($string1, $string2);
$analyzer->compare();

?>
