<?php
class mute extends Module {
    public $groupchat = true;
    
    public function run($params, $type, $xmpp) {
        if($xmpp->isOwner($type)) {
            $xmpp->muted = !$xmpp->muted;
            return "Готово!";
        }else{
            return "Недостаточно прав!";
        }        
    }
}
?>
