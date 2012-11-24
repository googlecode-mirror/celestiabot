<?php
class d extends Module {
    
    public $groupchat = true;
    public $params_no = true; 
    
    public function run($params, $type, $xmpp) {
        return "1d".$params[1]." | ".rand(1, $params[1]);
    }
}
?>
