<?php
@set_time_limit (0);
//@ini_set ('max_execution_time', "0");

include "config.php";
include "classes/bot.php";
include "XMPPHP/XMPP.php";
include "classes/module.php";

global $bot;

$bot = new Bot($config);
$bot->loadModules();
$bot->connect();
$bot->workCycle();
?>
