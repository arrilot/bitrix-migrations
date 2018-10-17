<?php


namespace Arrilot\BitrixMigrations\Constructors;


use Arrilot\BitrixMigrations\Logger;
use Bitrix\Main\Application;

class IBlockType
{
    use FieldConstructor;

    /**
     * Добавить тип инфоблока
     * @throws \Exception
     */
    public function add()
    {
        $obj = new \CIBlockType();
        if (!$obj->Add($this->getFieldsWithDefault())) {
            throw new \Exception($obj->LAST_ERROR);
        }

        Logger::log("Добавлен тип инфоблока {$this->fields['ID']}", Logger::COLOR_GREEN);
    }

    /**
     * Обновить тип инфоблока
     * @param $id
     * @throws \Exception
     */
    public function update($id)
    {
        $obj = new \CIBlockType();
        if (!$obj->Update($id, $this->fields)) {
            throw new \Exception($obj->LAST_ERROR);
        }

        Logger::log("Обновлен тип инфоблока {$id}", Logger::COLOR_GREEN);
    }

    /**
     * Удалить тип инфоблока
     * @param $id
     * @throws \Exception
     */
    public static function delete($id)
    {
        if (!\CIBlockType::Delete($id)) {
            throw new \Exception('Ошибка при удалении типа инфоблока');
        }

        Logger::log("Удален тип инфоблока {$id}", Logger::COLOR_GREEN);
    }

    /**
     * ID типа информационных блоков. Уникален.
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->fields['ID'] = $id;

        return $this;
    }

    /**
     * Разделяются ли элементы блока этого типа по разделам.
     * @param bool $has
     * @return $this
     */
    public function setSections($has = true)
    {
        $this->fields['SECTIONS'] = $has ? 'Y' : 'N';

        return $this;
    }

    /**
     * Полный путь к файлу-обработчику массива полей элемента перед сохранением на странице редактирования элемента.
     * @param string $editFileBefore
     * @return $this
     */
    public function setEditFileBefore($editFileBefore)
    {
        $this->fields['EDIT_FILE_BEFORE'] = $editFileBefore;

        return $this;
    }

    /**
     * Полный путь к файлу-обработчику вывода интерфейса редактирования элемента.
     * @param string $editFileAfter
     * @return $this
     */
    public function setEditFileAfter($editFileAfter)
    {
        $this->fields['EDIT_FILE_AFTER'] = $editFileAfter;

        return $this;
    }

    /**
     * Блоки данного типа экспортировать в RSS
     * @param bool $inRss
     * @return $this
     */
    public function setInRss($inRss = false)
    {
        $this->fields['IN_RSS'] = $inRss ? 'Y' : 'N';

        return $this;
    }

    /**
     * Порядок сортировки типа
     * @param int $sort
     * @return $this
     */
    public function setSort($sort = 500)
    {
        $this->fields['SORT'] = $sort;

        return $this;
    }

    /**
     * Указать языковые фразы
     * @param string $lang ключ языка (ru)
     * @param string $name
     * @param string $sectionName
     * @param string $elementName
     * @return $this
     */
    public function setLang($lang, $name, $sectionName = null, $elementName = null)
    {
        $setting = ['NAME' => $name];

        if ($sectionName) {
            $setting['SECTION_NAME'] = $sectionName;
        }
        if ($elementName) {
            $setting['ELEMENT_NAME'] = $elementName;
        }

        $this->fields['LANG'][$lang] = $setting;

        return $this;
    }
}