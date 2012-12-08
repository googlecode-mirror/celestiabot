<?php
class modules {
    public $groupchat = true;
    
    public function run($params, $type, $xmpp) {
        $inc = glob("modules/*.php");
        $modules = '';
        foreach($inc as $module) {
            $m0 = explode("/", $module);
            $m = explode(".", $m0[1]);
            $modules .= $m[0]." ";
        }
        return $modules;
    }
}

?>