<?php
class log extends Module {
    public $trigger = true;
    public $groupchat = true;
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        
        if($type[0] == "groupchat") {
            mysql_query("INSERT INTO `logs` (author, conference, message) VALUES('".mysql_real_escape_string($type[2])."', '".  mysql_real_escape_string($type[1])."', '". mysql_real_escape_string($message['body'])."')") or print(mysql_error()."\n");            
        }
    }
}
?>
