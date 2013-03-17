<?php
//error_reporting(0);
@set_time_limit (0);
//@ini_set ('max_execution_time', "0");

include "classes/bot.php";
include "XMPPHP/XMPP.php";
include "classes/Module.php";
include "classes/mysql.php";
include "classes/Misc.php";
global $bot;

$bot = new Bot(new SimpleXMLElement(file_get_contents("config.xml")));
$bot->loadModules();
$bot->connect();
$bot->workCycle();
?>
