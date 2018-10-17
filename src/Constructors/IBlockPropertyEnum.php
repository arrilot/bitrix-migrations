<?php


namespace Arrilot\BitrixMigrations\Constructors;


use Arrilot\BitrixMigrations\Logger;
use Bitrix\Main\Application;

class IBlockPropertyEnum
{
    use FieldConstructor;

    /**
     * Добавить значение списка
     * @throws \Exception
     */
    public function add()
    {
        $obj = new \CIBlockPropertyEnum();

        $property_enum_id = $obj->Add($this->getFieldsWithDefault());

        if (!$property_enum_id) {
            throw new \Exception("Ошибка добавления значения enum");
        }

        Logger::log("Добавлено значение списка enum {$this->fields['VALUE']}", Logger::COLOR_GREEN);

        return $property_enum_id;
    }

    /**
     * Обновить свойство инфоблока
     * @param $id
     * @throws \Exception
     */
    public function update($id)
    {
        $obj = new \CIBlockPropertyEnum();
        if (!$obj->Update($id, $this->fields)) {
            throw new \Exception("Ошибка обновления значения enum");
        }

        Logger::log("Обновлено значение списка enum {$id}", Logger::COLOR_GREEN);
    }

    /**
     * Удалить свойство инфоблока
     * @param $id
     * @throws \Exception
     */
    public static function delete($id)
    {
        if (!\CIBlockPropertyEnum::Delete($id)) {
            throw new \Exception('Ошибка при удалении значения enum');
        }

        Logger::log("Удалено значение списка enum {$id}", Logger::COLOR_GREEN);
    }

    /**
     * Установить настройки для добавления значения enum инфоблока по умолчанию
     * @param string $xml_id
     * @param string $value
     * @param int $propertyId
     * @return $this
     */
    public function constructDefault($xml_id, $value, $propertyId = null)
    {
         $this->setXmlId($xml_id)->setValue($value);

         if ($propertyId) {
             $this->setPropertyId($propertyId);
         }

         return $this;
    }

    /**
     * Код свойства.
     * @param string $propertyId
     * @return $this
     */
    public function setPropertyId($propertyId)
    {
        $this->fields['PROPERTY_ID'] = $propertyId;

        return $this;
    }

    /**
     * Внешний код.
     * @param string $xml_id
     * @return $this
     */
    public function setXmlId($xml_id)
    {
        $this->fields['XML_ID'] = $xml_id;

        return $this;
    }

    /**
     * Индекс сортировки.
     * @param int $sort
     * @return $this
     */
    public function setSort($sort = 500)
    {
        $this->fields['SORT'] = $sort;

        return $this;
    }

    /**
     * Значение варианта свойства.
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->fields['VALUE'] = $value;

        return $this;
    }

    /**
     * Значение варианта свойства.
     * @param bool $def
     * @return $this
     */
    public function setDef($def)
    {
        $this->fields['DEF'] = $def ? 'Y' : 'N';

        return $this;
    }
}