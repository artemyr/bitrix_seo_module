<?
class CAffettaseo
{
    public static function IsAdmin()
    {
        global $USER, $APPLICATION;
        if (!is_object($USER)) $USER = new CUser;
        if ($USER->IsAdmin()) return true;
        $FORM_RIGHT = $APPLICATION->GetGroupRight("affettaseo");
        if ($FORM_RIGHT>="W") return true;
    }

    public static function testAdd2Log($text)
    {
        foreach (GetModuleEvents('affettaseo', 'onBeforeAffettaseoTestLog', true) as $arEvent)
        {
            ExecuteModuleEventEx($arEvent, array(&$text));
        }

        return $text;

        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
        AddMessage2Log($text, "affettaseo");
    }
}