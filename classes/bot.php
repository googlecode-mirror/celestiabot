<?php

class Bot {

    public $xmpp;
    public $config;
    public $modules;
    public $triggers;
    public $muted = false;
    private $owners = array();
    public $mysql;
    public $misc;
    public $roles; // TODO!

    function __construct($configuration) {
        $this->config = $configuration;
        $this->misc = new Misc();
    }

    public function connect() {
        $this->xmpp = new XMPP($this->config->bot->server, 5222, $this->config->bot->username, $this->config->bot->password, $this->config->bot->resource, null, $printlog = True, $loglevel = LOGGING_INFO);
        $this->xmpp->addHandler('iq', 'jabber:client', 'income_iq');
        $this->xmpp->connect();
        $this->mysql = new mysql($this->config->mysql->host, $this->config->mysql->user, $this->config->mysql->pass, $this->config->mysql->base);
        $this->mysql->connect();
    }

    public function disconnect() {
        $this->xmpp->dicsonnect();
    }

    public function workCycle() {
        while (!$this->xmpp->disconnected) {
            $payloads = $this->xmpp->processUntil(array('message', 'presence', 'end_stream', 'session_start'));

            foreach ($payloads as $event) {
                $messages = $event[1];
                switch ($event[0]) {
                    //Сообщения
                    case 'message':
                        $this->parseMessage($messages);
                        break;

                    //Обработчик статусов пользователей
                    case 'presence':
                        $this->roles = $this->xmpp->r1;
                        array_unshift($this->roles, ' ');
                        $present = array();
                        for ($z = 0; $z < count($this->roles); $z++) {
                            $key = strpos($this->roles[$z], 'conference');
                            if ($key > 0)
                                array_push($present, $this->roles[$z]);
                        }
                        array_unshift($present, ' ');
                        /* echo "-----------\n";
                          foreach($this->roles as $role) {
                          foreach($role as $r) {
                          echo $r."|";
                          }
                          echo "\n";
                          } */
                        //print_r($this->roles);
                        break;

                    //Старт
                    case 'session_start':
                        $this->setStatus($this->config->bot->status_text, $this->config->bot->status);
                        $this->loadOwners($this->config->owners->owner);
                        sleep(1);
                        $this->enterConferences($this->config->conferences->conference);
                        break;
                }
            }
        }
    }

    private function parseMessage($message) {
        $mess = $this->getMessageType($message);

        foreach ($this->triggers as $trigger) {
            $trigger->run($message, $this);
        }

        if ($message['body']{0} == "!") {
            if (strlen($message['body']) == 1) {
                return;
            }
            $param = explode(" ", $message['body']);
            $param[0] = str_replace("!", "", $param[0]);
            if (isset($this->modules[$param[0]])) {
                if ($this->modules[$param[0]]->for_owner) {
                    if ($this->isOwner($mess)) {
                        $this->callModule($mess, $message, $param);
                    } else {
                        if ($mess[0] == "groupchat") {
                            $this->message($mess[1], $mess[2] . ": недостаточно прав!", "groupchat");
                        } else {
                            $this->message($mess[1], "Недостаточно прав!", "chat");
                        }
                    }
                } else {
                    $this->callModule($mess, $message, $param);
                }
            } else {
                if ($mess[0] == "groupchat") {
                    $this->message($mess[1], $mess[2] . ": модуль не найден!", "groupchat");
                } else {
                    $this->message($mess[1], "Модуль не найден!", "chat");
                }
            }
        }
    }

    private function callModule($mess, $message, $param) {
        if ($mess[0] == "chat") {
            if ($this->modules[$param[0]]->params_no) {
                $this->message($mess[1], $this->modules[$param[0]]->run($param, $this->getMessageType($message), $this), "chat");
            } else {
                if ($this->modules[$param[0]]->params == count($param) - 1) {
                    $this->message($mess[1], $this->modules[$param[0]]->run($param, $this->getMessageType($message), $this), "chat");
                } else {
                    $this->message($mess[1], "Неверное количество параметров!", "chat");
                }
            }
        } elseif ($mess[0] == "groupchat" && $this->modules[$param[0]]->groupchat) {
            if ($this->modules[$param[0]]->params_no) {
                $this->message($mess[1], $mess[2] . ": " . $this->modules[$param[0]]->run($param, $this->getMessageType($message), $this), "groupchat");
            } else {
                if ($this->modules[$param[0]]->params == count($param) - 1) {
                    $this->message($mess[1], $mess[2] . ": " . $this->modules[$param[0]]->run($param, $this->getMessageType($message), $this), "groupchat");
                } else {
                    $this->message($mess[1], $mess[2] . ": неверное количество параметров!", "groupchat");
                }
            }
        } elseif ($mess[0] == "groupchat" && !$this->modules[$param[0]]->groupchat) {
            $this->message($mess[1], $mess[2] . ": запрещен запуск модуля в конференции!", "groupchat");
        }
    }

    public function setStatus($text, $status) {
        $this->xmpp->presence($text, $status);
    }

    private function enterConferences($conf) {
        for ($cnf = 0; $cnf < count($conf); $cnf++) {
            $this->enterConference($conf[$cnf]);
        }
    }

    public function enterConference($conf) {
        $this->xmpp->joinc($conf->server . '/' . $conf->nick, $this->config->bot->status);
    }

    public function setTopic($to, $topic) {
        $this->xmpp->topic2($to, $topic);
    }

    public function getMessageType($message) {
        $msg_type = $message['type'];

        if (strpos($message['from'], "@conference") && $msg_type == 'chat') {
            $from_conf = $message['from'];
            $from_user = '';
        } else {
            $tmp = explode("/", $message['from']);
            $from_user = $tmp[1];
            $from_conf = $tmp[0];
        }

        if ($msg_type == 'error') {
            $from_conf = '';
            $msg_type = '';
            $from_user = '';
        }
        return array($msg_type, $from_conf, $from_user);
    }

    public function loadModules() {
        $this->modules = array();
        $this->triggers = array();

        $inc = glob("modules/*.php");
        foreach ($inc as $module) {
            $name = explode("/", $module);
            $name2 = explode(".", $name[1]);
            include $module;
            $mod = new $name2[0];
            $this->modules[$name2[0]] = $mod;
        }

        $inct = glob("triggers/*.php");
        foreach ($inct as $trigger) {
            $name = explode("/", $trigger);
            $name2 = explode(".", $name[1]);
            include $trigger;
            $trig = new $name2[0];
            $this->triggers[$name2[0]] = $trig;
        }
    }

    public function message($to, $text, $type) {
        if (!$this->muted) {
            $this->xmpp->message($to, $text, $type);
        }
    }

    public function messageAllConferences($text) {
        if (!$this->muted) {
            foreach ($this->config->conferences->conference as $c) {
                $this->xmpp->message($c->server, $text, 'groupchat');
            }
        }
    }

    public function loadOwners($own) {
        for ($o = 0; $o < count($own); $o++) {
            $this->owners[] = $own[$o];
        }
    }

    public function isOwner($type) {
        if ($type[0] == "groupchat") {
            $jid = $type[1] . "/" . $type[2];
        } elseif ($type[0] == "chat") {
            $jid = $type[1];
        }
        return in_array($jid, $this->owners);
    }

    public function getNickInConference($server) {
        foreach ($this->config->conferences->conference as $c) {
            if ($c->server == $server) {
                return $c->nick;
            }
        }
    }

    public function getRandomPhrase() {
        $r = rand(0, count($this->config->phrases->phrase) - 1);
        return $this->config->phrases->phrase[$r];
    }

}

?>
