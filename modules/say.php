<?php
class say extends Module {
    public $groupchat = true;
    public $params_no = true;
    
    public function run($params, $type, $xmpp) {
        if($xmpp->isOwner($type)) {
            if(strpos($params[1], "@conference")) {
                $xmpp->message($params[1], $params[2], "groupchat");
            }else{
                $xmpp->message($params[1], $params[2], "chat");
            }
        }else{
            return "Недостаточно прав!";
        }        
    }
}
?>
