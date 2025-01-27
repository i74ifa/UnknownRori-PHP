<?php

namespace Core;

use Core\Database\DB;
use Exception;

class Model implements IModel
{
    protected $table;

    protected $primary_key = 'id';

    /**
     * Find specific id value inside the model table
     * can get value in other table using relation but only once
     */
    public static function find($id, $relation = null)
    {
        $self = new static;

        if ($relation != null) {
            if (property_exists($self, $relation)) {
                $data = $self->$relation();
                $foreign_model = new $data[0]();

                if ($relation == 'hasMany') {
                    $query = "SELECT 
                    {$self->table}.{$self->primary_key} as {$self->table}_id, {$self->table}.*,
                    {$foreign_model->table}.{$foreign_model->primary_key} as {$foreign_model->table}_id, {$foreign_model->table}.*
                    FROM users 
                    LEFT JOIN post ON {$foreign_model->table}.{$data[1]} = {$self->table}.{$self->primary_key}
                    WHERE {$self->table}.{$self->primary_key}=?";
                    $method = 'fetchAll';
                } else if ($relation == 'belongsTo') {
                    $query = "SELECT {$self->table}.{$self->primary_key} as {$self->table}_id, {$self->table}.*, 
                    {$foreign_model->table}.{$foreign_model->primary_key} as {$foreign_model->table}_id, {$foreign_model->table}.* 
                    FROM users LEFT JOIN post ON {$foreign_model->table}.{$foreign_model->primary_key} = {$self->table}.{$data[1]} 
                    WHERE {$self->table}.{$self->primary_key}=?";
                    $method = 'fetch';
                }
                $result = DB::prepare($query)->$method([$id]);

                // Filter the result and remove all number

                if (is_array($result->get()[0])) {
                    $result->map(function ($data) {
                        return array_filter($data, function ($key) {
                            if (is_int($key)) return false;
                            return true;
                        }, ARRAY_FILTER_USE_KEY);
                    });
                } else {
                    $result->filter(function ($key) {
                        if (is_int($key)) return false;
                        return true;
                    });
                }
                $result->save();

                return $result;
            } else {
                throw new Exception("Undefined {$relation}, did you forget to attach these property in Model?");
            }
        }


        return DB::table($self->table)->find($id);
    }

    /**
     * Get all value inside the model table
     * can get value in other table using relation but only once
     * @param $relation string belongsTo, hasMany
     */
    public static function all($relation = null)
    {
        $self = new static;

        if (!is_null($relation)) {
            if (property_exists($self, $relation)) {
                $data = $self->$relation();
                $foreign_model = new $data[0]();

                if ($relation == 'hasMany') {
                    $query = "SELECT 
                    {$self->table}.{$self->primary_key} as {$self->table}_id, {$self->table}.*,
                    {$foreign_model->table}.{$foreign_model->primary_key} as {$foreign_model->table}_id, {$foreign_model->table}.*, 
                    {$self->table}.{$self->primary_key} as {$foreign_model->table}_{$self->table}_id
                    FROM users 
                    LEFT JOIN post ON {$foreign_model->table}.{$data[1]} = {$self->table}.{$self->primary_key}";
                } else if ($relation == 'belongsTo') {
                    $query = "SELECT 
                    {$self->table}.id as {$self->table}_id, {$self->table}.*,
                    {$foreign_model->table}.{$foreign_model->primary_key} as {$foreign_model->table}_id, {$foreign_model->table}.*, 
                    {$self->table}.{$foreign_model->table}_id as {$foreign_model->table}_{$self->table}_id
                    FROM users 
                    LEFT JOIN post ON {$foreign_model->table}.id = {$self->table}.{$data[1]}";
                }

                // Filter the result and remove all number

                $result = DB::prepare($query)->fetchAll();
                $result->map(function ($data) {
                    return array_filter($data, function ($key) {
                        if (is_int($key)) return false;
                        return true;
                    }, ARRAY_FILTER_USE_KEY);
                });

                $result->save();

                return $result;
            } else {
                throw new Exception("Undefined {$relation}, did you forget to attach these property in Model??");
            }
        }

        return DB::table($self->table)->all();
    }

    /**
     * Run sql where command inside the model table
     */
    public static function where($column, $value, $logic = '=')
    {
        $self = new static;
        return DB::table($self->table)->where($column, $value, $logic);
    }

    /**
     * Run sql insert inside model table
     */
    public static function insert(array $data)
    {
        $self = new static;
        return DB::table($self->table)->insert($data);
    }

    /**
     * Just like all this one do paginate thing inside the model table
     */
    public static function paginate($perPage)
    {
        $self = new static;
        return DB::table($self->table)->paginate($perPage);
    }

    protected function belongsTo()
    {
        return $this->belongsTo;
    }

    protected function hasMany()
    {
        return $this->hasMany;
    }
}
