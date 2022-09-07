<?php

namespace Bitrix\Affettaseo;

use COption;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Application;

class Utils {

    public static function onPageStartHendler()
    {
        global $APPLICATION;
        if(stripos($APPLICATION->GetCurDir(), '/bitrix/') !== false) return;
        if(COption::GetOptionString('affettaseo', "MODULE_ACTIVE") != 'Y') return;

        $cache = Cache::createInstance(); // Служба кеширования
        $taggedCache = Application::getInstance()->getTaggedCache(); // Служба пометки кеша тегами

        $cachePath = 'affettaseo_module';
        $cacheTtl = 3600;
        $cacheKey = 'affettaseo_module_first'.$APPLICATION->GetCurDir();
        $res['TARGET'] = $cacheKey;

        if ($cache->initCache($cacheTtl, $cacheKey, $cachePath)) {
            $res = $cache->getVars();

        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache($cachePath);

            //---------------------------------------------------------------
            $hlbl = COption::GetOptionString('affettaseo', "HB_ID");

            if(!\Bitrix\Main\Loader::includeModule("highloadblock")) return;
            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
            if(!$hlblock) return;

            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();

            try{
                $rsData = $entity_data_class::getList(array(
                    "select" => array("UF_H1"),
                    "order" => array("ID" => "ASC"),
                    "filter" => array("UF_URL" => $APPLICATION->GetCurDir(), "UF_ACTIVE" => true)
                ));

                if ($arData = $rsData->Fetch()) {
                    if (!empty($arData['UF_H1'])) $res['UF_H1'] = $arData['UF_H1'];
                }
            } catch (Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }
            //----------------------------------------------------------------------------

            $taggedCache->registerTag('affettaseo_module_cache');

            $cacheInvalid = false;
            if ($cacheInvalid) {
                $taggedCache->abortTagCache();
                $cache->abortDataCache();
            }

            $taggedCache->endTagCache();
            $cache->endDataCache($res);
        }

        if (isset($res['UF_H1']) && !empty($res['UF_H1'])) $APPLICATION->SetDirProperty('h1', $res['UF_H1']);
        if (isset($res['UF_H1']) && !empty($res['UF_H1'])) $APPLICATION->SetPageProperty('h1', $res['UF_H1']);
    }

    public static function onEpilogHendler()
    {
        global $APPLICATION;
        if(stripos($APPLICATION->GetCurDir(), '/bitrix/') !== false) return;
        if(COption::GetOptionString('affettaseo', "MODULE_ACTIVE") != 'Y') return;

        $cache = Cache::createInstance(); // Служба кеширования
        $taggedCache = Application::getInstance()->getTaggedCache(); // Служба пометки кеша тегами

        $cachePath = 'affettaseo_module';
        $cacheTtl = 3600;
        $cacheKey = 'affettaseo_module_second'.$APPLICATION->GetCurDir();
        $res['TARGET'] = $cacheKey;

        if ($cache->initCache($cacheTtl, $cacheKey, $cachePath)) {
            $res = $cache->getVars();

        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache($cachePath);

            //---------------------------------------------------------------
            $hlbl = COption::GetOptionString('affettaseo', "HB_ID");

            if(!\Bitrix\Main\Loader::includeModule("highloadblock")) return;
            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
            if(!$hlblock) return;

            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();

            try{
                $rsData = $entity_data_class::getList(array(
                    "select" => array("UF_TITLE","UF_DESCRIPTION"),
                    "order" => array("ID" => "ASC"),
                    "filter" => array("UF_URL" => $APPLICATION->GetCurDir(), "UF_ACTIVE" => true)
                ));

                if ($arData = $rsData->Fetch()) {
                    if (!empty($arData['UF_TITLE'])) $res['UF_TITLE'] = $arData['UF_TITLE'];
                    if (!empty($arData['UF_DESCRIPTION'])) $res['UF_DESCRIPTION'] = $arData['UF_DESCRIPTION'];
                }
            } catch (Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }
            //----------------------------------------------------------------------------

            $taggedCache->registerTag('affettaseo_module_cache');

            $cacheInvalid = false;
            if ($cacheInvalid) {
                $taggedCache->abortTagCache();
                $cache->abortDataCache();
            }

            $taggedCache->endTagCache();
            $cache->endDataCache($res);
        }

        if (isset($res['UF_TITLE']) && !empty($res['UF_TITLE'])) $APPLICATION->SetTitle($res['UF_TITLE']);
        if (isset($res['UF_DESCRIPTION']) && !empty($res['UF_DESCRIPTION'])) $APPLICATION->SetPageProperty('description', $res['UF_DESCRIPTION'], $APPLICATION->GetCurDir());
    }
}