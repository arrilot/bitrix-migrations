<?php

namespace Arrilot\BitrixMigrations;

class Helpers
{
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
}
