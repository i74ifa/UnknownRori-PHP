<?php

namespace Core;

interface IModel
{
    public static function find($id, $relation = null);
    public static function all($relation = null);
    public static function insert(array $data);
    public static function where($column, $value, $logic = '=');
    public static function paginate($perPage);
}
