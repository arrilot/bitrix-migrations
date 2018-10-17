<?php


namespace Arrilot\BitrixMigrations\Constructors;


use Arrilot\BitrixMigrations\Helpers;
use Arrilot\BitrixMigrations\Logger;
use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable;

class HighloadBlock
{
    use FieldConstructor;

    public $lang;

    /**
     * Добавить HL
     * @throws \Exception
     */
    public function add()
    {
        $result = HighloadBlockTable::add($this->getFieldsWithDefault());

        if (!$result->isSuccess()) {
            throw new \Exception(join(', ', $result->getErrorMessages()));
        }

        foreach ($this->lang as $lid => $name) {
            HighloadBlockLangTable::add([
                "ID" => $result->getId(),
                "LID" => $lid,
                "NAME" => $name
            ]);
        }

        Logger::log("Добавлен HL {$this->fields['NAME']}", Logger::COLOR_GREEN);

        return $result->getId();
    }

    /**
     * Обновить HL
     * @param $table_name
     * @throws \Exception
     */
    public function update($table_name)
    {
        $id = Helpers::getHlId($table_name);
        $result = HighloadBlockTable::update($id, $this->fields);

        if (!$result->isSuccess()) {
            throw new \Exception(join(', ', $result->getErrorMessages()));
        }

        Logger::log("Обновлен HL {$table_name}", Logger::COLOR_GREEN);
    }

    /**
     * Удалить HL
     * @param $table_name
     * @throws \Exception
     */
    public static function delete($table_name)
    {
        $id = Helpers::getHlId($table_name);
        $result = HighloadBlockTable::delete($id);

        if (!$result->isSuccess()) {
            throw new \Exception(join(', ', $result->getErrorMessages()));
        }

        Logger::log("Удален HL {$table_name}", Logger::COLOR_GREEN);
    }

    /**
     * Установить настройки для добавления HL по умолчанию
     * @param string $name Название highload-блока
     * @param string $table_name Название таблицы с элементами highload-блока.
     * @return $this
     */
    public function constructDefault($name, $table_name)
    {
        return $this->setName($name)->setTableName($table_name);
    }

    /**
     * Название highload-блока.
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->fields['NAME'] = $name;

        return $this;
    }

    /**
     * Название таблицы с элементами highload-блока.
     * @param string $table_name
     * @return $this
     */
    public function setTableName($table_name)
    {
        $this->fields['TABLE_NAME'] = $table_name;

        return $this;
    }

    /**
     * Установить локализацию
     * @param $lang
     * @param $text
     * @return HighloadBlock
     */
    public function setLang($lang, $text)
    {
        $this->lang[$lang] = $text;

        return $this;
    }
}