<?php
class who {
    public $groupchat = true;
    
    public function run($params, $type, $xmpp) {
        if($type[0] == "groupchat") {
            $users = '';
            foreach($xmpp->roles as $user) {
                $role = explode("/", $user[0]);
                if($role[0] == $type[1]) {
                    $users .= $role[1]." ";
                }
            }
            return "Нас тут ".(count(explode(" ", $users))-1).": ".$users;
        }
    }
}
?>