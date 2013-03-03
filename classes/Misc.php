<?php

class Misc {
    public function getTextFromTag($str, $begin, $end) {
        if(!preg_match('/'.preg_quote($begin).'(.+)'.preg_quote($end).'/isU', $str, $match)) {
            return null;
        }else{
            return $match[1];
        }
    }
}
?>
