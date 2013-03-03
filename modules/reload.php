<?php
class reload extends Module {
    public $groupchat = true;
    public $for_owner = true;
    
    public function run($params, $type, $xmpp) {
        $xmpp->setStatus($xmpp->config->bot->status_text, $xmpp->config->bot->status);
        return "Данные обновлены!";
    }
}
?>
