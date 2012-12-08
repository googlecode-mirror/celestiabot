<?php
class bash {
    public $groupchat = true;
    public $params_no = true;
    
    public function run($params, $type, $xmpp) {
        if(count($params) == 2) {
            if(!is_numeric($params[1])) {
                return "Количество параметров больше/меньше максимального или получено не-число!";
            }
            preg_match_all('|<div class=\"text\">(.*?)</div>|is', file_get_contents("http://bash.im/quote/".$params[1]), $respage);
            $respage = iconv("CP1251", "UTF-8", $respage[1][0]);
            $respage = str_replace("<br>", "\r\n", $respage);
            $respage = str_replace("<br />", "\r\n", $respage);
            $respage = html_entity_decode($respage);
            return $respage;
        }else{
            preg_match_all('|<div class=\"text\">(.*?)</div>|is', file_get_contents("http://bash.im/random"), $respage);
            $respage = iconv("CP1251", "UTF-8", $respage[1][0]);
            $respage = str_replace("<br>", "\r\n", $respage);
            $respage = str_replace("<br />", "\r\n", $respage);
            $respage = html_entity_decode($respage);
            return $respage;
        }
    }
}

?>