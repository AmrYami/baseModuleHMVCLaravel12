<?php

namespace App\Helpers;

class MoreImplementation
{
    private static $with = [];
    private static $withQuery = [];// using with function to add conditions
    private static $orWhere = [];
    private static $whereIn = [];
    private static $whereHas = [];
    private static $whereDoesntHave = [];
    private static $moreConditionsInFirstLevel = [];

    public static function reset()
    {
        self::$with = [];
        self::$orWhere = [];
        self::$whereIn = [];
        self::$whereHas = [];
        self::$whereDoesntHave = [];
        self::$withQuery = [];
        self::$moreConditionsInFirstLevel = [];
    }

    /**
     * @return array
     */
    public static function getWith(): array
    {
        return self::$with;
    }

    /**
     * @param array $with
     */
    public static function setWith(array $with): void
    {
        self::$with = array_merge(self::$with, $with);
    }

    /**
     * @return array
     */
    public static function getMoreConditionsInFirstLevel(): array
    {
        return self::$moreConditionsInFirstLevel;
    }

    /**
     * @param array $moreConditionsInFirstLevel
     */
    public static function setMoreConditionsInFirstLevel(array $moreConditionsInFirstLevel): void
    {
        self::$moreConditionsInFirstLevel = array_merge(self::$moreConditionsInFirstLevel, $moreConditionsInFirstLevel);
    }

    /**
     * @return array
     */
    public static function getWithQuery(): array
    {
        return self::$withQuery;
    }

    /**
     * @param array $withQuery
     */
    public static function setWithQuery(array $withQuery): void
    {
        self::$withQuery = array_merge(self::$withQuery, $withQuery);
    }

    /**
     * @return array
     */
    public static function getOrWhere()
    {
        return self::$orWhere;
    }

    /**
     * @param array $orWhere
     */
    public static function setOrWhere(array $orWhere): void
    {
        self::$orWhere = array_merge(self::$orWhere, $orWhere);
    }

    /**
     * @return array
     */
    public static function getWhereIn(): array
    {
        return self::$whereIn;
    }

    /**
     * @param array $whereIn
     */
    public static function setWhereIn(array $whereIn): void
    {
        self::$whereIn = array_merge(self::$whereIn, $whereIn);
    }

    /**
     * @return array
     */
    public static function getWhereHas(): array
    {
        return self::$whereHas;
    }


    /**
     * @param array $whereHas
     */
    public static function setWhereHas(array $whereHas): void
    {
        self::$whereHas = array_merge(self::$whereHas, $whereHas);
    }

    /**
     * @return array
     */
    public static function getWhereDoesntHave(): array
    {
        return self::$whereDoesntHave;
    }


    /**
     * @param array $whereDoesntHave
     */
    public static function setWhereDoesntHave(array $whereDoesntHave): void
    {
        self::$whereDoesntHave = array_merge(self::$whereDoesntHave, $whereDoesntHave);
    }
}
