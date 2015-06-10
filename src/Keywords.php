<?php namespace Aboustayyef;


/**
 *  Return Keywords from body of text
 *  Based on: https://github.com/aboustayyef/RAKE-PHP/blob/master/rake.php
 */

 class Keywords {

 	private $stoplist = array("that’s","what’s","he’s", "she’s", "won’t" ,"don’t", "i’ve", "you’ve", "doesn’t", "it’s", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

 	public function __construct() {
     #nothing
 	}

 	private function split_into_sentences( $text ) {
 		$pattern =  '/[.!?,;:\t\-\"\(\)\']/';
 		return preg_split( $pattern, $text );
 	}

 	private function split_into_words( $phrase, $min_chars = 0 ) {
 		$word_list = [];
 		$pattern = '/\P{L}+/u';
 		$words = preg_split( $pattern, $phrase );
 		foreach( $words as $word ) {
 			$word = trim( $word );
 			if ( strlen( $word ) > $min_chars && $word != "" && ! is_numeric( $word ) ) {
 				array_push( $word_list, $word );
 			}
 		}
 		return $word_list;
 	}


 	/* Build a regular expression containing our stopwords */

 	private function get_stopword_regex() {
 		$words_regex = implode( '|', $this->stoplist );
 		return '/\b(' . trim( $words_regex ) . ')\b/i';
 	}

 	/* Get phrases from our text using stopwords regex */

 	private function get_phrases( $sentence_list ) {
 		$phrase_list = [];
 		$stopword_pattern = $this->get_stopword_regex();
 		foreach( $sentence_list as $sentence ) {
 			$tmp = preg_replace( $stopword_pattern, '|', $sentence );
 			$phrases = explode( '|', $tmp );
 			foreach( $phrases as $phrase ) {
 				$phrase = strtolower( trim( $phrase ) );
 				if ( $phrase != "" ) {
 					array_push( $phrase_list , $phrase );
 				}
 			}
 		}
 		return $phrase_list;
 	}

 	/*
 	 * Get scores for individual words depending on their frequency,
 	 * degree and ratio of degree/freqeuncy
 	 */

 	private function get_word_scores( $phrase_list ) {

 		$word_frq = [];
 		$word_degree = [];

 		foreach( $phrase_list as $phrase ) {
 			$word_list = $this->split_into_words( $phrase );
 			$word_list_length = count( $word_list );
 			$word_list_degree = $word_list_length - 1;

 			foreach ( $word_list as $word ) {
 				if ( array_key_exists( $word, $word_frq) ) {
 					$word_frq[ $word ] += 1;
 				} else {
 					$word_frq[ $word ] = 1;
 				}
 				if ( array_key_exists( $word, $word_degree) ) {
 					$word_degree[ $word ] += $word_list_degree;
 				} else {
 					$word_degree[ $word ] = $word_list_degree;
 				}
 			}
 		}

 		foreach ( $word_frq as $item => $value ) {
 			$word_degree[ $item ] =
 				$word_degree[ $item ] + $word_frq[ $item ];
 		}

 		$word_score = [];

 		foreach ( $word_frq as $item => $value ) {
 			$word_score[ $item ] = round(
 				floatval( $word_degree[ $item ] ) / floatval( $word_frq[ $item ] )
 		        	,2 );
 		}
 		return $word_score;
 	}

 	public function get_phrase_scores( $phrase_list, $word_scores ) {
 		$phrase_scores = [];
 		foreach ( $phrase_list as $phrase ) {
 			if ( ! array_key_exists( $phrase, $phrase_scores ) ) {
 				$phrase_scores[ $phrase ] = 0;
 			}
 			$word_list = $this->split_into_words( $phrase );
 			$total_score = 0;
 			foreach ( $word_list as $word ) {
 				$total_score += $word_scores[ $word ];
 			}
 			$phrase_scores[ $phrase ] = $total_score;
 		}
 		return $phrase_scores;
 	}

 	public function extract( $text, $maxWords=3, $maxResults=5 ) {
 		$sentence_list = $this->split_into_sentences( $text );
 		$phrase_list = $this->get_phrases( $sentence_list );
 		$word_scores = $this->get_word_scores( $phrase_list );
 		$candidates = $this->get_phrase_scores( $phrase_list, $word_scores );


    // Max words (Mustapha)
    arsort($candidates);
    $results = [];

    foreach ($candidates as $candidate => $score) {
        if (str_word_count($candidate ) <= $maxWords) {
          $results[] = $candidate;
          if (count($results) == $maxResults ) {

            return $results;
          }
        }
    }
 		return $candidates;
 	}
 }

?>
