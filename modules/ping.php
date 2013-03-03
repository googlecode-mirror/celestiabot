<?php
class ping extends Module {
    public $groupchat = true;
    
    public function run($params, $type, $xmpp) {

        if ($type[0] == 'groupchat') {
            $nick = $xmpp->getNickInConference($type[1]);
            $xmpp->xmpp->vinfo($this->config->bot->username.'@'.$this->config->bot->server.'/'.$this->config->bot->resource, $type[1]."/".$type[2], $type[0], 'ping', $type[1]);            
           
            
        }else{
            return "Ошибка при вызове модуля!";
        }
         echo 1;
    }
}
?>
