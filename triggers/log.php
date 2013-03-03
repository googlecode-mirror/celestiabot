<?php
class log extends Module {
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        
        if($type[0] == "groupchat") {
            $xmpp->mysql->query("INSERT INTO `logs` (date, time, author, conference, message) VALUES('".date("Y-m-d")."', '".date("H:i:s")."', '".mysql_real_escape_string($type[2])."', '".  mysql_real_escape_string($type[1])."', '". mysql_real_escape_string($message['body'])."')") or print(mysql_error()."\n");            
        }
    }
}
?>