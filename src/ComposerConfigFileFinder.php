<?php

namespace Arrilot\BitrixMigrations;

use RuntimeException;
use SplFileInfo;

class ComposerConfigFileFinder
{
    const COMPOSER_JSON_FILENAME = 'composer.json';

    /**
     * @param string $sourceDir
     * @return SplFileInfo|null
     * @throws RuntimeException
     */
    public function find($sourceDir)
    {
        $sourceDir = realpath($sourceDir);

        if ($sourceDir === false) {
            throw new RuntimeException('Не удалось получить канонизированный абсолютный путь к директории поиска');
        }

        while (!file_exists($sourceDir . DIRECTORY_SEPARATOR . self::COMPOSER_JSON_FILENAME)) {
            $parentDir = dirname($sourceDir);

            if ($parentDir === $sourceDir) {
                break;
            }

            $sourceDir = $parentDir;
        }

        $filePath = $sourceDir . DIRECTORY_SEPARATOR . self::COMPOSER_JSON_FILENAME;

        if (file_exists($filePath)) {
            return new SplFileInfo($filePath);
        }

        return null;
    }
}