<?php
class nick extends Module {
    public $trigger = true;
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        if($type[0] == "groupchat") {
            $nick = $xmpp->getNickInConference($type[1]);
            if(preg_match('/'.$nick.'/i', $message['body']) && $type[2] !== (string)$nick) {
                $xmpp->message($type[1], $type[2].": ".$xmpp->getRandomPhrase(), "groupchat");
            }
        }
    }
}
?>

