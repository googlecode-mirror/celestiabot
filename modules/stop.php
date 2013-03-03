<?php
class stop extends Module {
    public $groupchat = true;
    public $for_owner = true;
    
    public function run($params, $type, $xmpp) {
        $xmpp->messageAllConferences("Остановка бота...");
        sleep(1);
        $xmpp->disconnect(); 
    }
}
?>
