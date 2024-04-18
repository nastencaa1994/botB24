<?php

namespace cp_upd\utility\ChatBots\LK_Bot;
//
use Bitrix\Main\Localization\Loc;
use Bitrix\ImBot\Itr as Itr;
use Bitrix\Im\Model;

//require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\UserTable;


Loc::loadMessages(__FILE__);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class OpenlinesMenuExample
{
    const MODULE_ID = "imbot";
    const BOT_CODE = "openlinemenu";

    public static function register(array $params = array())
    {
        if (!\Bitrix\Main\Loader::includeModule('im'))
            return false;

        $agentMode = isset($params['AGENT']) && $params['AGENT'] == 'Y';

        if (self::getBotId())
            return $agentMode ? "" : self::getBotId();



        $botId = \Bitrix\Im\Bot::register(array(
            'CODE' => self::BOT_CODE,
            'TYPE' => \Bitrix\Im\Bot::TYPE_OPENLINE,
            'MODULE_ID' => self::MODULE_ID,
            'CLASS' => __CLASS__,
            'METHOD_MESSAGE_ADD' => 'onMessageAdd',
            'METHOD_WELCOME_MESSAGE' => 'onChatStart',
            'METHOD_BOT_DELETE' => 'onBotDelete',
            'OPENLINE' => 'Y',
            'PROPERTIES' => array(
                'NAME' => "Bot LK",
                'WORK_POSITION' => "Get ITR menu for you open channel",
            )
        ));
        if ($botId) {
            self::setBotId($botId);
        }

        return $agentMode ? "" : $botId;
    }

    public static function unRegister()
    {
        if (!\Bitrix\Main\Loader::includeModule('im'))
            return false;

        $result = \Bitrix\Im\Bot::unRegister(array('BOT_ID' => self::getBotId()));
        if ($result) {
            self::setBotId(0);
        }

        return $result;
    }

    public static function onChatStart($dialogId, $joinFields)
    {
        if ($joinFields['MESSAGE_TYPE'] == IM_MESSAGE_PRIVATE)
            return false;

        self::itrRun($dialogId, $joinFields['USER_ID']);

        return true;
    }

    public static function onMessageAdd($messageId, $messageFields)
    {

        if ($messageFields['SYSTEM'] == 'Y')
            return false;

        self::itrRun($messageFields['DIALOG_ID'], $messageFields['FROM_USER_ID'], $messageFields['MESSAGE']);

        return true;
    }

    public static function onBotDelete($bodId)
    {
        return self::setBotId(0);
    }

    private static function prepareText($message)
    {
        $message = preg_replace("/\[s\].*?\[\/s\]/i", "-", $message);
        $message = preg_replace("/\[[bui]\](.*?)\[\/[bui]\]/i", "$1", $message);
        $message = preg_replace("/\\[url\\](.*?)\\[\\/url\\]/i" . BX_UTF_PCRE_MODIFIER, "$1", $message);
        $message = preg_replace("/\\[url\\s*=\\s*((?:[^\\[\\]]++|\\[ (?: (?>[^\\[\\]]+) | (?:\\1) )* \\])+)\\s*\\](.*?)\\[\\/url\\]/ixs" . BX_UTF_PCRE_MODIFIER, "$2", $message);
        $message = preg_replace("/\[USER=([0-9]{1,})\](.*?)\[\/USER\]/i", "$2", $message);
        $message = preg_replace("/\[CHAT=([0-9]{1,})\](.*?)\[\/CHAT\]/i", "$2", $message);
        $message = preg_replace("/\[PCH=([0-9]{1,})\](.*?)\[\/PCH\]/i", "$2", $message);
        $message = preg_replace('#\-{54}.+?\-{54}#s', "", str_replace(array("#BR#"), array(" "), $message));
        $message = strip_tags($message);

        return trim($message);
    }

    private static function itrRun($dialogId, $userId, $message = '')
    {

        if ($userId <= 0)
            return false;

        \Bitrix\Main\Loader::includeModule('im');
        \Bitrix\Main\Loader::includeModule('imopenlines');
        $chat = new \CIMChat($userId);
        $thisChatCount = $chat->GetUserCount(substr($dialogId,4));

        if($thisChatCount<=2){
            if (count($_SESSION['options']['contracts']) > 0) {

                if (in_array('WG', $_SESSION['options']['contracts'])) {

                    $menu0 = new Itr\Menu(0);
                    $menu0->setText('Меню');
                    $menu0->addItem(1, 'Сотрудник СВХ', Itr\Item::execFunction(function($context){
                        $userIdSVH = self::getChatUserGroup(62);
                        self::addChatUser($userIdSVH,$context->botId,substr($context->dialogId,4));
                    }, 'Вам ответит первый освободившийся оператор',true));


                    $menu0->addItem(2, 'Сотрудник Поддержки',  Itr\Item::execFunction(function($context){
                        $userIdSupport = self::getChatUserGroup(61);
                        self::addChatUser($userIdSupport,$context->botId,substr($context->dialogId,4));
                    }, 'Вам ответит первый освободившийся оператор',true));


                } else {

                    $menu0 = new Itr\Menu(0);
                    $menu0->setText('Меню');
                    $menu0->addItem(1, 'Сотрудник СВХ', Itr\Item::execFunction(function($context){
                        $userIdSVH = self::getChatUserGroup(62);
                        self::addChatUser($userIdSVH,$context->botId,substr($context->dialogId,4));
                    }, 'Вам ответит первый освободившийся оператор',true));


                    $menu0->addItem(2, 'Сотрудник Поддержки', Itr\Item::execFunction(function($context){
                        $userIdSupport = self::getChatUserGroup(61);
                        self::addChatUser($userIdSupport,$context->botId,substr($context->dialogId,4));
                    }, 'Вам ответит первый освободившийся оператор',true));


                    $menu0->addItem(3, 'Сотрудник Дикларант',Itr\Item::execFunction(function($context){
                        $userIdSVH = self::getChatUserGroup(63);
                        self::addChatUser($userIdSVH,$context->botId,substr($context->dialogId,4));
                    }, 'Вам ответит первый освободившийся оператор',true));

                }
            } else {
                $menu0 = new Itr\Menu(0);
                $menu0->setText('Меню');
                $menu0->addItem(1, 'Сотрудник Поддержки', Itr\Item::execFunction(function($context){

                    $userIdSupport = self::getChatUserGroup(61);
                    self::addChatUser($userIdSupport,$context->botId,substr($context->dialogId,4));

                }, 'Вам ответит первый освободившийся оператор',true));

            }

            $itr = new Itr\Designer('box', $dialogId, self::getBotId(), $userId);
            $itr->addMenu($menu0);
            $itr->run(self::prepareText($message));

        }

        return true;
    }

    private static function getChatUserGroup($idGroup)
    {
        $result = \Bitrix\Main\UserGroupTable::getList(
            array(
                'filter' => array('GROUP_ID' => $idGroup),
                'select' => array(
                    'USER_ID',
                ),
            )
        )->fetchAll();
        $return = [];
        foreach ($result as $userId) {
            $return[] = $userId['USER_ID'];
        }
        return $return;
    }

    private static function addChatUser($usersArr,$botId,$dialogId){
        $chat = new \CIMChat($botId);

        foreach ($usersArr as $userId){
            $chat->AddUser($dialogId, $userId);
        }

    }

    public static function getBotId()
    {
        return \Bitrix\Main\Config\Option::get(self::MODULE_ID, self::BOT_CODE . "_bot_id", 0);
    }

    public static function setBotId($id)
    {
        \Bitrix\Main\Config\Option::set(self::MODULE_ID, self::BOT_CODE . "_bot_id", $id);
        return true;
    }

}


