<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use Arrilot\BitrixMigrations\Exceptions\SkipHandlerException;
use CIBlockProperty;
use CIBlockPropertyEnum;

class OnBeforeIBlockPropertyUpdate extends BaseHandler implements HandlerInterface
{
    /**
     * Old property fields from DB.
     *
     * @var array
     */
    protected $dbFields;

    /**
     * Constructor.
     *
     * @param array $params
     *
     * @throws SkipHandlerException
     */
    public function __construct($params)
    {
        $this->fields = $params[0];

        $this->dbFields = $this->collectPropertyFieldsFromDB();

        if (!$this->propertyHasChanged() || !$this->fields['IBLOCK_ID']) {
            throw new SkipHandlerException();
        }
    }

    /**
     * Get migration name.
     *
     * @return string
     */
    public function getName()
    {
        return "auto_update_iblock_element_property_{$this->fields['CODE']}_in_ib_{$this->fields['IBLOCK_ID']}";
    }

    /**
     * Get template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'auto_update_iblock_element_property';
    }

    /**
     * Get array of placeholders to replace.
     *
     * @return array
     */
    public function getReplace()
    {
        return [
            'fields'   => var_export($this->fields, true),
            'iblockId' => $this->fields['IBLOCK_ID'],
            'code'     => "'".$this->fields['CODE']."'",
            'oldCode'     => "'".$this->dbFields['CODE']."'",
        ];
    }

    /**
     * Collect property fields from DB and convert them to format that can be compared from user input.
     *
     * @return array
     */
    protected function collectPropertyFieldsFromDB()
    {
        $fields = CIBlockProperty::getByID($this->fields['ID'])->fetch();
        $fields['VALUES'] = [];

        $filter = [
            'IBLOCK_ID'   => $this->fields['IBLOCK_ID'],
            'PROPERTY_ID' => $this->fields['ID'],
        ];
        $sort = [
            'SORT'  => 'ASC',
            'VALUE' => 'ASC',
        ];

        $propertyEnums = CIBlockPropertyEnum::GetList($sort, $filter);
        while ($v = $propertyEnums->GetNext()) {
            $fields['VALUES'][$v['ID']] = [
                'ID'     => $v['ID'],
                'VALUE'  => $v['VALUE'],
                'SORT'   => $v['SORT'],
                'XML_ID' => $v['XML_ID'],
                'DEF'    => $v['DEF'],
            ];
        }

        return $fields;
    }

    /**
     * Determine if property has been changed.
     *
     * @return bool
     */
    protected function propertyHasChanged()
    {
        foreach ($this->dbFields as $field => $value) {
            if (isset($this->fields[$field]) && ($this->fields[$field] != $value)) {
                return true;
            }
        }

        return false;
    }
}
