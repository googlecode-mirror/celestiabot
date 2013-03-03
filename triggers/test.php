<?php
class test extends Module {
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        if($type[0] == "groupchat") {
            $nick = $xmpp->getNickInConference($type[1]);
            if(preg_match('/(test|тест)/i', $message['body']) && $type[2] !== (string)$nick) {
                $xmpp->message($type[1], $type[2].": passed", "groupchat");
            }
        }
    }
}
?>
