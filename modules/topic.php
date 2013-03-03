<?php
class topic {
    public $groupchat = true;
    public $params_no = true;
    public $for_owner = true;
    
    public function run($params, $type, $xmpp) {
        if($type[0] == "groupchat") {
            $message = '';
            for($i=1; $i<count($params); $i++) {
                $message .= $params[$i].' ';
            }
            if($message !== '') {
                $xmpp->setTopic($type[1], $message);
                return "Тема изменена!";
            }
            return "Ошибка при изменении темы";
        }else{
            return "Тему можно изменить только в конференции!";
        }
    }
}
?>