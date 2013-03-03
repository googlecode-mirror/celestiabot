<?php
class send extends Module {
    public $groupchat = true;
    public $params_no = true;
    public $for_owner = true;
    
    public function run($params, $type, $xmpp) {
	$message = '';
	for($i=2; $i<count($params); $i++) {
            $message .= $params[$i].' ';
	}
        if($message == ' ') {
            if(strpos($params[1], "@conference")) {
                $xmpp->message($params[1], $message, "groupchat");
                return "Сообщение отправлено!";
            }else{
                $xmpp->message($params[1], $message, "chat");
                return "Сообщение отправлено!";
            }   
        }
        return "Ошибка при отправке";
    }
}
?>
