<?php

class ExtendedStringParser {

	public function parse($array) {
		for($i=0; $i<count($array); $i++) {
			$string = $array[$i];
			for($j=0; $j<strlen($string); $j++) {
				$s = $string{$j};
				switch($s) {
					case "{":
						break;
						
					case "}":
						break;
						
					case "\"":
						break;
						
				}
				
			}
		}
	}
}o
?>