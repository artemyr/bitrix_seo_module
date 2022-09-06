<?
/**
 * данный файл подключается в тот момент, когда речь идет о подключении модуля в коде,
 * в нем должны находиться включения всех файлов с библиотеками функций и классов модуля;
 */

global $DB, $MESS, $APPLICATION;

$DBType = mb_strtolower($DB->type);

CModule::AddAutoloadClasses(
    "affettaseo",
    array(
//        "CForm" => "classes/".$DBType."/form_cform.php",
        "CAffettaseo" => "classes/general/affettaseo_affettaseo.php",
        "CAffettaseo_ext" => "classes/".$DBType."/affettaseo_affettaseo.php",
    )
);