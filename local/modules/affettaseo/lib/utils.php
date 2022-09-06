<?php

namespace Bitrix\Affettaseo;

use Cutil;
use CFile;
use COption;

class Utils {
    public static function onPageStartHendler()
    {
        if(COption::GetOptionString('affettaseo', "MODULE_ACTIVE") != 'Y') return;
        global $APPLICATION;
        $hlbl = COption::GetOptionString('affettaseo', "HB_ID");

        if(!\Bitrix\Main\Loader::includeModule("highloadblock")) return;
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
        if(!$hlblock) return;

        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        try{
            $rsData = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => array("UF_URL" => $APPLICATION->GetCurDir(), "UF_ACTIVE" => true)
            ));

            if ($arData = $rsData->Fetch()) {
                if (!empty($arData['UF_H1'])) $APPLICATION->SetDirProperty('h1', $arData['UF_H1']);
                if (!empty($arData['UF_H1'])) $APPLICATION->SetPageProperty('h1', $arData['UF_H1']);
                if (!empty($arData['UF_DESCRIPTION'])) $APPLICATION->SetDirProperty('description', $arData['UF_DESCRIPTION'], $APPLICATION->GetCurDir());
            }
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }
    }

    public static function onEpilogHendler()
    {
        if(COption::GetOptionString('affettaseo', "MODULE_ACTIVE") != 'Y') return;
        global $APPLICATION;
        $hlbl = COption::GetOptionString('affettaseo', "HB_ID");

        if(!\Bitrix\Main\Loader::includeModule("highloadblock")) return;
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
        if(!$hlblock) return;

        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        try{
            $rsData = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => array("UF_URL" => $APPLICATION->GetCurDir(), "UF_ACTIVE" => true)
            ));

            if ($arData = $rsData->Fetch()) {
                if (!empty($arData['UF_TITLE'])) $APPLICATION->SetTitle($arData['UF_TITLE']);
            }
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }
    }
}