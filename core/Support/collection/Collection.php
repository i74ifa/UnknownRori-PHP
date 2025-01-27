<?php

namespace Core\Support;

use Core\Database\DB;
use Core\Support\Http\Request;

class Collection implements ICollection
{
    protected $table;
    protected $original;
    public $data;
    public $pagination;

    /**
     * Initialize Collection Instance
     */
    public function __construct($data)
    {
        $this->original = $data;
        $this->data = $data;
    }

    /**
     * Getting Collection Original Data
     */

    /**
     * Get the first original collection value
     */
    public function first()
    {
        return $this->original[0];
    }

    /**
     * Get the last original collection value
     */
    public function last()
    {
        return $this->original[count($this->original) - 1];
    }

    /**
     * Find value in the collection and return the key
     */
    public function find($needle)
    {
        return array_search($needle, $this->original);
    }

    /**
     * Fetch the Collection Original Data
     */
    public function get($key = null)
    {
        if (!is_null($key)) return $this->original[$key];

        return $this->original;
    }

    /**
     * Fetch the key inside the Collection Original Data
     */
    public function key()
    {
        return array_keys($this->original);
    }

    public function is_null()
    {
        return is_null($this->original);
    }

    /**
     * Collection Manipulation
     */

    /**
     * Map the Original Value of Collection
     */
    public function map($callback)
    {
        $this->data = array_map($callback, $this->original);
        return $this;
    }

    /**
     * Split the Original Value of Collection into smaller array
     */
    public function split(int $length)
    {
        $this->data = array_chunk($this->original, $length);
        return $this;
    }

    /**
     * Remove specific key in the collection
     */
    public function remove(array $key)
    {
        for ($j = 0; $j < count($key); $j++) {
            if (array_key_exists($key[$j], $this->original)) {
                unset($this->data[$key[$j]]);
            }
        }
        return $this;
    }

    /**
     * Fetch current manipulated data
     */
    public function getData($key = null)
    {
        if (!is_null($key)) return $this->data[$key];
        return $this->data;
    }

    /**
     * Push the value inside the collection
     */
    public function push(string|int $val)
    {
        array_push($this->data, $val);
        return $this;
    }

    /** 
     * Merge the input array into Collection
     */
    public function merge(array $array)
    {
        $this->data = array_merge_recursive($this->data, $array);
        return $this;
    }

    /**
     * Fill the collection key
     */
    public function fill(array $array)
    {
        $data_key = array_keys($this->data);
        $array_keys = array_keys($array);

        for ($i = 0; $i < count($this->data); $i++) {
            for ($j = 0; $j < count($array); $j++) {
                if ($data_key[$i] == $array_keys[$j]) {
                    $this->data[$data_key[$i]] = $array[$array_keys[$j]];
                }
            }
        }

        return $this;
    }

    /**
     * Destroy collection
     */
    public static function destroy($collection)
    {
        unset($collection);
    }

    /**
     * Filter original data collection
     */
    public function filter($callback, $mode = ARRAY_FILTER_USE_KEY)
    {
        $this->data = array_filter($this->original, $callback, $mode);
        return $this;
    }

    /**
     * Persist the change
     * if the collection has table object property it will try to persist on database
     */
    public function save()
    {
        $this->original = $this->data;
        if (!is_null($this->table)) {
            $this->filter(function ($key) {
                if (is_int($key)) {
                    return false;
                }
                return true;
            });

            return DB::table($this->table)->update($this->data);
        }
    }

    /**
     * Integrate Collection into Database Collection
     */

    public function set_table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Database Pagination
     */
    /**
     * Setting up pagination object property per page item inside collection
     */
    public function set_perPage(int $perPage)
    {
        $this->pagination['per-page'] = $perPage;
        return $this;
    }

    /**
     * Setting up pagination object property total item inside collection
     */
    public function set_total(int $total)
    {
        $this->pagination['total'] = $total;
        return $this;
    }

    /**
     * Next Page
     */
    public function nextPageUrl()
    {
        // $displayed = $this->pagination['per-page'] * Request::Get()['page'];
        if ($this->pagination['total'] > ($this->pagination['per-page'] * Request::Get()['page'])) {
            return Request::URI() . '?page=' . Request::Get()['page'] + 1;
        } else {
            return;
        }
    }

    /**
     * Previous Page
     */
    public function previousPageUrl()
    {
        if ($this->pagination['total'] < ($this->pagination['per-page'] * Request::Get()['page'])) {
            return Request::URI() . '?page=' . Request::Get()['page'] - 1;
        } else {
            return;
        }
    }
}
