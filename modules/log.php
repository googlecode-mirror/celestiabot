<?php
class log extends Module {
    public $trigger = true;
    public $groupchat = true;
    
    public function run($message, $xmpp) {
        $type = $xmpp->getMessageType($message);
        
        if($type[0] == "groupchat") {
            mysql_query("INSERT INTO `logs` (author, conference, message) VALUES('".mysqli_real_escape_string($type[2])."', '".  mysqli_real_escape_string($type[1])."', '". mysqli_real_escape_string($message['body'])."')") or print(mysqli_error()."\n");            
        }
    }
}
?>
