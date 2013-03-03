<?php
class mute extends Module {
    public $groupchat = true;
    public $for_owner = true;
    
    public function run($params, $type, $xmpp) {
        $xmpp->muted = !$xmpp->muted;
        return "Готово!";  
    }
}
?>
