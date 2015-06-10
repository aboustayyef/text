<?php namespace Aboustayyef;


/**
 *  Extracts the content
 */

class Extract
{
  public $url;
  private $html;
  private $readability;
  private $result;

  function __construct($url)
  {
    $this->url = $url;
    $this->html = @file_get_contents($this->url);

    if (function_exists('tidy_parse_string')) {
        $tidy = tidy_parse_string($this->html, array(), 'UTF8');
        $tidy->cleanRepair();
        $this->html = $tidy->value;
    }

    $this->readability = new \Readability($this->html, $this->url);
    $this->readability->debug = false;
    $this->readability->convertLinksToFootnotes = false;
    $this->result = $this->readability->init();
  }

  public function getText(){

    if ($this->result) {
        $content = $this->readability->getContent()->innerHTML;

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

  public function getTitle(){
    if ($this->result) {
      return $this->readability->getTitle()->textContent;
    }else{
      return "Sorry, could not get title";
    }
  }

}

?>
