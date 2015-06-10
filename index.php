<?php

require_once('vendor/autoload.php');

$string1 = extractIt('http://dailystar.com.lb/News/Lebanon-News/2015/Jun-10/301445-hezbollah-achieved-major-victories-against-nusra-on-arsal-outskirts-in-recent-battles-nasrallah.ashx');
$string2 = extractIt('http://blogbaladi.com/justice-for-issam-maalouf-but-dont-close-down-hospitals/');

// TEST FOR COMPARING TWO TEXTS
// $analyzer = new \Aboustayyef\Text($string1, $string2);
// $analyzer->compare();


// TEST FOR KEYWORD EXTRACTOR
$keywordsExtractor = new Aboustayyef\Keywords();

$keywords1 = $keywordsExtractor->extract($string1);
$keywords2 = $keywordsExtractor->extract($string2);

var_dump($keywords1);

// Convenience function
function extractIt($url){
  return ( new \Aboustayyef\Extract($url))->getText();
}

?>
