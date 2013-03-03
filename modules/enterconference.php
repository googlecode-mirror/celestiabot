<?php
class enterconference extends Module {
    public $groupchat = true;
    public $params = 2;
    
    public function run($params, $type, $xmpp) {
        if($xmpp->isOwner($type)) {
            $conf = new stdClass();
            $conf->server = $params[1];
            $conf->nick = $params[2];
            $xmpp->enterConference($conf);
        }else{
            return "Недостаточно прав!";
        }        
    }
}
?>
