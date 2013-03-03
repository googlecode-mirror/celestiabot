<?php
class modules {
    public $groupchat = true;
    
    public function run($params, $type, $xmpp) {
        $inc = glob("modules/*.php");
        $modules = '';
        foreach($xmpp->modules as $key => $value) {
            if($value->groupchat) {
                $key .= ' (доступен в конференции)';
            }
            $modules .= "\n".$key;
        }
        return "Загруженные модули:".$modules;
    }
}

?>