<?php
class status extends Module {
    public $groupchat = true;
	public $params = 2;
	
    public function run($params, $type, $xmpp) {
        if($xmpp->isOwner($type)) {
            $xmpp->setStatus($params[2], $params[1]);
            return "Данные обновлены!";
        }else{
            return "Недостаточно прав!";
        }        
    }
}
?>
