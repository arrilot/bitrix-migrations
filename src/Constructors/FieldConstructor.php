<?php


namespace Arrilot\BitrixMigrations\Constructors;


trait FieldConstructor
{
    /** @var array */
    public $fields = [];

    public static $defaultFields = [];

    /**
     * Получить итоговые настройки полей
     */
    public function getFieldsWithDefault()
    {
        return array_merge((array)static::$defaultFields[get_called_class()], $this->fields);
    }
}