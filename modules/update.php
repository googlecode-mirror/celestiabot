<?php
class update extends Module {
    public $groupchat = true;

    public function run($params, $type, $xmpp) {
        if($xmpp->isOwner($type)) {
            $xmpp->setStatus($xmpp->config->bot->status_text, $xmpp->config->bot->status);
            return "Данные обновлены!";
        }else{
            return "Недостаточно прав!";
        }        
    }
}
?>
