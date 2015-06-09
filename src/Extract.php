<?php namespace Aboustayyef;


/**
 *
 */
class Extract
{
  public $url;
  function __construct($url)
  {
    $this->url = $url;
  }

  public function getText(){

    $html = @file_get_contents($this->url);

    if (function_exists('tidy_parse_string')) {
        $tidy = tidy_parse_string($html, array(), 'UTF8');
        $tidy->cleanRepair();
        $html = $tidy->value;
    }

    $readability = new \Readability($html, $this->url);
    $readability->debug = false;
    $readability->convertLinksToFootnotes = false;
    $result = $readability->init();

    if ($result) {
        $content = $readability->getContent()->innerHTML;
        // if we've got Tidy, let's clean it up for output
        if (function_exists('tidy_parse_string')) {
            $tidy = tidy_parse_string($content, array('indent'=>true, 'show-body-only' => true), 'UTF8');
            $tidy->cleanRepair();
            $content = $tidy->value;
        }
        $content = strip_tags($content);
        $content = preg_replace('#\n#', "", $content);
        $content = preg_replace('#\s+#', " ", $content);
        return $content;
    } else {
        return 'Looks like we couldn\'t find the content. :(';
    }
  }

}

?>
