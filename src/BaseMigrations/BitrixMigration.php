<?php

namespace Arrilot\BitrixMigrations\BaseMigrations;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use CIBlock;
use CIBlockProperty;
use CUserTypeEntity;

class BitrixMigration implements MigrationInterface
{
    /**
     * DB connection.
     *
     * @var Connection
     */
    protected $db;

    /**
     * @var bool
     */
    public $use_transaction = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->db = Application::getConnection();
    }

    /**
     * Run the migration.
     *
     * @return mixed
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     */
    public function down()
    {
        //
    }

    /**
     * Does migration use transaction
     * @param bool $default
     * @return bool
     */
    public function useTransaction($default = false)
    {
        if (!is_null($this->use_transaction)) {
            return $this->use_transaction;
        }

        return $default;
    }

    /**
     * Find iblock id by its code.
     *
     * @param string $code
     * @param null|string $iBlockType
     *
     * @throws MigrationException
     *
     * @return int
     */
    protected function getIblockIdByCode($code, $iBlockType = null)
    {
        if (!$code) {
            throw new MigrationException('Не задан код инфоблока');
        }

        $filter = [
            'CODE'              => $code,
            'CHECK_PERMISSIONS' => 'N',
        ];

        if ($iBlockType !== null) {
            $filter['TYPE'] = $iBlockType;
        }

        $iblock = (new CIBlock())->GetList([], $filter)->fetch();

        if (!$iblock['ID']) {
            throw new MigrationException("Не удалось найти инфоблок с кодом '{$code}'");
        }

        return $iblock['ID'];
    }

    /**
     * Delete iblock by its code.
     *
     * @param string $code
     *
     * @throws MigrationException
     *
     * @return void
     */
    protected function deleteIblockByCode($code)
    {
        $id = $this->getIblockIdByCode($code);

        $this->db->startTransaction();
        if (!CIBlock::Delete($id)) {
            $this->db->rollbackTransaction();
            throw new MigrationException('Ошибка при удалении инфоблока');
        }

        $this->db->commitTransaction();
    }

    /**
     * Add iblock element property.
     *
     * @param array $fields
     *
     * @throws MigrationException
     *
     * @return int
     */
    public function addIblockElementProperty($fields)
    {
        $ibp = new CIBlockProperty();
        $propId = $ibp->add($fields);

        if (!$propId) {
            throw new MigrationException('Ошибка при добавлении свойства инфоблока '.$ibp->LAST_ERROR);
        }

        return $propId;
    }

    /**
     * Delete iblock element property.
     *
     * @param string     $code
     * @param string|int $iblockId
     *
     * @throws MigrationException
     */
    public function deleteIblockElementPropertyByCode($iblockId, $code)
    {
        if (!$iblockId) {
            throw new MigrationException('Не задан ID инфоблока');
        }

        if (!$code) {
            throw new MigrationException('Не задан код свойства');
        }

        $id = $this->getIblockPropIdByCode($code, $iblockId);

        CIBlockProperty::Delete($id);
    }

    /**
     * Add User Field.
     *
     * @param $fields
     *
     * @throws MigrationException
     *
     * @return int
     */
    public function addUF($fields)
    {
        if (!$fields['FIELD_NAME']) {
            throw new MigrationException('Не заполнен FIELD_NAME');
        }

        if (!$fields['ENTITY_ID']) {
            throw new MigrationException('Не заполнен код ENTITY_ID');
        }

        $oUserTypeEntity = new CUserTypeEntity();

        $fieldId = $oUserTypeEntity->Add($fields);

        if (!$fieldId) {
            throw new MigrationException("Не удалось создать пользовательское свойство с FIELD_NAME = {$fields['FIELD_NAME']} и ENTITY_ID = {$fields['ENTITY_ID']}");
        }

        return $fieldId;
    }

    /**
     * Get UF by its code.
     *
     * @param string $entity
     * @param string $code
     *
     * @throws MigrationException
     */
    public function getUFIdByCode($entity, $code)
    {
        if (!$entity) {
            throw new MigrationException('Не задана сущность свойства');
        }

        if (!$code) {
            throw new MigrationException('Не задан код свойства');
        }

        $filter = [
            'ENTITY_ID'  => $entity,
            'FIELD_NAME' => $code,
        ];

        $arField = CUserTypeEntity::GetList(['ID' => 'ASC'], $filter)->fetch();
        if (!$arField || !$arField['ID']) {
            throw new MigrationException("Не найдено свойство с FIELD_NAME = {$filter['FIELD_NAME']} и ENTITY_ID = {$filter['ENTITY_ID']}");
        }

        return $arField['ID'];
    }

    /**
     * @param $code
     * @param $iblockId
     *
     * @throws MigrationException
     *
     * @return array
     */
    protected function getIblockPropIdByCode($code, $iblockId)
    {
        $filter = [
            'CODE'      => $code,
            'IBLOCK_ID' => $iblockId,
        ];

        $prop = CIBlockProperty::getList(['sort' => 'asc', 'name' => 'asc'], $filter)->getNext();
        if (!$prop || !$prop['ID']) {
            throw new MigrationException("Не удалось найти свойство с кодом '{$code}'");
        }

        return $prop['ID'];
    }
}
