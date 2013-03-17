<?php

class ping extends Module {
    public $groupchat = true;
    public function run($params, $type, $xmpp) {

        if ($type[0] == 'groupchat') {
            $nick = $xmpp->getNickInConference($type[1]);
            $xmpp->xmpp->vinfo($xmpp->config->bot->username . '@' . $xmpp->config->bot->server . '/' . $xmpp->config->bot->resource, $type[1] . "/" . $type[2], $type[0], 'ping', $type[1], $type[2]);
            return "Подождите...";
        } else {
            return "Ошибка при вызове модуля!";
        }
    }
}
?>
