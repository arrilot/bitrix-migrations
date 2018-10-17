<?php

namespace Arrilot\BitrixMigrations;

use Bitrix\Main\Application;

class Helpers
{
    protected static $hls = [];
    protected static $ufs = [];

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    /**
     * Рекурсивный поиск миграций с поддирректориях
     * @param $pattern
     * @param int $flags Does not support flag GLOB_BRACE
     * @return array
     */
    public static function rGlob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, static::rGlob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    /**
     * Получить ID HL по названию таблицы
     * @param $table_name
     * @return mixed
     */
    public static function getHlId($table_name)
    {
        if (!isset(static::$hls[$table_name])) {
            $dbRes = Application::getConnection()->query('SELECT `ID`, `NAME`, `TABLE_NAME` FROM b_hlblock_entity');
            while ($block = $dbRes->fetch()) {
                static::$hls[$block['TABLE_NAME']] = $block;
            }
        }

        return static::$hls[$table_name]['ID'];
    }

    /**
     * Получить ID UF
     * @param $obj
     * @param $field_name
     * @return mixed
     */
    public static function getFieldId($obj, $field_name)
    {
        if (!isset(static::$ufs[$obj][$field_name])) {
            $dbRes = Application::getConnection()->query('SELECT * FROM b_user_field');
            while ($uf = $dbRes->fetch()) {
                static::$ufs[$uf['ENTITY_ID']][$uf['FIELD_NAME']] = $uf;
            }
        }

        return static::$ufs[$obj][$field_name]['ID'];
    }
}
