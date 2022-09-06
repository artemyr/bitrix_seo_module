<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(class_exists("affettaseo")) return;

Class affettaseo extends CModule
{
    var $MODULE_ID = "affettaseo";  // - хранит ID модуля (полный код партнерского модуля);
    var $MODULE_VERSION;            //- текущая версия модуля в формате XX.XX.XX;
    var $MODULE_VERSION_DATE;       //- строка содержащая дату версии модуля; дата должна быть задана в формате YYYY-MM-DD HH:MI:SS;
    var $MODULE_NAME;               // - имя модуля;
    var $MODULE_DESCRIPTION;        //- описание модуля;
    var $MODULE_CSS;                //
    var $MODULE_GROUP_RIGHTS = "Y"; //- если задан метод GetModuleRightList, то данное свойство должно содержать Y

    public function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\","/",__FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include(__DIR__.'/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = 'affettaseo';
        $this->MODULE_DESCRIPTION = 'affettaseo desc';
    }


    function InstallDB($install_wizard = true)
    {
        RegisterModule("affettaseo");
        RegisterModuleDependences("main", "OnPageStart", "affettaseo", "\\Bitrix\\Affettaseo\\Utils", "onPageStartHendler");
        RegisterModuleDependences("main", "OnEpilog", "affettaseo", "\\Bitrix\\Affettaseo\\Utils", "onEpilogHendler");
        return true;
    }

    function UnInstallDB($arParams = Array())
    {
        RegisterModuleDependences("main", "OnPageStart", "affettaseo", "\\Bitrix\\Affettaseo\\Utils", "onPageStartHendler");
        RegisterModuleDependences("main", "OnEpilog", "affettaseo", "\\Bitrix\\Affettaseo\\Utils", "onEpilogHendler");
        UnRegisterModule("affettaseo");
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles()
    {
        $result = Bitrix\Highloadblock\HighloadBlockTable::add(array(
            'NAME' => 'AffettaSeoInfoHB',//должно начинаться с заглавной буквы и состоять только из латинских букв и цифр
            'TABLE_NAME' => 'affettaseoinfohb',//должно состоять только из строчных латинских букв, цифр и знака подчеркивания
        ));
        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
        } else {
            $id = $result->getId();
            COption::SetOptionInt("affettaseo", "HB_ID", $id);

            $arCartFields = Array(
                'UF_ACTIVE'=>Array(
                    'ENTITY_ID' => "HLBLOCK_".$id,
                    'FIELD_NAME' => 'UF_ACTIVE',
                    'USER_TYPE_ID' => 'boolean',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Акт.'),
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Акт.'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Акт.'),
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    'SETTINGS' => array('DEFAULT_VALUE' => true)
                ),
                'UF_URL'=>Array(
                    'ENTITY_ID' => "HLBLOCK_".$id,
                    'FIELD_NAME' => 'UF_URL',
                    'USER_TYPE_ID' => 'string',
                    'MANDATORY' => 'Y',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Ссылка на страницу'),
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Ссылка на страницу'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Ссылка на страницу'),
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    "HELP_MESSAGE" => Array('ru'=>'Например: "/catalog/"', 'en'=>''),
                ),
                'UF_H1'=>Array(
                    'ENTITY_ID' => "HLBLOCK_".$id,
                    'FIELD_NAME' => 'UF_H1',
                    'USER_TYPE_ID' => 'string',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Заголовок страницы H1'),
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Заголовок страницы H1'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Заголовок страницы H1'),
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_TITLE'=>Array(
                    'ENTITY_ID' => "HLBLOCK_".$id,
                    'FIELD_NAME' => 'UF_TITLE',
                    'USER_TYPE_ID' => 'string',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Заголовок браузера TITLE'),
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Заголовок браузера TITLE'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Заголовок браузера TITLE'),
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_DESCRIPTION'=>Array(
                    'ENTITY_ID' => "HLBLOCK_".$id,
                    'FIELD_NAME' => 'UF_DESCRIPTION',
                    'USER_TYPE_ID' => 'string',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Описание страницы DESCRIPTION'),
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Описание страницы DESCRIPTION'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Описание страницы DESCRIPTION'),
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                )
            );

            foreach($arCartFields as $arCartField){
                $obUserField  = new CUserTypeEntity;
                $obUserField->Add($arCartField);
            }
        }

        return true;
    }

    function UnInstallFiles()
    {
//        Bitrix\Highloadblock\HighloadBlockTable::delete();
        return true;
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB(false);
    }

    function DoUninstall()
    {
//        $this->UnInstallFiles();
        $this->UnInstallDB(false);
    }

    function GetModuleRightList()
    {
        global $MESS;
        $arr = array(
            "reference_id" => array("D","R","W"),
            "reference" => array(
                "[D] ".GetMessage("FORM_DENIED"),
                "[R] ".GetMessage("FORM_OPENED"),
                "[W] ".GetMessage("FORM_FULL"))
        );
        return $arr;
    }
}
?>