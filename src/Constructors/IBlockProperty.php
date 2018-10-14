<?php


namespace Arrilot\BitrixMigrations\Constructors;


use Arrilot\BitrixMigrations\Logger;
use Bitrix\Main\Application;

class IBlockProperty
{
    use FieldConstructor;

    /**
     * Добавить свойство инфоблока
     * @throws \Exception
     */
    public function add()
    {
        $obj = new \CIBlockProperty();
        if (!$obj->Add($this->getFieldsWithDefault())) {
            throw new \Exception($obj->LAST_ERROR);
        }

        Logger::log("Добавлено свойство инфоблока {$this->fields['ID']}", Logger::COLOR_GREEN);
    }

    /**
     * Обновить свойство инфоблока
     * @param $id
     * @throws \Exception
     */
    public function update($id)
    {
        $obj = new \CIBlockProperty();
        if (!$obj->Update($id, $this->fields)) {
            throw new \Exception($obj->LAST_ERROR);
        }

        Logger::log("Обновлено свойство инфоблока {$id}", Logger::COLOR_GREEN);
    }

    /**
     * Удалить свойство инфоблока
     * @param $id
     * @throws \Exception
     */
    public static function delete($id)
    {
        Application::getConnection()->startTransaction();
        if (!\CIBlockProperty::Delete($id)) {
            Application::getConnection()->rollbackTransaction();
            throw new \Exception('Ошибка при удалении свойства инфоблока');
        }

        Application::getConnection()->commitTransaction();

        Logger::log("Удалено свойство инфоблока {$id}", Logger::COLOR_GREEN);
    }
}