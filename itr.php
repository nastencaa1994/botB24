<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/im/lib/bot.php");
//include("./OpenlinesMenuExample.php");

use cp_upd\utility\ChatBots\LK_Bot\OpenlinesMenuExample;
//удаления бота
//$res = OpenlinesMenuExample::unRegister();

//регистрация бота
$res = OpenlinesMenuExample::register(['AGENT' => 'Y']);







