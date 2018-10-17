<?php


namespace Arrilot\BitrixMigrations\Constructors;


class Constructor
{
    const OBJ_USER = 'USER'; // для пользователя
    const OBJ_BLOG_BLOG = 'BLOG_BLOG'; // для блога
    const OBJ_BLOG_POST = 'BLOG_POST'; // для сообщения в блоге
    const OBJ_BLOG_COMMENT = 'BLOG_COMMENT'; // для комментария сообщения
    const OBJ_TASKS_TASK = 'TASKS_TASK'; // для задач
    const OBJ_CALENDAR_EVENT = 'CALENDAR_EVENT'; // для событий календаря
    const OBJ_LEARN_ATTEMPT = 'LEARN_ATTEMPT'; // для попыток теста
    const OBJ_SONET_GROUP = 'SONET_GROUP'; // для групп соцсети
    const OBJ_WEBDAV = 'WEBDAV'; // для библиотек документов
    const OBJ_FORUM_MESSAGE = 'FORUM_MESSAGE'; // для сообщений форума

    /**
     * для highload-блока с ID=N
     * @param $id
     * @return string
     */
    public static function objHLBlock($id)
    {
        return "HLBLOCK_{$id}";
    }

    /**
     * для секций инфоблока с ID = N
     * @param $id
     * @return string
     */
    public static function objIBlockSection($id)
    {
        return "IBLOCK_{$id}_SECTION";
    }

    /**
     * Для инфоблока с ID = N
     * @param $id
     * @return string
     */
    public static function objIBlock($id)
    {
        return "IBLOCK_{$id}";
    }
}