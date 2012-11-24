<?php
class nick extends Module {
    public $trigger = true;
    public $groupchat = true;
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        if($type[0] == "groupchat") {
            $nick = $xmpp->getConfParamByServer($type[1]);
            if(preg_match('/'.$nick.'/i', $message['body'])) {
                $xmpp->message($type[1], $type[2].": ".$xmpp->getRandomPhrase(), "groupchat");
            }
        }
    }
}
?>

