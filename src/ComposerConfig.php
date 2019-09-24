<?php

namespace Arrilot\BitrixMigrations;

use RuntimeException;

/**
 * @method array|null getExtra()
*/
class ComposerConfig
{
    /**
     * Массив с данными из composer.json
    */
    private $config = null;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $file
     * @return ComposerConfig
     * @throws RuntimeException
     */
    public static function createFromFile($file)
    {
        $content = file_get_contents($file);

        if (file_exists($file) === false || $content === false) {
            throw new RuntimeException('Переданный файл не существует или не смогли считать данные из него');
        }

        $config = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Не удалось декодировать данные из файла");
        }

        return new static($config);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        $isGetMethod = strpos($name, 'get');

        if ($isGetMethod === false || $isGetMethod !== 0) {
            throw new RuntimeException("Не существует метода ${name}");
        }

        $propertyName = substr($name, 3);

        if ($propertyName === false) {
            throw new RuntimeException("Не удалось извлечь имя свойства из метода ${name}");
        }

        $propertyName = strtolower($propertyName);

        if (!isset($this->config[$propertyName])) {
            throw new RuntimeException("Не существует свойства для метода ${name}");
        }

        return $this->config[$propertyName];
    }
}