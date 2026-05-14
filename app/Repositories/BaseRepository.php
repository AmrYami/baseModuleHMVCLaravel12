<?php

namespace App\Repositories;

use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @throws Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Make Model instance
     *
     * @throws Exception
     */
    public function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (! $model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    // for search make request has value => array(operator, $value)

    public function search($query, $search)
    {
        if (! is_countable($search) || count($search) === 0) {
            return $query;
        }

        foreach ($search as $key => $value) {
            if (! in_array($key, $this->getFieldsSearchable())) {
                continue;
            }

            if (is_array($value)) {
                if (count($value) < 2) {
                    continue;
                }

                $query->where($key, $value[0], $value[1]);
            } else {
                $query->where($key, $value);
            }
        }

        return $query;
    }
}
