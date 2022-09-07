<?php
/**
 * @var global $mid - module id
 * @var global $APPLICATION
 * @var global $Update - if post method save
 * @var global $RestoreDefaults - if post method restoreDefaults
 */

use Bitrix\Main\Loader;
use \Bitrix\Main\Application;

Loader::includeModule('affettaseo');   //инитит классы типа CAffettaseo
IncludeModuleLangFile(__FILE__);

//user rigths for buttons
$FORM_RIGHT = $APPLICATION->GetGroupRight($mid);

//if request method restore
//require before option list
if ($_SERVER['REQUEST_METHOD'] == "GET" && CAffettaseo::IsAdmin() && $RestoreDefaults <> '' && check_bitrix_sessid())
{
    COption::RemoveOption($mid);
    $z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
    while($zr = $z->Fetch())
    {
        $APPLICATION->DelGroupRight($mid, array($zr["ID"]));
    }
}

//module option list and default values
$arAllOptions = array(
    array("MODULE_ACTIVE", "Состояние", array("checkbox", COption::GetOptionString($mid, "MODULE_ACTIVE"))),
    array("HB_ID", "ID Highload-блока работающего с СЕО", array("text", COption::GetOptionInt($mid, "HB_ID"))),
);



//if request method update
if($_SERVER['REQUEST_METHOD'] == "POST" && CAffettaseo::IsAdmin() && $Update <> '' && check_bitrix_sessid())
{
    foreach($arAllOptions as $ar)
    {
        $name = $ar[0];
        $val = ${$name};
        if($ar[2][0] == "checkbox" && $val != "Y")
        {
            $val = "N";
        }

        COption::SetOptionString($mid, $name, $val);
    }
}

//if request method clear cache
if($_SERVER['REQUEST_METHOD'] == "POST" && CAffettaseo::IsAdmin() && $_POST['clearCache'] == 'Y' && check_bitrix_sessid())
{
    $taggedCache = Application::getInstance()->getTaggedCache(); // Служба пометки кеша тегами
    $taggedCache->clearByTag('affettaseo_module_cache');
}

//tabs list title and name
$aTabs = array(
    array("DIV" => "settings", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "form_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    array("DIV" => "desc", "TAB" => "Описание", "ICON" => "form_settings", "TITLE" => "Как это работает?"),
    array("DIV" => "rights", "TAB" => "Доступ", "ICON" => "form_settings", "TITLE" => "Уровень доступа к модулю"),
);

//tabs init
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();

//form init
?><form method="POST" id="affettaseo_form_settings" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>"><?=bitrix_sessid_post()?>
    <input type="hidden" name="clearCache" value="">
    <?
//first tab
$tabControl->BeginNextTab();?>
<?php
if(COption::GetOptionString($mid, "MODULE_ACTIVE") == 'Y')
    echo "Модуль роботает!! Не мешай ему.";
else
    echo "Включи его";

//arr options
if (is_array($arAllOptions)):
    foreach($arAllOptions as $Option):
        $val = COption::GetOptionString($mid, $Option[0]);
        $type = $Option[2];
	?>
    <tr>
        <td valign="top" width="50%"><?	if($type[0]=="checkbox")
                echo "<label for=\"".htmlspecialcharsbx($Option[0])."\">".$Option[1]."</label>";
            else
                echo $Option[1];?>
        </td>
        <td valign="top" nowrap width="50%"><?
            if($type[0]=="checkbox"):
                ?><input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>><?
            elseif($type[0]=="text"):
                ?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?
            elseif($type[0]=="textarea"):
                ?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?echo htmlspecialcharsbx($val)?></textarea><?
            endif;
            ?></td>
    </tr>
    <?
    endforeach;
endif;

//third tab
$tabControl->BeginNextTab();?>
<div class="adm-info-message" style="width: 60%">
    Модуль перебивает seo мета-теги title, h1 и description.<br>
    Но при соблюдении следующих условий:<br>
    <ul>
        <li>title - задан функцией "<?=htmlspecialchars('<title><? $APPLICATION->ShowTitle(false);?> </title>')?>"</li>
        <li>h1 - задан функцией "<?=htmlspecialchars('<h1><?= $APPLICATION->GetDirProperty("h1") ?></h1>')?>" на не детальных страницах, чтобы подтягивалось дефолтное значение из .section<br>
            для детальных страниц "<?=htmlspecialchars('<h1><?= $APPLICATION->GetPageProperty("h1", $arResult["NAME"]) ?></h1>')?>", чтобы при отсутствии значение выводился заголовок элемента</li>
        <li>description - вообще никак не затронут (можно задавать дефолтное значение в файле .section.php раздела)</li>
    </ul>
    <pre>
    пример файла .section
    $sSectionName = "Главная";
    $arDirProperties = Array(
        "description" => "Дизайнерское бюро MILLENNIUM ARCHITECTS",
        "h1" => "Дизайнерское бюро MILLENNIUM ARCHITECTS",
    );
    </pre>
    При установки модуля атоматически создается highloadblock AffettaSeoInfoHB со всеми необходимыми параметрами. ID созданого инфоблока записывается в поле "ID Highload-блока работающего с СЕО" в настройках модуля affettaseo.<br>
    При удалении модуля ни highloadblock AffettaSeoInfoHB, ни дополнительные пользовательские поля созданные для него не удаляются.
</div>
<script type="text/javascript">
    function ClearCache()
    {
        if(confirm('Будет очищен тольлко кэш модуля')){
            document.querySelector('input[name=clearCache]').value = 'Y';
            document.querySelector('#affettaseo_form_settings').submit();
        }
    }
</script>
    <br>
<input <?if ($FORM_RIGHT<"W") echo "disabled" ?> type="submit" onclick="ClearCache();" value="<?echo GetMessage("CLEAR_IMAGE_FOLDER")?>">
<?
//forth tab
$tabControl->BeginNextTab();?>
<?
echo "Ваши права: ".$FORM_RIGHT;
$module_id = "affettaseo"; //need for rights
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

<? //buttons
$tabControl->Buttons();?>
    <input <?if ($FORM_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?echo GetMessage("FORM_SAVE_CUSTOM")?>">
    <script type="text/javascript">
        function RestoreDefaults()
        {
            if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
        }
    </script>
    <input <?if ($FORM_RIGHT<"W") echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <input type="reset" name="reset" value="<?=GetMessage("FORM_RESET")?>">
<?$tabControl->End();?>

</form>
